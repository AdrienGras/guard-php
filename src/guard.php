<?php

declare(strict_types=1);

if (!function_exists('guard')) {
    /**
     * Guard helper - Asserts a condition and throw an exception if it fails.
     *
     * @param bool $condition Condition to check.
     * @param (Throwable|callable():Throwable|string|null) $onFailure
     *    Optional. If the condition is false, this can be:
     * - A Throwable instance to throw directly.
     * - A callable that returns a Throwable to throw.
     * - A string message to use in an InvalidArgumentException.
     * - null (default) to throw an InvalidArgumentException with a default message.
     * @param int|null $code Optional. Error code for the exception.
     * @param Throwable|null $previous Optional. Previous exception for chaining.
     *
     * @throws Throwable
     */
    function guard(
        bool $condition,
        Throwable|callable|string|null $onFailure = null,
        ?int $code = null,
        ?Throwable $previous = null
    ): void {
        if ($condition) {
            return;
        }

        // callable → fabrique l'exception à la demande
        if (is_callable($onFailure)) {
            $ex = $onFailure();
            if (!$ex instanceof Throwable) {
                throw new LogicException('guard() callable must return a Throwable');
            }
            throw $ex;
        }

        // exception fournie
        if ($onFailure instanceof Throwable) {
            throw $onFailure;
        }

        // message simple
        $message = $onFailure ?? 'Guard assertion failed';
        throw new InvalidArgumentException((string)$message, $code ?? 0, $previous);
    }
}