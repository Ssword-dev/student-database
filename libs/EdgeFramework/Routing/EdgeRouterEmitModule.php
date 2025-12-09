<?php

use EdgeFramework\Routing\EdgeContext;
use EdgeFramework\Routing\RouteResult;
use EdgeFramework\Routing\ModuleSystem\Module;
use EdgeFramework\View\Element;
use EdgeFramework\View\Node;
use EdgeFramework\View\Renderer;
use EdgeFramework\View\Text;

class EdgeRouterEmitModule extends Module {
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

    public function register(): void
    {
        $this->hook('applyResult', function (EdgeContext $context, array $headers, RouteResult $routeResult) {
            $this->applyRouteResult($context, $headers, $routeResult);
        });
    }
}