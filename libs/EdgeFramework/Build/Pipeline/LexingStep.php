<?php
/*
    todo: fix raw template parsing.
      - the raw template inner loop incorrectly pushes tokens into the open token array.
      - the content and close emits use the wrong token arrays.
      - the loop never advances correctly because the escape/quote logic is wrong.
      - the termination condition for {!! ... !!} is not correct.

    todo: fix attribute parsing.
      - comparing $current()->value === $quoteToken is wrong because $quoteToken is an object.
      - the inner attribute value loop never advances, causing infinite loops.
      - attribute value parsing does not store or emit values correctly.
      - name="value" is the only supported pattern but the code does not guarantee this.
      - attributes without quotes (width=200) should either be supported or explicitly rejected.
      - boolean attributes (disabled, selected) are not handled at all.
      - need consistent behavior for ignoring or preserving whitespace inside attribute lists.

    todo: fix tag name parsing.
      - the condition checking whitespace && word is impossible and will never run.
      - tagName extraction should explicitly allow leading whitespace after `<`.
      - malformed or missing tag names should surface a clear lexing error.

    todo: fix emits.
      - $gtToken = $next; is a bug; you forgot to call $next().
      - templateClose and rawTemplateClose emit incorrect token sets.
      - tag-level emits should be consistent (openingTagStart, openingTagEnd, tagName, attributes, etc).

    todo: implement missing html constructs.
      - closing tags </tag>.
      - text nodes between tags.
      - nested elements (requires correct streaming and multi-token emission).
      - <!doctype>, comments <!-- ... -->.
      - self-closing recognition should be hardened and strictly validated.

    todo: general lexer cleanup.
      - unify token consumption into helper fns: consume(), expect(), advance(), etc.
      - pointer closures (next, current, peek) should be hardened against out-of-range access.
      - whitespace handling should be spec-defined: skip, preserve, or branch depending on context.
      - malformed patterns should throw structured lexing errors.

    todo: template parsing improvements.
      - nested {{ }} or {!! !!} templates are not supported.
      - template content should be able to contain arbitrary raw tokens without unsafe infinite loops.
      - need consistent rules for escaping inside template blocks.

    todo: write tests for all edge cases.
      - whitespace around tag names.
      - attributes with different quoting: single, double, missing.
      - escaped quotes inside attribute values.
      - malformed tags: <tag, <tag ", <tag foo=, <tag foo=bar>.
      - template blocks with unexpected eof.
      - self-closing tags with whitespace (<img   />).
      - unexpected characters inside tag declarations.
*/


namespace EdgeFramework\Build\Pipeline;

use EdgeFramework\Build\Tokenizer\RawToken;
use EdgeFramework\Build\Lexer\SyntaxToken;


/**
 * @extends parent<array<int,RawToken>,array<int,SyntaxToken>>
 */
final class LexingStep extends Step
{
    public function process($rawTokens)
    {
        $syntaxTokens = [];
        $rawTokenCount = count($rawTokens);
        $currentIndex = 0;

        // current token + move pointer back.
        $prev = function () use ($rawTokens, &$currentIndex) {
            return $rawTokens[$currentIndex--];
        };

        // current token + move pointer forward.
        $next = function () use ($rawTokens, &$currentIndex) {
            return $rawTokens[$currentIndex++];
        };

        $peekPrev = function ($offset = 1) use ($rawTokens, &$currentIndex) {
            return $rawTokens[$currentIndex - $offset];
        };

        $peekNext = function ($offset = 1) use ($rawTokens, &$currentIndex) {
            return $rawTokens[$currentIndex + $offset];
        };

        // current token.
        $current = function () use ($rawTokens, &$currentIndex): ?RawToken {
            return $rawTokens[$currentIndex];
        };

        // tests if current token matches a pattern.
        $currentMatches = function (string $pattern) use ($current) {
            return preg_match($pattern, $current());
        };

        // emits a syntax token.
        $emit = function (string $type, array $tokens) use (&$syntaxTokens) {
            $syntaxTokens[] = new SyntaxToken($type, $tokens);
        };

        $eof = function () use ($currentIndex, $rawTokenCount) {
            return $currentIndex >= $rawTokenCount;
        };

        while (!$eof()) {
            if ($current()->type === 'lcurly') {
                // {{ ... }}
                if ($peekNext()->type === 'lcurly') {
                    $templateOpenTokens = [
                        $next(), // {
                        $next(), // {
                    ];

                    $templateContentTokens = [];

                    while (!($current()->type === 'rcurly' && $peekNext()->type === 'rcurly')) {
                        $templateContentTokens[] = $next();
                    }

                    $templateCloseTokens = [
                        $next(), // }
                        $next(), // }
                    ];

                    $emit('templateOpen', $templateOpenTokens);
                    $emit('templateContent', $templateContentTokens);
                    $emit('templateClose', $templateCloseTokens);
                } else if ($peekNext()->type === 'exclaimation' && $peekNext(2)->type === 'exclaimation') {
                    $templateOpenTokens = [
                        $next(), // {
                        $next(), // !
                        $next(), // !
                    ];

                    $templateContentTokens = [];

                    while (!($current()->type === 'exclaimation' && $peekNext()->type === 'exclaimation' && $peekNext(2)->type === 'rcurly')) {
                        $templateOpenTokens[] = $next();
                    }

                    $templateCloseTokens = [
                        $next(), // ! 
                        $next(), // !
                        $next(), // }
                    ];

                    $emit('rawTemplateOpen', $templateOpenTokens);
                    $emit('rawTemplateContent', $templateCloseTokens);
                    $emit('rawTemplateClose', $templateCloseTokens);
                }
            }

            // <
            elseif ($current()->type === 'lt') {
                // allow whitespaces.
                $startTagOpen = $next();
                $tagName = null;

                if ($current()->type === 'word') {
                    $tagName = $next(); // tag
                } else if (
                    $current()->type === 'whitespace' &&
                    $peekNext()->type === 'word'
                ) {
                    $next(); // whitespace
                    $tagName = $next();   // tag
                } else {
                    // probably throw about malformed elements.
                }

                // the attributes
                $attributeTokens = [];

                while ($current()->type === 'word' || $current()->type === 'whitespace') {
                    if ($current()->type === 'whitespace') {
                        $next();
                    }

                    // TODO: validate this is actually a
                    // word.
                    $name = $next();

                    while ($current()->type === 'whitespace') {
                        $next();
                    }

                    // TODO: validate this is actually =
                    $equalToken = $next();

                    // TODO: validate this is actually a quote, else. raise error,
                    $quoteToken = $next();

                    if ($quoteToken->value === '"' || $quoteToken->value === '\'') {
                        $attributeInnerValueTokens = [];

                        while (true) {
                            // \' and \" are escape sequences.
                            if ($current()->value === $quoteToken && $peekPrev(1)->value === '\\')
                                break;
                            $attributeInnerValueTokens[] = $current()->value;
                        }

                        $endQuoteToken = $next();

                        // attr="..." is done lexing.
                        $attributeTokens[] = [$name, $quoteToken];
                    }

                    // we can parse a self closing, or a normal opening tag.
                    // we can find out which by checking for the presence of /
                    if ($current()->type === 'whitespace') {
                        $next(); // consume space.
                    }

                    if ($current()->value === '/') {
                        // self closing.
                        $slashToken = $next();
                        $gtToken = $next();

                        $emit('selfCloseTagStart', [$startTagOpen]);
                        $emit('tagName', [$tagName]);
                        $emit('attributes', $attributeTokens);
                        $emit('selfCloseTagEnd', [$slashToken, $gtToken]);
                    } else if ($current()->value === '>') {
                        $gtToken = $next();

                        $emit('openingTagStart', [$startTagOpen]);
                        $emit('tagName', [$tagName]);
                        $emit('attributes', $attributeTokens);
                        $emit('openingTagEnd', [$gtToken]);
                    } else {
                        // TODO: Complain here.
                    }
                }
            }
        }

        return $syntaxTokens;
    }
}