<?php

it('does nothing when condition is true', function () {
    expect(fn() => guard(true))->not->toThrow(Throwable::class);
});

it('throws InvalidArgumentException with message when condition is false and message passed', function () {
    expect(fn() => guard(false, 'Price must be non-negative'))
        ->toThrow(InvalidArgumentException::class, 'Price must be non-negative');
});

it('throws provided Throwable as-is', function () {
    $ex = new DomainException('User not found');
    expect(fn() => guard(false, $ex))
        ->toThrow(DomainException::class, 'User not found');
});

it('lazily creates exception via callable', function () {
    $sku = 'ABC123';
    expect(fn() => guard(false, fn() => new OutOfRangeException("Not enough stock for {$sku}")))
        ->toThrow(OutOfRangeException::class, 'Not enough stock for ABC123');
});

it('errors if callable does not return a Throwable', function () {
    expect(fn() => guard(false, fn() => 'oops'))
        ->toThrow(ErrorException::class, 'guard() callable must return a Throwable');
});

it('sets code and previous when using message form', function () {
    $prev = new RuntimeException('prev');
    try {
        guard(false, 'Operation failed', 400, $prev);
        expect()->unreachable('guard() should have thrown');
    } catch (InvalidArgumentException $e) {
        expect($e->getCode())->toBe(400)
            ->and($e->getPrevious())->toBe($prev);
    }
});

it('is globally available thanks to autoload.files', function () {
    // Just assert function exists (sanity check for Composer autoload)
    expect(function_exists('guard'))->toBeTrue();
});

it('moves exception origin to caller when blameCaller is true', function () {
    $ex = new DomainException('Boom');

    $expectedFile = __FILE__;
    $expectedLine = __LINE__ + 2; // la prochaine ligne est l'appel à guard()
    try {
        guard(false, $ex);
        expect()->unreachable('guard() should have thrown');
    } catch (DomainException $caught) {
        expect($caught->getFile())->toBe($expectedFile)
            ->and($caught->getLine())->toBe($expectedLine);
    }
});
