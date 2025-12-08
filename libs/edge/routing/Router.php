<?php
namespace EdgeFramework\Routing;

use EdgeFramework\View\Element;
use EdgeFramework\View\Node;
use EdgeFramework\View\Renderer;
use EdgeFramework\View\Text;

class EdgeRouter {
        /**
     * @var array<int, Route>
     */
    public array $_routes;

    public ?string $_base;

    public function __construct(string $projectDir){
        $this->_routes = [];
        
        // edge router wamp compat.
        // project router.
        $base = basename($projectDir);
        $this->_base = '/' . $base;
    }

    public function addRoute(Route $route): void {
        $this->_routes[] = $route;
    }

    public function serve(){
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $headers = $this->getHeaders();
        $query = strtolower($method) === 'POST' ? $_POST : $_GET;
        $routePath = $this->getRoutePath($method, $uri);
        $context = new EdgeContext();

        // get the first matching route.
        foreach ($this->_routes as $route) {
            $matchResult = $route->match($routePath);

            if ($matchResult->matched()){
                $routeResult = strtolower($method) === 'POST' ? $route->post($context, $matchResult, $query) : $route->get($context, $matchResult, $query);
                $this->applyRouteResult($context, $headers, $routeResult);
                break;
            }
        };
    }

    public function getHeaders(){
        return getallheaders();
    }

    public function getQueryParams(string $method): array {
        // Example: parse query params from $_GET or $_POST
        if (strtoupper($method) === 'POST') {
            return $_POST ?? [];
        }

        return $_GET;
    }
    
    public function getRoutePath(string $method, $uri): string {
        $parsedUrl = parse_url($uri, PHP_URL_PATH);
        $routePath = $parsedUrl ??'/';
        // strip the base url.
        if (strpos($routePath, $this->_base) === 0){
            return substr($routePath, strlen($this->_base));
        }

        return $routePath;
    }

    private function acceptsEdgePartial(): bool {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return stripos($accept, 'application/x-edge-partial-content') !== false;
    }

    private function applyRouteResultAsPartial(RouteResult $routeResult){
        header('Content-Type: application/x-edge-partial-content');

        // send over a partial content.
        /**
         * @var Node
         */
        $body = $routeResult->getBody();
    }

    public function buildTemplateResponse(EdgeContext $context, Node $body){
        $title = new Element('title', null, [new Text($context->title)]);

        $metaElements = [];
        foreach ($context->meta as $key => $val){
            if (\is_string($val)) {
                $metaElements[] = new Element('meta', ['name' => $key, 'value' => $val], []);
            }

            else {
                $metaElements[] = new Element('meta', ['name'=> $key, ...$val], []);
            }
        }

        $styleElements = array_map(fn(string $url) => new Element('link', ['rel' => 'stylesheet', 'href'=> $url], []), $context->styles);
        $scriptElements = array_map(fn(string $url) => new Element('script', ['src'=> $url, 'type' => 'module'], []), $context->scripts);

        $document = new Element('html', null, [
            new Element('head', null, [
                $title,
                ...$metaElements,
                ...$styleElements,
            ]),
            new Element('body', null, [
                $body,
                ...$scriptElements
            ])
        ]);

        $renderer = new Renderer();
        $html = $renderer->render($document);
        $responseBody = "<!DOCTYPE html>\n\n$html";

        return $responseBody;
    }

    public function applyRouteResult(EdgeContext $context, array $headers, RouteResult $routeResult): void {
        if ($this->acceptsEdgePartial()) {
            // TODO: Implement this part.
        }

        http_response_code($routeResult->getStatusCode());
        foreach ($routeResult->getHeaders() as $header) {
            header($header->getKey() . ': ' . $header->getValue());
        }
        
        $body = $routeResult->getBody();

        if ($body instanceof Node){
            echo $this->buildTemplateResponse($context, $body);
        }
        else {
            echo $body;
        }
        
    }

    public function getBody(mixed $responseBody): string
    {
        return (string) $responseBody;
    }
}