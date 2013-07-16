<?php namespace Monstergfx\Menubuilder;

use Illuminate\Support\ServiceProvider;
use Monstergfx\Menubuilder\Commands;

class MenubuilderServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('monstergfx/menubuilder');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['menubuilder'] = $this->app->share(function($app)
		{
			return new Menubuilder;
		});

		$this->app['commands.menubuilder.make'] = $this->app->share(function($app)
		{
			return new Commands\MenubuilderCommand();
		});
		$this->commands('commands.menubuilder.make');

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}