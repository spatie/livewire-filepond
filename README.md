# Upload files using Filepond in Livewire components

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/livewire-filepond.svg?style=flat-square)](https://packagist.org/packages/spatie/livewire-filepond)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/spatie/livewire-filepond/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/spatie/livewire-filepond/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/spatie/livewire-filepond/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/spatie/livewire-filepond/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/livewire-filepond.svg?style=flat-square)](https://packagist.org/packages/spatie/livewire-filepond)

[Filepond](https://pqina.nl/filepond/) is a powerful JavaScript library to upload files.

This repository contains a Livewire component that allow you to use Filepond easily in your  projects.

Here's an example of how you can use it in your views:

```bladehtml
<x-filepond::upload wire:model="file" />
```

Here's how it looks like in action in [mailcoach.app](https://mailcoach.app) (a product we built at Spatie):

![screenshot](https://github.com/spatie/livewire-filepond/blob/main/docs/images/upload.png)

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

First, add the scripts to your layout or view where you want to use Filepond:

```bladehtml
@filepondScripts
```

Next, add the `WithFilePond` trait to your component:

```php
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

class MyLivewireComponent extends Component
{
    use WithFilePond;
    
    public $file;
}
```

Finally, add the component to your view:

```bladehtml
<x-filepond::upload wire:model="file" />
```

### Customizing the component

Optionally, you can use these component properties to customize the component:

- `multiple`: Allow multiple files to be uploaded. Default: `false`.
- `required`: Make the file input required. Default: `false`.
- `disabled`: Disable the file input. Default: `false`.
- `placeholder`: Placeholder text for the file input. Default: `Drag & Drop your files or <span class="filepond--label-action"> Browse </span>`.

Additionally, you can also pass [any property that the Filepond component accepts](https://pqina.nl/filepond/docs/api/instance/properties/) and [plugins properties](https://pqina.nl/filepond/docs/api/plugins/). Make sure to use kebab case the property. For example, to set the maximum number of files to 5, you can do this:

```bladehtml
<x-filepond::upload wire:model="file" max-files="5" />
```

Localization automatically works based on the current locale. If you want to customize the language, you can publish the language file using:

```bash
php artisan vendor:publish --tag="livewire-filepond-translations"
```

If you want to change your locale you can do so by change the `.env` file:

```env
APP_LOCALE=id # change to Indonesian for example
```

or by setting the locale using laravel's `App` facade: 

```php
use Illuminate\Support\Facades\App;

App::setLocale('id'); // change to Indonesian for example
```
The last method can be used to change the locale on the fly. Like when a user changes the language (You need to implement this yourself).

## Events

Optionally, you can use these Alpine.js events when needed:

- `filepond-upload-started`: Started file upload.
- `filepond-upload-finished`: Finished file upload.
- `filepond-upload-reverted`: File upload reverted by user.
- `filepond-upload-file-removed`: File removed from list by user.
- `filepond-upload-completed`: All files in the list have been processed and uploaded.

## Server Side Validation on upload

Optionally, you can validate the uploaded file immediately. This is useful to inform the user of an error and process file uploads without requiring the user to click a button.

```php
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

class MyLivewireComponent extends Component
{
    use WithFilePond;
    
    public $file;

    public function rules(): array
    {
        return [
            'file' => 'required|mimetypes:image/jpg,image/jpeg,image/png|max:3000',
        ];
    }
    public function validateUploadedFile()
    {
        $this->validate();

        return true;
    }
}
```

## Publishing assets

Livewire Filepond automatically loads the scripts through an endpoint. If you want to serve the assets directly, you can publish them:

```shell
php artisan vendor:publish --tag=livewire-filepond-assets --force
```

To keep the assets up to that at all times, you can add the command above to your Composer's `post-update-cmd` scripts.

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

- [Rias Van der Veken](https://github.com/riasvdv)
- [All Contributors](../../contributors)
- [Ewilan Rivière](https://ewilan-riviere.com/articles/laravel-filepond-livewire)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
