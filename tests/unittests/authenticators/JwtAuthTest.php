<?php

/**
 * Test the JWT auth mechanism
 * @todo: add more tests
 */
class JwtAuthTest extends SapphireTest
{


    public function testJwtEncode()
    {
        $data = [
            "sub" => "1234567890",
            "name" => "John Doe",
            "admin" => true
        ];
        $result = JwtAuth::jwt_encode($data, "secret");
        $this->assertEquals(
            "eyJ0eXAiOiJKV1QifQ.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.2a2f70f937182a2daa5c1e79ed832899c3ebb14412b214c0cb0484b8199b64a2",
            $result
        );
    }

    public function testJwtDecode()
    {
        $token = "eyJ0eXAiOiJKV1QifQ.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.2a2f70f937182a2daa5c1e79ed832899c3ebb14412b214c0cb0484b8199b64a2";
        $result = JwtAuth::jwt_decode($token, "secret");
        $this->assertEquals("1234567890", $result['sub']);
        $this->assertEquals(true, $result['admin']);
        $this->assertEquals("John Doe", $result['name']);
    }
}
