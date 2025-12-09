<?php
namespace EdgeFramework\Build\Tokenizer;

class RawToken
{
    public string $type;
    public string $value;  // actual text

    public function __construct(string $type, string $value)
    {
        $this->type = $type;
        $this->value = $value;
    }
}