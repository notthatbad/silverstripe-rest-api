<?php

/**
 *
 */
class SimpleAuth  implements IAuth {


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
            $session->Token = self::generateToken();

            return $session;
        }
    }

    /**
     * @param $request
     * @return mixed
     * @throws RestUserException
     */
    public static function delete($request) {
        if($id = $request->param('ID')) {
            $user = Member::currentUser();
            if(!$user || $id != 'me') {
                throw new RestUserException("No session found", 404);
            }
            $user->logOut();
        } else {
            throw new RestUserException("No id specified for deletion", 404);
        }
    }


    public static function current($request) {
        return Member::currentUser();
    }

    private static function generateToken() {
        return "token1";
    }
}