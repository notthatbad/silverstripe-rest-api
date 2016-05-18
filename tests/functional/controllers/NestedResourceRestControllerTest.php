<?php

/**
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class NestedResourceRestControllerTest extends RestTest {

    public function setUp() {
        parent::setUp();
        Config::inst()->update('Director', 'rules', [
            'v/1/RestTestRoute/$ID/Nested/$OtherID' => 'NestedTestController',
        ]);
    }

    public function testControllerGET() {
        $id = 'Foo';
        $result = $this->makeApiRequest("RestTestRoute/$id/Nested");
        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals('Test GET', $result['message']);
        $this->assertEquals($id, $result['resource']['id']);
    }

    public function testNotProvidedID() {
        $this->makeApiRequest("RestTestRoute//Nested", ['code' => 404]);
    }


    public function testControllerDELETE() {
        $id = 'Bar';
        $result = $this->makeApiRequest("RestTestRoute/$id/Nested", ['method' => 'DELETE']);
        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals('Test DELETE', $result['message']);
        $this->assertEquals($id, $result['resource']['id']);
    }
}

class NestedTestController extends NestedResourceRestController implements TestOnly {

    private static $allowed_actions = [
        'delete' => true,
        'get' => true
    ];

    public function get($request, $resource) {
        return ['message' => 'Test GET', 'resource' => $resource];
    }

    public function delete($request, $resource) {
        return ['message' => 'Test DELETE', 'resource' => $resource];
    }

    protected function getRootResource($id) {
        return ['id' => $id];
    }
}
