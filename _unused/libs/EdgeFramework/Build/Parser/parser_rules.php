<?php

namespace EdgeFramework\View\Build\Parser;

use EdgeFramework\View\Build\Lexer\Token;
use EdgeFramework\View\Build\Lexer\TokenType;

/**
 * Abstract base class for parser rules
 * Each rule encapsulates logic to test and parse a specific AST node type
 */
abstract class ParserRule
{
    /**
     * Test if this rule can parse from the current token
     *
     * @param ParserState $state Current parser state
     * @return bool True if this rule can handle the current token
     */
    abstract public function test(ParserState $state): bool;

    /**
     * Parse the tokens and return an AST node
     *
     * @param ParserState $state Current parser state
     * @param Parser $parser Reference to parser for recursive calls
     * @return ASTNode The parsed AST node
     */
    abstract public function parse(ParserState $state, Parser $parser): ASTNode;
}

/**
 * Parser rule for text nodes
 */
class TextParserRule extends ParserRule
{
    public function test(ParserState $state): bool
    {
        $token = $state->peek();
        return $token !== null && $token->type === TokenType::$Text;
    }

    public function parse(ParserState $state, Parser $parser): ASTNode
    {
        $textToken = $state->expect(TokenType::$Text);
        return new TextNode($textToken->value);
    }
}

/**
 * Parser rule for entity nodes (&nbsp;, &amp;, etc.)
 */
class EntityParserRule extends ParserRule
{
    public function test(ParserState $state): bool
    {
        $token = $state->peek();
        return $token !== null && $token->type === TokenType::$Entity;
    }

    public function parse(ParserState $state, Parser $parser): ASTNode
    {
        $entityToken = $state->expect(TokenType::$Entity);
        return new EntityNode($entityToken->value);
    }
}

/**
 * Parser rule for self-closing tags (<br />, <img />, etc.)
 */
class SelfClosingTagParserRule extends ParserRule
{
    public function test(ParserState $state): bool
    {
        $token = $state->peek();
        return $token !== null && $token->type === TokenType::$SelfClosingTag;
    }

    public function parse(ParserState $state, Parser $parser): ASTNode
    {
        $tagToken = $state->expect(TokenType::$SelfClosingTag);
        $tagName = $tagToken->value;
        return new TagNode($tagName, [], [], true);
    }
}

/**
 * Parser rule for opening tags and their children
 */
class TagParserRule extends ParserRule
{
    public function test(ParserState $state): bool
    {
        $token = $state->peek();
        return $token !== null && $token->type === TokenType::$OpeningTag;
    }

    public function parse(ParserState $state, Parser $parser): ASTNode
    {
        $tagToken = $state->expect(TokenType::$OpeningTag);
        $tagName = $tagToken->value;

        // Parse children until matching closing tag
        $children = $parser->parseNodes($tagName);

        return new TagNode($tagName, [], $children, false);
    }
}

/**
 * Parser rule for template value expressions {{ ... }}
 */
class TemplateValueParserRule extends ParserRule
{
    public function test(ParserState $state): bool
    {
        $token = $state->peek();
        return $token !== null && $token->type === TokenType::$TemplateValueOpen;
    }

    public function parse(ParserState $state, Parser $parser): ASTNode
    {
        $state->expect(TokenType::$TemplateValueOpen);

        // Collect tokens until we hit TemplateValueClose
        $expression = '';
        while (!$state->eof()) {
            $token = $state->peek();

            if ($token === null) {
                break;
            }

            if ($token->type === TokenType::$TemplateValueClose) {
                $state->next();
                break;
            }

            if ($token->type === TokenType::$Text) {
                $expression .= $token->value;
            } elseif ($token->type === TokenType::$Entity) {
                $expression .= '&' . $token->value . ';';
            }

            $state->next();
        }

        return new TemplateValueNode(trim($expression));
    }
}
