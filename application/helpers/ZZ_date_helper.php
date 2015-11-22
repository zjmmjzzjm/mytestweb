<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * 日期时间格式化
 *
 * @param           $format             string          格式
 * @param           $ts                 integer         时间戳
 * @return                              string            
 */
if(!function_exists('datetime')) {
	function datetime($format = 'Y-m-d H:i:s', $ts = null)
	{
        if (empty($ts)) 
        {
            return date($format, time());
        }
        else 
        {
            $ts = intval($ts);
            return date($format, $ts);
        }
	}
}


// ------------------------------------------------------------------------

/**
 * 解析日期时间
 *
 * @param               $str            string              字符串
 * @param               $format         string              字符串
 * @return                              string|false            
 */
if(!function_exists('parse_datetime')) {
    function parse_datetime($str, $format = '%d-%d-%d %d:%d:%d') 
    {
        $str = strval($str);
        $format = strval($format);

        $ret = sscanf($str, $format);
        if (!empty($ret) && is_array($ret)) 
        {
            return $ret;
        }
        return false;
    }
}


// ------------------------------------------------------------------------

/**
 * 判断是否日期时间格式 Y-m-d H:i:s
 *
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('is_datetime')) {
    function is_datetime($str) 
    {
        $str = strval($str);
        $arr = parse_datetime($str, "%d-%d-%d %d:%d:%d");
        if (false === $arr) 
        {
            return false;
        }

        foreach ($arr as $key=>$val) 
        {
            if (is_null($val)) 
            {
                return false;
            }
        }
        if (6 === count($arr) && $arr[0] >= 1 && 
            $arr[1] >= 1 && $arr[1] <= 12 &&
            $arr[2] >= 1 && $arr[2] <= 31 &&
            checkdate($arr[1], $arr[2], $arr[0]) && 
            $arr[3] >= 0 && $arr[3] <= 23 && 
            $arr[4] >= 0 && $arr[4] <= 59 && 
            $arr[5] >= 0 && $arr[5] <= 59) 
        {
            return true;
        }
        return false;

    }
}


// ------------------------------------------------------------------------

/**
 * 判断是否日期格式 Y-m-d
 *
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('is_date')) {
    function is_date($str) 
    {
        $str = strval($str);
        $arr = parse_datetime($str, "%d-%d-%d");
        if (false === $arr) 
        {
            return false;
        }

        foreach ($arr as $key=>$val) 
        {
            if (is_null($val)) 
            {
                return false;
            }
        }
        if (3 === count($arr) && $arr[0] >= 1 && 
            $arr[1] >= 1 && $arr[1] <= 12 &&
            $arr[2] >= 1 && $arr[2] <= 31 &&
            checkdate($arr[1], $arr[2], $arr[0])) 
        {
            return true;
        }
        return false;

    }
}

// ------------------------------------------------------------------------

/**
 * 日期格式 Y-m-d 转为时间戳
 *
 * @param           $str                string          字符串
 * @return                              integer|false                
 */
if(!function_exists('date2timestamp')) {
    function date2timestamp($str) 
    {
        $str = strval($str);
        $str = date2datetime($str);
        if (false === $str) 
        {
            return false;
        }

        $ts = datetime2timestamp($str);
        return $ts;
    }
}

// ------------------------------------------------------------------------

/**
 * 日期格式 Y-m-d H:i:s 转为时间戳
 *
 * @param           $str                string          字符串
 * @return                              integer|false            
 */
if(!function_exists('datetime2timestamp')) {
    function datetime2timestamp($str) 
    {
        return strtotime($str);
    }
}

// ------------------------------------------------------------------------

/**
 * 生成日期范围
 *
 * @param           $beginDate          string          日期格式 Y-m-d
 * @param           $endDate            string          日期格式 Y-m-d
 * @return                              array|false            
 */
if(!function_exists('day_range')) {
    function day_range($beginDate, $endDate) 
    {
        if (!is_date($beginDate) || !is_date($endDate)) 
        {
            return false;
        }

        $beginDate = date2timestamp($beginDate);
        $endDate = date2timestamp($endDate);
        if ($beginDate > $endDate) 
        {
            $tmp = $beginDate;
            $beginDate = $endDate;
            $endDate = $beginDate;
        }

        $ret = array(date('Y-m-d', $beginDate));
        while ($beginDate < $endDate)
        {
            $beginDate = strtotime('+1 day', $beginDate);
            array_push($ret, date('Y-m-d', $beginDate));
            
        }
        return $ret;
    }
}

