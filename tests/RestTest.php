<?php

/**
 * Rest test class can work as base class for your functional tests. It provides some helpful methods to test your rest
 * api more easily.
 */
class RestTest extends FunctionalTest {
    /**
     * @var bool Set whether to include this test in the TestRunner or to skip this.
     */
    protected $skipTest = true;
    /**
     * The namespace of your api.
     * @var string
     */
    protected $namespace = 'v/1';
    // TODO: set json as as mime type
    /**
     * Perform an api request with the given options
     *
     * @param string $path the request path; can consist of resource name, identifier and GET params
     * @param array $options
     *  * string `body` the data
     *  * int `code` the expected response code
     *  * string `method` the http method
     *  * Session `session` the test session
     * @return array
     * @throws SS_HTTPResponse_Exception
     */
    public function makeApiRequest($path, $options=[]) {
        $settings = array_merge([
            'session' => null,
            'method' => 'GET',
            'body' => null,
            'code' => 200
        ], $options);
        $response = Director::test(
            Controller::join_links($this->namespace, $path),
            null,
            $settings['session'],
            $settings['method'],
            $settings['body']);
        $this->assertEquals($settings['code'], $response->getStatusCode(), "Wrong status code: {$response->getBody()}");

        return json_decode($response->getBody(), true);
    }
}