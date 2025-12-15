<?php
namespace EdgeFramework\Build\Scanner;

final class Scanner
{
    public function scan(string $source)
    {
        $index = 0;
        $length = strlen($source);

        $peek = fn() => $source[$index] ?? '';
        $consume = fn() => $source[$index++] ?? '';

        $isWhitespace = fn() => preg_match('/\s/', $peek());
        $isAlpha = fn() => preg_match('/[a-zA-Z]/', $peek());
        $isDigit = fn() => preg_match('/\d/', $peek());

        while ($index < $length) {
            $char = $peek();

            if (isset(self::$singleCharSymbolRegistry[$char])) {
                yield new Symbol(self::$singleCharSymbolRegistry, $consume());
            } elseif ($isWhitespace()) {
                $buffer = $consume();

                while ($isWhitespace()) {
                    $buffer .= $consume();
                }

                yield new Symbol('whitespace', $buffer);
            } elseif ($isAlpha()) {
                $buffer = $consume();

                while ($isAlpha()) {
                    $buffer .= $consume();
                }

                yield new Symbol('word', $buffer);
            } elseif ($isDigit()) {
                $hasDecimal = false;
                $buffer = $consume();

                while (
                    $isDigit() ||
                    ($peek() === '.' && !$hasDecimal)
                ) {
                    if ($peek() === '.') {
                        $hasDecimal = true;
                    }

                    $buffer .= $consume();
                }

                yield new Symbol('number', $buffer);
            } else {
                yield new Symbol('unknown', $consume());
            }
        }

        return null;
    }

    // * some are borrowed from html entity symbol name.
    public static $singleCharSymbolRegistry = [
        '<' => 'lt',
        '>' => 'gt',
        '{' => 'lbrace',
        '}' => 'rbrace',
        '&' => 'amp',
        '.' => 'dot',
        '(' => 'lpar',
        ')' => 'rpar',
        '[' => 'lbracket',
        ']' => 'rbracket',
        '+' => 'plus',
        '-' => 'minus',
        '*' => 'asterisk',
        '/' => 'slash',
        '%' => 'percent',
        '=' => 'equals',
        '!' => 'exclaim',
        '?' => 'question',
        ':' => 'colon',
        ',' => 'comma',
        ';' => 'semicolon',
        '$' => 'dollar',
        '@' => 'at',
        '#' => 'hash',
        '_' => 'underscore',
        '\'' => 'apos',
        '"' => 'quot',
        '`' => 'backtick',
        '\\' => 'backslash',
        '\n' => 'newline'
    ];
}