// ------------------------------------------------------------------------

/**
 * 判断是否时间格式 H:i:s
 *
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('is_time')) {
    function is_time($str) 
    {
        $str = strval($str);
        $arr = parse_datetime($str, "%d:%d:%d");
        if (false === $arr) 
        {
            return false;
        }

        foreach ($arr as $key=>$val) 
        {
            if (is_null($val)) 
            {
                return false;
            }
        }
        if (3 === count($arr) && 
            $arr[0] >= 0 && $arr[0] <= 23 && 
            $arr[1] >= 0 && $arr[1] <= 59 && 
            $arr[2] >= 0 && $arr[2] <= 59) 
        {
            return true;
        }
        return false;

    }
}

// ------------------------------------------------------------------------

/**
 * 日期格式 Y-m-d 转为 日期时间格式 Y-m-d H:i:s
 *
 * @param           $str                string          字符串
 * @return                              string|bool            
 */
if(!function_exists('date2datetime')) {
    function date2datetime($str) 
    {
        $str = strval($str);
        if (!is_date($str)) 
        {
            return false;
        }
        $arr = parse_datetime($str, "%d-%d-%d");
        if (false === $arr) 
        {
            return false;
        }
        $arr = array_pad($arr, 6, null);

        foreach ($arr as $key=>$val) 
        {
            if (is_null($val)) 
            {
                switch ($key) 
                {
                    case 0:
                        $arr[$key] = 1970;
                        break;
                    case 1:
                    case 2:
                        $arr[$key] = 1;
                        break;
                    case 3:
                    case 4:
                    case 5:
                        $arr[$key] = 0;
                        break;
                    default:
                        break;
                }
            }
        }
        if (6 === count($arr) && $arr[0] >= 1 && 
            $arr[1] >= 1 && $arr[1] <= 12 &&
            $arr[2] >= 1 && $arr[2] <= 31 &&
            checkdate($arr[1], $arr[2], $arr[0]) && 
            $arr[3] >= 0 && $arr[3] <= 23 && 
            $arr[4] >= 0 && $arr[4] <= 59 && 
            $arr[5] >= 0 && $arr[5] <= 59) 
        {
            $str = sprintf("%d-%02d-%02d %02d:%02d:%02d", 
                            $arr[0], $arr[1], $arr[2], $arr[3], $arr[4], $arr[5]);
            return $str;
        }
        return false;

    }
}

// ------------------------------------------------------------------------

/**
 * 时间格式 H:i:s 转为 日期时间格式 Y-m-d H:i:s
 *
 * @param           $str                string          字符串
 * @return                              string|bool            
 */
if(!function_exists('time2datetime')) {
    function time2datetime($str) 
    {
        $str = strval($str);
        if (!is_time($str)) 
        {
            return false;
        }
        $arr = parse_datetime($str, "%d:%d:%d");
        if (false === $arr) 
        {
            return false;
        }
        $arr = array_pad($arr, -6, null);

        foreach ($arr as $key=>$val) 
        {
            if (is_null($val)) 
            {
                switch ($key) 
                {
                    case 0:
                        $arr[$key] = 1970;
                        break;
                    case 1:
                    case 2:
                        $arr[$key] = 1;
                        break;
                    case 3:
                    case 4:
                    case 5:
                        $arr[$key] = 0;
                        break;
                    default:
                        break;
                }
            }
        }
        if (6 === count($arr) && $arr[0] >= 1 && 
            $arr[1] >= 1 && $arr[1] <= 12 &&
            $arr[2] >= 1 && $arr[2] <= 31 &&
            checkdate($arr[1], $arr[2], $arr[0]) && 
            $arr[3] >= 0 && $arr[3] <= 23 && 
            $arr[4] >= 0 && $arr[4] <= 59 && 
            $arr[5] >= 0 && $arr[5] <= 59) 
        {
            $str = sprintf("%d-%02d-%02d %02d:%02d:%02d", 
                            $arr[0], $arr[1], $arr[2], $arr[3], $arr[4], $arr[5]);
            return $str;
        }
        return false;

    }
}

