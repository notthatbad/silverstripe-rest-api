<?php

/**
 * Base class for the rest resource controllers.
 */
abstract class BaseRestController extends Controller
{

    /**
     * The default limit.
     * Can be overridden in children.
     * @var int
     */
    protected static $default_limit = 20;

    /**
     * The default offset.
     * Can be overridden in children.
     * @var int
     */
    protected static $default_offset = 0;

    /**
     * Returns the offset, either given in request by `offset` or from the default settings in the controller.
     *
     * @param SS_HTTPRequest $request
     * @return int the offset value
     */
    protected static function offset($request)
    {
        $offset = (int)$request->getVar('offset');
        if ($offset && is_int($offset) && $offset >= 0) {
            return $offset;
        } else {
            return static::$default_offset;
        }
    }

    /**
     * Returns the limit, either given in request by `limit` or from the default settings in the controller.
     *
     * @param SS_HTTPRequest $request
     * @return int the limit value
     */
    protected static function limit($request)
    {
        $limit = (int)$request->getVar('limit');
        if ($limit && is_int($limit) && $limit > 0) {
            return $limit;
        } else {
            return static::$default_limit;
        }
    }

    /**
     */
    public function init()
    {
        parent::init();
        // check for CORS options request
        if ($this->request->httpMethod() === 'OPTIONS') {
            // create direct response without requesting any controller
            $response = $this->getResponse();
            // set CORS header from config
            $response = $this->addCORSHeaders($response);
            $response->output();
            exit;
        }
    }

    /**
     * handleAction implementation for rest controllers. This handles the requested action differently then the standard
     * implementation.
     *
     * @param SS_HTTPRequest $request
     * @param string $action
     * @return HTMLText|SS_HTTPResponse
     */
    protected function handleAction($request, $action)
    {
        foreach ($request->latestParams() as $k => $v) {
            if ($v || !isset($this->urlParams[$k])) {
                $this->urlParams[$k] = $v;
            }
        }
        // set the action to the request method / for developing we could use an additional parameter to choose another method
        $action = $this->getMethodName($request);
        $this->action = $action;
        $this->requestParams = $request->requestVars();
        $className = $this->class;
        // create serializer
        $serializer = SerializerFactory::create_from_request($request);
        $response = $this->getResponse();
        // perform action
        try {
            if (!$this->hasAction($action)) {
                // method couldn't found on controller
                throw new RestUserException("Action '$action' isn't available on class $className.", 404);
            }

            if (!$this->checkAccessAction($action)) {
                throw new RestUserException("Action '$action' isn't allowed on class $className.", 404);
            }

            $res = $this->extend('beforeCallActionHandler', $request, $action);
            if ($res) {
                return reset($res);
            }
            // perform action
            $actionRes = $this->$action($request);
            $res = $this->extend('afterCallActionHandler', $request, $action);
            if ($res) {
                return reset($res);
            }
            // set content type
            $body = $actionRes;
        } catch (RestUserException $ex) {
            // a user exception was caught
            $response->setStatusCode($ex->getHttpStatusCode());
            $body = [
                'message' => $ex->getMessage(),
                'code' => $ex->getCode()
            ];
            SS_Log::log(
                json_encode(array_merge($body, ['file' => $ex->getFile(), 'line' => $ex->getLine()])),
                SS_Log::INFO);
        } catch (RestSystemException $ex) {
            // a system exception was caught
            $response->addHeader('Content-Type', $serializer->contentType());
            $response->setStatusCode("500");
            $body = [
                'message' => $ex->getMessage(),
                'code' => $ex->getCode()
            ];
            if (Director::isDev()) {
                $body = array_merge($body, [
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                    'trace' => $ex->getTrace()
                ]);
            }
            SS_Log::log(
                json_encode(array_merge($body, ['file' => $ex->getFile(), 'line' => $ex->getLine()])),
                SS_Log::WARN);
        } catch (Exception $ex) {
            // an unexpected exception was caught
            $response->addHeader('Content-Type', $serializer->contentType());
            $response->setStatusCode("500");
            $body = [
                'message' => $ex->getMessage(),
                'code' => $ex->getCode()
            ];
            if (Director::isDev()) {
                $body = array_merge($body, [
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                    'trace' => $ex->getTrace()
                ]);
            }
            SS_Log::log(
                json_encode(array_merge(
                    $body, ['file' => $ex->getFile(), 'line' => $ex->getLine(), 'trace' => $ex->getTrace()])),
                SS_Log::ERR);
        }
        // serialize content and set body of response
        $response->setBody($serializer->serialize($body));
        // set CORS header from config
        $response = $this->addCORSHeaders($response);
        return $response;
    }

    /**
     * Returns the http method for this request. If the current environment is a development env, the method can be
     * changed with a `method` variable.
     *
     * @param SS_HTTPRequest $request the current request
     * @return string the used http method as string
     */
    private function getMethodName($request)
    {
        $method = '';
        if (Director::isDev() && ($varMethod = $request->getVar('method'))) {
            if (in_array(strtoupper($varMethod), array('GET', 'POST', 'PUT', 'DELETE', 'HEAD'))) {
                $method = $varMethod;
            }
        } else {
            $method = $request->httpMethod();
        }
        return strtolower($method);
    }

    /**
     * @return bool
     * @throws RestSystemException
     */
    protected function isAuthenticated()
    {
        return $this->currentUser() ? true : false;
    }

    /**
     * @return bool
     * @throws RestSystemException
     */
    protected function isAdmin()
    {
        $member = $this->currentUser();
        return $member && Permission::checkMember($member, 'ADMIN');
    }

    protected function addCORSHeaders($response)
    {
        $response->addHeader('Access-Control-Allow-Origin', Config::inst()->get('BaseRestController', 'CORSOrigin'));
        $response->addHeader('Access-Control-Allow-Methods', Config::inst()->get('BaseRestController', 'CORSMethods'));
        $response->addHeader('Access-Control-Max-Age', Config::inst()->get('BaseRestController', 'CORSMaxAge'));
        $response->addHeader('Access-Control-Allow-Headers', Config::inst()->get('BaseRestController', 'CORSAllowHeaders'));

        return $response;
    }

    protected function currentUser()
    {
        return AuthFactory::createAuth()->current($this->request);
    }
}
