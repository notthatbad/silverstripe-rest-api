<?php

/**
 * ApiSession controller is the controller for the session resource.
 */
class SessionController extends BaseRestController {

    private static $allowed_actions = array (
        'post' => true,
        'delete' => '->isLoggedIn'
    );

    /**
     * @param SS_HTTPRequest $request
     * @return array
     * @throws RestUserException
     */
    public function post($request) {
        $data = json_decode($request->getBody(), true);
        if(!$data) {
            throw new RestUserException("No data for session provided.", 404);
        }

        try{
            $validated = SessionValidator::validate($data);

            $session = AuthFactory::createAuth()->authenticate($validated['Email'], $validated['Password']);
            if(!$session) {
                throw new RestUserException("Login incorrect",404);
            }
        } catch(ValidationException $e) {
            throw new RestUserException($e->getMessage(), 404);
        } catch(Exception $e) {
            error_log($e->getMessage());
            throw new RestUserException($e->getMessage(), 404);
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