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
        return Injector::inst()->get('Authenticator');
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

    /**
     * Returns the token from the request.
     *
     * Silverstripe doesn't include Authorization header in its requests. We should check it, because we can use the
     * mechanism in the tests.
     * @param SS_HTTPRequest $request
     * @return String the token
     * @throws Exception
     */
    public static function get_token($request) {
        // try to get the token from request object
        $tokenStrFromHeader = $request->getHeader('Authorization');
        $tokenStrFromVar = $request->requestVar('access_token');
        if (!empty($tokenStrFromHeader))  {
            // string must have format: type token
            return explode(' ', $tokenStrFromHeader)[1];
        } else if(!empty($tokenStrFromVar)) {
            // try variables
            return $tokenStrFromVar;
        } else if(function_exists('getallheaders')) {
            // get all headers from apache server
            $headers = getallheaders();
            if(isset($headers['Authorization'])) {
                return explode(' ', $headers['Authorization'])[1];
            }
        }
        throw new Exception("Token can't be read or was not specified");
    }
}