<?php

namespace EdgeFramework\View\Build\Parser;

use EdgeFramework\View\Build\Lexer\Token;
use EdgeFramework\View\Build\Lexer\TokenType;

/**
 * Parser state - tracks position and tokens during parsing
 */
class ParserState
{
    /** @var array<Token> */
    public array $tokens;
    public int $position;
    public int $length;

    /**
     * @param array<Token> $tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
        $this->position = 0;
        $this->length = \count($tokens);
    }

    public function peek(): ?Token
    {
        if ($this->position < $this->length) {
            return $this->tokens[$this->position];
        }
        return null;
    }

    public function peekAhead(int $offset = 1): ?Token
    {
        $pos = $this->position + $offset;
        if ($pos < $this->length) {
            return $this->tokens[$pos];
        }
        return null;
    }

    public function next(): ?Token
    {
        if ($this->position < $this->length) {
            return $this->tokens[$this->position++];
        }
        return null;
    }

    public function eof(): bool
    {
        return $this->position >= $this->length;
    }

    public function expect(TokenType $type): Token
    {
        $token = $this->peek();
        if ($token === null || $token->type !== $type) {
            throw new ParseException("Expected token type " . $type->name . " but got " . ($token ? $token->type->name : "EOF"));
        }
        return $this->next();
    }
}

/**
 * Exception thrown during parsing
 */
class ParseException extends \Exception
{
}

/**
 * Main parser class - converts token stream to AST
 */
class Parser
{
    private ParserState $state;
    /** @var array<ParserRule> */
    private array $rules;

    public function __construct()
    {
        $this->rules = [
            new SelfClosingTagParserRule(),
            new TagParserRule(),
            new TemplateValueParserRule(),
            new EntityParserRule(),
            new TextParserRule(),
        ];
    }

    /**
     * Parse tokens into an AST
     *
     * @param array<Token> $tokens
     * @return DocumentNode|FragmentNode
     */
    public function parse(array $tokens)
    {
        $this->state = new ParserState($tokens);
        return $this->parseDocument();
    }

    /**
     * Parse a complete document
     */
    private function parseDocument()
    {
        $children = $this->parseNodes();
        
        // If there's only one top-level element, wrap in DocumentNode
        // Otherwise, use a fragmented document node.
        if (count($children) === 1 && $children[0] instanceof TagNode) {
            return new DocumentNode($children);
        }
        
        return new DocumentNode([new FragmentNode($children)]);
    }

    /**
     * Parse a sequence of nodes
     *
     * @param string|null $untilClosingTag Stop parsing when encountering this closing tag
     * @return array<ASTNode>
     */
    public function parseNodes(?string $untilClosingTag = null): array
    {
        $nodes = [];

        while (!$this->state->eof()) {
            $token = $this->state->peek();

            if ($token === null) {
                break;
            }

            // Check for closing tag
            if ($token->type === TokenType::$ClosingTag) {
                if ($untilClosingTag !== null && $token->value === $untilClosingTag) {
                    $this->state->next(); // consume closing tag
                    break;
                }
                // Unmatched closing tag - skip it
                $this->state->next();
                continue;
            }

            // Try each parser rule
            $matched = false;
            foreach ($this->rules as $rule) {
                if ($rule->test($this->state)) {
                    $nodes[] = $rule->parse($this->state, $this);
                    $matched = true;
                    break;
                }
            }

            // If no rule matched, skip the token
            if (!$matched) {
                $this->state->next();
            }
        }

        return $nodes;
    }
}
