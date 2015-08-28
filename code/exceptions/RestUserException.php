<?php

/**
 * The user exception can be used for indicating the wrong usage of the rest api.
 */
class RestUserException extends Exception {

    public function __construct($message, $errorCode) {
        parent::__construct($message, $errorCode);
    }
}