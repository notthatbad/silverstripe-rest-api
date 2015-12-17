<?php

/**
 * Class SessionValidator
 */
class SessionValidator implements IRestValidator
{
    const MinPasswordLength = 3;

    public static function validate($data)
    {
        $mail = RestValidatorHelper::validate_email($data, 'email');
        $password = RestValidatorHelper::validate_string($data, 'password', ['min' => self::MinPasswordLength]);

        return [
            'Email' => $mail,
            'Password' => $password
        ];
    }
}
