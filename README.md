[![MIT Licensed](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)

Adds "quick access links" and permissions to Statamic, for popular Laravel (Horizon, Nova, Telescope) packages.

## Requirements

* Statamic 3.1 or higher

## Installation

You can install the package via composer:

```shell
composer require handmadeweb/statamic-laravel-packages
```

Then you can assign permissions via Statamic which will be usable in the form of `can` / `cant`, `access laravel telescope`, `access laravel horizon` or `access laravel nova`

Then just update the service provider for your chosen package, for example with Telescope we might use the following.

`\App\Providers\TelescopeServiceProvider`
```php
    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewTelescope', function ($user) {
            return $user->can('access laravel telescope');
        });
    }
```

And now you will be able to manage access via Statamic.

## Usage

### Permissions
You'll be able to add permissions for each of the installed `Laravel Packages` in the Statamic control panel.

![Permissions](https://user-images.githubusercontent.com/54159303/127936231-da467d4f-fe8c-48fc-9a90-83f877e54af0.png)


### Control Panel
Any package/links that the admin/user has permission to access (and is installed) will appear on the sidebar.

![Sidebar](https://user-images.githubusercontent.com/54159303/127937372-9ec5a9c1-903a-4df8-8b57-b98dd932d1c3.png)

## Changelog

Please see [CHANGELOG](https://statamic.com/addons/handmadeweb/statamic-laravel-packages/release-notes) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/handmadeweb/statamic-laravel-packages/blob/main/CONTRIBUTING.md) for details.

## Credits

- [Handmade Web & Design](https://github.com/handmadeweb)
- [Michael Rook](https://github.com/michaelr0)
- [All Contributors](https://github.com/handmadeweb/statamic-laravel-packages/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](https://github.com/handmadeweb/statamic-laravel-packages/blob/main/LICENSE) for more information.
