<?php

/**
 * Authentication mechanism using a token in the request header. Valid tokens are saved in cache.
 */
class TokenAuth extends Object implements IAuth {

    public static function authenticate($email, $password) {
        $authenticator = new MemberAuthenticator();
        if($user = $authenticator->authenticate(['Password' => $password, 'Email' => $email])) {
            // create session
            $session = ApiSession::create();
            $session->User = $user;
            $session->Token = AuthFactory::generate_token($user);

            // save session
            $cache = SS_Cache::factory('rest_cache');
            $cache->save(json_encode(['token' => $session->Token, 'user' => $session->User->ID]), $session->Token);

            return $session;
        }
    }

    public static function delete($request) {
        try {
            $token = AuthFactory::get_token($request);
            $cache = SS_Cache::factory('rest_cache');
            $cache->remove($token);
        } catch(Exception $e) {
            SS_Log::log($e->getMessage(), SS_Log::INFO);
        }
    }

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
        $cache = SS_Cache::factory('rest_cache');
        if($data = $cache->load($token)) {
            $data = json_decode($data, true);
            $id = (int)$data['user'];
            $user = DataObject::get(Config::inst()->get('BaseRestController', 'Owner'))->byID($id);
            if(!$user) {
                throw new RestUserException("Owner not found in database", 404);
            }
            return $user;
        } else if(Director::isDev() && $token == Config::inst()->get('TokenAuth', 'DevToken')) {
            return DataObject::get(Config::inst()->get('BaseRestController', 'Owner'))->first();
        }
        throw new RestUserException("Owner not found in database", 404);
    }

}