// ------------------------------------------------------------------------

/**
 * 转为时间格式 H:i:s
 *
 * @param           $str                string          字符串
 * @return                              string|bool            
 */
if(!function_exists('to_time')) {
    function to_time($str) 
    {
        $str = strval($str);
        $arr = parse_datetime($str, "%d:%d:%d");
        if (false === $arr) 
        {
            return false;
        }
        $arr = array_pad($arr, 3, null);

        foreach ($arr as $key=>$val) 
        {
            if (is_null($val)) 
            {
                switch ($key) 
                {
                    case 0:
                    case 1:
                    case 2:
                        $arr[$key] = 0;
                        break;
                    default:
                        break;
                }
            }
        }
        if (3 === count($arr) && 
            $arr[0] >= 0 && $arr[0] <= 23 && 
            $arr[1] >= 0 && $arr[1] <= 59 && 
            $arr[2] >= 0 && $arr[2] <= 59) 
        {
            return sprintf("%02d:%02d:%02d", $arr[0], $arr[1], $arr[2]);
        }
        return false;

    }
}

// ------------------------------------------------------------------------

/**
 * 转为日期格式 Y-m-d
 *
 * @param           $str                string          字符串
 * @return                              string|bool            
 */
if(!function_exists('to_date')) {
    function to_date($str) 
    {
        $str = strval($str);
        $arr = parse_datetime($str, "%d-%d-%d");
        if (false === $arr) 
        {
            return false;
        }
        $arr = array_pad($arr, 3, null);

        foreach ($arr as $key=>$val) 
        {
            if (is_null($val)) 
            {
                switch ($key) 
                {
                    case 0:
                        $arr[$key] = 1970;
                        break;
                    case 1:
                    case 2:
                        $arr[$key] = 1;
                        break;
                    default:
                        break;
                }
            }
        }
        if (3 === count($arr) && $arr[0] >= 1 && 
            $arr[1] >= 1 && $arr[1] <= 12 &&
            $arr[2] >= 1 && $arr[2] <= 31 &&
            checkdate($arr[1], $arr[2], $arr[0])) 
        {
            return sprintf("%d-%02d-%02d", $arr[0], $arr[1], $arr[2]);
        }
        return false;

    }
}

// ------------------------------------------------------------------------

/**
 * 某天开始时间戳
 *
 * @param           $ts     string|integer      时间戳
 * @return                  integer|false            
 */
if(!function_exists('day_begin')) {
    function day_begin($ts) 
    {
        if (!is_numeric($ts)) 
        {
            return false;
        }
        $arr = getdate($ts);
        if (!$arr) 
        {
            return false;
        }
        else 
        {
            return mktime(0, 0, 0, $arr['mon'], $arr['mday'], $arr['year']);
        }
    }
}

// ------------------------------------------------------------------------

/**
 * 某天结束时间戳
 *
 * @param           $ts     string|integer      时间戳
 * @return                  integer|false            
 */
if(!function_exists('day_end')) {
    function day_end($ts) 
    {
        if (!is_numeric($ts)) 
        {
            return false;
        }
        $arr = getdate($ts);
        if (!$arr) 
        {
            return false;
        }
        else 
        {
            return mktime(23, 59, 59, $arr['mon'], $arr['mday'], $arr['year']);
        }
    }
}

// ------------------------------------------------------------------------

/**
 * 根据参数获取星期几（数字、英文、中文字符串）
 *
 * @param           $str        string|integer      星期
 * @return          false|array
 */
