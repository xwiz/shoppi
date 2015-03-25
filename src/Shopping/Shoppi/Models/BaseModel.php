<?php

namespace Shopping\Shoppi\Models;

use \Illuminate\Support\Facades\Session;
use Shopping\Shoppi\ApiUser;

class BaseModel {
    
    /**
     * ApiUser $authUser
     * 
     * @var ApiUser
     */
    public $authUser;
    
    /**
     * Specifies if creation of models on the API should be authenticated
     * 
     * @var boolean
     */
    protected $createAuth;    
    /**
     * Private variable to save any properties this class is initialized with
     * 
     * @var Array
     */
    private $_data;
    
    /**
     * The base url for the API
     */
    protected $baseUrl;    
    /**
     * The api endpoint url to use while creating this class/model data
     * 
     * @var string
     */
    protected $createUrl;
    /**
     * The api endpoint url to use while retrieving a class/model data from the api
     * 
     * @var string
     */
    protected $getUrl;
    /**
     * The api endpoint url to use while running updates on this class/model data
     * 
     * @var string
     */
    protected $updateUrl;    
    /**
     * The api endpoint url to use while deleting this class/model data from the api
     * 
     * @var string
     */
    protected $deleteUrl;

    /**
     * Default constructor for the Base model
     * @param Array $properties Class properties to initialize if any
     * @param boolean $create True to attempt creating this resource on API with initialization data
     */
    public function __construct(Array $properties = array(), $create = false)
    {
        if(Session::has('authUser'))
        {
            $this->authUser = Session::get('authUser');
        }
        else
        {
            $this->authUser = new ApiUser();
        }
        $this->baseUrl = $this->authUser->getBaseUrl();
        if(is_array($properties))
        {
            $this->_data = $properties;
        }
        if(!empty($this->_data) && $create === true)
        {
            $this->create();
        }
    }
    
    /**
     * Store class/model data on API
     * @param array  $data   Array of data to send to the api for creation
     * @param string $method The http method to use while storing data.
     */
    public function create($data = false, $method = 'POST')
    {
        $result = $this->authUser->httpRequest($this->baseUrl . $this->createUrl, $method, $data === false ? $this->_data : $data, $this->createAuth);
        $data = json_decode($result);
        if($data != null)
        {
            $this->_data['id'] = $data->id;
        }
        return $this;
    }

    // magic set method
    public function __set($property, $value)
    {
        return $this->_data[$property] = $value;
    }
    
    // magic get method
    public function __get($property)
    {
        return array_key_exists($property, $this->_data)
            ? $this->_data[$property]
            : null;
    }
    
    /**
     * Get class/model data from API using specified ID
     * @param int    $id     The ID of the model to retrieve
     * @param string $method The method to use in retrieving this class/model
     */
    public function find($id, $method = 'GET')
    {
        $url = sprintf($this->baseUrl . $this->getUrl, $id);
        $result = $this->authUser->httpRequest($url, $method, null, TRUE);
        $data = json_decode($result);
        if($data != null)
        {
            $this->_data = (array) $data;
        }
        return $this;
    }
    
    /**
     * Update class/model data on API
     * @param integer $id The id of the resource to update
     * @param Array $data The data to use while updating this resource
     * @param string $method  The http method to use while updating data.
     */
    public function update($id, $data = false, $method = 'PUT')
    {
        $url = sprintf($this->baseUrl . $this->updateUrl, $id);
        $result = $this->authUser->httpRequest($url, $method, $data === false ? $this->_data : $data, TRUE);
        $data = json_decode($result);
        if($data != null)
        {
            $this->_data = (array) $data;
        }
        return $this;
    }
    
    /**
     * Delete class/model data from API
     * @param integer $id The id of the resource to delete
     * @param string $method  The http method to use while deleting data.
     */
    public function delete($id, $method = 'DELETE')
    {
        $url = sprintf($this->baseUrl . $this->deleteUrl, $id);
        $result = $this->authUser->httpRequest($url, $method, null, TRUE);
        if($result == '')
        {
            $this->_data = null;
        }
        return $result;
    }
    
    /**
     * Make a http request using the APiUser class
     * Once a user is authenticated, you can use this class to make authenticated requests
     * @param  string  $path            The path to make a request to
     * @param  string  [$method        = 'GET']         The method to use in making this request. Default is 'GET'
     * @param  mixed   [$postdata      = NULL]          The post data to use if any. This should have been built with http_build_query
     * @param  boolean [$authenticated = false]         True if this should be an authenticated request
     * @return string  Returns a string representation of the request's response
     */
    public function request($path, $method = 'GET', $postdata = NULL, $authenticated = false)
    {
        return $this->authUser->httpRequest($this->baseUrl . $path, $method, $postdata, $authenticated);
    }    

    /**
     * Make a http request using the APiUser class
     * Once a user is authenticated, you can use this class to make authenticated requests
     * @param  string  $path            The path to make a request to
     * @param  string  [$method        = 'GET']         The method to use in making this request. Default is 'GET'
     * @param  mixed   [$postdata      = NULL]          The post data to use if any. This should have been built with http_build_query
     * @param  boolean [$authenticated = false]         True if this should be an authenticated request
     * @return string  Returns a JSON decoded form of the request response
     */
    public function jsonRequest($path, $method = 'GET', $postdata = NULL, $authenticated = false)
    {
        $result = $this->authUser->httpRequest($this->baseUrl . $path, $method, $postdata, $authenticated);
        return json_decode($result);
    }
    
    /**
     * Returns the errors associated with this class
     * @return Array Array of Exceptions that have occurred in this class
     */
    public function errors()
    {
        return $this->authUser->errors();
    }

}
