<?php

/**
 * Authentication mechanism which uses the internal Silverstripe authentication (session based).
 */
class SimpleAuth extends Object implements IAuth {

    public static function authenticate($email, $password) {
        // auth
        $authenticator = new \MemberAuthenticator();
        if($user = $authenticator->authenticate(['Password' => $password, 'Email' => $email])) {
            $user->logIn();
            // create session
            $session = \Ntb\Session::create();
            $session->User = $user;
            $session->Token = AuthFactory::generate_token($user);

            return $session;
        }
    }

    public static function delete($request) {
        $user = Member::currentUser();
        if(!$user) {
            throw new RestUserException("No session found", 404);
        }
        $user->logOut();
        return true;
    }


    public static function current($request) {
        return Member::currentUser();
    }
}