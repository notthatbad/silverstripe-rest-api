<?php

namespace Ntb\RestAPI;

/**
 * The user exception can be used for indicating the wrong usage of the rest api.
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class RestUserException extends \Exception {

	/**
	 * @var int - override the default http status code if needed
	 */
	protected $httpStatusCode = 400;

	/**
	 * @param string $message
	 * @param int $errorCode
	 * @param int $httpStatusCode
	 */
    public function __construct($message, $errorCode, $httpStatusCode = 400) {
        parent::__construct($message, $errorCode);
	    $this->httpStatusCode = $httpStatusCode;
    }

	/**
	 * @return int
	 */
	public function getHttpStatusCode() {
		return $this->httpStatusCode;
	}

	/**
	 * @param int $httpStatusCode
	 * @return $this
	 */
	public function setHttpStatusCode($httpStatusCode) {
		$this->httpStatusCode = $httpStatusCode;
		return $this;
	}

}
