<?php

namespace AbstractHTML\Build\Lexer;

/**
 * A TokenType is an intermediate representation of the token type
 * discriminators. each instance has a code (for switch/case) and a name (for debugging / JSON
 * output).
 */
class TokenType implements \JsonSerializable
{
    public int $code;
    public string $name;

    public function __construct(int $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function jsonSerialize(): mixed{
        return $this->name;
    }

    // tag token types.
    public static TokenType $OpeningTag;
    public static TokenType $SelfClosingTag;
    public static TokenType $ClosingTag;

    // attribute token types.
    public static TokenType $AttributeName;

    // value token types.

    // string.
    public static TokenType $String; // a static string value.

    // escape sequences for string values.
    public static TokenType $EscapeSequence; // \"\' and metacharacters.

    // {{ ... }}
    // template value token types.
    public static TokenType $TemplateValueOpen; // {{
    public static TokenType $TemplateValueClose; // }}

    // text node token type.
    public static TokenType $Text; // any text between tags.

    // entity token type.
    public static TokenType $Entity; // &...;

    public static function initialize(): void
    {
        self::$OpeningTag = new TokenType(1, "OpeningTag");
        self::$SelfClosingTag = new TokenType(2, "SelfClosingTag");
        self::$ClosingTag = new TokenType(3, "ClosingTag");
        self::$AttributeName = new TokenType(4, "AttributeName");
        self::$String = new TokenType(5, "String");
        self::$EscapeSequence = new TokenType(6, "EscapeSequence");
        self::$TemplateValueOpen = new TokenType(7, "TemplateValueOpen");
        self::$TemplateValueClose = new TokenType(8, "TemplateValueClose");
        self::$Text = new TokenType(9, "Text");
        self::$Entity = new TokenType(10, "Entity");
    }
}

TokenType::initialize();