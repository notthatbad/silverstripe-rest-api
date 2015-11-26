<?php

/**
 * Test the JWT auth mechanism
 * @todo: add more tests
 */
class JwtAuthTest extends SapphireTest {


    public function testJwtEncode() {
        $data = [
            "sub" => "1234567890",
            "name" => "John Doe",
            "admin" => true
        ];
        $result = JwtAuth::jwt_encode($data, "secret");
        print_r($result);
        $this->assertEquals(
            "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.pcHcZspUvuiqIPVB_i_qmcvCJv63KLUgIAKIlXI1gY8",
            $result
        );
    }

    public function testJwtDecode() {
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.pcHcZspUvuiqIPVB_i_qmcvCJv63KLUgIAKIlXI1gY8";
        $result = JwtAuth::jwt_decode($token, "secret");
        $this->assertEquals("1234567890", $result['sub']);
        $this->assertEquals(true, $result['admin']);
        $this->assertEquals("John Doe", $result['name']);
    }

}
