<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Quanta\Http\Stack;
use Quanta\Http\Route;
use Quanta\Http\MethodList;
use Quanta\Http\RouteKeyList;

describe('Route', function () {

    describe('->methods()', function () {

        it('should return an array containing the http methods', function () {
            $handler = mock(RequestHandlerInterface::class)->get();

            $route = new Route(new MethodList('GET', 'POST'), '', new RouteKeyList, [], $handler);

            $test = $route->methods();

            expect($test)->toEqual(['GET', 'POST']);
        });

    });

    describe('->pattern()', function () {

        it('should return the pattern', function () {
            $handler = mock(RequestHandlerInterface::class)->get();

            $route = new Route(new MethodList('GET'), '/pattern', new RouteKeyList, [], $handler);

            $test = $route->pattern();

            expect($test)->toEqual('/pattern');
        });

    });

    describe('->hasName()', function () {

        context('when the RouteKeyList is empty', function () {

            it('should return false', function () {
                $handler = mock(RequestHandlerInterface::class)->get();

                $route = new Route(new MethodList('GET'), '', new RouteKeyList, [], $handler);

                $test = $route->hasName();

                expect($test)->toBeFalsy();
            });

        });

        context('when the RouteKeyList is not empty', function () {

            it('should return true', function () {
                $handler = mock(RequestHandlerInterface::class)->get();

                $route = new Route(new MethodList('GET'), '', new RouteKeyList('key'), [], $handler);

                $test = $route->hasName();

                expect($test)->toBeTruthy();
            });

        });

    });

    describe('->name()', function () {

        context('when the RouteKeyList is empty', function () {

            it('should throw a LogicException', function () {
                $handler = mock(RequestHandlerInterface::class)->get();

                $route = new Route(new MethodList('GET'), '', new RouteKeyList, [], $handler);

                $test = fn() => $route->name();

                expect($test)->toThrow(new LogicException);
            });

        });

        context('when the RouteKeyList contains one key', function () {

            it('should return the key', function () {
                $handler = mock(RequestHandlerInterface::class)->get();

                $route = new Route(new MethodList('GET'), '', new RouteKeyList('key'), [], $handler);

                $test = $route->name();

                expect($test)->toEqual('key');
            });

        });

        context('when the RouteKeyList contains many keys', function () {

            context('when no separator is given', function () {

                it('should return the keys concatened with a dot', function () {
                    $handler = mock(RequestHandlerInterface::class)->get();

                    $route = new Route(new MethodList('GET'), '', new RouteKeyList('key1', 'key2', 'key3'), [], $handler);

                    $test = $route->name();

                    expect($test)->toEqual('key1.key2.key3');
                });

            });

            context('when a separator is given', function () {

                it('should return the keys concatened with the given separator', function () {
                    $handler = mock(RequestHandlerInterface::class)->get();

                    $route = new Route(new MethodList('GET'), '', new RouteKeyList('key1', 'key2', 'key3'), [], $handler);

                    $test = $route->name('-');

                    expect($test)->toEqual('key1-key2-key3');
                });

            });

        });

    });

    describe('->has()', function () {

        context('when the given key is not a metadata of the route', function () {

            it('should return false', function () {
                $handler = mock(RequestHandlerInterface::class)->get();

                $route = new Route(new MethodList('GET'), '', new RouteKeyList, [], $handler);

                $test = $route->has('key');

                expect($test)->toBeFalsy();
            });

        });

        context('when the given key is a metadata of the route', function () {

            it('should return true', function () {
                $handler = mock(RequestHandlerInterface::class)->get();

                $route = new Route(new MethodList('GET'), '', new RouteKeyList, ['key' => 'value'], $handler);

                $test = $route->has('key');

                expect($test)->toBeTruthy();
            });

        });

    });

    describe('->get()', function () {

        context('when the given key is not a metadata of the route', function () {

            context('when no default value is given', function () {

                it('should return null', function () {
                    $handler = mock(RequestHandlerInterface::class)->get();

                    $route = new Route(new MethodList('GET'), '', new RouteKeyList, [], $handler);

                    $test = $route->get('key');

                    expect($test)->toBeNull();
                });

            });

            context('when a default value is given', function () {

                it('should return the default value', function () {
                    $handler = mock(RequestHandlerInterface::class)->get();

                    $route = new Route(new MethodList('GET'), '', new RouteKeyList, [], $handler);

                    $test = $route->get('key', 'default');

                    expect($test)->toEqual('default');
                });

            });

        });

        context('when the given key is a metadata of the route', function () {

            it('should return the value associated to the key', function () {
                $handler = mock(RequestHandlerInterface::class)->get();

                $route = new Route(new MethodList('GET'), '', new RouteKeyList, ['key' => 'value'], $handler);

                $test = $route->has('key');

                expect($test)->toEqual('value');
            });

        });

    });

    describe('->handler()', function () {

        context('when there is no middleware', function () {

            it('should return the request handler', function () {
                $handler = mock(RequestHandlerInterface::class)->get();

                $route = new Route(new MethodList('GET'), '', new RouteKeyList, ['key' => 'value'], $handler);

                $test = $route->handler();

                expect($test)->toBe($handler);
            });

        });

        context('when there is at least one middleware', function () {

            it('should return a stack with the middleware and the request handler', function () {
                $handler = mock(RequestHandlerInterface::class)->get();
                $middleware1 = mock(MiddlewareInterface::class)->get();
                $middleware2 = mock(MiddlewareInterface::class)->get();
                $middleware3 = mock(MiddlewareInterface::class)->get();

                $route = new Route(
                    new MethodList('GET'),
                    '',
                    new RouteKeyList,
                    ['key' => 'value'],
                    $handler,
                    $middleware1,
                    $middleware2,
                    $middleware3,
                );

                $test = $route->handler();

                expect($test)->toEqual(
                    new Stack(
                        new Stack(
                            new Stack($handler, $middleware3),
                            $middleware2,
                        ),
                        $middleware1,
                    ),
                );
            });

        });

    });

});
