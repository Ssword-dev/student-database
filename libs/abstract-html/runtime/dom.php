<?php

// namespace.
namespace Ssword\AbstractHTML;

// self closing tags.
// in a hash map. actually just assoc array.
// but ti's a hashmap.



// constants for Node::$type.
const TEXT_NODE = 1;
const ELEMENT_NODE = 2;
const FUNCTIONAL_COMPONENT = 3;

// a basic soul-less node.
// OOP base.
class Node
{
    // the type of the node.
    public int $type;

    // the first child.
    public ?Node $child;

    // the next sibling.
    public ?Node $sibling;

    // the parent.
    public ?Node $return;

    // the constructor.
    public function __construct(int $type, $children = null)
    {
        $this->type = $type;

        // declare no siblings. (parent declares the actual siblings).
        $this->sibling = null;

        // declare no parent. (parent binds the children's return to itself).
        $this->return = null;

        // if there is no children. use null as child.
        if ($children === null || !isset($children[0])) {
            $this->child = null;
        }

        // if there *is* a children. go bind them to the current node.
        else {
            // bind the child property to the first child.
            $this->child = $children[0];

            // set the initial previous child to null. (no previous child.)
            $previousChild = null;

            // count the number of children to use in for loop.
            $childrenCount = count($children);

            for ($idx = 0; $idx < $childrenCount; $idx++) {
                // get the current child.
                $child = $children[$idx];

                // bind the return property of the child to this node.
                $child->return = $this;

                // there is a previous child (not the first child).
                // then bind the sibling property of the previous child to
                // the current child. this makes the graph of nodes all connected.
                if ($previousChild !== null) {
                    $previousChild->sibling = $child;
                }

                // now the current child is now the previous child.
                $previousChild = $child;
            }
        }
    }
}

// a text node.
class Text extends Node
{
    // the text to render.
    public string $textContent;

    // the constructor.
    public function __construct(string $textContent)
    {
        // call the parent constructor.
        parent::__construct(TEXT_NODE, null);

        // set the text content to the given value in the content
        // parameter.
        $this->textContent = $textContent;
    }
}

class Element extends Node
{
    // the element tag. preferably HTML5 elements.
    // e.g.: div, span, etc.
    public readonly string $tag;

    // the html attributes.
    // null if no attributes.
    public readonly ?array $attributes;

    // the constructor.
    public function __construct($tag, $attributes = null, $children = null)
    {
        // call parent constructor
        parent::__construct(ELEMENT_NODE, $children);

        // assign the tag.
        $this->tag = $tag;

        // if the attributes is null, or an empty array [],
        // then assign null to the attributes property.
        if ($attributes === null || count($attributes) === 0) {
            $this->attributes = null;
        }

        // else, meaning there is an attribute or more. assign the given attributes.
        else {
            $this->attributes = $attributes;
        }
    }
}

class Renderer
{
    public static function toHTML(Node $root)
    {
        // result html.
        $html = '';

        // the current node. (react style.)
        $currentNode = $root;

        // the current node being null means
        // no more to process.
        while ($currentNode !== null) {
            // no child, meaning to stop descending and
            // actually go up. and apply closing tags.
            if ($currentNode->child === null) {
                $html .= Renderer::htmlFromNodeWithoutChild($currentNode);
                $bubbleResult = Renderer::climbUpAndGatherClosingTags($currentNode);

                // append te closing tags of the parents.
                $html .= $bubbleResult[1];

                // set the current node to null if
                $currentNode = $bubbleResult[0] !== null ? $bubbleResult[0]->sibling : null;
                continue;
            }

            // traverse the depths.
            $html .= Renderer::htmlElementOpeningTag($currentNode);
            $currentNode = $currentNode->child;
        }

        return $html;
    }

    public static function htmlElementOpeningTag(Element $element)
    {
        if ($element->attributes !== null) {
            $attributeString = Renderer::htmlAttributesFromAssociativeAttributeArray($element->attributes);
            return "<{$element->tag} {$attributeString}>";
        }

        return "<{$element->tag}>";
    }

    public static function htmlElementClosingTag(Element $element)
    {
        return "</{$element->tag}>";
    }

    public static function htmlFromNodeWithoutChild(Node $node)
    {
        switch ($node->type) {
            case TEXT_NODE:
                return htmlspecialchars($node->textContent, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);

            case ELEMENT_NODE:
                return Renderer::htmlElementOpeningTag($node) . Renderer::htmlElementClosingTag($node);
        }
    }

    public static function climbUpAndGatherClosingTags(Node $element)
    {
        $currentNode = $element->return;
        $closingTags = '';

        while ($currentNode !== null) {
            $closingTags .= Renderer::htmlElementClosingTag($currentNode);

            // stop climbing if the current node has sibling.
            if ($currentNode->sibling !== null) {
                break;
            }

            $currentNode = $currentNode->return;
        }

        return [$currentNode, $closingTags];
    }

    public static function htmlAttributesFromAssociativeAttributeArray(array $attributes)
    {
        $attributeString = "";

        foreach ($attributes as $name => $value) {
            if ($attributeString !== "") {
                // the space between the attributes. so i do not have to implode all this age
                $attributeString .= " ";
            }

            $safeValue = htmlspecialchars($value, ENT_COMPAT | ENT_QUOTES);
            $attributeString .= "$name=\"$safeValue\"";
        }

        return $attributeString;
    }
}