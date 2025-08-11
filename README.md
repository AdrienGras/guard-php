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
- PHP **8.1+** compatible
- Zero runtime dependencies

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
