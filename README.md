REST module
===========

This module models the REST api of our system.

It depends on modules for users, opinions and topics.

All routes for the different resources should resolved by a controller which extends the BaseRestController.

## Routes

 * GET /users
 * GET /users/$id
 * POST /users
 * PUT /users/$id
 * DELETE /users/$id
 
 * GET /topics
 * GET /topics/$id
 * POST /topics
 * PUT /topics/$id 
 * DELETE /topics/$id
 
 * GET /votes
 * GET /votes/$id
 * POST /votes
 * PUT /votes/$id 
 * DELETE /votes/$id
 
 * GET /voting
 * GET /voting/$id
 * POST /voting
 * PUT /voting/$id 
 * DELETE /voting/$id
 
 * GET /messages
 * GET /messages/$id
 * POST /messages
 * PUT /messages/$id 
 * DELETE /messages/$id 
 
 * GET /suggestions/$type
 
## Definitions

**Resource:**

Resources are the basic definitions of the service. A user can work with them in a CRUD like fashion, where the http
method with which the resource is accessed (the endpoint), defines the operation on the resource.

**Namespace:**

The namespace is the part between the domain and the resource and should encode the version of the api. The current
chosen namespace is `v/1`.

**Endpoint:**



## Features

 * Queries
 * Field list
 * Different serialisation formats
 * Pagination
 * Presenters