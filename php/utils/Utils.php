<?php

namespace utils;

/**
 * class Utils
 */
class Utils {

    const CONTAINS = 0;
    const STARTS_FROM = 1;
    const EQUALS = 2;

    /**
     * escape string
     * @param type $regexp_str
     * @return type
     */
    public static function escape($regexp_str) {
        //. + ? [ ] ( ) { } = ! < > | : -
        $formed_string = str_replace(
                array('.', '+', '?', '[', ']', '(', ')', '{', '}', '=', '!', '<', '>', '|', ':', '-'), array('\.', '\+', '\?', '\[', '\]', '\(', '\)', '\{', '\}', '\=', '\!', '\<', '\>', '\|', '\:', '\-'), $regexp_str
        );

        return $formed_string;
    }

    /**
     * Check for empty string value
     * @param type $val
     * @return type NULL or "not empty" string
     */
    public static function formatValue($val) {
        $check_val = trim($val);
        if ($check_val === '') {
            return null;
        } else {
            return $check_val;
        }
    }

    /**
     * Check permission
     * @param type $userRole
     * @return boolean
     */
    public static function checkPermission($userRole) {
        if (isset($_SESSION['usr_id']) &&
                isset($_SESSION['usr_role']) &&
                intval($_SESSION['usr_role']) == $userRole) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Escape special chars
     * @param type $str
     * @return type String - JSON encoded string
     */
    public static function json_string_encode($str) {
        $from = array('"');    // Array of values to replace
        $to = array('\\"');    // Array of values to replace with
        // Replace the string passed
        return str_replace($from, $to, $str);
    }

    /**
     * Create regular expression 
     * @param type $str
     * @param type $searchType
     * @return string
     */
    public static function createRegExp($str, $searchType) {
        $regExp = '';

        switch ($searchType) {
            case self::CONTAINS:
                $regExp = '^.*' . self::escape($str) . '.*$';
                if ($regExp === '^.*.*$') {
                    $regExp = '^.*$';
                }
                break;
            case self::STARTS_FROM:
                $regExp = '^' . self::escape($str) . '.*$';
                break;
            case self::EQUALS:
                $regExp = '^' . self::escape($str) . '$';
                if ($regExp === '^$') {
                    $regExp = '^.*$';
                }
                break;
            default:
                $regExp = '^.*' . self::escape($str) . '.*$';
                if ($regExp === '^.*.*$') {
                    $regExp = '^.*$';
                }
                break;
        }

        return $regExp;
    }
    
    /**
     * for checking return value of filter_input() method
     * @param type $postValue
     * @return boolean
     */
    public static function postValueIsValid($postValue){
        if( $postValue === false || is_null($postValue) ){
            return false;
        }
        else {
            return true;
        }
    }

}
