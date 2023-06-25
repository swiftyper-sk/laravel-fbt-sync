<h1 align="center">
  <img src="icon.png" height="150" width="150" alt="FBT"/>
</h1>

# Laravel FBT sync

This library allows you to import native phrases and automatically deploy reviewed translations.

[Get started with Swiftyper Translations](https://translations.swiftyper.sk)

## Requirements
* [FBT package](https://www.github.com/richarddobron/fbt)
* PHP 7.0 or higher
* Laravel 5.5 or higher
* [Composer](https://getcomposer.org) is required for installation

## 📦 Installing

```shell
$ composer require swiftyper/laravel-fbt-sync
```

## 🔧 Configuration

These steps are required:
1. Register your FBT project on [Swiftyper Translations](https://translations.swiftyper.sk)


2. Publish config file:
```shell
$ php artisan vendor:publish --provider="Swiftyper\fbt\IntlServiceProvider" --tag=swiftyper-config
```

3. Set option **api_key** in /config/fbt.php.


4. Init project settings:
```shell
$ php artisan swiftyper:fbt --init
```

### Options

The following options can be defined:

* **api_key** `string`: Project API key (required)
* **routes** `bool`: Enable routes: `/intl/deploy`, `/intl/upload`, `/intl/sync`
* **verify_signature** `bool`: Verify signature from response

## 	🚀 Artisan Command

```shell
php artisan swiftyper:fbt
```

### Options

| Option          | Description                                                     |
|-----------------|-----------------------------------------------------------------|
| --deploy        | Deploy reviewed app translations                                |
| --upload        | Upload phrases/translations to swiftyper (from default storage) |
| --upload=[path] | Upload phrases/translations to swiftyper                        |
| --init          | Connect fbt project with swiftyper                              |
| --pretty        | Pretty print output (default: `true`)                           |

## 📜 License
The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
