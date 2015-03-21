<?php namespace Shopping\Shoppi\Events;
 
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Shopping\Shoppi\ApiUser;
use Shopping\Shoppi\Models\ApiUserModel;

class AuthEventSubscriber {
    
    /**
     * When a user is logged in
     * 
     */
    public function onLogin($user)
    {
        $authUser = new ApiUser;
        //user email and password are flashed to the Session when a user is authenticated
        $email = Session::get('api_email');
        $cpwd = Session::get('api_password');
        if($authUser->authenticate($email, Crypt::decrypt($cpwd)))
        {
            //set authUser so BaseModels can automatically inherit
            Session::set('authUser', $authUser);
            //create new ApiUserModel from api_user_id and assign to apiUser
            $apiUser = new ApiUserModel;
            $apiUser->find($user->api_user_id);
            Session::set('apiUser', $apiUser);
        }
        else
        {
            //authentication failed, so flash data again for possible re-authentication
            Session::flash('api_email', $email);
            Session::flash('api_password', $cpwd);
        }
    }

    /**
     * When a user is logged out
     */
    public function onLogout($user)
    {
        //delete authUser and apiUser form session
        if(Session::has('authUser'))
        {
            Session::forget('authUser');
        }
        if(Session::has('apiUser'))
        {
            Session::forget('apiUser');
        }
    }
    
    /**
     * Register the listeners for the subscriber.
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen('auth.login', 'Shopping\Shoppi\Events\AuthEventSubscriber@onLogin');
        $events->listen('auth.logout', 'Shopping\Shoppi\Events\AuthEventSubscriber@onLogout');
    }
}
