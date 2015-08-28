<?php

/**
 * The interface for serialize formatters for the rest module provides defines
 * a method for working with the given data.
 */
interface IRestSerializeFormatter {

    /**
     * Formats a given model object into a serializable array, that can be used
     * by an instance of IRestSerializer.
     *
     * The formatter can change the property
     * names to something that corresponds with the rest documentation.
     *
     * It also applies access rights on properties and can filter different
     * fields.
     *
     * @param Object $data can be an object or a data object
     * @param array $access the access rights for particular fields
     * @param array $fields a list of requested fields
     * @return array the serializable result
     */
    public static function format($data, $access=null, $fields=null);
}