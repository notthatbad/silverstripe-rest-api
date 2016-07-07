<?php

namespace Ntb\RestAPI;
use Member;
use SS_HTTPRequest;

/**
 * Interface for authentication mechanisms.
 * @author Christian Blank <c.blank@notthatbad.net>
 */
interface IAuth {

    /**
     * @param string $email the email of the
     * @param string $password
     * @deprecated - use createSession instead
     * @return ApiSession
     */
    public static function authenticate($email, $password);

	/**
	 * @param Member $member
	 * @return ApiSession
	 */
	public static function createSession($member);

    /**
     * @param SS_HTTPRequest $request
     * @return bool
     */
    public static function delete($request);

    /**
     * @param SS_HTTPRequest $request
     * @return Member
     */
    public static function current($request);
}
