<?php

require_once __DIR__ . '/../lexer/token_type.php';
require_once __DIR__ . '/../lexer/lexer.php';
require_once __DIR__ . '/../lexer/lexer_rules.php';
require_once __DIR__ . '/ast_nodes.php';
require_once __DIR__ . '/parser_rules.php';
require_once __DIR__ . '/parser.php';

use EdgeFramework\View\Build\Lexer\TokenType;
use EdgeFramework\View\Build\Lexer\Lexer;
use EdgeFramework\View\Build\Parser\Parser;

TokenType::initialize();

$testCases = [
    'Simple text' => 'Hello world!',
    'Opening tag' => '<div>content</div>',
    'Nested tags' => '<div><span>Hello</span></div>',
    'Self-closing tag' => '<br />',
    'Entity' => '<p>Hello &nbsp; world</p>',
];

echo "=== Parser Rules Test ===\n\n";

$lexer = new Lexer();

foreach ($testCases as $name => $html) {
    echo "Test: $name\n";
    echo "Input: $html\n";

    try {
        $tokens = $lexer->tokenize($html);
        echo "Tokens: " . count($tokens) . "\n";

        $parser = new Parser();
        $ast = $parser->parse($tokens);

        echo "AST:\n";
        echo json_encode($ast, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    echo "\n";
}
