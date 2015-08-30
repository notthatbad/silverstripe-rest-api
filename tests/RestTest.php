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

    /**
     *
     *
     * @param string $path the request path; can consist of resource name, identifier and GET params
     * @param string $method the http method
     * @param string|array $body the data
     * @param int $responseCode the expected response code
     * @return array
     */
    public function makeApiRequest($path, $method='GET', $body=null, $responseCode=200) {
        // TODO: set json as as mime type
        $response = Director::test(Controller::join_links($this->namespace, $path), null, null, $method, $body);
        $this->assertEquals($responseCode, $response->getStatusCode());

        return json_decode($response->getBody(), true);
    }
}