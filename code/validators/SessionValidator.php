<?php

namespace Ntb\RestAPI;
use Config;

/**
 * Class SessionValidator
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class SessionValidator implements IRestValidator {
    const MinPasswordLength = 3;

    public static function validate($data) {
        $emailName = Config::inst()->get('SessionValidatorWithSocial', 'email_name');
        $passwordName = Config::inst()->get('SessionValidatorWithSocial', 'password_name');
        $mail = RestValidatorHelper::validate_email($data, $emailName);
        $password = RestValidatorHelper::validate_string($data, $passwordName, ['min' => self::MinPasswordLength]);
        return [
            'Email' => $mail,
            'Password' => $password
        ];
    }
}
