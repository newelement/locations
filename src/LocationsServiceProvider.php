<?php
namespace Newelement\Locations;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

use Newelement\Locations\Facades\Locations as LocationFacade;

class LocationsServiceProvider extends ServiceProvider
{

    public function register()
    {

        $loader = AliasLoader::getInstance();
        $loader->alias('Locations', LocationFacade::class);
        $this->app->singleton('locations', function () {
            return new Locations();
        });

        $this->loadHelpers();
        $this->registerConfigs();

        if ($this->app->runningInConsole()) {
            $this->registerPublishableResources();
            $this->registerConsoleCommands();
        }
    }

    public function boot(Router $router, Dispatcher $event)
    {

        $viewsDirectory = __DIR__.'/../resources/views';
        $publishAssetsDirectory = __DIR__.'/../publishable/assets';

        $this->loadViewsFrom($viewsDirectory, 'locations');

        $this->publishes([$viewsDirectory => base_path('resources/views/vendor/locations')], 'views');
        $this->publishes([ $publishAssetsDirectory => public_path('vendor/newelement/locations') ], 'public');
        $this->loadMigrationsFrom(realpath(__DIR__.'/../migrations'));

        $this->registerNeutrinoItems();

        // Register routes
        // PUBLIC
        $router->group([
            'namespace' => 'Newelement\Locations\Http\Controllers',
            'as' => 'locations.',
            'middleware' => ['web']
        ], function ($router) {
            require __DIR__.'/../routes/web.php';
        });

        // ADMIN
        $router->group([
            'namespace' => 'Newelement\Locations\Http\Controllers\Admin',
            'prefix' => 'admin',
            'as' => 'locations.',
            'middleware' => ['web', 'admin.user']
        ], function ($router) {
            require __DIR__.'/../routes/admin.php';
        });

    }

    /**
     * Register the publishable files.
     */
    private function registerPublishableResources()
    {
        $publishablePath = dirname(__DIR__).'/publishable';

        $publishable = [
            'config' => [
                "{$publishablePath}/config/locations.php" => config_path('locations.php'),
            ],
            'seeds' => [
                "{$publishablePath}/database/seeds/" => database_path('seeds'),
            ],
        ];
        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    public function registerConfigs()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/publishable/config/locations.php', 'locations'
        );
    }

    protected function loadHelpers()
    {
        foreach (glob(__DIR__.'/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Register the commands accessible from the Console.
     */
    private function registerConsoleCommands()
    {
        $this->commands(Commands\InstallCommand::class);
    }

    private registerNeutrinoItems()
    {
        $bond = app('NeutrinoBond');

        $menuItems = [
            [
            'slot' => 4,
            'url' => '/admin/locations',
            'parent_title' => 'Locations',
            'named_route' => 'neutrino.locations',
            'fa-icon' => 'fa-map-marked',
            'children' => [
                [ 'url' => '/admin/locations', 'title' => 'All Locations' ],
                [ 'url' => '/admin/location', 'title' => 'Create Location' ],
            ],
        ];

        $bond->registerMenuItems($menuItems);

    }

}
