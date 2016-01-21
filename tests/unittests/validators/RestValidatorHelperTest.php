<?php

/**
 * Tests for the rest validation helper.
 * @todo: add more tests
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class RestValidatorHelperTest extends SapphireTest {

    public function testIsUrl() {
        $correctUrls = [
            'https://example.com',
            'http://example.com',
            'http://foo.com/blah_blah',
            'http://foo.com/blah_blah/',
            'http://foo.com/blah_blah_(wikipedia)',
            'http://foo.com/blah_blah_(wikipedia)_(again)',
            'http://www.example.com/wpstyle/?p=364',
            'https://www.example.com/foo/?bar=baz&inga=42&quux',
            'http://df.ws/123',
            'http://userid:password@example.com:8080',
            'http://userid:password@example.com:8080/',
            'http://userid@example.com',
            'http://userid@example.com/',
            'http://userid@example.com:8080',
            'http://userid@example.com:8080/',
            'http://userid:password@example.com',
            'http://userid:password@example.com/',
            'http://142.42.1.1/',
            'http://142.42.1.1:8080/',
            'http://fg.ws/',
            'http://fg.ws',
            'http://foo.com/blah_(wikipedia)#cite-1',
            'http://foo.com/blah_(wikipedia)_blah#cite-1',
            'http://foo.com/unicode_(✪)_in_parens',
            'http://foo.com/(something)?after=parens',
            'http://☺.damowmow.com/',
            'http://code.google.com/events/#&product=browser',
            'http://j.mp',
            'ftp://foo.bar/baz',
            'http://foo.bar/?q=Test%20URL-encoded%20stuff',
            'http://مثال.إختبار',
            'http://例子.测试',
            'http://उदाहरण.परीक्',
            "http://-.~_!$&'()*+,;=:%40:80%2f::::::@example.com",
            'http://1337.net',
            'http://a.b-c.de',
            'http://223.255.255.254'
        ];

        $incorrectUrls = [
            'http://',
            'http://.',
            'http://..',
            'http://../',
            'http://?',
            'http://??',
            'http://??/',
            'http://#',
            'http://##',
            'http://##/',
            'http://foo.bar?q=Spaces should be encoded',
            '//',
            '//a',
            '///a',
            '///',
            'http:///a',
            'foo.com',
            'rdar://1234',
            'h://test',
            'http:// shouldfail.com',
            ':// should fail',
            'http://foo.bar/foo(bar)baz quux',
            'ftps://foo.bar/',
            'http://-error-.invalid/',
            'http://-a.b.co',
            'http://a.b-.co',
            'http://0.0.0.0',
            'http://10.1.1.0',
            'http://10.1.1.255',
            'http://224.1.1.1',
            'http://1.1.1.1.1',
            'http://123.123.123',
            'http://3628126748',
            'http://.www.foo.bar/',
            'http://.www.foo.bar./',
            'http://10.1.1.1',
            'http://10.1.1.254'
        ];

        foreach($correctUrls as $url) {
            $t = $url;
            $this->assertTrue(RestValidatorHelper::is_url($url), "$t should be valid");
        }

        foreach($incorrectUrls as $url) {
            $t = $url;
            $this->assertFalse(RestValidatorHelper::is_url($url), "$t should be invalid");
        }
    }

    public function testValidateCountryCode() {
        $this->assertEquals('US', RestValidatorHelper::validate_country_code(['cc' => 'US'], 'cc'));
        $this->assertEquals(null, RestValidatorHelper::validate_country_code([], 'cc', ['required' => false]));

        TestHelper::assertException(function() {
            RestValidatorHelper::validate_country_code(['cc' => 'FOO'], 'cc');
        }, 'ValidationException');
        TestHelper::assertException(function() {
            RestValidatorHelper::validate_country_code([], 'cc');
        }, 'ValidationException');
    }

    public function testValidateEmail() {
        $this->assertEquals('j@d.com', RestValidatorHelper::validate_email(['mail' => 'j@d.com'], 'mail'));

        TestHelper::assertException(function() {
            RestValidatorHelper::validate_email(['mail' => 'FOO'], 'mail');
        }, 'ValidationException');

        $this->assertEquals(null, RestValidatorHelper::validate_email([], 'mail', ['required' => false]));
        TestHelper::assertException(function() {
            RestValidatorHelper::validate_email([], 'mail');
        }, 'ValidationException');
    }

    public function testValidateUrl() {
        $this->assertEquals('http://test.com', RestValidatorHelper::validate_url(['url' => 'http://test.com'], 'url'));

        TestHelper::assertException(function() {
            RestValidatorHelper::validate_url(['url' => 'FOO'], 'url');
        }, 'ValidationException');

        $this->assertEquals(null, RestValidatorHelper::validate_url([], 'url', ['required' => false]));
        TestHelper::assertException(function() {
            RestValidatorHelper::validate_url([], 'url');
        }, 'ValidationException');
    }


    public function testValidateString() {
        $this->assertEquals('foo bar', RestValidatorHelper::validate_string(['str' => 'foo bar'], 'str'));
        $this->assertEquals(null, RestValidatorHelper::validate_string([], 'str', ['required' => false]));

        TestHelper::assertException(function() {
            RestValidatorHelper::validate_string([], 'str');
        }, 'ValidationException');

        TestHelper::assertException(function() {
            RestValidatorHelper::validate_string(['str' => 15], 'str');
        }, 'ValidationException');

        TestHelper::assertException(function() {
            RestValidatorHelper::validate_string(['str' => 'foo bar'], 'str', ['max' => 4]);
        }, 'ValidationException');
        $this->assertEquals('foo', RestValidatorHelper::validate_string(['str' => 'foo'], 'str'), ['max' => 4]);


        TestHelper::assertException(function() {
            RestValidatorHelper::validate_string(['str' => 'fo'], 'str', ['min' => 3, 'max' => 4]);
        }, 'ValidationException');
        $this->assertEquals('foo', RestValidatorHelper::validate_string(['str' => 'foo'], 'str', ['min' => 2, 'max' => 4]));
    }


    public function testValidateInt() {
        $this->assertEquals(5, RestValidatorHelper::validate_int(['int' => 5], 'int'));
        $this->assertEquals(null, RestValidatorHelper::validate_int([], 'int', ['required' => false]));

        TestHelper::assertException(function() {
            RestValidatorHelper::validate_int([], 'int');
        }, 'ValidationException');

        TestHelper::assertException(function() {
            RestValidatorHelper::validate_int(['int' => 'foo bar'], 'int');
        }, 'ValidationException');

        TestHelper::assertException(function() {
            RestValidatorHelper::validate_int(['int' => 5], 'int', ['max' => 4]);
        }, 'ValidationException');
        $this->assertEquals(-5, RestValidatorHelper::validate_int(['int' => -5], 'int', ['max' => 0]));


        TestHelper::assertException(function() {
            RestValidatorHelper::validate_int(['int' => 1], 'int', ['min' => 3, 'max' => 4]);
        }, 'ValidationException');
        $this->assertEquals(2, RestValidatorHelper::validate_int(['int' => 2], 'int', ['min' => 2, 'max' => 4]));
        $this->assertEquals(4, RestValidatorHelper::validate_int(['int' => 4], 'int', ['min' => 2, 'max' => 4]));
    }


    public function testValidateDatetime() {
        $this->assertEquals('2015-08-07 12:13:14', RestValidatorHelper::validate_datetime(['date' => '2015-08-07 12:13:14'], 'date'));
        $this->assertEquals(null, RestValidatorHelper::validate_datetime([], 'date', ['required' => false]));

        TestHelper::assertException(function() {
            RestValidatorHelper::validate_datetime([], 'date');
        }, 'ValidationException');

        TestHelper::assertException(function() {
            RestValidatorHelper::validate_datetime(['date' => 'error'], 'date');
        }, 'ValidationException');
    }

}
