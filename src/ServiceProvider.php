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
                'url' =>  '/'.trim(config('horizon.path'), '/'),
            ],
            \App\Providers\NovaServiceProvider::class => [
                'name' => 'nova',
                'url' =>  '/'.trim(config('nova.path'), '/'),
            ],
            // 'spark',
            \App\Providers\TelescopeServiceProvider::class => [
                'name' => 'telescope',
                'url' =>  '/'.trim(config('telescope.path'), '/'),
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
            $children = [];

            foreach ($this->laravelPackageProviders() as $provider => $value) {
                if ($this->providerExists($provider) && $this->userHasPermission($value['name'])) {
                    if ($value['url'] !== '/') {
                        $children[] = $nav->item(Str::ucfirst($value['name']))->url($value['url']);
                    }
                }
            }

            $nav->create('Laravel')
                ->icon('charts')
                ->section('Tools')
                ->children($children);
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
