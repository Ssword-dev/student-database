<?php

namespace EdgeFramework\View;

require_once __DIR__ . "/NodeTypes.php";
require_once __DIR__ . "/Node.php";

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
