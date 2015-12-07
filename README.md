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

If you visit your api through a browser, the controller will render it as html as long as you not specified an `accept`
GET param, eg. `?accept=json`.


## Testing

For functional tests, you can extend the `RestTest` class and use it to test your application. We recommend to use 
fixtures for testing like it is explained in the Silverstripe documentation.