if(!function_exists('week_day')) {
    function week_day($str) 
    {
        $wday = strtolower(strval($str));
        switch ($wday) 
        {
            case '6':
            case 'sat':
            case 'saturday':
            case '六':
            case '星期六':
                $wday = array('6', 'SAT', 'Saturday', '六', '星期六');
                break;
            case '5':
            case 'fri':
            case 'friday':
            case '六':
            case '星期六':
                $wday = array('5', 'FRI', 'Friday', '五', '星期五');
                break;
            case '4':
            case 'thu':
            case 'thursday':
            case '六':
            case '星期六':
                $wday = array('4', 'THU', 'Thursday', '四', '星期四');
                break;
            case '3':
            case 'wed':
            case 'wednesday':
            case '六':
            case '星期六':
                $wday = array('3', 'WED', 'Wednesday', '三', '星期三');
                break;
            case '2':
            case 'tue':
            case 'tuesday':
            case '六':
            case '星期六':
                $wday = array('2', 'TUE', 'Tuesday', '二', '星期二');
                break;
            case '1':
            case 'mon':
            case 'monday':
            case '六':
            case '星期六':
                $wday = array('1', 'MON', 'Monday', '一', '星期一');
                break;
            case '0':
            case 'sun':
            case 'sunday':
            case '日':
            case '星期日':
            case '星期天':
                $wday = array('0', 'SUN', 'Sunday', '日', '星期日');
                break;
            default:
                return false;
                break;
        }
        return $wday;
    }
}


// ------------------------------------------------------------------------

/**
 * 从星期几起算一周开始的时间戳
 *
 * @param           $ts     string|integer      时间戳
 * @param           $wday   string|integer      0（星期天）到 6（星期六）及其英文
 * @return                  integer|false            
 */
if(!function_exists('latest_week_begin')) {
    function latest_week_begin($ts, $wday = null) 
    {
        $from = strtotime('-7 days', $ts);
        if (false === $from || -1 === $from) 
        {
            return false;
        }
        $arr = getdate($from);
        if (!$arr) 
        {
            return false;
        }

        if (!is_null($wday)) 
        {
            $wday = week_day($wday);
            if (false === $wday) 
            {
                return false;
            }
        }
        else 
        {
            $wday = array($arr['wday']);
        }

        if ($arr['wday'] == $wday[0]) 
        {
            return mktime(0, 0, 0, $arr['mon'], $arr['mday'], $arr['year']);
        }
        else 
        {
            if ($wday[0] < $arr['wday']) 
            {
                $offset = intval($arr['wday']) - intval($wday[0]);
                $begin = strtotime("-{$offset} days", $from);
            }
            else 
            {
                $offset = 6 - (intval($wday[0]) - intval($arr['wday'])) + 1;
                $begin = strtotime("-{$offset} days", $from);
            }

            if (!$begin) 
            {
                return false;
            }
            $arr = getdate($begin);
            if (!$arr) 
            {
                return false;
            }
        }
        return mktime(0, 0, 0, $arr['mon'], $arr['mday'], $arr['year']);
    }
}

// ------------------------------------------------------------------------

/**
 * 从星期几起算一周结束的时间戳
 *
 * @param           $ts     string|integer      时间戳
 * @param           $wday   string|integer      0（星期天）到 6（星期六）及其英文
 * @return                  integer|false            
 */
if(!function_exists('latest_week_end')) {
    function latest_week_end($ts, $wday = null) 
    {
        $from = strtotime('-7 days', $ts);
        if (false === $from || -1 === $from) 
        {
            return false;
        }
        $arr = getdate($from);
        if (!$arr) 
        {
            return false;
        }

        if (!is_null($wday)) 
        {
            $wday = week_day($wday);
            if (false === $wday) 
            {
                return false;
            }
        }
        else 
        {
            $wday = array($arr['wday']);
        }

        if ($arr['wday'] == $wday[0]) 
        {
            $from = strtotime('-1 days', $ts);
            if (false === $from || -1 === $from) 
            {
                return false;
            }
            $arr = getdate($from);
            if (!$arr) 
            {
                return false;
            }
        }
        else 
        {
            if ($wday[0] < $arr['wday']) 
            {
                $offset = 6 - (intval($arr['wday']) - intval($wday[0]));
                $end = strtotime("+{$offset} days", $from);
            }
            else 
            {
                $offset = intval($wday[0]) - intval($arr['wday']) - 1;
                $end = strtotime("+{$offset} days", $from);
            }

            if (!$end) 
            {
                return false;
            }
            $arr = getdate($end);
            if (!$arr) 
            {
                return false;
            }
        }
        return mktime(23, 59, 59, $arr['mon'], $arr['mday'], $arr['year']);
    }
}

