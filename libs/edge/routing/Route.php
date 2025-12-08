<?php

namespace EdgeFramework\Routing;

abstract class Route {
    // handlers.
    abstract public function match(string $routePath): MatchResult;
    abstract public  function get(EdgeContext $context, MatchResult $matchResult, array $query): RouteResult;
    abstract public function post(EdgeContext $context, MatchResult $matchResult, array $query): RouteResult;
}