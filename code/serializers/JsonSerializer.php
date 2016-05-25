<?php

/**
 * Serializer for json.
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class JsonSerializer extends Object implements IRestSerializer {

    /**
     * @config
     */
    private static $is_active = true;
    
    /**
     * The content type.
     * @var string
     */
    private $contentType = "application/json";

    /**
     * Serializes the given data into a json string.
     *
     * @param array $data the data that should be serialized
     * @return string a json formatted string
     */
    public function serialize($data) {
        return json_encode($data, JSON_PRETTY_PRINT);
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
