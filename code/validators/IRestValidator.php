<?php

/**
 * Interface for data validators in the rest api.
 */
interface IRestValidator
{
    /**
     * Validates the given data and returns a mapped version back to the caller.
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public static function validate($data);
}
