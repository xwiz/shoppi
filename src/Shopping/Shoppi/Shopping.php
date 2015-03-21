<?php

namespace Shopping\Shoppi;

use Illuminate\Support\Facades\Session;

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
}