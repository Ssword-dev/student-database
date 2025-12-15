<?php
namespace EdgeFramework\Build\Lexer;

use EdgeFramework\Build\Scanner\Symbol;

class Token
{
    public string $type;

    /**
     * @var array<int, Symbol>
     */
    public array $symbols;

    public function __construct(string $type, array $symbols)
    {
        $this->type = $type;
        $this->symbols = $symbols;
    }
}
