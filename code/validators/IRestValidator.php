<?php

namespace Ntb\RestAPI;

/**
 * Interface for data validators in the rest api.
 * @author Christian Blank <c.blank@notthatbad.net>
 */
interface IRestValidator {
    /**
     * Validates the given data and returns a mapped version back to the caller.
     *
     * @param array $data
     * @return array
     * @throws \ValidationException
     */
    public static function validate($data);
} 