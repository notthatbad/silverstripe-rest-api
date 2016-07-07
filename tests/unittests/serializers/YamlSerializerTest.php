<?php
use Ntb\RestAPI\YamlSerializer;

/**
 * Tests for the yaml serializer.
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class YamlSerializerTest extends SapphireTest {

    /**
     * @var YamlSerializer
     */
    private $serializer;

    public function setUp() {
        parent::setUp();
        $this->serializer = new YamlSerializer();
    }

    public function testContentType() {
        $this->assertEquals('application/yaml', $this->serializer->contentType());
    }

    public function testSerialize() {
        $this->assertTrue(is_string($this->serializer->serialize(['data' => ['a' => 1, 'b' => 2]])));
    }
}
