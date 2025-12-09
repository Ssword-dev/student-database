<?php

use EdgeFramework\Routing\EdgeContext;
use EdgeFramework\Routing\MatchResult;
use EdgeFramework\Routing\Route;
use EdgeFramework\Routing\RouteResult;
use EdgeFramework\View\Text;
use function EdgeFramework\View\IntrinsicElements\{
    div,
    h1,
    main
};

function NotFoundPage()
{
    return div(
        [
            'class' => 'flex flex-col justify-center align-center h-screen w-screen'
        ],
        main(
            [],
            h1(['class' => 'text-3xl'], new Text('The page you are looking for does not exist.'))
        )
    );
}

class NotFoundRoute extends Route
{
    public function match(string $routePath): MatchResult
    {
        return MatchResult::createBuilder()
            ->withMatchState(true)
            ->build();
    }

    public function get(EdgeContext $context, MatchResult $matchResult, array $query): RouteResult
    {
        $context->style('/student-database/static/css/shared.css');
        return new RouteResult([], NotFoundPage(), 404, 'Not Found');
    }

    public function post(EdgeContext $context, MatchResult $matchResult, array $query): RouteResult
    {
        return $this->get($context, $matchResult, $query);
    }
}