// ------------------------------------------------------------------------

/**
 * 某月开始时间戳
 *
 * @param           $ts         string|integer      时间戳
 * @return                      integer|false    
 */
if(!function_exists('month_begin')) {
    function month_begin($ts) 
    {
        if (!is_numeric($ts)) 
        {
            return false;
        }

        $arr = getdate($ts);
        if (!$arr) 
        {
            return false;
        }
        else 
        {
            return mktime(0, 0, 0, $arr['mon'], 1, $arr['year']);
        }
    }
}

// ------------------------------------------------------------------------

/**
 * 某月结束时间戳
 *
 * @param           $ts         string|integer      时间戳
 * @return                      integer|false                
 */
if(!function_exists('month_end')) {
    function month_end($ts) 
    {
        if (!is_numeric($ts)) 
        {
            return false;
        }

        $arr = getdate($ts);
        if (!$arr) 
        {
            return false;
        }
        else 
        {
            $days = days_in_month($arr['mon'], $arr['year']);
            return mktime(23, 59, 59, $arr['mon'], $days, $arr['year']);
        }
    }
}


// ------------------------------------------------------------------------

/**
 * 某年开始时间戳
 *
 * @param           $ts         string|integer      时间戳
 * @return                      integer|false    
 */
if(!function_exists('year_begin')) {
    function year_begin($ts) 
    {
        if (!is_numeric($ts)) 
        {
            return false;
        }

        $arr = getdate($ts);
        if (!$arr) 
        {
            return false;
        }
        else 
        {
            return mktime(0, 0, 0, 1, 1, $arr['year']);
        }
    }
}

// ------------------------------------------------------------------------

/**
 * 某年结束时间戳
 *
 * @param           $ts         string|integer      时间戳
 * @return                      integer|false                
 */
if(!function_exists('year_end')) {
    function year_end($ts) 
    {
        if (!is_numeric($ts)) 
        {
            return false;
        }

        $arr = getdate($ts);
        if (!$arr) 
        {
            return false;
        }
        else 
        {
            return mktime(23, 59, 59, 12, 31, $arr['year']);
        }
    }
}

// ------------------------------------------------------------------------

/**
 * 该周开始的时间戳
 *
 * @param       $ts             string|integer      时间戳
 * @param       $day_offset     string|integer      0（星期天）到 6（星期六）
 * @return                      integer|false           
 */
if(!function_exists('week_begin')) {
    function week_begin($ts, $day_offset = 0) 
    {
        if (!is_numeric($ts) || !is_integer($day_offset)) 
        {
            return false;
        }

        $arr = getdate($ts);
        if (!$arr) 
        {
            return false;
        }
        else 
        {
            $offset = 0 - $arr['wday'] + $day_offset;
            $ts = strtotime($offset . " days", $ts);
            $arr = getdate($ts);
            if (!$arr) 
            {
                return false;
            }
            else 
            {
                return mktime(0, 0, 0, $arr['mon'], $arr['mday'], $arr['year']);
            }
        }
    }
}

// ------------------------------------------------------------------------

/**
 * 该周结束的时间戳
 *
 * @param       $ts             string|integer      时间戳
 * @param       $day_offset     string|integer      0（星期天）到 6（星期六）
 * @return                      integer|false            
 */
if(!function_exists('week_end')) {
    function week_end($ts, $day_offset = 0) 
    {
        if (!is_numeric($ts) || !is_integer($day_offset)) 
        {
            return false;
        }

        $arr = getdate($ts);
        if (!$arr) 
        {
            return false;
        }
        else 
        {
            $offset = 6 - $arr['wday'] + $day_offset;
            $ts = strtotime($offset . " days", $ts);
            $arr = getdate($ts);
            if (!$arr) 
            {
                return false;
            }
            else 
            {
                return mktime(23, 59, 59, $arr['mon'], $arr['mday'], $arr['year']);
            }
        }
    }
}


// ------------------------------------------------------------------------

/**
 * 小时开始的时间戳
 *
 * @param       $ts             string|integer      时间戳
 * @return                      integer|false            
 */
