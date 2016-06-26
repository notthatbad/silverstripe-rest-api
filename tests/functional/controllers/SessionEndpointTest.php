<?php

namespace Ntb\RestAPI;

/**
 * Class SessionControllerTest
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class SessionEndpointTest extends RestTest {

    public function setUp() {
        parent::setUp();
        \Config::inst()->update('Director', 'rules', [
            'v/1/SessionControllerTestRoute/$ID' => 'Ntb\RestAPI\TestSessionController',
            'v/1/sessions/$ID' => 'Ntb\RestAPI\TestSessionController',
        ]);
    }

    protected static $fixture_file = [
        'app_fixtures/Setting.yml',
        'app_fixtures/User.yml'
    ];

    public function testTryGetRestrictedResourceWithoutSession() {
        $this->makeApiRequest('SessionControllerTestRoute', ['code' => 401]);
    }

    public function testGetRestrictedResource() {
        $session = $this->createSession();
        $this->makeApiRequest('SessionControllerTestRoute', ['token' => $session['token']]);
    }

    public function testDeleteSession() {
        $session = $this->createSession();
        $this->makeApiRequest('sessions/me', ['token' => $session['token'], 'method' => 'DELETE']);
    }

    public function testTryDeleteSessionWithoutID() {
        $session = $this->createSession();
        $result = $this->makeApiRequest('sessions', ['token' => $session['token'], 'method' => 'DELETE', 'code' => 400]);

        $this->assertTrue(array_key_exists('code', $result));
        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals(404, $result['code']);
    }

    public function testTryDeleteSessionWithWrongId() {
        $session = $this->createSession();
        $result = $this->makeApiRequest('sessions/-2', ['token' => $session['token'], 'method' => 'DELETE', 'code' => 400]);

        $this->assertTrue(array_key_exists('code', $result));
        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals(404, $result['code']);
    }

    public function testCreateSession() {
        $session = $this->createSession();

        $this->assertTrue(is_array($session));
        $this->assertTrue(array_key_exists('user', $session));
        $this->assertTrue(array_key_exists('token', $session));
        $this->assertEquals('jones-jena', $session['user']);
    }

    public function testTryCreateSessionWithWrongPassword() {
        $data = [
            'email' => 'considine.colby@gmail.com',
            'password' => 'wrong'
        ];

        $dataString = json_encode($data);
        $result = $this->makeApiRequest('sessions', ['body' => $dataString, 'method' => 'POST', 'code' => 401]);

        $this->assertTrue(array_key_exists('code', $result));
        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals(401, $result['code']);
    }

    public function testTryCreateSessionWithWrongEmail() {
        $data = [
            'email' => 'foo.com',
            'password' => 'wrong'
        ];

        $dataString = json_encode($data);
        $result = $this->makeApiRequest('sessions', ['body' => $dataString, 'method' => 'POST', 'code' => 422]);

        $this->assertTrue(array_key_exists('code', $result));
        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals(422, $result['code']);
    }
}


class TestSessionController extends BaseRestController implements \TestOnly {

    private static $allowed_actions = array (
        'get' => '->isAuthenticated'
    );

    public function get() {
        return ['message' => 'Test GET'];
    }
}
