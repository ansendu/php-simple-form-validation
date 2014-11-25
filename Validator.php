<?php

/**
 * Validation
 * Author:Wudi <0x07de@gmail.com>
 * Date: 2014-07-13
 */
class Validator
{

    /**
     * Filter Rules
     *
     * @var array
     */
    private static $filter = array();

    /**
     * Multi error list
     *
     * @var array
     */
    private static $error = array();

    /**
     * @var
     */
    private static $_data;

    /**
     * @param array $filter
     */
    public static function registerFilter(array $filter = array())
    {
        self::$filter += $filter;
    }

    /**
     * Execure Validate
     *
     * @param array $data
     * @param array $filter
     *
     * @return bool
     */
    public static function execute(array $data, array $filter = array())
    {
        self::$_data = $data;

        $filter && self::registerFilter($filter);

        foreach (self::$filter as $detail) {

            //var_dump($detail);

            $field = &$detail[0];//First item as field
            $rules = &$detail[1];//Second item as rules
            $rules = explode(',', $rules);
            //..
            $error = $detail[count($detail) - 1];//Last item as error message

            $field_exists = &$data[$field];
            if ($field_exists) {
                $result = false;
                foreach ($rules as $rule_type) {
                    //Third item as params1
                    if (!$result = self::validate($rule_type, $detail[2], $data[$field])) {
                        $result = false;
                        break;
                    }
                }

                if (!$result) {
                    self::$error[$field] = $error;
                }
            } else {
                if (in_array('required', $rules)) {
                    self::$error[$field] = $error;
                }
            }
        }

        return !count(self::$error);
    }

    /**
     * Validate Filter
     *
     * @param $rule_type
     * @param $matcher
     * @param $data
     *
     * @return bool|mixed
     */
    public static function validate($rule_type, $match_args, $data)
    {
        $func = strtolower($rule_type) . "Matcher";//join
        if (method_exists(__CLASS__, $func)) {
            return self::$func($data, $match_args);
        }

        return false;
    }

    /**
     * @param $data
     *
     * @return bool
     */
    public static function requiredMatcher($data)
    {
        return !is_null($data);
    }

    /**
     * @param $data
     * @param $pattern
     *
     * @return bool
     */
    public static function regexpMatcher($data, $pattern)
    {
        return preg_match($pattern, $data);
    }

    /**
     * Validate IP address
     *
     * @param $data
     *
     * @return mixed
     */
    public static function ipMatcher($data)
    {
        return filter_var($data, FILTER_VALIDATE_IP);
    }

    /**
     * Validate Email
     *
     * @param $data
     *
     * @return mixed
     */
    public static function emailMatcher($data)
    {
        return filter_var($data, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate URL
     *
     * @param $data
     *
     * @return mixed
     */
    public static function urlMatcher($data)
    {
        return filter_var($data, FILTER_VALIDATE_URL);
    }

    /**
     * Validate Int Type
     *
     * @param $data
     *
     * @return bool
     */
    public static function intMatcher($data)
    {
        return is_int($data);
    }

    /**
     * Validate Float Type
     *
     * @param $data
     *
     * @return bool
     */
    public static function floatMatcher($data)
    {
        return is_float($data);
    }

    /**
     * Validate Array Type
     *
     * @param $data
     *
     * @return bool
     */
    public static function arrayMatcher($data)
    {
        return is_array($data);
    }

    /**
     * Validate Number
     *
     * @param $data
     *
     * @return bool
     */
    public static function numberMatcher($data)
    {
        return is_numeric($data);
    }

    /**
     * Validate less than
     *
     * @param $data
     * @param $target
     *
     * @return bool
     */
    public static function ltMatcher($data, $target)
    {
        return $data < $target;
    }

    /**
     * Validate less than and equal
     *
     * @param $data
     * @param $target
     *
     * @return bool
     */
    public static function eltMatcher($data, $target)
    {
        return $data <= $target;
    }

    /**
     * Validate greater than
     *
     * @param $data
     * @param $target
     *
     * @return bool
     */
    public static function gtMatcher($data, $target)
    {
        return $data > $target;
    }

    /**
     * Validate greater than and equal
     *
     * @param $data
     * @param $target
     *
     * @return bool
     */
    public static function egtMatcher($data, $target)
    {
        return $data >= $target;
    }

    /**
     * Validate equal
     *
     * @param $data
     * @param $target
     *
     * @return bool
     */
    public static function eqMatcher($data, $target)
    {
        return $data == $target;
    }

    /**
     * Validate not equal
     *
     * @param $data
     * @param $target
     *
     * @return bool
     */
    public static function neqMatcher($data, $target)
    {
        return $data != $target;
    }

    /**
     * Validate in section
     *
     * @param       $data
     * @param array $target
     *
     * @return bool
     */
    public static function inMatcher($data, array $target)
    {
        return in_array($data, $target);
    }

    /**
     * Custom callback function
     *
     * @param $data
     * @param $function
     *
     * @return bool|mixed
     */
    public static function callbackMatcher($data, $function)
    {
        if (is_callable($function)) {
            return call_user_func($function, $data);
        }

        return false;
    }

    /**
     * 获取字段数据
     *
     * @param $field
     *
     * @return null
     */
    public static function getField($field)
    {
        return isset(self::$_data[$field]) ? self::$_data[$field] :NULL;
    }

    /**
     * 获取校验错误信息
     *
     * @param null $filed
     *
     * @return array
     */
    public static function getAllError($filed = NULL)
    {
        if (is_string($filed)) {
            $error = &self::$error[$filed];

            return $error;
        }

        return self::$error;
    }

    /**
     * 获取校验出现的第一条错误信息
     *
     * @return mixed
     */
    public static function getError()
    {
        if (count(self::$error)) {
            return array_shift(self::$error);
        }

        return NULL;
    }

}
