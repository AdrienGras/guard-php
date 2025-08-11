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
        ?Throwable $previous = null,
        bool $blameCaller = true
    ): void {
        if ($condition) {
            return;
        }

        // 1) Build the exception to throw
        if (is_callable($onFailure)) {
            $ex = $onFailure();
            if (!$ex instanceof Throwable) {
                // blame caller for misuse
                $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                $caller = $bt[1] ?? null;
                throw new ErrorException(
                    'guard() callable must return a Throwable',
                    0,
                    E_USER_ERROR,
                    $caller['file'] ?? __FILE__,
                    $caller['line'] ?? __LINE__
                );
            }
        } elseif ($onFailure instanceof Throwable) {
            $ex = $onFailure;
        } else {
            $ex = new InvalidArgumentException((string)($onFailure ?? 'Guard assertion failed'), $code ?? 0, $previous);
        }

        if (!$blameCaller) {
            throw $ex;
        }

        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 0); // un peu de marge
        $callerFile = __FILE__;
        $callerLine = __LINE__;

        foreach ($bt as $frame) {
            if (!isset($frame['file'], $frame['line'])) {
                continue;
            }
            $file = (string)$frame['file'];

            // Skip: notre fichier + tout ce qui est dans vendor/
            if (str_contains($file, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR)) {
                continue;
            }
            if ($file === __FILE__) {
                continue;
            }

            $callerFile = $file;
            $callerLine = (int)$frame['line'];
            break;
        }

        // 3) Tenter de réécrire file/line, sinon wrap
        static $relocate = null;
        $relocate ??= static function (Throwable $e, string $file, int $line): bool {
            try {
                $rpFile = new ReflectionProperty($e, 'file');
                $rpLine = new ReflectionProperty($e, 'line');
                $rpFile->setAccessible(true);
                $rpLine->setAccessible(true);
                $rpFile->setValue($e, $file);
                $rpLine->setValue($e, $line);
                return true;
            } catch (Throwable) {
                return false;
            }
        };

        if ($relocate($ex, $callerFile, $callerLine)) {
            throw $ex; // type conservé
        }

        // Fallback: wrap pour “blâmer” l'appelant
        throw new ErrorException(
            $ex->getMessage(),
            $ex->getCode(),
            E_USER_ERROR,
            $callerFile,
            $callerLine,
            $ex
        );
    }
}
