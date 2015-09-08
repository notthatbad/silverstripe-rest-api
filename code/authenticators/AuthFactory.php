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
}