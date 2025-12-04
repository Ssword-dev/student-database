<?php

/**
 * A TokenType is an intermediate representation of the token type
 * discriminators. each instance has a code (for switch/case) and a name (for debugging / JSON
 * output).
 */
class TokenType implements JsonSerializable
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

class Token implements JsonSerializable
{
    public TokenType $type;
    public string $value;
    public ?string $raw;

    public function __construct(TokenType $type, string $value, ?string $raw = null)
    {
        $this->type = $type;
        $this->value = $value;
        $this->raw = $raw;
    }

    public function jsonSerialize(): mixed {
        return [
            "type"=> $this->type,
            "value"=> $this->value,
            "raw" => $this->raw
        ];
    }
}

abstract class LexerStateBase
{
    public int $position;
    public int $length;
    public string $input;
    public array $tokens;

    public function __construct(string $input)
    {
        $this->input = $input;
        $this->length = strlen($input);
        $this->position = 0;

        // output.
        $this->tokens = [];
    }
}

trait LexerUtilities
{
    public function captureWhile(callable $condition): string
    {
        $result = '';
        while (!$this->eof() && $condition($this->peek())) {
            $result .= $this->next();
        }
        return $result;
    }

    public function captureUntil(callable $condition): string
    {
        $result = '';
        while (!$this->eof() && !$condition($this->peek())) {
            $result .= $this->next();
        }
        return $result;
    }

    public function captureRegex(string $pattern): string
    {
        return $this->captureWhile(fn($char) => preg_match($pattern, $char));
    }

    public function skipWhitespace(): void
    {
        $this->captureWhile('ctype_space');
    }

    public function next(): ?string
    {
        if ($this->position < $this->length) {
            return $this->input[$this->position++];
        }
        return null;
    }

    public function peek(): ?string
    {
        if ($this->position < $this->length) {
            return $this->input[$this->position];
        }
        return null;
    }

    public function eof(): bool
    {
        return $this->position >= $this->length;
    }

    public function addToken(Token $token): void
    {
        $this->tokens[] = $token;
    }
}

class LexerState extends LexerStateBase
{
    use LexerUtilities;
}

class Lexer
{
    public function _tokenizeAttributes(LexerState $state)
    {
        while (!$state->eof() && $state->peek() !== '>' && $state->peek() !== '/') {
            $attrName = $state->captureRegex("/[a-zA-Z_][a-zA-Z0-9_\-]*/");
            $state->skipWhitespace();
            $state->next(); // consume '='
            $state->skipWhitespace();
            $quote = $state->next(); // consume opening quote
            $attrValue = $state->captureUntil(fn($c) => $c === $quote);
            $state->next(); // consume closing quote
            $state->skipWhitespace();
            $state->addToken(new Token(TokenType::$AttributeName, $attrName));
            $state->addToken(new Token(TokenType::$String, $attrValue));
        }
    }

    public function tokenize(string $input): array
    {
        $state = new LexerState($input);
        while (!$state->eof()) {
            $char = $state->next(); // consume next character.

            // closing tag.
            if ($char === '<' && $state->peek() === '/') {
                $state->next(); // consume '/'
                $tagName = $state->captureRegex("/[a-zA-Z][a-zA-Z0-9_\-]*/");
                $state->skipWhitespace();
                $state->next(); // consume '>'
                $state->addToken(new Token(TokenType::$ClosingTag, $tagName));
                continue;
            }

            // opening or self-closing tag.
            if ($char === '<') {
                $tagName = $state->captureRegex("/[a-zA-Z][a-zA-Z0-9_\-]*/");
                $state->skipWhitespace();

                // attributes.
                $this->_tokenizeAttributes($state);

                // allow whitespace before closing tag.
                // for like <div /> which i like doing.
                $state->skipWhitespace();

                // self-closing tag.
                if ($state->peek() === '/') {
                    $state->next(); // consume '/'
                    $state->next(); // consume '>'
                    $state->addToken(new Token(TokenType::$SelfClosingTag, $tagName));
                } else {
                    // regular opening tag.
                    $state->next(); // consume '>'
                    $state->addToken(new Token(TokenType::$OpeningTag, $tagName));
                }
                continue;
            }


            // html entities.
            if ($char === '&') {
                $name = $state->captureUntil(fn($c) => $c === ';');
                $state->addToken(new Token(TokenType::$Entity, $name));
                continue;
            }

            if ($char !== '<' && $char !== '&') {
                $text = $char . $state->captureUntil(fn($c) => $c === '<' || $c === '&');
                $state->addToken(new Token(TokenType::$Text, $text));
                continue;
            }

            continue;
        }

        return $state->tokens;
    }
}

$html = "<div><span>Hello world!</span></div><br />";
TokenType::initialize();
$lexer = new Lexer();

$tokens = $lexer->tokenize($html);

header("Content-Type: application/json");
echo json_encode($tokens);