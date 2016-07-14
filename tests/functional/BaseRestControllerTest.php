<?php

namespace Ntb\RestAPI;
use Config;

/**
 * Tests for the base rest controller.
 *
 * @todo: test different serializers, pagination and error handling
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class BaseRestControllerTest extends RestTest {

    public function setUp() {
        parent::setUp();
        Config::inst()->update('Director', 'rules', [
            'v/1/RestTestRoute/$ID/$OtherID' => 'Ntb\RestAPI\TestController',
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
}

class TestController extends BaseRestController implements \TestOnly {

    private static $allowed_actions = array (
        'post' => true,
        'delete' => true,
        'get' => true,
        'put' => true
    );

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