if(!function_exists('hour_begin')) {
    function hour_begin($ts) 
    {
        if (!is_numeric($ts)) 
        {
            return false;
        }
        else 
        {
            $arr = getdate($ts);
            if (!$arr) 
            {
                return false;
            }
            else 
            {
                return mktime($arr['hours'], 0, 0, $arr['mon'], $arr['mday'], $arr['year']);
            }
        }
    }
}

// ------------------------------------------------------------------------

/**
 * 小时结束的时间戳
 *
 * @param       $ts             string|integer      时间戳
 * @return                      integer|false            
 */
if(!function_exists('hour_end')) {
    function hour_end($ts) 
    {
        if (!is_numeric($ts)) 
        {
            return false;
        }
        else 
        {
            $arr = getdate($ts);
            if (!$arr) 
            {
                return false;
            }
            else 
            {
                return mktime($arr['hours'], 59, 59, $arr['mon'], $arr['mday'], $arr['year']);
            }
        }
    }
}

// ------------------------------------------------------------------------

/**
 * 切分日期
 * @param       string      $date           如：20080303
 * @return      string                      如：2008-03-03
 */
if(!function_exists('change_date')) {
    function change_date($date)
    {
        if(!preg_match("/([0-9]{4})([0-9]{2})([0-9]{2})/", $date, $match))
        {
            return "";
        }
        return "$match[1]-$match[2]-$match[3]";
    }
}

// ------------------------------------------------------------------------

/**
 * 合并日期
 * @param       string      $date           如：2008-03-03
 * @return      string                      如：20080303
 */
if(!function_exists('merge_date')) {
    function merge_date($date)
    {
        if(!preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $date, $match))
        {
            return "";
        }
        return "$match[1]$match[2]$match[3]";
    }
}

// ------------------------------------------------------------------------

/**
 * 计算明天的日期 
 *
 * @param       $ts         integer         时间戳
 * @param       $format     string          格式化字符串
 * @return      string                      明天日期
 */
if(!function_exists('next_day')) {
    function next_day($ts = '', $format = 'Y-m-d')
    {
        if (empty($ts)) 
        {
            $ts = time();
        }
        $ts = strtotime('+1 day', $ts);
        return date($format, $ts);
    }
}

// ------------------------------------------------------------------------

/**
 * 计算昨天的日期 
 *
 * @param       $ts         integer         时间戳
 * @param       $format     string          格式化字符串
 * @return      string                      昨天日期
 */
if(!function_exists('previous_day')) {
    function previous_day($ts = '', $format = 'Y-m-d')
    {
        if (empty($ts)) 
        {
            $ts = time();
        }
        $ts = strtotime('-1 day', $ts);
        return date($format, $ts);
    }
}

// ------------------------------------------------------------------------

/**
 * 计算两个日期的相差的天数
 *
 * @param       $startday       string          起始日期
 * @param       $endday         string          结束日期
 * $return      $days
 */
if(!function_exists('daydiff')) {
    function daydiff($startday,$endday)
    {
        $CI = & get_instance();
        $CI->load->helper('date');
        if(!is_date($startday) || !is_date($endday))
        {
            return false;
        }

        list($cursY, $cursM, $cursD) = explode("-", $startday);
        list($cureY, $cureM, $cureD) = explode("-", $endday);
        $starttime = mktime(0, 0, 0, $cursM, $cursD, $cursY);
        $endtime = mktime(0, 0, 0, $cureM, $cureD, $cureY);
        $sseconds = date("U", $starttime);
        $eseconds = date("U", $endtime);
        $distance = $eseconds - $sseconds;
        $days = intval(ceil($distance/(3600*24)));
        return $days;
    }
}

// ------------------------------------------------------------------------

/**
 * Timezone 映射表
 *
 * @return						array
 */
if (!function_exists('timezone_map'))
{
	function timezone_map()
	{
		$CI =& get_instance();
		$CI->lang->load('date');

		$arr = array();
		foreach (timezones() as $key => $val)
		{
			$arr[strval($val)] = $CI->lang->line($key);
		}
		return $arr;
	}
}

// ------------------------------------------------------------------------

/* End of file date_helper.php */
/* Location: ./system/helpers/date_helper.php */