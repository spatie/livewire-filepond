# A Livewire Filepond component

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/livewire-filepond.svg?style=flat-square)](https://packagist.org/packages/spatie/livewire-filepond)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/spatie/livewire-filepond/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/spatie/livewire-filepond/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/spatie/livewire-filepond/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/spatie/livewire-filepond/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/livewire-filepond.svg?style=flat-square)](https://packagist.org/packages/spatie/livewire-filepond)

A Livewire Filepond component that you can `wire:model` to.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/livewire-filepond.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/livewire-filepond)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/livewire-filepond
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="livewire-filepond-views"
```

## Usage

Add the component to your view:

```bladehtml
<x-filepond::upload wire:model="file" />
```

Add the `WithFilePond` trait to your component:

```php
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

class MyLivewireComponent extends Component
{
    use WithFilePond;
    
    public $file;
}
```

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

- [Rias](https://github.com/riasvdv)
- [All Contributors](../../contributors)
- [Ewilan Rivière](https://ewilan-riviere.com/articles/laravel-filepond-livewire)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
