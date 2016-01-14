REST module for Silverstripe 
============================

[![Build Status](https://travis-ci.org/notthatbad/silverstripe-rest-api.svg)](https://travis-ci.org/notthatbad/silverstripe-rest-api)
[![Latest Stable Version](https://poser.pugx.org/ntb/silverstripe-rest-api/v/stable)](https://packagist.org/packages/ntb/silverstripe-rest-api)
[![License](https://poser.pugx.org/ntb/silverstripe-rest-api/license)](https://packagist.org/packages/ntb/silverstripe-rest-api)

This module models the REST api in a simple fashion.

All routes for the different resources should resolved by a controller which extends the BaseRestController.

After that, you can add your routes into your YAML config file.

## Features

 * Queries
 * Field list
 * Different serialisation formats
 * Pagination
 * Presenters
 
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

### GET Parameters

If you visit your api through a browser, the controller will render it as html as long as you not specify an `accept`
GET param, eg. `?accept=json`.

You can specify the access token with `access_token`. This can be used for accessing restricted resources with specific
privileges.

## Testing

For functional tests, you can extend the `RestTest` class and use it to test your application. We recommend to use 
fixtures for testing like it is explained in the Silverstripe documentation.

