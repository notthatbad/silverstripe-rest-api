<?php

/**
 * Factory for different kind of rest serializers.
 */
class SerializerFactory {

    /**
     * Returns a new instance of a serializer depending on the given type.
     *
     * @param string $type the serializer type; Default: json
     * @return IRestSerializer an instance of a serializer
     */
    public static function create($type='json') {
        if($type === 'json') {
            return new JsonSerializer();
        } else if($type === 'html') {
            return new HtmlSerializer();
        }
    }
}