<?php

/**
 * The session formatter can format a session into a representation which is consumable from a IRestSerializer.
 */
class SessionFormatter implements IRestSerializeFormatter {

    /**
     * Returns an array with entries for `user`.
     *
     * @param ApiSession $data
     * @param array $access
     * @param array $fields
     * @return array the user data in a serializable structure
     */
    public static function format($data, $access=null, $fields=null) {
        return [
            'user' => $data->User->URLSegment,
            'token' => $data->Token
        ];
    }
}
