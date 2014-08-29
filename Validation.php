<?php

/**
 * Validation
 *
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
     * @return bool
     */
    public static function execute(array $data, array $filter = array())
    {
        self::$_data = $data;

        count($filter) && self::registerFilter($filter);

        foreach (self::$filter as $detail) {

            // var_dump($detail);

            $field = array_shift($detail); //First item as field
            $rules = explode(',', array_shift($detail)); //Second item as rules
            //..
            $error = (string)array_pop($detail); //Last item as error message

            if (isset($data[$field])) {
                $result = false;
                foreach ($rules as $rule_type) {
                    if ($result = self::validate($rule_type, $detail + array(), $data[$field])) {
                        continue;
                    } else {
                        $result = false;
                        break;
                    }
                }

                if ($result) {
                    continue;
                } else {
                    //var_dump($error);
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
     * @return bool|mixed
     */
    public static function validate($rule_type, $matcher, $data)
    {

        $third = array_shift($matcher); //Third item as params1

        switch (strtolower($rule_type)) {
            case 'regexp':
                return self::regexpMatcher($data, $third);
                break;
            case 'ip':
                return self::ipMatcher($data);
                break;
            case 'email':
                return self::emailMatcher($data);
                break;
            case 'url':
                return self::urlMatcher($data);
                break;
            case 'int':
                return self::intMatcher($data);
                break;
            case 'float':
                return self::floatMatcher($data);
                break;
            case 'array':
                return self::arrayMatcher($data);
                break;
            case 'number':
                return self::numberMatcher($data);
                break;
            case 'lt':
                return self::ltMatcher($data, $third);
                break;
            case 'elt':
                return self::eltMatcher($data, $third);
                break;
            case 'gt':
                return self::gtMatcher($data, $third);
                break;
            case 'egt':
                return self::egtMatcher($data, $third);
                break;
            case 'eq':
                return self::eqMatcher($data, $third);
                break;
            case 'neq':
                return self::neqMatcher($data, $third);
                break;
            case 'in':
                return self::inMatcher($data, $third);
                break;
            case 'required':
                return !is_null($data);
                break;
            case 'callback':
                return self::callbackMatcher($data, $third);
                break;
            case 'inner_eq':
                $filed_data = & self::$_data[$third];
                return $data == $filed_data;
            case 'inner_neq':
                $filed_data = & self::$_data[$third];
                return $data != $filed_data;
            default:
                return false;
        }

    }


    /**
     * @param $data
     * @param $pattern
     * @return mixed
     */
    public static function regexpMatcher($data, $pattern)
    {
        return filter_var($data, FILTER_VALIDATE_REGEXP, array(
            'options' => array(
                'regexp' => "/^{$pattern}$/i"
            )
        ));
    }


    /**
     * Validate IP address
     *
     * @param $data
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
     * @return bool
     */
    public static function eqMatcher($data, $target)
    {
        return $data >= $target;
    }

    /**
     * Validate not equal
     *
     * @param $data
     * @param $target
     * @return bool
     */
    public static function neqMatcher($data, $target)
    {
        return $data >= $target;
    }

    /**
     * Validate in section
     *
     * @param $data
     * @param array $target
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
     * @return null
     */
    public static function getField($field)
    {
        return isset(self::$_data[$field]) ? self::$_data[$field] : NULL;
    }


    /**
     * 获取校验错误信息
     *
     * @param null $filed
     * @return array
     */
    public static function error($filed = NULL)
    {
        if (is_string($filed)) {
            $error = & self::$error[$filed];

            return $error;
        }

        return self::$error;
    }


    /**
     * 获取校验出现的第一条错误信息
     *
     * @param bool $intact
     * @return mixed
     */
    public static function firstError($intact = false)
    {
        if (count(self::$error)) {
            return  array_shift(self::$error);
        }

        return NULL;
    }

}
