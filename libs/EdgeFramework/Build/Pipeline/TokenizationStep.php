<?php

namespace EdgeFramework\Build\Pipeline;
use EdgeFramework\Build\Tokenizer\RawToken;

/**
 * @extends parent<string, array<int, RawToken>>
 */
final class TokenizationStep extends Step
{
    public function process($source)
    {
        $singleCharTokens = [
            '<' => 'lt',
            '>' => 'gt',
            '{' => 'lcurly',
            '}' => 'rcurly',
            '&' => 'amp',
            '.' => 'dot',
            '(' => 'lparen',
            ')' => 'rparen',
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
            '\'' => 'squote',
            '"' => 'dquote',
            '`' => 'backtick',
            '\\' => 'backslash',
        ];

        $rawTokens = [];

        $currentIndex = 0;

        $prev = function () use ($source, &$currentIndex) {
            return $source[$currentIndex--];
        };

        $next = function () use ($source, &$currentIndex) {
            return $source[$currentIndex++];
        };

        $current = function () use ($source, &$currentIndex) {
            return $source[$currentIndex] ?? '';
        };

        $currentMatches = function (string $pattern) use ($current) {
            return preg_match($pattern, $current()); };

        $emit = function (string $type, string $value) use (&$rawTokens) {
            $rawTokens[] = new RawToken($type, $value);
        };

        $sourceLength = strlen($source);

        while ($currentIndex < $sourceLength) {
            if (isset($singleCharTokens[$current()])) {
                $emit($singleCharTokens[$current()], value: $current());
                $next();
            }

            // handle whitespace.
            else if ($currentMatches("/\s/")) {
                $whitespaceBuffer = $next();

                while ($currentMatches("/\s/")) {
                    $whitespaceBuffer .= $next();
                }

                $emit('whitespace', $whitespaceBuffer);
            } else if ($currentMatches('/[a-zA-Z]/')) {
                $wordBuffer = $next();

                while ($currentMatches('/[a-zA-Z]/')) {
                    $wordBuffer .= $next();
                }

                $emit('word', $wordBuffer);
            } else if ($currentMatches('/\d/')) {
                $hasEncounteredDecimalPoint = false;
                $numberBuffer = $next();

                while ($currentMatches('/\d/') || ($current() === '.' && !$hasEncounteredDecimalPoint)) {
                    $numberBuffer .= $next();
                }

                $emit('number', $numberBuffer);
            } else {
                $emit('unknown', $next());
            }
        }

        return $rawTokens;
    }
}