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
    
    /**
     * Attempts to login the user using the credentials stored in session
     * @return mixed The ApiUserModel or false on authentication failure
     */
    public static function login()
    {
        $authUser = new ApiUser;
        //user email and encrypted password must be flashed to the Session when a user is authenticated
        if(!Session::has('api_email') || !Session::has('api_password'))
        {
            throw new \Exception('User email and password must be flashed to the session to use this method. See sample code for details');
        }
        $email = Session::get('api_email');
        $cpwd = Session::get('api_password');
        return Shopping::authenticate($email, Crypt::decrypt($cpwd));
    }
    
    /**
     * Attempts to directly authenticate a user to the API
     * @param  mixed The credentials array or user email for authentication
     * @param  string $password The password for authentication
     * @return mixed The ApiUserModel or false on authentication failure
     */
    public static function authenticate($mixed, $password)
    {
        $authUser = new ApiUser;
        if(is_array($mixed))
        {
            $email = $mixed['email'];
            $password = $mixed['password'];
        }
        else
        {
            $email = $mixed;
        }
        if($authUser->authenticate($email, $password))
        {
            //set authUser so BaseModels can automatically inherit
            Session::set('authUser', $authUser);
            //create new ApiUserModel from api_user_id and assign to apiUser
            $apiUser = new ApiUserModel;
            $apiUser = $apiUser->findByEmail($email);
            $user = new ApiUserModel((array)$apiUser);
            Session::set('apiUser', $user);
            return $user;
        }
        else
        {
            return false;
        }        
    }
    
    /**
     * Make a http request using the APiUser class
     * Once a user is authenticated, you can use this class to make authenticated requests
     * @param  string  $path            The path (without the base url) to make a request to
     * @param  string  [$method        = 'GET']         The method to use in making this request. Default is 'GET'
     * @param  mixed   [$postdata      = NULL]          The post data to use if any. This should have been built with http_build_query
     * @param  boolean [$authenticated = false]         True if this should be an authenticated request
     * @return string  Returns a string representation of the request's response
     */
    public static function request($path, $method = 'GET', $postdata = NULL, $authenticated = false)
    {
        $apiUser = Session::get('apiUser');
        if($apiUser)
        {
            return $apiUser->request($path, $method, $postdata, true);
        }
        else
        {
            $apiUser = new ApiUserModel;
            return $apiUser->request($path, $method, $postdata, false);
        }
    }
    
    /**
     * Make a http request using the APiUser class
     * Once a user is authenticated, you can use this class to make authenticated requests
     * @param  string  $path            The path (without the base url) to make a request to
     * @param  string  [$method        = 'GET']         The method to use in making this request. Default is 'GET'
     * @param  mixed   [$postdata      = NULL]          The post data to use if any. This should have been built with http_build_query
     * @param  boolean [$authenticated = false]         True if this should be an authenticated request
     * @return string  Returns a json representation of the request's response
     */
    public static function jsonRequest($path, $method = 'GET', $postdata = NULL, $authenticated = false)
    {
        $apiUser = Session::get('apiUser');
        if($apiUser)
        {
            return $apiUser->jsonRequest($path, $method, $postdata, true);
        }
        else
        {
            $apiUser = new ApiUserModel;
            return $apiUser->jsonRequest($path, $method, $postdata, false);
        }
    }
    
    /**
     * Removes the current authenticated user from session if any
     */
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