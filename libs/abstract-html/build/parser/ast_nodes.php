<?php

namespace AbstractHTML\Build\Parser;

use JsonSerializable;

/**
 * Base class for all AST nodes
 */
abstract class ASTNode implements JsonSerializable
{
    abstract public function jsonSerialize(): mixed;
}

/**
 * Text node - represents plain text content
 */
class TextNode extends ASTNode
{
    public string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'TextNode',
            'content' => $this->content,
        ];
    }
}

/**
 * Entity node - represents HTML entities like &nbsp;
 */
class EntityNode extends ASTNode
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'EntityNode',
            'name' => $this->name,
        ];
    }
}

/**
 * Attribute - represents tag attributes
 */
class Attribute implements JsonSerializable
{
    public string $name;
    public string $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
        ];
    }
}

/**
 * Tag node - represents opening/closing tags
 */
class TagNode extends ASTNode
{
    public string $name;
    /** @var array<Attribute> */
    public array $attributes;
    /** @var array<ASTNode> */
    public array $children;
    public bool $selfClosing;

    /**
     * @param string $name
     * @param array<Attribute> $attributes
     * @param array<ASTNode> $children
     * @param bool $selfClosing
     */
    public function __construct(
        string $name,
        array $attributes = [],
        array $children = [],
        bool $selfClosing = false
    ) {
        $this->name = $name;
        $this->attributes = $attributes;
        $this->children = $children;
        $this->selfClosing = $selfClosing;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'TagNode',
            'name' => $this->name,
            'attributes' => $this->attributes,
            'children' => $this->children,
            'selfClosing' => $this->selfClosing,
        ];
    }
}

/**
 * Template value node - represents {{ ... }} expressions
 */
class TemplateValueNode extends ASTNode
{
    public string $expression;

    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'TemplateValueNode',
            'expression' => $this->expression,
        ];
    }
}

/**
 * represents the entire parsed document
 */
class DocumentNode extends ASTNode
{
    /** @var array<ASTNode> */
    public array $children;

    /**
     * @param array<ASTNode> $children
     */
    public function __construct(array $children = [])
    {
        $this->children = $children;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'DocumentNode',
            'children' => $this->children,
        ];
    }
}

/**
 * represents a fragment of nodes without a root element
 */
class FragmentNode extends ASTNode
{
    /** @var array<ASTNode> */
    public array $children;

    /**
     * @param array<ASTNode> $children
     */
    public function __construct(array $children = [])
    {
        $this->children = $children;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'FragmentNode',
            'children' => $this->children,
        ];
    }
}