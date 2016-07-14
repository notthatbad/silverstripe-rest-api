<?php
use Ntb\RestAPI\HtmlSerializer;
use Ntb\RestAPI\JsonSerializer;
use Ntb\RestAPI\SerializerFactory;
use Ntb\RestAPI\TestHelper;
use Ntb\RestAPI\XmlSerializer;
use Ntb\RestAPI\YamlSerializer;

/**
 * Tests for the serializer factory.
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class SerializerFactoryTest extends SapphireTest {

    public function testNotProvidedMimeType() {
        $this->assertTrue(SerializerFactory::create() instanceof JsonSerializer);
    }

    public function testMimeTypes() {
        $this->assertTrue(SerializerFactory::create('application/json') instanceof JsonSerializer);
        $this->assertTrue(SerializerFactory::create('application/xml') instanceof XmlSerializer);
        $this->assertTrue(SerializerFactory::create('application/yaml') instanceof YamlSerializer);
        $this->assertTrue(SerializerFactory::create('text/html') instanceof HtmlSerializer);
    }

    public function testEmptyMimeType() {
        TestHelper::assertException(function() {
            SerializerFactory::create("");
        }, 'Ntb\RestAPI\RestUserException');
    }

    public function testUnsupportedMimeType() {
        TestHelper::assertException(function() {
            SerializerFactory::create("foo/bar");
        }, 'Ntb\RestAPI\RestUserException');
    }

    public function testCreateFromRequestWithAcceptHeader() {
        $request = new SS_HTTPRequest("GET", "");
        $request->addHeader('Accept', 'text/html,multipart/mixed,*/*');
        $this->assertTrue(SerializerFactory::create_from_request($request) instanceof HtmlSerializer);
        $request1 = new SS_HTTPRequest("GET", "");
        $request1->addHeader('Accept', 'application/json,text/html,multipart/mixed,*/*');
        $this->assertTrue(SerializerFactory::create_from_request($request1) instanceof JsonSerializer);
        $request2 = new SS_HTTPRequest("GET", "");
        $request2->addHeader('Accept', 'foo/bar,application/yaml,text/html,multipart/mixed,*/*');
        $this->assertTrue(SerializerFactory::create_from_request($request2) instanceof YamlSerializer);
        $request3 = new SS_HTTPRequest("GET", "");
        $request3->addHeader('Accept', 'foo/bar,application/xml,text/html,multipart/mixed,*/*');
        $this->assertTrue(SerializerFactory::create_from_request($request3) instanceof XmlSerializer);
    }

    public function testCreateFromRequestWithUnsupportedAcceptHeader() {
        $request = new SS_HTTPRequest("GET", "");
        $request->addHeader('Accept', 'foo/bar, multipart/mixed, */*');
        $this->assertTrue(SerializerFactory::create_from_request($request) instanceof JsonSerializer);
    }

    public function testCreateFromRequestWithGetVar() {
        $request = new SS_HTTPRequest("GET", "", ['accept' => 'json']);
        $this->assertTrue(SerializerFactory::create_from_request($request) instanceof JsonSerializer);
        $request1 = new SS_HTTPRequest("GET", "", ['accept' => 'yaml']);
        $this->assertTrue(SerializerFactory::create_from_request($request1) instanceof YamlSerializer);
        $request2 = new SS_HTTPRequest("GET", "", ['accept' => 'xml']);
        $this->assertTrue(SerializerFactory::create_from_request($request2) instanceof XmlSerializer);
    }

    public function testCreateFromRequestWithWrongGetVar() {
        $request = new SS_HTTPRequest("GET", "", ['accept' => 'foo']);
        $this->assertTrue(SerializerFactory::create_from_request($request) instanceof JsonSerializer);
    }

    public function testGetVarBeforeHeader() {
        $request = new SS_HTTPRequest("GET", "", ['accept' => 'yaml']);
        $request->addHeader('Accept', 'text/html,multipart/mixed,*/*');
        $this->assertTrue(SerializerFactory::create_from_request($request) instanceof YamlSerializer);
    }

    public function testGetVarAndHeaderEmpty() {
        $request = new SS_HTTPRequest("GET", "");
        $request->addHeader('Accept', '');
        $this->assertTrue(SerializerFactory::create_from_request($request) instanceof JsonSerializer);

    }
}
