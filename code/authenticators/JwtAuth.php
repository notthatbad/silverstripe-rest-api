<?php

/**
 * Authentication mechanism using a token in the request header and validates it on every request.
 *
 * The mechanism works stateless. JWT is described in RFC 7519.
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class JwtAuth extends Object implements IAuth {

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
		// create session
		$session = ApiSession::create();
		$session->User = $user;
		$session->Token = JwtAuth::generate_token($user);
		return $session;
	}

	public static function delete($request) {
        // nothing to do here
    }

    /**
     * @param SS_HTTPRequest $request
     * @return Member
     */
    public static function current($request) {
        try {
            $token = AuthFactory::get_token($request);
            return self::get_member_from_token($token);
        } catch(Exception $e) {
            SS_Log::log($e->getMessage(), SS_Log::INFO);
        }
        return false;
    }

    /**
     *
     *
     * @param string $token
     * @throws RestUserException
     * @return Member
     */
    private static function get_member_from_token($token) {
        try {
            $data = self::jwt_decode($token, self::get_key());
            if($data) {
                // todo: check expire time
                if(time() > $data['expire']) {
                    throw new RestUserException("Session expired", 403);
                }
                $id = (int)$data['userId'];
                $user = DataObject::get(Config::inst()->get('BaseRestController', 'Owner'))->byID($id);
                if(!$user) {
                    throw new RestUserException("Owner not found in database", 403);
                }
                return $user;
            }
        } catch(RestUserException $e) {
            throw $e;
        } catch(Exception $e) {
            if(Director::isDev() && $token == Config::inst()->get('JwtAuth', 'DevToken')) {
                return DataObject::get(Config::inst()->get('BaseRestController', 'Owner'))->first();
            }
        }
        throw new RestUserException("Token invalid", 403);
    }

    /**
     * @param Member $user
     * @return string
     */
    private static function generate_token($user) {
        $iat = time();
        $data = [
            'iat' => $iat,
            'jti' => AuthFactory::generate_token($user),
            'iss' => Config::inst()->get('JwtAuth', 'Issuer'),
            'expire' => $iat + Config::inst()->get('JwtAuth', 'ExpireTime'),
            'userId' => $user->ID
        ];
        $key = self::get_key();
        return self::jwt_encode($data, $key);
    }

    /**
     * @param array $data
     * @param string $key
     * @return string
     */
    public static function jwt_encode($data, $key) {
        $header = ['typ' => 'JWT'];
        $headerEncoded = self::base64_url_encode(json_encode($header));
        $dataEncoded = self::base64_url_encode(json_encode($data));
        $signature = hash_hmac(Config::inst()->get('JwtAuth', 'HashAlgorithm'), "$headerEncoded.$dataEncoded", $key);
        return "$headerEncoded.$dataEncoded.$signature";
    }

    private static function get_key() {
        return Config::inst()->get('JwtAuth', 'Key');
    }

    /**
     * @param string $token
     * @param string $key
     * @return array
     * @throws Exception
     */
    public static function jwt_decode($token, $key) {
        $exploded = explode('.', $token);
        if(count($exploded) < 3) {
            throw new Exception("No valid JWT token");
        }
        list($headerEncoded, $dataEncoded, $signature) = $exploded;
        $selfRun = hash_hmac(Config::inst()->get('JwtAuth', 'HashAlgorithm'), "$headerEncoded.$dataEncoded", $key);
        if($selfRun === $signature) {
            return json_decode(self::base64_url_decode($dataEncoded), true);
        }
        return false;
    }

    static function base64_url_encode($data) {
        return rtrim(base64_encode($data), '=');
    }

    static function base64_url_decode($base64) {
        return base64_decode(strtr($base64, '-_', '+/'));
    }

}
