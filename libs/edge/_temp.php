
class Header {
    private string $_key;
    private string $_value;

    public function __construct(string $key, string $value) {
        $this->_key = $key;
        $this->_value = $value;
    }

    public function getKey(): string {
        return $this->_key;
    }

    public function getValue(): string {
        return $this->_value;
    }
}

abstract class HTTPMessage {
    public string $_body;
    private string $_method;
    private string $_uri;
    private array $_headers = [];
    private array $_cookies = [];

    public function __construct()
    {
        $this->_method = '';
        $this->_uri = '';
        $this->_headers = [];
        $this->_cookies = [];
    }

    public function setBody(string $body) {
        $this->_body = $body;
    }

    public function getBody(): string {
        return $this->_body;
    }

    public function getMethod(): string {
        return $this->_method;
    }

    public function getUri(): string {
        return $this->_uri;
    }

    public function getHeaders(): array {
        return $this->_headers;
    }

    public function getCookies(): array {
        return $this->_cookies;
    }

    public function getHeader(string $name): string {
        return $this->_headers[$name] ?? '';
    }

    public function getCookie(string $name): string {
        return $this->_cookies[$name] ?? '';
    }

    public function setMethod(string $method) {
        $this->_method = $method;
        return $this;
    }

    public function setUri(string $uri) {
        $this->_uri = $uri;
        return $this;
    }

    public function setHeaders(array $headers) {
        $this->_headers = $headers;
        return $this;
    }

    public function addHeader(string $name, string $value) {
        $this->_headers[$name] = $value;
    }

    public function removeHeader(string $name) {
        unset($this->_headers[$name]);
        return $this;
    }

    public function setCookies(array $cookies) {
        $this->_cookies = $cookies;
        return $this;
    }

    public function addCookie(string $name, string $value) {
        $this->_cookies[$name] = $value;
        return $this;
    }

    public function removeCookie(string $name) {
        unset($this->_cookies[$name]);
        return $this;
    }
}

class Request extends HTTPMessage {
    public function __construct(string $method, string $uri, array $headers = []) {
        parent::__construct();
        $this->setMethod($method);
        $this->setUri($uri);
        $this->setHeaders($headers);

        // parsed query.
        $queryString = parse_url($this->getUri(), PHP_URL_QUERY);
        $query = [];
        parse_str($queryString, $query);
        
        $this->_query = $query;
    }

    public function setQuery($query){
        $this->_query = $query;
        return $this;
    }

    public function getQuery($name){
        return $this->_query[$name] ?? null;
    }
};

class Response extends HTTPMessage {};
