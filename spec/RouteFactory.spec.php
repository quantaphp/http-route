<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Quanta\Http\Route;
use Quanta\Http\MethodList;
use Quanta\Http\RouteKeyList;
use Quanta\Http\RouteFactory;
use Quanta\Http\LazyMiddleware;
use Quanta\Http\LazyRequestHandler;

describe('RouteFactory::root()', function () {

    it('should return an empty RouteFactory', function () {
        $test = RouteFactory::root();

        expect($test)->toEqual(new RouteFactory('', new RouteKeyList, []));
    });

});

describe('RouteFactory', function () {

    describe('->named()', function () {

        it('should return a new RouteFactory with the given keys', function () {
            $middleware = mock(MiddlewareInterface::class)->get();

            $test1 = new RouteFactory('/pattern', new RouteKeyList, ['key' => 'value'], $middleware);

            $test2 = $test1->named('key1');

            expect($test2)->not->toBe($test1);
            expect($test2)->toEqual(new RouteFactory(
                '/pattern',
                new RouteKeyList('key1'),
                ['key' => 'value'],
                $middleware,
            ));

            $test3 = $test2->named('key2', 'key3');

            expect($test3)->not->toBe($test2);
            expect($test3)->toEqual(new RouteFactory(
                '/pattern',
                new RouteKeyList('key1', 'key2', 'key3'),
                ['key' => 'value'],
                $middleware,
            ));
        });

    });

    describe('->matching()', function () {

        it('should return a new RouteFactory with the given pattern', function () {
            $middleware = mock(MiddlewareInterface::class)->get();

            $test1 = new RouteFactory('', new RouteKeyList('key'), ['key' => 'value'], $middleware);

            $test2 = $test1->matching('/pattern1');

            expect($test2)->not->toBe($test1);
            expect($test2)->toEqual(new RouteFactory(
                '/pattern1',
                new RouteKeyList('key'),
                ['key' => 'value'],
                $middleware,
            ));

            $test3 = $test2->matching('/pattern2');

            expect($test3)->not->toBe($test2);
            expect($test3)->toEqual(new RouteFactory(
                '/pattern1/pattern2',
                new RouteKeyList('key'),
                ['key' => 'value'],
                $middleware,
            ));
        });

    });

    describe('->with()', function () {

        it('should return a new RouteFactory with the given metadata', function () {
            $middleware = mock(MiddlewareInterface::class)->get();

            $test1 = new RouteFactory('/pattern', new RouteKeyList('key'), [], $middleware);

            $test2 = $test1->with('key1', 'value1');

            expect($test2)->not->toBe($test1);
            expect($test2)->toEqual(new RouteFactory(
                '/pattern',
                new RouteKeyList('key'),
                ['key1' => 'value1'],
                $middleware,
            ));

            $test3 = $test2->with('key2', 'value2');

            expect($test3)->not->toBe($test2);
            expect($test3)->toEqual(new RouteFactory(
                '/pattern',
                new RouteKeyList('key'),
                ['key1' => 'value1', 'key2' => 'value2'],
                $middleware,
            ));
        });

    });

    describe('->middleware()', function () {

        it('should return a new RouteFactory with the given middleware', function () {
            $middleware1 = mock(MiddlewareInterface::class)->get();
            $middleware2 = mock(MiddlewareInterface::class)->get();
            $middleware3 = fn() => mock(MiddlewareInterface::class)->get();
            $middleware4 = mock(MiddlewareInterface::class)->get();

            $test1 = new RouteFactory('/pattern', new RouteKeyList('key'), ['key' => 'value']);

            $test2 = $test1->middleware($middleware1);

            expect($test2)->not->toBe($test1);
            expect($test2)->toEqual(new RouteFactory(
                '/pattern',
                new RouteKeyList('key'),
                ['key' => 'value'],
                $middleware1,
            ));

            $test3 = $test2->middleware($middleware2, $middleware3, $middleware4);

            expect($test3)->not->toBe($test2);
            expect($test3)->toEqual(new RouteFactory(
                '/pattern',
                new RouteKeyList('key'),
                ['key' => 'value'],
                $middleware1,
                $middleware2,
                new LazyMiddleware($middleware3),
                $middleware4,
            ));
        });

    });

    describe('->get()', function () {

        it('should return a Route with GET method and the given request handler', function () {
            $middleware1 = mock(MiddlewareInterface::class)->get();
            $middleware2 = mock(MiddlewareInterface::class)->get();
            $middleware3 = mock(MiddlewareInterface::class)->get();
            $handler = mock(RequestHandlerInterface::class)->get();

            $factory = new RouteFactory('/pattern', new RouteKeyList('key'), ['key' => 'value'], $middleware1, $middleware2, $middleware3);

            $test = $factory->get($handler);

            expect($test)->toEqual(new Route(
                new MethodList('GET'),
                '/pattern',
                new RouteKeyList('key'),
                ['key' => 'value'],
                $handler,
                $middleware1,
                $middleware2,
                $middleware3,
            ));
        });

    });

    describe('->post()', function () {

        it('should return a Route with POST method and the given request handler', function () {
            $middleware1 = mock(MiddlewareInterface::class)->get();
            $middleware2 = mock(MiddlewareInterface::class)->get();
            $middleware3 = mock(MiddlewareInterface::class)->get();
            $handler = mock(RequestHandlerInterface::class)->get();

            $factory = new RouteFactory('/pattern', new RouteKeyList('key'), ['key' => 'value'], $middleware1, $middleware2, $middleware3);

            $test = $factory->post($handler);

            expect($test)->toEqual(new Route(
                new MethodList('POST'),
                '/pattern',
                new RouteKeyList('key'),
                ['key' => 'value'],
                $handler,
                $middleware1,
                $middleware2,
                $middleware3,
            ));
        });

    });

    describe('->put()', function () {

        it('should return a Route with PUT method and the given request handler', function () {
            $middleware1 = mock(MiddlewareInterface::class)->get();
            $middleware2 = mock(MiddlewareInterface::class)->get();
            $middleware3 = mock(MiddlewareInterface::class)->get();
            $handler = mock(RequestHandlerInterface::class)->get();

            $factory = new RouteFactory('/pattern', new RouteKeyList('key'), ['key' => 'value'], $middleware1, $middleware2, $middleware3);

            $test = $factory->put($handler);

            expect($test)->toEqual(new Route(
                new MethodList('PUT'),
                '/pattern',
                new RouteKeyList('key'),
                ['key' => 'value'],
                $handler,
                $middleware1,
                $middleware2,
                $middleware3,
            ));
        });

    });

    describe('->delete()', function () {

        it('should return a Route with DELETE method and the given request handler', function () {
            $middleware1 = mock(MiddlewareInterface::class)->get();
            $middleware2 = mock(MiddlewareInterface::class)->get();
            $middleware3 = mock(MiddlewareInterface::class)->get();
            $handler = mock(RequestHandlerInterface::class)->get();

            $factory = new RouteFactory('/pattern', new RouteKeyList('key'), ['key' => 'value'], $middleware1, $middleware2, $middleware3);

            $test = $factory->delete($handler);

            expect($test)->toEqual(new Route(
                new MethodList('DELETE'),
                '/pattern',
                new RouteKeyList('key'),
                ['key' => 'value'],
                $handler,
                $middleware1,
                $middleware2,
                $middleware3,
            ));
        });

    });

    describe('->patch()', function () {

        it('should return a Route with PATCH method and the given request handler', function () {
            $middleware1 = mock(MiddlewareInterface::class)->get();
            $middleware2 = mock(MiddlewareInterface::class)->get();
            $middleware3 = mock(MiddlewareInterface::class)->get();
            $handler = mock(RequestHandlerInterface::class)->get();

            $factory = new RouteFactory('/pattern', new RouteKeyList('key'), ['key' => 'value'], $middleware1, $middleware2, $middleware3);

            $test = $factory->patch($handler);

            expect($test)->toEqual(new Route(
                new MethodList('PATCH'),
                '/pattern',
                new RouteKeyList('key'),
                ['key' => 'value'],
                $handler,
                $middleware1,
                $middleware2,
                $middleware3,
            ));
        });

    });

    describe('->head()', function () {

        it('should return a Route with HEAD method and the given request handler', function () {
            $middleware1 = mock(MiddlewareInterface::class)->get();
            $middleware2 = mock(MiddlewareInterface::class)->get();
            $middleware3 = mock(MiddlewareInterface::class)->get();
            $handler = mock(RequestHandlerInterface::class)->get();

            $factory = new RouteFactory('/pattern', new RouteKeyList('key'), ['key' => 'value'], $middleware1, $middleware2, $middleware3);

            $test = $factory->head($handler);

            expect($test)->toEqual(new Route(
                new MethodList('HEAD'),
                '/pattern',
                new RouteKeyList('key'),
                ['key' => 'value'],
                $handler,
                $middleware1,
                $middleware2,
                $middleware3,
            ));
        });

    });

    describe('->options()', function () {

        it('should return a Route with OPTIONS method and the given request handler', function () {
            $middleware1 = mock(MiddlewareInterface::class)->get();
            $middleware2 = mock(MiddlewareInterface::class)->get();
            $middleware3 = mock(MiddlewareInterface::class)->get();
            $handler = mock(RequestHandlerInterface::class)->get();

            $factory = new RouteFactory('/pattern', new RouteKeyList('key'), ['key' => 'value'], $middleware1, $middleware2, $middleware3);

            $test = $factory->options($handler);

            expect($test)->toEqual(new Route(
                new MethodList('OPTIONS'),
                '/pattern',
                new RouteKeyList('key'),
                ['key' => 'value'],
                $handler,
                $middleware1,
                $middleware2,
                $middleware3,
            ));
        });

    });

    describe('->all()', function () {

        it('should return a Route with all http methods and the given request handler', function () {
            $middleware1 = mock(MiddlewareInterface::class)->get();
            $middleware2 = mock(MiddlewareInterface::class)->get();
            $middleware3 = mock(MiddlewareInterface::class)->get();
            $handler = mock(RequestHandlerInterface::class)->get();

            $factory = new RouteFactory('/pattern', new RouteKeyList('key'), ['key' => 'value'], $middleware1, $middleware2, $middleware3);

            $test = $factory->all($handler);

            expect($test)->toEqual(new Route(
                new MethodList('GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'),
                '/pattern',
                new RouteKeyList('key'),
                ['key' => 'value'],
                $handler,
                $middleware1,
                $middleware2,
                $middleware3,
            ));
        });

    });

    describe('->route()', function () {

        it('should return a Route with the given http method list and the given request handler', function () {
            $middleware1 = mock(MiddlewareInterface::class)->get();
            $middleware2 = mock(MiddlewareInterface::class)->get();
            $middleware3 = mock(MiddlewareInterface::class)->get();
            $handler = mock(RequestHandlerInterface::class)->get();

            $factory = new RouteFactory('/pattern', new RouteKeyList('key'), ['key' => 'value'], $middleware1, $middleware2, $middleware3);

            $test = $factory->route(new MethodList('GET', 'POST'), $handler);

            expect($test)->toEqual(new Route(
                new MethodList('GET', 'POST'),
                '/pattern',
                new RouteKeyList('key'),
                ['key' => 'value'],
                $handler,
                $middleware1,
                $middleware2,
                $middleware3,
            ));
        });

    });

});
