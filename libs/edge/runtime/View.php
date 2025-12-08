<?php
namespace EdgeFramework\Runtime;

use EdgeFramework\View\Element;
use EdgeFramework\View\Node;
use EdgeFramework\View\Text as TextElement;
use LogicException;

function normalizeChild(mixed $node){
    if ($node instanceof Node){
        return $node;
    }

    if (is_scalar($node)){
        return [new TextElement((string) $node)];
    }

    throw new LogicException("Oops! Seems like there was a mising case in 'normalizeChild'!");
}

function normalizeChildren(mixed $children): array {
    if (is_null($children)) {
        return [];
    }

    if (is_array($children)) {
        return array_map('normalizeChild', $children);
    }

    // single child, normalize to array.
    if (is_object($children)) {
        return [$children];
    }

    if (isPrimitive($children)) {
        return [new TextElement((string) $children)];
    }

    throw new LogicException("Oops! Seems like i forgot to handle some parts of 'normalizeChildren'!");
}

// SYMBOL.
class Text {}

// Hyperscript.
function h(mixed $type, mixed $props, $children = null): Node{
    // determine the node depending on the `type`.
    if ($type === Text::class){
        return new TextElement((string) $props);
    }

    if (is_callable($type)) {
        // TODO: implement this properly..
        return $type($props, $children);
    }

    if (is_string($type)) {
        return new Element((string) $type, $props, normalizeChildren($children));
    }

    throw new LogicException("Oops! There was a missing case in the hyperscript function!");
}