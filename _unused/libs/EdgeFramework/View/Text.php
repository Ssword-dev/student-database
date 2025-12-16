<?php
namespace EdgeFramework\View;

require_once __DIR__ ."/NodeTypes.php";
require_once __DIR__ . '/Node.php';

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