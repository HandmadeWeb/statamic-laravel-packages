<?php

namespace HandmadeWeb\StatamicLaravelPackages;

use Illuminate\Support\Facades\Auth;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Permission;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Support\Str;

class ServiceProvider extends AddonServiceProvider
{
    protected $laravelPackageProviders = [];

    public function __construct($app)
    {
        $this->laravelPackageProviders = [
            \App\Providers\HorizonServiceProvider::class => [
                'name' => 'horizon',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" fill="#5B676E"><path d="M5.26176342 26.4094389C2.04147988 23.6582233 0 19.5675182 0 15c0-4.1421356 1.67893219-7.89213562 4.39339828-10.60660172C7.10786438 1.67893219 10.8578644 0 15 0c8.2842712 0 15 6.71572875 15 15 0 8.2842712-6.7157288 15-15 15-3.716753 0-7.11777662-1.3517984-9.73823658-3.5905611zM4.03811305 15.9222506C5.70084247 14.4569342 6.87195416 12.5 10 12.5c5 0 5 5 10 5 3.1280454 0 4.2991572-1.9569336 5.961887-3.4222502C25.4934253 8.43417206 20.7645408 4 15 4 8.92486775 4 4 8.92486775 4 15c0 .3105915.01287248.6181765.03811305.9222506z"></path></svg>',
                'url' => '/'.trim(config('horizon.path'), '/'),
            ],
            \App\Providers\NovaServiceProvider::class => [
                'name' => 'nova',
                'icon' => '<svg class="h-8 md:h-10" viewBox="-2 -2 40 40" xmlns="http://www.w3.org/2000/svg" fill="#5B676E"><g fill-rule="nonzero" fill="#5B676E"><path d="M30.343 9.99a14.757 14.757 0 0 1 .046 20.972 18.383 18.383 0 0 1-13.019 5.365A18.382 18.382 0 0 1 3.272 29.79c7.209 5.955 17.945 5.581 24.713-1.118a11.477 11.477 0 0 0 0-16.345c-4.56-4.514-11.953-4.514-16.513 0a4.918 4.918 0 0 0 0 7.006 5.04 5.04 0 0 0 7.077 0 1.68 1.68 0 0 1 2.359 0 1.639 1.639 0 0 1 0 2.333 8.4 8.4 0 0 1-11.794 0 8.198 8.198 0 0 1 0-11.674c5.861-5.805 15.366-5.805 21.229 0ZM17.37 0a18.38 18.38 0 0 1 14.097 6.538C24.257.583 13.52.958 6.756 7.653v.002a11.477 11.477 0 0 0 0 16.346c4.558 4.515 11.95 4.515 16.51 0a4.918 4.918 0 0 0 0-7.005 5.04 5.04 0 0 0-7.077 0 1.68 1.68 0 0 1-2.358 0 1.639 1.639 0 0 1 0-2.334 8.4 8.4 0 0 1 11.794 0 8.198 8.198 0 0 1 0 11.674c-5.862 5.805-15.367 5.805-21.23 0a14.756 14.756 0 0 1-.02-20.994A18.383 18.383 0 0 1 17.37 0Z" fill="#5B676E"></path></g></svg>',
                'url' => '/'.trim(config('nova.path'), '/'),
            ],
            // 'spark',
            \App\Providers\TelescopeServiceProvider::class => [
                'name' => 'telescope',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" fill="#5B676E"><path class="fill-primary" d="M0 40a39.87 39.87 0 0 1 11.72-28.28A40 40 0 1 1 0 40zm34 10a4 4 0 0 1-4-4v-2a2 2 0 1 0-4 0v2a4 4 0 0 1-4 4h-2a2 2 0 1 0 0 4h2a4 4 0 0 1 4 4v2a2 2 0 1 0 4 0v-2a4 4 0 0 1 4-4h2a2 2 0 1 0 0-4h-2zm24-24a6 6 0 0 1-6-6v-3a3 3 0 0 0-6 0v3a6 6 0 0 1-6 6h-3a3 3 0 0 0 0 6h3a6 6 0 0 1 6 6v3a3 3 0 0 0 6 0v-3a6 6 0 0 1 6-6h3a3 3 0 0 0 0-6h-3zm-4 36a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM21 28a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path></svg>',
                'url' => '/'.trim(config('telescope.path'), '/'),
            ],
        ];

        parent::__construct($app);
    }

    public function boot()
    {
        parent::boot();
        $this->bootNavigation();
        $this->bootPermissions();
    }

    protected function bootNavigation(): void
    {
        Nav::extend(function ($nav) {
            foreach ($this->laravelPackageProviders() as $provider => $value) {
                if ($this->providerExists($provider) && $this->userHasPermission($value['name'])) {
                    if ($value['url'] !== '/') {
                        $nav->create(Str::ucfirst($value['name']))
                            ->icon($value['icon'])
                            ->section('Laravel')
                            ->url($value['url']);
                    }
                }
            }
        });
    }

    protected function bootPermissions(): void
    {
        Permission::group('laravel', 'Laravel', function () {
            foreach ($this->laravelPackageProviders() as $provider => $value) {
                if ($this->providerExists($provider)) {
                    $packageUcFirst = Str::ucfirst($value['name']);
                    Permission::register("access laravel {$value['name']}", function ($permission) use ($packageUcFirst) {
                        return $permission
                            ->label($packageUcFirst)
                            ->description("Grants access to {$packageUcFirst}");
                    });
                }
            }
        });
    }

    protected function laravelPackageProviders(): array
    {
        return $this->laravelPackageProviders;
    }

    protected function providerExists(string $provider): bool
    {
        return class_exists($provider);
    }

    protected function userHasPermission(string $permission): bool
    {
        return Auth::guest() ? false : Auth::user()->can("access laravel {$permission}");
    }
}
