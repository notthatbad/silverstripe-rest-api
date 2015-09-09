<?php

/**
 *
 */
class SimpleAuth extends Object implements IAuth {


    /**
     * @param string $email
     * @param string $password
     * @return \Ntb\Session
     */
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

    /**
     * @param $request
     * @return mixed
     * @throws RestUserException
     */
    public static function delete($request) {
        $user = Member::currentUser();
        if(!$user) {
            throw new RestUserException("No session found", 404);
        }
        $user->logOut();
    }


    public static function current($request) {
        return Member::currentUser();
    }
}