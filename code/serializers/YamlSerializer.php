<?php

namespace Ntb\RestAPI;

use Symfony\Component\Yaml\Dumper;

/**
 * Serializer for yaml.
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class YamlSerializer extends \SS_Object implements IRestSerializer {

    /**
     * @config
     */
    private static $is_active = true;

    /**
     * The content type.
     * @var string
     */
    private $contentType = "application/yaml";

    /**
     * Serializes the given data into a yaml string.
     *
     * @param array $data the data that should be serialized
     * @return string a yaml formatted string
     */
    public function serialize($data) {
        $yamlDumper = new Dumper();
        return $yamlDumper->dump($data, 5);
    }

    public function contentType() {
        return $this->contentType;
    }

    /**
     * Indicates if the serializer is active.
     * Serializers can be deactivated to use another implementation for the same mime type.
     *
     * @return boolean
     */
    public function active() {
        return $this->config()->get('is_active');
    }
}
