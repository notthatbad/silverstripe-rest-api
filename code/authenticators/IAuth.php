<?php

/**
 *
 */
interface IAuth {

    public static function authenticate($email, $password);

    /**
     * @param $request
     * @return mixed
     */
    public static function delete($request);

    public static function current($request);
}