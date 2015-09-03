<?php

/**
 * Some generic validators for incoming data.
 */
class RestValidatorHelper {
    const DefaultMaxLength = 1600;
    const PHP_INT_MIN = -2147483648;
    /**
     * @param array $data the data from the request
     * @param string $field the field, that should be checked
     * @param bool $required
     * @param int $maxLength
     * @param int $minLength
     * @return string
     * @throws ValidationException
     */
    public static function validate_string($data, $field, $required=true, $maxLength=self::DefaultMaxLength, $minLength=0) {
        if(isset($data[$field]) && is_string($data[$field])) {
            $string = $data[$field];
            // TODO: maybe the converting should not be made in validator
            $string = Convert::raw2sql(trim($string));
            $length = strlen($string);
            if($length > $maxLength) {
                throw new ValidationException("Given $field is to long");
            } else if($length < $minLength) {
                throw new ValidationException("Given $field is to short");
            }
            return $string;
        } else if($required) {
            throw new ValidationException("No $field given, but $field is required");
        }
    }

    /**
     * @param $data
     * @param $field
     * @param bool $required
     * @param int $max
     * @param int $min
     * @return int
     * @throws ValidationException
     */
    public static function validate_int($data, $field, $required=true, $max=PHP_INT_MAX, $min=self::PHP_INT_MIN) {
        if(isset($data[$field]) && is_numeric($data[$field])) {
            $int = (int) $data[$field];

            if($int >= $min && $int <= $max) {
                return $int;
            } else {
                throw new ValidationException("Given integer '$int' are not in range");
            }
        } else if($required) {
            throw new ValidationException("No $field given, but $field is required");
        }
    }

    public static function validate_date($data, $field, $required=true) {
        if(isset($data[$field]) && is_string($data[$field])) {
            $date = $data[$field];
            $dateTime = new SS_Datetime();
            $dateTime->setValue($date);

            return $dateTime->Format('Y-m-d H:i:s');
        } else if($required) {
            throw new ValidationException("No $field given, but $field is required");
        }
    }


    public static function validate_url($data, $field, $required=true) {
        if(isset($data[$field]) && is_string($data[$field])) {
            $url = $data[$field];
            if(!self::is_url($url)) {
                throw new ValidationException("No valid url given");
            }
            return $url;
        } else if($required) {
            throw new ValidationException("No $field given, but $field is required");
        }
    }

    /**
     * Validates an URL (defined in RFC 3986) with http or https protocol. If no
     * protocol was found, 'http' is set automatically.
     *
     * @param string $url
     * @return boolean
     */
    private static function is_url(&$url) {
        $matches = array();
        if (preg_match('/^(https?:\/\/)?(?:.+@)?(?:[\p{L}\d-]+\.)+\w{2,6}/', $url, $matches)) {
            if (!$matches[1]) {
                $url = 'http://' . $url;
            }
            return true;
        } else {
            return false;
        }
    }
}