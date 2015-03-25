<?php

namespace Shopping\Shoppi;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Shopping\Shoppi\ApiUser;
use Shopping\Shoppi\Models\ApiUserModel;

/**
 * Main Helper class for Shopp!ng Library
 */
class Shopping
{
    /**
     * Gets the user currently logged in to the API
     * @return \Shopping\Models\ApiUser\Model The ApiUserModel object for the currently logged in user
     */
    public static function user()
    {
        return Session::get('apiUser');
    }
    
    public static function login()
    {
        $authUser = new ApiUser;
        //user email and encrypted password must be flashed to the Session when a user is authenticated
        if(!Session::has('api_email') || !Session::has('api_password'))
        {
            throw new \Exception('User email and password must be flashed to the session to use this library. See sample code for details');
        }
        $email = Session::get('api_email');
        $cpwd = Session::get('api_password');
        return Shopping::authenticate($email, Crypt::decrypt($cpwd));
    }
    
    public static function authenticate($email, $password)
    {
        if($authUser->authenticate($email, $password))
        {
            //set authUser so BaseModels can automatically inherit
            Session::set('authUser', $authUser);
            //create new ApiUserModel from api_user_id and assign to apiUser
            $apiUser = new ApiUserModel;
            $apiUser->findByEmail($email);
            Session::set('apiUser', $apiUser);
            return $apiUser;
        }
        else
        {
            //authentication failed, so flash data again for possible re-authentication
            ret false;
        }        
    }
    
    public static function logout()
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
}