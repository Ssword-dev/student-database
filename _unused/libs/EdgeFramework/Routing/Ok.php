<?php
namespace EdgeFramework\Routing;

class Ok extends RouteResult {
    public function __construct(array $headers, mixed $body) {
        parent::__construct($headers, $body, 200, 'OK');
    }
}
