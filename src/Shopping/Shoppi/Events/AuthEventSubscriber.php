<?php namespace Shopping\Shoppi\Events;
 
use Shopping\Shoppi\Shopping;
use Illuminate\Support\Facades\Config:

class AuthEventSubscriber {
    
    /**
     * When a user is logged in
     * 
     */
    public function onLogin($user)
    {
        Shopping::login();
    }

    /**
     * When a user is logged out
     */
    public function onLogout($user)
    {
        Shopping::logout();
    }
    
    /**
     * Register the listeners for the subscriber.
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        //only subscribe to auth events if the Shopping Auth Driver is used.
        if(Config::get('auth.driver') === 'shopping_auth')
        {
            $events->listen('auth.login', 'Shopping\Shoppi\Events\AuthEventSubscriber@onLogin');
            $events->listen('auth.logout', 'Shopping\Shoppi\Events\AuthEventSubscriber@onLogout');
        }
    }
}
