<?php

declare(strict_types=1);

use Quanta\Http\MethodList;

describe('MethodList::get()', function () {

    it('should return a GET method', function () {
        $test = MethodList::get();

        expect($test)->toEqual(new MethodList('GET'));
    });

});

describe('MethodList::post()', function () {

    it('should return a POST method', function () {
        $test = MethodList::post();

        expect($test)->toEqual(new MethodList('POST'));
    });

});

describe('MethodList::put()', function () {

    it('should return a PUT method', function () {
        $test = MethodList::put();

        expect($test)->toEqual(new MethodList('PUT'));
    });

});

describe('MethodList::delete()', function () {

    it('should return a DELETE method', function () {
        $test = MethodList::delete();

        expect($test)->toEqual(new MethodList('DELETE'));
    });

});

describe('MethodList::patch()', function () {

    it('should return a PATCH method', function () {
        $test = MethodList::patch();

        expect($test)->toEqual(new MethodList('PATCH'));
    });

});

describe('MethodList::head()', function () {

    it('should return a HEAD method', function () {
        $test = MethodList::head();

        expect($test)->toEqual(new MethodList('HEAD'));
    });

});

describe('MethodList::options()', function () {

    it('should return a OPTIONS method', function () {
        $test = MethodList::options();

        expect($test)->toEqual(new MethodList('OPTIONS'));
    });

});

describe('MethodList::all()', function () {

    it('should return a MethodList with all http methods', function () {
        $test = MethodList::all();

        expect($test)->toEqual(new MethodList('GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'));
    });

});

describe('MethodList', function () {

    it('should throw when no http method is given', function () {
        $test = fn () => new MethodList;

        expect($test)->toThrow();
    });

    describe('->values()', function () {

        it('should return an array containing the http methods', function () {
            $methods = new MethodList('GET', 'POST');

            $test = $methods->values();

            expect($test)->toEqual(['GET', 'POST']);
        });

    });

});
