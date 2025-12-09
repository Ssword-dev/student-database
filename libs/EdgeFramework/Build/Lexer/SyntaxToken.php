<?php
namespace EdgeFramework\Build\Lexer;

use EdgeFramework\Build\Tokenizer\RawToken;

class SyntaxToken
{
    public string $type;

    /**
     * @var array<int, RawToken>
     */
    public array $rawTokens;

    public function __construct(string $type, array $rawTokens)
    {
        $this->type = $type;
        $this->rawTokens = $rawTokens;
    }
}
