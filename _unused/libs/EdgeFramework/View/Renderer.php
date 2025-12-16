<?php
namespace EdgeFramework\View;

require_once __DIR__ . '/Node.php';
use DocumentSpec;
use EdgeFramework\Foundation\OutputInstrument;
use EdgeFramework\View\voidTags;

class Renderer
{
    private OutputInstrument $outputInstrument;

    public function __construct(OutputInstrument $outputInstrument) {
        $this->outputInstrument = $outputInstrument;
    }

    public function render(Node $root)
    {
        $spec = new DocumentSpec();
        // result html.
        // $html = '';
        $write = function($data) use ($this) {
            $this->outputInstrument->write($data);
        };

        // the current node. (react style.)
        $currentNode = $root;

        // the current node being null means
        // no more to process.
        while ($currentNode !== null) {
            if ($currentNode instanceof Element && isset($spec->voidTags[$currentNode->tag])) {
                $write(
                    $this->htmlElementVoidTag($currentNode)
                );

                $bubbleResult = $this->climbUpAndGatherClosingTags($currentNode);
                $write($bubbleResult[1]);
                $currentNode = $bubbleResult[0];
                continue;
            }

            // no child, meaning to stop descending and
            // actually go up. and apply closing tags.
            if ($currentNode->child === null) {
                $write($this->htmlFromNodeWithoutChild($currentNode));
                $bubbleResult = $this->climbUpAndGatherClosingTags($currentNode);

                // append the closing tags collected while climbing up
                $write($bubbleResult[1]);

                $currentNode = $bubbleResult[0];
                continue;
            }

            // traverse the depths.
            $write($this->htmlElementOpeningTag($currentNode));
            $currentNode = $currentNode->child;
        }
    }

    public function htmlElementOpeningTag(Element $element)
    {
        if ($element->attributes !== null && \count($element->attributes) !== 0) {
            $attributeString = $this->htmlAttributesFromAssociativeAttributeArray($element->attributes);
            return "<{$element->tag} {$attributeString}>";
        }

        return "<{$element->tag}>";
    }

    public function htmlElementVoidTag(Element $element)
    {
        if ($element->attributes !== null && \count($element->attributes) !== 0) {
            $attributeString = $this->htmlAttributesFromAssociativeAttributeArray($element->attributes);
            return "<{$element->tag} {$attributeString} />";
        }

        return "<{$element->tag} />";
    }

    public function htmlElementClosingTag(Element $element)
    {
        return "</{$element->tag}>";
    }

    public function htmlFromNodeWithoutChild(Node $node)
    {
        switch ($node->type) {
            case TEXT_NODE:
                return htmlspecialchars($node->textContent, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);

            case ELEMENT_NODE:
                return $this->htmlElementOpeningTag($node) . $this->htmlElementClosingTag($node);
        }
    }

    public function climbUpAndGatherClosingTags(Node $element)
    {
        $current = $element;
        $closingTags = '';

        // Climb up until we find an ancestor where the current node has a sibling
        // (meaning there is more to traverse), or until we reach the root.
        while ($current->return !== null) {
            $parent = $current->return;

            // If current has no sibling, we finished this parent's children, so emit parent's closing tag
            if ($current->sibling === null) {
                $closingTags .= $this->htmlElementClosingTag($parent);
                $current = $parent;
                continue;
            }

            // Found a parent where current has a sibling, return that sibling to continue traversal
            return [$current->sibling, $closingTags];
        }

        // Reached the root node; nothing more to traverse.
        return [null, $closingTags];
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
