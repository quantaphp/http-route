<?php

declare(strict_types=1);

use Quanta\Http\RouteKeyList;

describe('RouteKeyList', function () {

    describe('->with()', function () {

        it('should return a new RouteKeyList with the given key', function () {
            $test1 = new RouteKeyList;

            $test2 = $test1->with('key1');

            expect($test2)->not->toBe($test1);
            expect($test2)->toEqual(new RouteKeyList('key1'));

            $test3 = $test2->with('key2', 'key3');

            expect($test3)->not->toBe($test2);
            expect($test3)->toEqual(new RouteKeyList('key1', 'key2', 'key3'));
        });

    });

    describe('->isEmpty()', function () {

        context('when there is no key', function () {

            it('should return true', function () {
                $keys = new RouteKeyList;

                $test = $keys->isEmpty();

                expect($test)->toBeTruthy();
            });

        });

        context('when there is at least one key', function () {

            it('should return false', function () {
                $keys = new RouteKeyList('key');

                $test = $keys->isEmpty();

                expect($test)->toBeFalsy();
            });

        });

    });

    describe('->value()', function () {

        context('when there is no key', function () {

            it('should return an empty string', function () {
                $keys = new RouteKeyList;

                $test = $keys->value('.');

                expect($test)->toEqual('');
            });

        });

        context('when there is one key', function () {

            it('should return the key', function () {
                $keys = new RouteKeyList('key');

                $test = $keys->value('.');

                expect($test)->toEqual('key');
            });

        });

        context('when there more than one key', function () {

            it('should return the keys concatened with the given separator', function () {
                $keys = new RouteKeyList('key1', 'key2', 'key3');

                $test = $keys->value('.');

                expect($test)->toEqual('key1.key2.key3');
            });

        });

    });

});
