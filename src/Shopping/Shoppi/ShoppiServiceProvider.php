<?php namespace Shopping\Shoppi;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Shopping\Shoppi\Events\AuthEventSubscriber;

class ShoppiServiceProvider extends ServiceProvider {

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
		$this->package('shopping/shoppi');
        Auth::extend('shopping_auth', function($app) {
            $provider =  new \Shopping\Shoppi\Auth\ApiUserProvider();
            return new \Illuminate\Auth\Guard($provider, $app['session.store']);
        });
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app->events->subscribe(new AuthEventSubscriber);
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
