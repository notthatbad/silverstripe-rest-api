<?php

/**
 * Authentication mechanism which uses the internal Silverstripe authentication (session based).
 */
class SessionAuth extends Object implements IAuth {

    public static function authenticate($email, $password) {
        // auth
        $authenticator = new \MemberAuthenticator();
        if($user = $authenticator->authenticate(['Password' => $password, 'Email' => $email])) {
            $user->logIn();
            $user = DataObject::get(Config::inst()->get('BaseRestController', 'Owner'))->byID($user->ID);
            // create session
            $session = ApiSession::create();
            $session->User = $user;
            $session->Token = AuthFactory::generate_token($user);

            return $session;
        }
    }

    public static function delete($request) {
        $owner = self::current($request);
        if(!$owner) {
            throw new RestUserException("No session found", 404);
        }
        $owner->logOut();
        return true;
    }


    /**
     * @param SS_HTTPRequest $request
     * @return Member
     */
    public static function current($request) {
        $id = Member::currentUserID();
        return DataObject::get(Config::inst()->get('BaseRestController', 'Owner'))->byID($id);
    }
}