<?php

/**
 * Tests for the base rest controller.
 *
 * @todo: test different serializers, pagination and error handling
 */
class BaseRestControllerTest extends RestTest {
    protected $skipTest = false;

    public function setUp() {
        parent::setUp();
        Config::inst()->update('Director', 'rules', [
            'v/1/RestTestRoute/$ID/$OtherID' => 'TestController',
        ]);
    }

    public function testControllerGET() {
        $result = $this->makeApiRequest('RestTestRoute', 'GET');

        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals('Test GET', $result['message']);
    }

    public function testControllerDELETE() {
        $result = $this->makeApiRequest('RestTestRoute', 'DELETE');

        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals('Test DELETE', $result['message']);
    }

    public function testControllerPOST() {
        $result = $this->makeApiRequest('RestTestRoute', 'POST');

        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals('Test POST', $result['message']);
    }

    public function testControllerPUT() {
        $result = $this->makeApiRequest('RestTestRoute', 'PUT');

        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals('Test PUT', $result['message']);
    }

    public function testUnsupportedMethods() {
        $this->makeApiRequest('RestTestRoute', 'OPTIONS', null, 404);
        $this->makeApiRequest('RestTestRoute', 'TRACE', null, 404);
        $this->makeApiRequest('RestTestRoute', 'CONNECT', null, 404);
        $this->makeApiRequest('RestTestRoute', 'HEAD', null, 404);
    }

}

class TestController extends BaseRestController implements TestOnly {

    public function get() {
        return ['message' => 'Test GET'];
    }

    public function post() {
        return ['message' => 'Test POST'];
    }

    public function put() {
        return ['message' => 'Test PUT'];
    }

    public function delete() {
        return ['message' => 'Test DELETE'];
    }
}