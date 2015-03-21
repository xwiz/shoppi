<?php

namespace Shopping\Shoppi\Models;

class ApiUserModel extends BaseModel
{
    /**
     * You obvioulsy don't want to authenticate user creation functions
     */
    protected $createAuth = FALSE;
    /**
     * The api endpoint url to use while creating this class/model data
     * 
     * @var string
     */
    protected $createUrl = 'users';
    /**
     * The api endpoint url to use while retrieving a class/model data from the api
     * 
     * @var string
     */
    protected $getUrl = 'users/%d';
    /**
     * The api endpoint url to use while running updates on this class/model data
     * 
     * @var string
     */
    protected $updateUrl = 'users/%d';
    /**
     * The api endpoint url to use while deleting this class/model data from the api
     * 
     * @var string
     */
    protected $deleteUrl = 'users/%d';
    
}