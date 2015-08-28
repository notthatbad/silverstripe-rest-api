<?php

class RestTest extends FunctionalTest {
    /**
     * @var bool Set whether to include this test in the TestRunner or to skip this.
     */
    protected $skipTest = true;

    protected $namespace = 'v/1';

    /**
     * @param string $path
     * @param string $method
     * @param string $body
     * @param int $code
     * @return mixed
     * @throws SS_HTTPResponse_Exception
     */
    public function rest($path, $method='GET', $body=null, $code=200) {
        // TODO: set json as as mime type
        $response = Director::test(Controller::join_links($this->namespace, $path), null, null, $method, $body);
        $this->assertEquals($code, $response->getStatusCode());

        return json_decode($response->getBody(), true);
    }
}