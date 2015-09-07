<?php

/**
 * Interface IAuthentication
 */
interface IAuthentication {

    /**
     * Returns the current member, which has performed the request.
     *
     *
     * @return Member the current member or null
     */
    public static function current();

    /**
     * Returns the identifier for the member in the request.
     *
     * @return string the identifier or null
     */
    public static function identifier();
}