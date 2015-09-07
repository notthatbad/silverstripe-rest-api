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
     * @param array $options
     * @return string
     * @throws ValidationException
     */
    public static function validate_string($data, $field, $options=[]) {
        $options = array_merge([
            'required' => true,
            'min' => 0,
            'max' => self::DefaultMaxLength
        ], $options);
        $required = $options['required'];
        $minLength = $options['min'];
        $maxLength = $options['max'];
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
     * @param array $options
     * @return int
     * @throws ValidationException
     */
    public static function validate_int($data, $field, $options=[]) {
        $options = array_merge([
            'required' => true,
            'min' => self::PHP_INT_MIN,
            'max' => PHP_INT_MAX
        ], $options);
        $required = $options['required'];
        $min = $options['min'];
        $max = $options['max'];
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

    /**
     * @param $data
     * @param $field
     * @param array $options
     * @return string
     * @throws ValidationException
     */
    public static function validate_datetime($data, $field, $options=[]) {
        $options = array_merge([
            'required' => true
        ], $options);
        $required = $options['required'];
        if(isset($data[$field]) && is_string($data[$field])) {
            $date = $data[$field];
            $dateTime = new SS_Datetime();
            $dateTime->setValue($date);
            if(!$dateTime->getValue()) {
                throw new ValidationException("No valid datetime given.");
            }

            return $dateTime->Format('Y-m-d H:i:s');
        } else if($required) {
            throw new ValidationException("No $field given, but $field is required");
        }
    }

    /**
     * @param $data
     * @param $field
     * @param array $options
     * @return string
     * @throws ValidationException
     */
    public static function validate_url($data, $field, $options=[]) {
        $options = array_merge([
            'required' => true
        ], $options);
        $required = $options['required'];
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
     * Validates an URL (defined in RFC 3986).
     *
     * @param string $url the url, that should be validated
     * @return boolean
     */
    public static function is_url($url) {
        /**
         * @author https://gist.github.com/dperini/729294
         */
        $regex = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,}))\.?)(?::\d{2,5})?(?:[/?#]\S*)?$_iuS';

        return preg_match($regex, $url) === 1;
    }

    /**
     * @param $data
     * @param $field
     * @param array $options
     * @return string
     * @throws ValidationException
     */
    public static function validate_country_code($data, $field, $options=[]) {
        $options = array_merge([
            'required' => true
        ], $options);
        $required = $options['required'];
        if(isset($data[$field]) && is_string($data[$field])) {
            $code = $data[$field];
            $countries = Zend_Locale::getTranslationList('territory', i18n::get_locale(), 2);
            if(!array_key_exists(strtoupper($code), $countries)) {
                throw new ValidationException("No valid country code given");
            }
            return $code;
        } else if($required) {
            throw new ValidationException("No $field given, but $field is required");
        }
    }

    /**
     * @param $data
     * @param $field
     * @param array $options
     * @return string
     * @throws ValidationException
     */
    public static function validate_email($data, $field, $options=[]) {
        $options = array_merge([
            'required' => true
        ], $options);
        $required = $options['required'];
        if(isset($data[$field]) && is_string($data[$field])) {
            $email = $data[$field];
            if(Email::is_valid_address($email) === 0) {
                throw new ValidationException("No valid email given");
            }
            return $email;
        } else if($required) {
            throw new ValidationException("No $field given, but $field is required");
        }
    }
}