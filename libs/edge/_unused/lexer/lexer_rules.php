<?php

namespace AbstractHTML\Build\Lexer;

class ClosingTagLexerRule extends GrammarRule
{
    public function test(LexerState $state): bool
    {
        return $state->peek() === '<' && $this->peekAhead($state, 1) === '/';
    }

    public function tokenize(LexerState $state): Token
    {
        $state->next(); // consume '<'
        $state->next(); // consume '/'
        $tagName = $state->captureRegex("/[a-zA-Z][a-zA-Z0-9_\-]*/");
        $state->skipWhitespace();
        $state->next(); // consume '>'
        return new Token(TokenType::$ClosingTag, $tagName);
    }

    private function peekAhead(LexerState $state, int $offset): ?string
    {
        $pos = $state->position + $offset;
        if ($pos < $state->length) {
            return $state->input[$pos];
        }
        return null;
    }
}

class SelfClosingTagLexerRule extends GrammarRule
{
    public function test(LexerState $state): bool
    {
        if ($state->peek() !== '<') {
            return false;
        }
        
        // Look ahead to check if this is a self-closing tag
        $savedPos = $state->position;
        $state->next(); // consume '<'
        
        // Try to match tag name
        $tagName = $state->captureRegex("/[a-zA-Z][a-zA-Z0-9_\-]*/");
        if (empty($tagName)) {
            $state->position = $savedPos;
            return false;
        }
        
        $state->skipWhitespace();
        
        // Check for />
        $isSelfClosing = $state->peek() === '/' && $this->peekAhead($state, 1) === '>';
        
        $state->position = $savedPos;
        return $isSelfClosing;
    }

    public function tokenize(LexerState $state): Token
    {
        $state->next(); // consume '<'
        $tagName = $state->captureRegex("/[a-zA-Z][a-zA-Z0-9_\-]*/");
        $state->skipWhitespace();
        $state->next(); // consume '/'
        $state->next(); // consume '>'
        return new Token(TokenType::$SelfClosingTag, $tagName);
    }

    private function peekAhead(LexerState $state, int $offset): ?string
    {
        $pos = $state->position + $offset;
        if ($pos < $state->length) {
            return $state->input[$pos];
        }
        return null;
    }
}

class OpeningTagLexerRule extends GrammarRule
{
    public function test(LexerState $state): bool
    {
        return $state->peek() === '<' && $this->peekAhead($state, 1) !== '/' && $this->peekAhead($state, 1) !== '!';
    }

    public function tokenize(LexerState $state): Token
    {
        $state->next(); // consume '<'
        $tagName = $state->captureRegex("/[a-zA-Z][a-zA-Z0-9_\-]*/");
        $state->skipWhitespace();
        
        // Skip attributes for now
        while (!$state->eof() && $state->peek() !== '>' && $state->peek() !== '/') {
            $state->next();
        }
        
        $state->skipWhitespace();
        if ($state->peek() === '/') {
            $state->next(); // consume '/'
        }
        $state->next(); // consume '>'
        return new Token(TokenType::$OpeningTag, $tagName);
    }

    private function peekAhead(LexerState $state, int $offset): ?string
    {
        $pos = $state->position + $offset;
        if ($pos < $state->length) {
            return $state->input[$pos];
        }
        return null;
    }
}

/**
 * Grammar rule for HTML entities: &name;
 */
class EntityLexerRule extends GrammarRule
{
    public function test(LexerState $state): bool
    {
        return $state->peek() === '&';
    }

    public function tokenize(LexerState $state): Token
    {
        $state->next(); // consume '&'
        $name = $state->captureUntil(fn($c) => $c === ';');
        $state->next(); // consume ';'
        return new Token(TokenType::$Entity, $name);
    }
}

/**
 * Grammar rule for template values: {{ ... }}
 */
class TemplateValueLexerRule extends GrammarRule
{
    public function test(LexerState $state): bool
    {
        return $state->peek() === '{' && $this->peekAhead($state, 1) === '{';
    }

    public function tokenize(LexerState $state): Token
    {
        $state->next(); // consume '{'
        $state->next(); // consume '{'
        $state->skipWhitespace();
        return new Token(TokenType::$TemplateValueOpen, '{{');
    }

    private function peekAhead(LexerState $state, int $offset): ?string
    {
        $pos = $state->position + $offset;
        if ($pos < $state->length) {
            return $state->input[$pos];
        }
        return null;
    }
}

/**
 * Grammar rule for text content
 */
class TextLexerRule extends GrammarRule
{
    public function test(LexerState $state): bool
    {
        $char = $state->peek();
        return $char !== null && $char !== '<' && $char !== '&' && $char !== '{';
    }

    public function tokenize(LexerState $state): Token
    {
        $text = $state->next();
        $text .= $state->captureUntil(fn($c) => $c === '<' || $c === '&');
        return new Token(TokenType::$Text, $text);
    }

    private function peekAhead(LexerState $state, int $offset): ?string
    {
        $pos = $state->position + $offset;
        if ($pos < $state->length) {
            return $state->input[$pos];
        }
        return null;
    }
}

/**
 * Grammar rule for attribute names
 */
class AttributeNameLexerRule extends GrammarRule
{
    public function test(LexerState $state): bool
    {
        // This rule is typically used within tag context
        return false;
    }

    public function tokenize(LexerState $state): Token
    {
        $attrName = $state->captureRegex("/[a-zA-Z_][a-zA-Z0-9_\-]*/");
        return new Token(TokenType::$AttributeName, $attrName);
    }
}

/**
 * Grammar rule for string values
 */
class StringLexerRule extends GrammarRule
{
    public function test(LexerState $state): bool
    {
        return $state->peek() === '"' || $state->peek() === "'";
    }

    public function tokenize(LexerState $state): Token
    {
        $quote = $state->next(); // consume opening quote
        $value = $state->captureUntil(fn($c) => $c === $quote);
        $state->next(); // consume closing quote
        return new Token(TokenType::$String, $value);
    }
}

/**
 * Grammar rule for escape sequences
 */
class EscapeSequenceLexerRule extends GrammarRule
{
    public function test(LexerState $state): bool
    {
        return $state->peek() === '\\';
    }

    public function tokenize(LexerState $state): Token
    {
        $state->next(); // consume '\'
        $char = $state->next(); // consume escaped character
        return new Token(TokenType::$EscapeSequence, '\\' . $char);
    }
}
