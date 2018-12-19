<?php

namespace Ntb\RestAPI;

/**
 *
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class ApiSession extends \SS_Object {
    /**
     * @var \Member
     */
    public $User;
    /**
     * @var string
     */
    public $Token;
}
