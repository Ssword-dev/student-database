<?php
namespace EdgeFramework\Routing;

class RouteResult {
    /**
     * @var array<string, string>
     */
    private array $_headers;
    private mixed $_body;
    private ?string $_statusMessage;
    private int $_statusCode;

    public function __construct(
        array $headers,
        mixed $body,
        int $statusCode,
        string $statusMessage
    ) {
        $this->_headers = $headers;
        $this->_body = $body;
        $this->_statusMessage = $statusMessage;
        $this->_statusCode = $statusCode;
    }

    public function getHeaders(): array {
        return $this->_headers;
    }

    public function getBody(): mixed {
        return $this->_body;
    }

    public function getStatusMessage(): ?string {
        return $this->_statusMessage;
    }

    public function getStatusCode(): int {
        return $this->_statusCode;
    }
}

class Ok extends RouteResult {
    public function __construct(array $headers, mixed $body) {
        parent::__construct($headers, $body, 200, 'OK');
    }
}
