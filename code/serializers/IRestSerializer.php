<?php

/**
 * Provides methods for serializing data.
 */
interface IRestSerializer
{
    /**
     * Serialize the data in a specific format like html, json, xml etc.
     *
     * @param array $data the data that should be serialized
     * @return string the serialized data
     */
    public function serialize($data);

    /**
     * Returns the content type of a serializer. This could be the mime type
     * of json or html for example.
     *
     * @return string
     */
    public function contentType();
}
