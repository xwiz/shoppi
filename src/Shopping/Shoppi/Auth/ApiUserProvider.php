<?php

namespace Shopping\Shoppi\Auth;

use Illuminate\Auth\UserProviderInterface;
use Illuminate\Auth\UserInterface;
use Illuminate\Support\Facades\Hash;
use Shopping\Shoppi\ApiUser;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class ApiUserProvider implements UserProviderInterface {
	/**
	 * The Eloquent user model.
	 *
	 * @var string
	 */
	protected $model;
    
    /**
     * Specifies if the ApiUser owner of this model is authenticated
     * 
     * @var bool
     */
    protected $isAuthenticated;
    
	/**
	 * Create a new ApiUserProvider.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public function __construct()
	{
		$this->model ='User';
	}

	/**
	 * Retrieve a user by their unique identifier.
	 *
	 * @param  mixed  $identifier
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public function retrieveById($identifier)
	{
		return $this->createModel()->newQuery()->find($identifier);
	}

	/**
	 * Retrieve a user by their unique identifier and "remember me" token.
	 *
	 * @param  mixed  $identifier
	 * @param  string  $token
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public function retrieveByToken($identifier, $token)
	{
		$model = $this->createModel();

		return $model->newQuery()
                        ->where($model->getKeyName(), $identifier)
                        ->where($model->getRememberTokenName(), $token)
                        ->first();
	}

	/**
	 * Update the "remember me" token for the given user in storage.
	 *
	 * @param  \Illuminate\Auth\UserInterface  $user
	 * @param  string  $token
	 * @return void
	 */
	public function updateRememberToken(UserInterface $user, $token)
	{
		$user->setRememberToken($token);

		$user->save();
	}

	/**
	 * Retrieve a user by the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public function retrieveByCredentials(array $credentials)
	{
		// First we will add each credential element to the query as a where clause.
		// Then we can execute the query and, if we found a user, return it in a
		// Eloquent User "model" that will be utilized by the Guard instances.
		$query = $this->createModel()->newQuery();

		foreach ($credentials as $key => $value)
		{
			if ( ! str_contains($key, 'password')) $query->where($key, $value);
		}

		return $query->first();
	}

	/**
	 * Validate a user against the given credentials.
	 *
	 * @param  \Illuminate\Auth\UserInterface  $user
	 * @param  array  $credentials
	 * @return bool
	 */
	public function validateCredentials(UserInterface $user, array $credentials)
	{
		$plain = $credentials['password'];
        //flash $credentials
        Session::flash('api_email', $credentials['email']);
        Session::flash('api_password', Crypt::encrypt($plain));

		return Hash::check($plain, $user->getAuthPassword());
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}
}