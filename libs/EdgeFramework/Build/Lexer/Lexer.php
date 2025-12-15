<?php
namespace EdgeFramework\Build\Lexer;

use EdgeFramework\Build\Scanner\Symbol;

class Lexer
{
    /**
     * Summary of source
     * @var \Generator<int, Symbol, mixed, mixed>
     */
    public \Generator $source;

    /**
     * 
     * @param \Generator<Symbol> $source
     */
    public function __construct(\Generator $source)
    {
        $this->source = $source;
    }

    private function eof(): bool
    {
        return !$this->source->valid();
    }

    private function current()
    {
        return $this->source->current();
    }

    private function next()
    {
        if ($this->eof()) {
            return null;
        }

        $this->source->next();
        return $this->source->current();
    }

    public function tokenize()
    {
        while (!$this->eof()) {
            if ($this->current()->type === 'lcurly') {
                // {{ ... }}
                if ($peekNext()->type === 'lcurly') {
                    $templateOpenTokens = [
                        $this->next(), // {
                        $this->next(), // {
                    ];

                    $templateContentTokens = [];

                    while (!($current()->type === 'rcurly' && $peekNext()->type === 'rcurly')) {
                        $templateContentTokens[] = $this->next();
                    }

                    $templateCloseTokens = [
                        $this->next(), // }
                        $this->next(), // }
                    ];

                    $emit('templateOpen', $templateOpenTokens);
                    $emit('templateContent', $templateContentTokens);
                    $emit('templateClose', $templateCloseTokens);
                } else if ($peekNext()->type === 'exclamation' && $peekNext(2)->type === 'exclamation') {
                    $templateOpenTokens = [
                        $this->next(), // {
                        $this->next(), // !
                        $this->next(), // !
                    ];

                    $templateContentTokens = [];

                    while (!($current()->type === 'exclamation' && $peekNext()->type === 'exclamation' && $peekNext(2)->type === 'rcurly')) {
                        $templateOpenTokens[] = $this->next();
                    }

                    $templateCloseTokens = [
                        $this->next(), // ! 
                        $this->next(), // !
                        $this->next(), // }
                    ];

                    $emit('rawTemplateOpen', $templateOpenTokens);
                    $emit('rawTemplateContent', $templateCloseTokens);
                    $emit('rawTemplateClose', $templateCloseTokens);
                }
            }

            // <
            elseif ($current()->type === 'lt') {
                // allow whitespaces.
                $startTagOpen = $this->next();
                $tagName = null;

                if ($current()->type === 'word') {
                    $tagName = $this->next(); // tag
                } else if (
                    $current()->type === 'whitespace' &&
                    $peekNext()->type === 'word'
                ) {
                    $this->next(); // whitespace
                    $tagName = $this->next();   // tag
                } else {
                    // probably throw about malformed elements.
                }

                // the attributes
                $attributeTokens = [];

                while ($current()->type === 'word' || $current()->type === 'whitespace') {
                    if ($current()->type === 'whitespace') {
                        $this->next();
                    }

                    // TODO: validate this is actually a
                    // word.
                    $name = $this->next();

                    while ($current()->type === 'whitespace') {
                        $this->next();
                    }

                    // TODO: validate this is actually =
                    $equalToken = $this->next();

                    // TODO: validate this is actually a quote, else. raise error,
                    $quoteToken = $this->next();

                    if ($quoteToken->value === '"' || $quoteToken->value === '\'') {
                        $attributeInnerValueTokens = [];

                        while (true) {
                            // \' and \" are escape sequences.
                            if ($current()->value === $quoteToken && $peekPrev(1)->value === '\\')
                                break;
                            $attributeInnerValueTokens[] = $current()->value;
                        }

                        $endQuoteToken = $this->next();

                        // attr="..." is done lexing.
                        $attributeTokens[] = [$name, $quoteToken];
                    }

                    // we can parse a self closing, or a normal opening tag.
                    // we can find out which by checking for the presence of /
                    if ($current()->type === 'whitespace') {
                        $this->next(); // consume space.
                    }

                    if ($current()->value === '/') {
                        // self closing.
                        $slashToken = $this->next();
                        $gtToken = $this->next();

                        $emit('selfCloseTagStart', [$startTagOpen]);
                        $emit('tagName', [$tagName]);
                        $emit('attributes', $attributeTokens);
                        $emit('selfCloseTagEnd', [$slashToken, $gtToken]);
                    } else if ($current()->value === '>') {
                        $gtToken = $this->next();

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
