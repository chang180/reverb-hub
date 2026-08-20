<?php

use App\Actions\Applications\ParseAllowedOrigins;

test('strips scheme, port, and path so origins match Reverb\'s host-only comparison', function () {
    $parse = new ParseAllowedOrigins;

    expect($parse('https://shop.example.com'))->toBe(['shop.example.com'])
        ->and($parse('http://shop.example.com:8080/path'))->toBe(['shop.example.com'])
        ->and($parse('shop.example.com'))->toBe(['shop.example.com']);
});

test('splits comma or whitespace separated origins and dedupes', function () {
    $parse = new ParseAllowedOrigins;

    expect($parse('https://a.test, https://b.test https://a.test'))->toBe(['a.test', 'b.test']);
});

test('preserves the wildcard as-is', function () {
    $parse = new ParseAllowedOrigins;

    expect($parse('*'))->toBe(['*'])
        ->and($parse(''))->toBe(['*']);
});

test('preserves wildcard subdomain patterns', function () {
    $parse = new ParseAllowedOrigins;

    expect($parse('*.example.com'))->toBe(['*.example.com']);
});
