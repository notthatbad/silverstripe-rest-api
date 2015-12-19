<?php

/**
 * Serializer for json.
 */
class JsonSerializer implements IRestSerializer
{

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
    public function serialize($data)
    {
        return json_encode($data);
    }

    public function contentType()
    {
        return $this->contentType;
    }
}
