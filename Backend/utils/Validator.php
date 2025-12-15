<?php

class Validator {

    public static function required($value) {
        return isset($value) && trim($value) !== '';
    }

    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function minLength($value, $length) {
        return strlen($value) >= $length;
    }

    public static function isNumber($value) {
        return is_numeric($value);
    }

    public static function positiveNumber($value) {
        return is_numeric($value) && $value > 0;
    }

    public static function phone($phone) {
        return preg_match('/^[0-9]{10,15}$/', $phone);
    }

    public static function inArray($value, $array) {
        return in_array($value, $array);
    }

    public static function dateTime($datetime) {
        return (bool) strtotime($datetime);
    }
}
