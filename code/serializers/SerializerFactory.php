<?php

namespace Ntb\RestAPI;

/**
 * Factory for different kind of rest serializers.
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class SerializerFactory {
    private static $lookup = [
        'json' => 'application/json',
        'html' => 'text/html',
        'xml' => 'application/xml',
        'yaml' => 'application/yaml'
    ];

    /**
     * Returns a new instance of a serializer depending on the given type.
     *
     * @param string $mimeType the serializer type; Default: application/json
     * @return IRestSerializer an instance of a serializer
     * @throws RestUserException
     */
    public static function create($mimeType='application/json') {
        $availableSerializers = \ClassInfo::implementorsOf('Ntb\RestAPI\IRestSerializer');
        foreach($availableSerializers as $serializer) {
            /** @var IRestSerializer $instance */
            $instance = new $serializer();
            if($instance->active() && $instance->contentType() === $mimeType) {
                return $instance;
            }
        }
        throw new RestUserException("Requested Accept '$mimeType' not supported", 404);
    }

    /**
     * Determines the correct serializer from an incoming request.
     *
     * @param \SS_HTTPRequest $request the request object
     * @return IRestSerializer a new instance of a serializer which fits the request best
     * @throws RestUserException
     */
    public static function create_from_request($request) {
        if($type = $request->getVar('accept')) {
            try {
                if(array_key_exists($type, self::$lookup)) {
                    return self::create(self::$lookup[$type]);
                }
            } catch(\Exception $e) {}
        }
        $types = $request->getAcceptMimetypes();
        foreach($types as $type) {
            try {
                return self::create($type);
            } catch(RestUserException $e) {}
        }
        return self::create();
    }
}
