<?php

/**
 * Rest test class can work as base class for your functional tests. It provides some helpful methods to test your rest
 * api more easily.
 */
abstract class RestTest extends SapphireTest
{

    /**
     * @var bool Set whether to include this test in the TestRunner or to skip this.
     */
    protected $skipTest = true;
    /**
     * The namespace of your api.
     * @var string
     */
    protected $namespace = 'v/1';

    protected $defaultToken;

    /**
     * The route to the session without the namespace.
     * @var string
     */
    protected $sessionRoute = 'sessions';

    public function setUp()
    {
        parent::setUp();
        $this->defaultToken = Config::inst()->get('TokenAuth', 'DevToken');
        // clear cache
        SS_Cache::factory('rest_cache')->clean(Zend_Cache::CLEANING_MODE_ALL);
    }


    /**
     * Perform an api request with the given options
     *
     * @param string $path the request path; can consist of resource name, identifier and GET params
     * @param array $options
     *  * string `body` the data
     *  * int `code` the expected response code
     *  * string `method` the http method
     *  * ApiSession `session` the test session
     * @return array
     * @throws SS_HTTPResponse_Exception
     */
    protected function makeApiRequest($path, $options=[])
    {
        $settings = array_merge([
            'session' => null,
            'token' => null,
            'method' => 'GET',
            'body' => null,
            'code' => 200
        ], $options);
        $response = Director::test(
            Controller::join_links($this->namespace, $path),
            null,
            $settings['session'],
            $settings['method'],
            $settings['body'],
            [
                'Authorization' => "Bearer ".$settings['token'],
                'Accept' => 'application/json'
            ]);
        $this->assertEquals($settings['code'], $response->getStatusCode(), "Wrong status code: {$response->getBody()}");
        return json_decode($response->getBody(), true);
    }

    /**
     * Creates a session for the api.
     *
     * @param string $email the email of the user
     * @param string $password the password for the user
     * @return array the current session with `token`
     */
    protected function createSession($email='considine.colby@gmail.com', $password='password')
    {
        $data = [
            'email' => $email,
            'password' => $password
        ];
        $dataString = json_encode($data);
        $result = $this->makeApiRequest('sessions', ['body' => $dataString, 'method' => 'POST']);
        return $result['session'];
    }
}
