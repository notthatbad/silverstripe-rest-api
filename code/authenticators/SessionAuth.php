<?php

namespace Ntb\RestAPI;

/**
 * Authentication mechanism which uses the internal Silverstripe authentication (session based).
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class SessionAuth extends \SS_Object implements IAuth {

    public static function authenticate($email, $password) {
        // auth
        $authenticator = \Injector::inst()->get('ApiMemberAuthenticator');
        if($user = $authenticator->authenticate(['Password' => $password, 'Email' => $email])) {
	        return self::createSession($user);
        }
    }

	/**
	 * @param \Member $user
	 * @return ApiSession
	 */
	public static function createSession($user) {
		$user->logIn();
		/** @var \Member $user */
		$user = \DataObject::get(\Config::inst()->get('BaseRestController', 'Owner'))->byID($user->ID);
		// create session
		$session = ApiSession::create();
		$session->User = $user;
		$session->Token = AuthFactory::generate_token($user);
		return $session;
	}

    /**
     * @param \SS_HTTPRequest $request
     * @return bool
     * @throws RestUserException
     */
	public static function delete($request) {
        $owner = self::current($request);
        if(!$owner) {
            throw new RestUserException("No session found", 404, 404);
        }
        $owner->logOut();
        return true;
    }

    /**
     * @param \SS_HTTPRequest $request
     * @return \Member
     */
    public static function current($request) {
        $id = \Member::currentUserID();
        return $id ? \DataObject::get(\Config::inst()->get('BaseRestController', 'Owner'))->byID($id) : null;
    }
}
