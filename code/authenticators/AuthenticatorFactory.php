<?php

/**
 * Factory for different kind of rest authenticators.
 */
class AuthenticatorFactory extends Object {

    /**
     * Returns a new instance of an authenticator depending on the given type.
     *
     * @param string $type
     * @return IAuthentication an instance of a serializer
     * @throws RestUserException
     */
    public static function create($type) {
        $availableAuthenticators = ClassInfo::implementorsOf('IAuthentication');
        foreach($availableAuthenticators as $authenticator) {
            $instance = $authenticator::create();
            if($instance->class == $type) {
                return $instance;
            }
        }
        throw new RestUserException("Requested Accept '$type' not supported", 404);
    }
}