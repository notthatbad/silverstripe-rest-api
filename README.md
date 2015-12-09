REST module for Silverstripe 
============================

[![Build Status](https://travis-ci.org/notthatbad/silverstripe-rest-api.svg)](https://travis-ci.org/notthatbad/silverstripe-rest-api)

This module models the REST api in a simple fashion.

All routes for the different resources should resolved by a controller which extends the BaseRestController.

After that, you can add your routes into your YAML config file.

## Features

 * Queries
 * Field list
 * Different serialisation formats
 * Pagination
 * Presenters
 
## Usage

Every endpoint has its own controller. The controller should extend `BaseRestController`.

We will look at a simplified version of a user endpoint to give a quick entry for using this module. The endpoint allows
us to create, update, read and delete users (aka members). `/v/1/users`

### Controller

A sample implementation for a controller is already given in code/controllers/SessionController.
The api implementation supports the actions post, delete, get and put. Like every other controller in Silverstripe
you must specify the `allowed_actions` array. For this purpose, we have implemented `isAuthenticated` and `isAdmin` to
check for authenticated users and admins.

Example implementation for the controller of a user endpoint.

```php
<?php

/**
 * User controller is the controller for the member resource.
 */
class UserController extends BaseRestController {

    private static $allowed_actions = array (
        'post' => true,
        'delete' => '->isAuthenticated',
        'get' => true,
        'put' => '->isAuthenticated'
    );

    public function get($request) {
        $users = Member::get();
        $meta = [
            'count' => $users->Count(),
            'timestamp' => time()
        ];
        // check param for id
        if($id = $request->param('ID')) {
            $user = $users->byID($id);
            if(!$user) {
                throw new RestUserException("User with id $id not found", 404);
            }
            $data = [
                'user' => UserFormatter::format($user)
            ];
        } else {
            $limit = self::limit($request);
            $offset = self::offset($request);
            $data = [
                'users' => array_map(function($user) {
                    return UserFormatter::format($user);
                }, $users->limit($limit, $offset)->toArray())
            ];
            $meta['offset'] = $offset;
            $meta['limit'] = $limit;
        }

        $data['meta'] = $meta;
        return $data;
    }

    public function post($request) {
        $data = json_decode($request->getBody(), true);
        if(!$data) {
            throw new RestUserException("No data for user provided.", 404);
        }
        try {
            $validated = UserValidator::validate($data);
            $user = new Member([
                'FirstName' => $validated['FirstName'],
                'Surname' => $validated['Surname'],
                'Email' => $validated['Email']
            ]);
            $user->write();
        } catch(ValidationException $e) {
            throw new RestUserException($e->getMessage(), 4043);
        } catch(Exception $e) {
            SS_Log($e->getMessage(), SS_Log::INFO);
            throw new RestUserException($e->getMessage(), 4044);
        }
        $users = Member::get();
        $meta = ['count' => $users->Count()];
        $result = [
            'user' => UserFormatter::format($user)
        ];
        $result['meta'] = $meta;
        return $result;
    }

    public function put($request) {
        $users = Member::get();
        // check data
        $data = json_decode($request->getBody(), true);
        if(!$data) {
            throw new RestUserException("No data for user provided.", 404);
        }
        // check param for id
        $id = $request->param('ID');
        if(!$id) {
            throw new RestUserException("No id for user provided.", 404);
        }
        // fetch specified user
        $user = $users->byID($id);
        if (!$user) {
            throw new RestUserException("User with id $id not found", 404);
        }
        try {
            //
        } catch(Exception $e) {
            throw new RestUserException($e->getMessage(), 404);
        }
        $meta = [
            'count' => $users->Count(),
            'timestamp' => time()
        ];
        $result = [
            'user' => UserFormatter::format($user)
        ];
        $result['meta'] = $meta;
        return $result;
    }

    /**
     * @param SS_HTTPRequest $request
     * @return array
     * @throws RestUserException
     */
    public function delete($request) {
        $users = Member::get();
        // check param for id
        if($id = $request->param('ID')) {
            $user = $users->byURL($id);
            if(!$user) {
                throw new RestUserException("User with id $id not found", 404);
            }
            $data = [
                'user' => UserFormatter::format($user)
            ];
            $user->delete();
        } else {
            throw new RestUserException("No id specified for deletion", 4041);
        }
        $meta = ['count' => $users->Count(), 'timestamp' => time()];
        $data['meta'] = $meta;
        return $data;
    }
}
```

As you can see above, the actions `get` and  `post` are accessible by everyone, `put` and `delete` need a authenticated
user to perform.

### Formatter

The formatters can format a data object into a representation which is consumable from a IRestSerializer. 
The class also works as a presenter, because it changes the view from the internal to the external format of the api
data.

```php
<?php

class UserFormatter implements IRestSerializeFormatter {

    public static function format($data, $access=null, $fields=null) {
        $user = [
            'id' => $data->ID,
            'firstName' => $data->firstName,
            'surname' => $data->Surname,
            'email' => $data->Email
        ];
        return $user;
    }
}
```

### Routes

For the definition of your routes, use the Silverstripe config system. You can add nested routes before the base route
of that endpoint. In this example we have `v/1/sessions`, `v/1/users/<ID>` and `v/1/users/<ID>/friends`.

```yml
Director:
  rules:
    'v/1/users/$ID!/friends/$FriendID': 'FriendshipController'
    'v/1/users/$ID': 'UserController'
    'v/1/sessions/$ID': 'SessionController'
```

### Authenticators

The module comes with three different authenticators:

 * `SessionAuth`: based on the Silverstripe session mechanism
 * `TokenAuth`: token based with storing valid tokens in a cache
 * `JwtAuth`: token based authenticator without any state on the server

### GET Parameters

If you visit your api through a browser, the controller will render it as html as long as you not specify an `accept`
GET param, eg. `?accept=json`.

You can specify the access token with `access_token`. This can be used for accessing restricted resources with specific
privileges.

## Testing

For functional tests, you can extend the `RestTest` class and use it to test your application. We recommend to use 
fixtures for testing like it is explained in the Silverstripe documentation.

