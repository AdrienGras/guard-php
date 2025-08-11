# guard-php

[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/AdrienGras/guard-php/tests.yml?branch=main&logo=github)](https://github.com/AdrienGras/guard-php/actions)
[![Packagist Version](https://img.shields.io/packagist/v/adriengras/guard-php?logo=packagist)](https://packagist.org/packages/adriengras/guard-php)
[![PHP Version](https://img.shields.io/packagist/php-v/adriengras/guard-php?logo=php)](https://packagist.org/packages/adriengras/guard-php)
[![License](https://img.shields.io/github/license/AdrienGras/guard-php)](LICENSE)

A tiny PHP library bringing the **`guard`** concept (inspired by Kotlin, Swift, Dart…) to PHP.  
Available globally via Composer — works in **plain PHP, Symfony, and Laravel**.

---

## ✨ Features

- **Global `guard()` function** available everywhere
- Throws an exception if a condition is not met
- Supports:
  - Simple error message
  - Existing exception instance
  - Lazy exception factory (`callable`)
- **Caller blame** mode: the error is shown as if it occurred where `guard()` was called, not inside the package
- PHP **8.1+** compatible
- Zero runtime dependencies

## ❓ Why this package?

Defensive programming is a key practice to make your code more reliable and easier to debug.  
Languages like **Kotlin** and **Swift** provide a native `guard` statement to quickly validate assumptions and fail fast when something is wrong.

In PHP, similar checks often look like this:

```php
if ($price < 0) {
    throw new InvalidArgumentException('Price must be non-negative');
}
```

This package brings a **concise, expressive, and consistent way** to write those checks:

```php
guard($price >= 0, 'Price must be non-negative');
```

Benefits:

- **Readability** – One-line, expressive intent
- **Consistency** – Same syntax across all projects and frameworks
- **Less boilerplate** – No repetitive if + throw blocks
- **Framework-agnostic** – Works in plain PHP, Symfony, Laravel…

## ⚙️ How it works — Caller blame mode

By default, guard() is in caller **blame mode** (`$blameCaller = true`):

- It scans the **debug backtrace** to find the first frame **outside** `vendor/` and outside the `guard.php` file itself.
- It then _tries_ to rewrite the `$file` and `$line` properties of the exception via `ReflectionProperty` so the error appears to originate from your code.
- If PHP forbids modifying these properties (some runtimes mark them internally as readonly), `guard()` falls back to **wrapping** the original exception in an `ErrorException`:
  - The new exception has `file` and `line` set to the caller’s location
  - The original exception is preserved as `$previous`

Example — without blame:

```txt
In vendor/your-vendor/guard-php/src/functions.php line 47:
Price must be non-negative
```

Example — with blame (default):

```txt
In src/Service/CheckoutService.php line 123:
Price must be non-negative
```

You can disable caller blame explicitly:

```php
guard($condition, 'message', null, null, false);
```

## 📦 Installation

```bash
composer require adriengras/guard-php
```

> The global function is automatically loaded via autoload.files — no extra configuration needed.

## 🚀 Usage

### Basic example

```php
guard($price >= 0, 'Price must be non-negative');
```

### With a custom exception

```php
guard($user !== null, new DomainException('User not found'));
```

### Lazy exception (callable)

```php
guard($stock >= $qty, fn() => new OutOfRangeException("Not enough stock for SKU {$sku}"));
```

## 🧩 Framework compatibility

- **Symfony** – works out-of-the-box in controllers, services, commands…
- **Laravel** – works directly in controllers, jobs, events…
- **Plain PHP** – just require 'vendor/autoload.php';

## 🧪 Tests

Tests are written with [Pest](https://pestphp.com/).

### Run tests:

```bash
composer test
```

### Run tests with coverage:

```bash
XDEBUG_MODE=coverage composer test:cov
```

## 🤝 Contributing

Contributions are welcome!  
If you’d like to contribute:

- Fork the repository
- Create a new branch (`git checkout -b feature/my-feature`)
- Make your changes
- Run the tests (`composer test`)
- Submit a Pull Request

## 📄 License

MIT - You can find the licence in the [LICENSE](LICENSE) file.
