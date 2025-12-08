<?php

namespace App\Routes\Home;

use EdgeFramework\Routing\EdgeContext;
use EdgeFramework\View\Element;
use EdgeFramework\View\Renderer;
use EdgeFramework\View\Text;
use EdgeFramework\Routing\MatchResult;
use EdgeFramework\Routing\Ok;
use EdgeFramework\Routing\Route as RouterRoute;
use EdgeFramework\Routing\RouteResult;
use function EdgeFramework\View\IntrinsicElements\{div};
function Header(){
}

function Page()
{
    return
        new Element('div', ['id' => 'home-page', 'class' =>
            'min-h-screen flex flex-col font-sans ' .
            'bg-gradient-to-br from-indigo-400 to-purple-600'
        ], [
            new Element('header', ['class' =>
                'inline-flex w-full bg-black/80 text-white py-5 shadow-lg'
            ], [
                new Element('h1', ['class' => 'text-xl pl-5'], [
                    new Text('Student Database Management System')
                ]),
            ]),

            new Element('div', ['class' =>
                'max-w-screen-xl mx-auto my-10 px-5 flex-1'
            ], [

                // hero section
                new Element('div', ['class' =>
                    'bg-white rounded-xl p-10 shadow-2xl mb-8'
                ], [
                    new Element('h2', ['class' => 'text-3xl text-gray-800 mb-4'], [
                        new Text('Welcome to the Student Database')
                    ]),
                    new Element('p', ['class' => 'text-gray-600 leading-relaxed mb-6'], [
                        new Text('Manage student records efficiently and securely. Access, update, and organize student information all in one place.')
                    ]),

                    // button group
                    new Element('div', ['class' => 'flex flex-wrap gap-4'], [
                        new Element('a', [
                            'href' => '/students',
                            'class' =>
                                'px-6 py-3 rounded-md text-white bg-indigo-500 ' .
                                'hover:bg-indigo-600 transition transform ' .
                                'hover:-translate-y-1 shadow'
                        ], [new Text('View Students')]),

                        new Element('a', [
                            'href' => '/add-student',
                            'class' =>
                                'px-6 py-3 rounded-md text-white bg-indigo-500 ' .
                                'hover:bg-indigo-600 transition transform ' .
                                'hover:-translate-y-1 shadow'
                        ], [new Text('Add New Student')]),

                        new Element('a', [
                            'href' => '/settings',
                            'class' =>
                                'px-6 py-3 rounded-md border-2 border-indigo-500 ' .
                                'text-muted bg-gray-100 hover:bg-indigo-500 hover:text-white ' .
                                'transition transform hover:-translate-y-1'
                        ], [new Text('Settings')]),
                    ]),
                ]),

                // features grid
                new Element('div', ['class' =>
                    'grid gap-5 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
                ], [

                    new Element('div', ['class' =>
                        'bg-white p-8 rounded-xl shadow-lg text-center transition transform hover:-translate-y-1'
                    ], [
                        new Element('h3', ['class' => 'text-indigo-500 mb-2'], [
                            new Text('📚 Student Records')
                        ]),
                        new Element('p', ['class' => 'text-gray-600 text-sm leading-relaxed'], [
                            new Text('Maintain comprehensive student records with personal information, academic history, and contact details.')
                        ])
                    ]),

                    new Element('div', ['class' =>
                        'bg-white p-8 rounded-xl shadow-lg text-center transition transform hover:-translate-y-1'
                    ], [
                        new Element('h3', ['class' => 'text-indigo-500 mb-2'], [
                            new Text('📊 Analytics')
                        ]),
                        new Element('p', ['class' => 'text-gray-600 text-sm leading-relaxed'], [
                            new Text('Track student performance, enrollment trends, and generate detailed reports for analysis.')
                        ])
                    ]),

                    new Element('div', ['class' =>
                        'bg-white p-8 rounded-xl shadow-lg text-center transition transform hover:-translate-y-1'
                    ], [
                        new Element('h3', ['class' => 'text-indigo-500 mb-2'], [
                            new Text('🔒 Security')
                        ]),
                        new Element('p', ['class' => 'text-gray-600 text-sm leading-relaxed'], [
                            new Text('Secure access controls and data encryption ensure student information remains protected at all times.')
                        ])
                    ]),
                ]),
            ]),
        ]);
}

class Route extends RouterRoute
{
    public function match(string $routePath): MatchResult
    {
        $result = MatchResult::createBuilder();
        $result->withMatchState($routePath === '/' || $routePath === '');
        return $result->build();
    }

    public function get(EdgeContext $context, MatchResult $matchResult, array $query): RouteResult
    {
        $context->title('Student Database | Homepage');
        // you no longer need the css files:
        $context->style('./static/css/shared.css');
        $context->script('./static/js/jquery.js');
        $context->script('./static/js/home.js');
        return new Ok([], Page());
    }

    public function post(EdgeContext $context, MatchResult $matchResult, array $query): RouteResult
    {
        throw new \Exception('Not implemented');
    }
}
