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

use EdgeFramework\Build\Lexer\Lexer;
use EdgeFramework\Build\Scanner\Symbol;
use EdgeFramework\Build\Tokenizer\RawToken;
use EdgeFramework\Build\Lexer\SyntaxToken;


/**
 * @extends parent<\Generator<Symbol>>, Token[]>
 */
final class LexingStep implements Step
{
  public function process($rawTokens)
  {
    $lexer = new Lexer();
    return $lexer->tokenize($rawTokens);
  }
}
