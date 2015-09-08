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
        $result = $this->makeApiRequest('RestTestRoute');

        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals('Test GET', $result['message']);
    }

    public function testControllerDELETE() {
        $result = $this->makeApiRequest('RestTestRoute', ['method' => 'DELETE']);

        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals('Test DELETE', $result['message']);
    }

    public function testControllerPOST() {
        $result = $this->makeApiRequest('RestTestRoute', ['method' => 'POST']);

        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals('Test POST', $result['message']);
    }

    public function testControllerPUT() {
        $result = $this->makeApiRequest('RestTestRoute', ['method' => 'PUT']);

        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals('Test PUT', $result['message']);
    }

    public function testUnsupportedMethods() {
        $this->makeApiRequest('RestTestRoute', ['method' => 'OPTIONS', 'code' => 404]);
        $this->makeApiRequest('RestTestRoute', ['method' => 'TRACE', 'code' => 404]);
        $this->makeApiRequest('RestTestRoute', ['method' => 'CONNECT', 'code' => 404]);
        $this->makeApiRequest('RestTestRoute', ['method' => 'HEAD', 'code' => 404]);
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