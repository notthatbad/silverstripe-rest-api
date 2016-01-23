<?php

/**
 * Class PaginationExtension
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class PaginationExtension extends Extension {

    /**
     * The default limit.
     * Can be overridden in children.
     * @var int
     */
    protected static $default_limit = 20;

    /**
     * The default offset.
     * Can be overridden in children.
     * @var int
     */
    protected static $default_offset = 0;

    /**
     * Returns the offset, either given in request by `offset` or from the default settings in the controller.
     *
     * @param SS_HTTPRequest $request
     * @return int the offset value
     */
    protected static function offset($request) {
        $offset = (int)$request->getVar('offset');
        if($offset && is_int($offset) && $offset >= 0) {
            return $offset;
        } else {
            return static::$default_offset;
        }
    }

    /**
     * Returns the limit, either given in request by `limit` or from the default settings in the controller.
     *
     * @param SS_HTTPRequest $request
     * @return int the limit value
     */
    protected static function limit($request) {
        $limit = (int)$request->getVar('limit');
        if($limit && is_int($limit) && $limit > 0) {
            return $limit;
        } else {
            return static::$default_limit;
        }
    }
}