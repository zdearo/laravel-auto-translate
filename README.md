# Laravel Auto Translate

[![Latest Version on Packagist](https://img.shields.io/packagist/v/zdearo/laravel-auto-translate.svg?style=flat-square)](https://packagist.org/packages/zdearo/laravel-auto-translate)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/zdearo/laravel-auto-translate/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/zdearo/laravel-auto-translate/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/zdearo/laravel-auto-translate/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/zdearo/laravel-auto-translate/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/zdearo/laravel-auto-translate.svg?style=flat-square)](https://packagist.org/packages/zdearo/laravel-auto-translate)

A Laravel package for extracting and managing translation strings in your Laravel applications. This package helps you identify untranslated strings in your codebase and manage translations for multiple languages.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-auto-translate.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-auto-translate)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

### Local Installation

You can install the package via composer in your Laravel project:

```bash
composer require zdearo/laravel-auto-translate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-auto-translate-config"
```

### Global Installation

You can also install this package globally to use it across multiple Laravel projects:

```bash
composer global require zdearo/laravel-auto-translate
```

Make sure to add the global Composer bin directory to your PATH:

```bash
# For Bash/ZSH
export PATH="$PATH:$HOME/.composer/vendor/bin"

# For Windows/PowerShell
$env:Path += ";$env:APPDATA\Composer\vendor\bin"
```

#### Linux/Mac Users

If you're using Linux or Mac, you may need to make the bin files executable:

```bash
chmod +x ~/.composer/vendor/bin/extract-translations
chmod +x ~/.composer/vendor/bin/merge-translations
```

## Usage

### Extract Translations

This command scans your Laravel application for translation strings and extracts them to a JSON file:

```bash
# If installed locally
php artisan translations:extract {locale}

# If installed globally
extract-translations {locale}
```

Replace `{locale}` with your desired language code (e.g., `en`, `pt_BR`, `es_ES`).

The command will:
1. Scan your application for translation strings
2. Create a `new_strings_{locale}.json` file with the found strings
3. Optionally merge the new strings into your existing translation file

### Merge Translations

This command merges translated strings from `new_strings_{locale}.json` to your main translation file:

```bash
# If installed locally
php artisan translations:merge {locale}

# If installed globally
merge-translations {locale}
```

The command offers three merge options:
1. Only merge strings that have translations
2. Merge all strings (including empty translations)
3. Interactive mode: review each untranslated string

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Adryel Dearo](https://github.com/zdearo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
