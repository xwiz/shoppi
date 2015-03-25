#Shopping User Authentication Library
Laravel package to help with consuming the Shopp!ng API

## Laravel 4.2 and Above

You may install this package by either of the following ways:

 1. Through Composer -- edit your project's composer.json file to require shopping/shoppi.
    "require-dev": {
        "shopping/shoppi": "dev-master"
    }
  Next, update Composer by running: `composer update --dev`
  
 2. Directly through command line, -- run `composer require shopping/shoppi:dev-master`

Once the operation completes, the final step is to add the service provider. Open app/config/app.php, and add a new item to the providers array.

'Shopping\Shoppi\ShoppiServiceProvider'

To optionally make your experience easier, you may also add `Shopping\Shoppi\Shopping` to the Facades array list.

That's all! You are now set to authenticate to the Shopp!ng API.

## Basic Authentication Example

To authenticate a user, it is required that you supply a username and password that the user will be authenticated with.
This may be flashed to the Session with the password encrypted internally using Laravel Bcrypt class or sent directly to the `Shopping::authenticate` method.

E.g. to login a user

  if(Shopping::authenticate($email, $password))
  {
    dd(Session::get('apiUser');
  }
  
The above code should dump the user's detail as retrieved from the API.
