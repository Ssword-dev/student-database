<?php

namespace AbstractHTML\Build\Lexer;

class Token implements \JsonSerializable
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

abstract class GrammarRule {
    abstract public function test(LexerState $state): bool;

    abstract public function tokenize(LexerState $state): Token;
}

class LexerState extends LexerStateBase
{
    use LexerUtilities;
}

class Lexer
{
    /**
     * @var array<GrammarRule>
     */
    public array $rules;

    public function __construct() {
        $this->rules = [
            new ClosingTagLexerRule(),
            new SelfClosingTagLexerRule(),
            new OpeningTagLexerRule(),
            new TemplateValueLexerRule(),
            new EntityLexerRule(),
            new EscapeSequenceLexerRule(),
            new StringLexerRule(),
            new TextLexerRule(),
        ];
    }

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
            $matched = false;
            foreach($this->rules as $rule){
                if ($rule->test($state)) {
                    $state->addToken(
                        $rule->tokenize($state)
                    );
                    $matched = true;
                    break;
                }
            }
            
            // Skip character if no rule matched
            if (!$matched) {
                $state->next();
            }
        }

        return $state->tokens;
    }
}

TokenType::initialize();

require_once __DIR__ . '/lexer_rules.php';