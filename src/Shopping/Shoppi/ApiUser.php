<?php

namespace Shopping\Shoppi;
//use Shopping\Encryption\AesCtr;
//use Shopping\Encryption\Aes;
use Exception;
use Illuminate\Support\Facades\Config;

class ApiUser
{
    /**
     * The base url for the API
     */
    protected $baseUrl;    
    
    /**
     * Array of Exceptions that have occurred in this class
     * 
     * @var Array
     */
    private $errors = array();
    
    /**
     * The auth token for this class
     * 
     * @var string
     */
    protected $token;
    
    /**
     * Specifies if this class has been authenticated
     * 
     * @var bool
     */
    protected $isAuthenticated;
    
    public function __construct($baseUrl = '')
    {
        if($baseUrl === '')
        {
            $baseUrl = Config::get('shoppi::config.API_BASE_URI');
        }
        $this->baseUrl = $baseUrl;
    }
    
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
    
    public function isAuthenticated()
    {
        return $this->isAuthenticated;
    }
    
    /**
     * Authenticates a user to the API
     * @param  string $email    The user's email
     * @param  string $password The user's password
     * @return string The user's token
     */
    public function authenticate($email, $password)
    {
        $data = array(
            'email' => $email,
            'password' => $password
        );
        $result = $this->httpRequest($this->baseUrl.'authenticate/basic', 'POST', $data);
        $auth = json_decode($result, TRUE);
        if(isset($auth['authentication_token']))
        {
            $this->token = $auth['authentication_token'];
            $this->isAuthenticated = true;
            return $this->token;
        }
        else
        {
            $this->isAuthenticated = false;
            return null;
        }
    }
    
    /**
     * Gets the ApiUser's token
     * @return string User token for this session
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * Make a http request using this class
     * Once a user is authenticated, you can use this class to make authenticated requests
     * @param  string  $url            The url to make a request to
     * @param  string  [$method        = 'GET']         The method to use in making this request. Default is 'GET'
     * @param  mixed   [$postdata      = NULL]          The post data to use if any. This should have been built with http_build_query
     * @param  boolean [$authenticated = false]         True if this should be an authenticated request
     * @return string  Returns a string representation of the request's response
     */
    public function httpRequest($url, $method = 'GET', $postdata = NULL, $authenticated = false)
    {
        if(is_array($postdata))
        {
            $postdata = http_build_query($postdata);
        }
        if($authenticated && $postdata)
        {
            $opts = array(
                'http' => array( 
                    'request_fulluri'=>true, 
                    'header' => array(
                        "Authorization: {$this->token}",
                        'Content-type: application/x-www-form-urlencoded',
                    ),
                    'method' => $method, 
                    'content' => $postdata
                )
            );
            $context = stream_context_create($opts);
            try
            {
                return file_get_contents($url, false, $context);
            }
            catch(Exception $e)
            {
                array_push($this->errors, $e);
                return $e;
            }
        }
        elseif($postdata)
        {
            $opts = array(
                'http' => array( 
                    'request_fulluri'=>true, 
                    'method' => $method,
                    'header' => array(
                        'Content-type: application/x-www-form-urlencoded',
                    ),
                    'content' => $postdata
                )
            );
            $context = stream_context_create($opts);
            try
            {
                return file_get_contents($url, false, $context);
            }
            catch(Exception $e)
            {
                array_push($this->errors, $e);
                return $e;
            }
        }
        elseif($authenticated)
        {
            $opts = array(
                'http' => array( 
                    'request_fulluri'=>true, 
                    'method' => $method, 
                    'header' => array(
                        "Authorization: {$this->token}",
                    ),
                )
            );
            $context = stream_context_create($opts);
            try
            {
                return file_get_contents($url, false, $context);            
            }
            catch(Exception $e)
            {
                array_push($this->errors, $e);
                return $e;
            }
        }
        else
        {
            try
            {
                return file_get_contents($url);
            }
            catch(Exception $e)
            {
                array_push($this->errors, $e);
                return $e;
            }
        }
    }
    
    /**
     * Returns the errors associated with this class
     * @return Array Array of Exceptions that have occurred in this class
     */
    public function errors()
    {
        return $this->errors;
    }
    
    /**
     * Returns the errors in this class and clears the error internally
     * @return Array Array of Exceptions that have occurred in this class
     */
    public function getErrors()
    {
        $errors = $this->$errors();
        $this->clearErrors();
        return $errors;
    }
    
    /**
     * Clears the errors associated with this class
     */
    public function clearErrors()
    {
        $this->errors = null;
        $this->errors = array();
    }

}

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */