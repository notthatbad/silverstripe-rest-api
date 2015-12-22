<?php

/**
 * ApiSession controller is the controller for the session resource.
 */
class SessionController extends BaseRestController {

    private static $allowed_actions = array (
        'post' => true,
        'delete' => '->isAuthenticated'
    );

	/**
	 * @param SS_HTTPRequest $request
	 * @return array
	 * @throws RestSystemException
	 * @throws RestUserException
	 */
    public function post($request) {
        $data = json_decode($request->getBody(), true);
        if (!$data) {
            throw new RestUserException("No data for session provided.", 401, 401);
        }
        try{
            $validated = Injector::inst()->get('SessionValidator')->validate($data);
            $user = Injector::inst()->get('ApiMemberAuthenticator')->authenticate($validated);
            $session = $user ? AuthFactory::createAuth()->createSession($user) : null;
            if (!$session) {
                throw new RestUserException("Login incorrect", 401, 401);
            }
        } catch(ValidationException $e) {
	        throw new RestUserException($e->getMessage(), 422, 422);
        } catch(RestUserException $e) {
	        throw $e;
        } catch(Exception $e) {
            error_log($e->getMessage());
            throw new RestSystemException($e->getMessage(), $e->getCode() ?: 500);
        }
        $meta = ['timestamp' => time()];
        $result = [
            'session' => SessionFormatter::format($session)
        ];
        $result['meta'] = $meta;
        return $result;
    }

    /**
     * @param SS_HTTPRequest $request
     * @return array
     * @throws RestUserException
     */
    public function delete($request) {
        // check param for id
        $data = [];
        try {
            if($id = $request->param('ID')) {
                if($id != 'me') {
                    throw new RestUserException("No session found", 404);
                }
                AuthFactory::createAuth()->delete($request);
            } else {
                throw new RestUserException("No id specified for deletion", 404);
            }
        } catch(RestUserException $e) {
            throw $e;
        } catch(Exception $e) {
            throw new RestUserException("ApiSession was not found", 404);
        }
        $meta = ['timestamp' => time()];
        $data['meta'] = $meta;
        return $data;
    }
}
