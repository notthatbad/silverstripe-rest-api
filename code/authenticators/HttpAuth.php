<?php

/**
 * Authentication mechanism using a BasicAuth request.
 *
 * @author Andre Lohmann <lohmann.andre@gmail.com>
 */
class HttpAuth extends Object implements IAuth {

    public static function authenticate($email, $password) {
        $authenticator = Injector::inst()->get('ApiMemberAuthenticator');
        if($user = $authenticator->authenticate(['Password' => $password, 'Email' => $email])) {
	        return self::createSession($user);
        }
    }

	/**
	 * @param Member $user
	 * @return ApiSession
	 */
	public static function createSession($user) {
		$user->logIn();
		/** @var Member $user */
		$user = DataObject::get(Config::inst()->get('BaseRestController', 'Owner'))->byID($user->ID);

		// create session
		$session = ApiSession::create();
		$session->User = $user;
		$session->Token = AuthFactory::generate_token($user);

		return $session;
	}

	public static function delete($request) {
        $owner = self::current($request);
        if(!$owner) {
            throw new RestUserException("No session found", 404, 404);
        }
        $owner->logOut();
        return true;
    }


        /**
         * @param SS_HTTPRequest $request
         * @return Member
         */
        public static function current($request) {
            $member = self::getBasicAuthMember();
            return ($member instanceof Member) ? DataObject::get(Config::inst()->get('BaseRestController', 'Owner'))->byID($member->ID) : null;
        }
        
        /**
         * @return Member
         */
        protected static function getBasicAuthMember(){
            $realm = Config::inst()->get('HttpAuth', 'Realm');
            $permissionCode = Config::inst()->get('HttpAuth', 'PermissionCode');
            $isRunningTests = (class_exists('SapphireTest', false) && SapphireTest::is_running_test());
            $tryUsingSessionLogin = $isRunningTests || Config::inst()->get('HttpAuth', 'TryUsingSessionLogin');

            try{
                $member = BasicAuth::requireLogin($realm, $permissionCode, $tryUsingSessionLogin);
                return $member;
            } catch (Exception $ex) {
                return null;
            }
        }

}
