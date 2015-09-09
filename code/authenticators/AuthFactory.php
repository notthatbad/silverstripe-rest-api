<?php

/**
 * Factory for different kind of rest authenticators.
 */
class AuthFactory extends Object {

    /**
     * Returns a new instance of an authentication mechanism depending on the configured type.
     * @return IAuth an instance of an authentication mechanism
     * @throws RestSystemException
     */
    public static function createAuth() {
        $availableAuths = ClassInfo::implementorsOf('IAuth');
        $type = Config::inst()->get('AuthFactory', 'AuthType');

        if(in_array($type, $availableAuths)) {
            return $type::create();
        }

        throw new RestSystemException("Configured auth type '$type' not supported", 404);
    }

    /**
     * Generates an encrypted random token.
     * @param Member $user
     * @throws PasswordEncryptor_NotFoundException
     * @return string
     */
    public static function generate_token($user) {
        $generator = new RandomGenerator();
        $tokenString = $generator->randomToken();
        $e = PasswordEncryptor::create_for_algorithm('blowfish');
        $salt = $e->salt($tokenString);
        $token = sha1($e->encrypt($tokenString, $salt)) . substr(md5($user->Created.$user->LastEdited.$user->ID), 7);
        return $token;
    }
}