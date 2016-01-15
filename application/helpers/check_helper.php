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
 * 校验是否为IPV6
 * FFFF::1111
 *
 * @param           $ip                 string          IPV6
 * @return                              bool            
 */
if(!function_exists('check_ipv6')) {
	function check_ipv6($ip) 
	{
		// 8 groups, separated by :
		// 0-ffff per group
		// one set of consecutive 0 groups can be collapsed to ::

		$groups = 8;
		$collapsed = FALSE;

		$chunks = array_filter(
			preg_split('/(:{1,2})/', $ip, NULL, PREG_SPLIT_DELIM_CAPTURE)
		);

		// Rule out easy nonsense
		if (current($chunks) == ':' OR end($chunks) == ':')
		{
			return FALSE;
		}

		// PHP supports IPv4-mapped IPv6 addresses, so we'll expect those as well
		if (strpos(end($chunks), '.') !== FALSE)
		{
			$ipv4 = array_pop($chunks);

			if ( ! check_ipv4($ipv4))
			{
				return FALSE;
			}

			$groups--;
		}

		while ($seg = array_pop($chunks))
		{
			if ($seg[0] == ':')
			{
				if (--$groups == 0)
				{
					return FALSE;	// too many groups
				}

				if (strlen($seg) > 2)
				{
					return FALSE;	// long separator
				}

				if ($seg == '::')
				{
					if ($collapsed)
					{
						return FALSE;	// multiple collapsed
					}

					$collapsed = TRUE;
				}
			}
			elseif (preg_match("/[^0-9a-f]/i", $seg) OR strlen($seg) > 4)
			{
				return FALSE; // invalid segment
			}
		}

		return $collapsed OR $groups == 1;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验是否为IPV4（非特殊限制的校验）
 * 192.168.0.1
 *
 * @param           $ip                 string          IPV4
 * @param           $strict             bool            严格比较，排除0开头的IP
 * @return                              bool            
 */
if(!function_exists('check_loose_ipv4')) {
	function check_loose_ipv4($ip, $strict = false)
	{
		return check_ipv4($ip, $strict);
	}
}

// ------------------------------------------------------------------------

/**
 * 校验是否为IPV4（限制性特殊校验）
 * 192.168.0.1
 *
 * @param           $ip                 string          IPV4
 * @param           $strict             bool            严格比较，排除0开头的IP
 * @return                              bool            
 */
if(!function_exists('check_ipv4')) {
	function check_ipv4($ip, $strict = false)
	{
		$strict = (bool)$strict;
		$ip_segments = explode('.', $ip);

		// Always 4 segments needed
		if (count($ip_segments) !== 4)
		{
			return FALSE;
		}
		// IP can not start with 0
		if (true === $strict && $ip_segments[0][0] == '0')
		{
			return FALSE;
		}

		// Check each segment
		foreach ($ip_segments as $segment)
		{
			// IP segments must be digits and can not be
			// longer than 3 digits or greater then 255
			if ($segment == '' OR 
				preg_match("/[^0-9]/", $segment) OR $segment > 255 OR 
				strlen($segment) > 3)
			{
				return FALSE;
			}
		}

		return TRUE;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验是否为IP
 *
 * @param           $ip                 string          IP地址
 * @param           $which              string          IP版本
 * @return                              bool            
 */
if(!function_exists('check_ip')) {
	function check_ip($ip, $which = '')
	{
		$which = strtolower($which);

		// First check if filter_var is available
		if (is_callable('filter_var'))
		{
			switch ($which) {
				case 'ipv4':
					$flag = FILTER_FLAG_IPV4;
					break;
				case 'ipv6':
					$flag = FILTER_FLAG_IPV6;
					break;
				default:
					$flag = '';
					break;
			}

			return (bool) filter_var($ip, FILTER_VALIDATE_IP, $flag);
		}

		if ($which !== 'ipv6' && $which !== 'ipv4')
		{
			if (strpos($ip, ':') !== FALSE)
			{
				$which = 'ipv6';
			}
			elseif (strpos($ip, '.') !== FALSE)
			{
				$which = 'ipv4';
			}
			else
			{
				return FALSE;
			}
		}

		if ('ipv4' === $which) 
		{
			return check_ipv4($ip);
		}
		else if ('ipv6' === $which) 
		{
			return check_ipv6($ip);
		}
		else 
		{
			return false;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 email address
 * send@domain.com,recv@domain.com
 *
 * @param           $address            string|array    邮箱
 * @param           $delimiter          string          定界符
 * @return                              bool            
 */
if ( ! function_exists('check_emails'))
{
	function check_emails($address, $delimiter = ',')
	{
		if (!is_array($address)) 
		{
			$CI = & get_instance();
			$CI->load->helper('string');
			$address = reduce_multiples($address, $delimiter);
			$address = explode($delimiter, $address);
		}

		foreach ($address as $key=>$val) 
		{
			$val = trim(str_replace(array('\r', '\n'), '', strval($val)), " ");
			if (!empty($val) && 
				!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $val))
			{
				return false;
			}
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 email address suffix
 * @domain.com
 *
 * @param           $address            string|array    邮箱后缀
 * @param           $delimiter          string          定界符
 * @return                              bool            
 */
if ( ! function_exists('check_emails_suffix'))
{
	function check_emails_suffix($address, $delimiter = ',')
	{
		if (!is_array($address)) 
		{
			$CI = & get_instance();
			$CI->load->helper('string');
			$address = reduce_multiples($address, $delimiter);
			$address = explode($delimiter, $address);
		}

		foreach ($address as $key=>$val) 
		{
			$val = trim(str_replace(array('\r', '\n'), '', strval($val)), " ");
			if (!empty($val) && 
				!preg_match("/^@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $val))
			{
				return false;
			}
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 日期时间格式 Y-m-d H:i:s
 *
 * @param           $str                string          日期时间
 * @param           $delimiter          string          定界符，默认为-
 * @return                              bool            
 */
if ( ! function_exists('check_datetime'))
{
	function check_datetime($str, $delimiter = '')
	{
		$CI = & get_instance();
		$CI->load->helper('date');

		$str = strval($str);
		$delimiter = strval($delimiter);
		if (!empty($delimiter)) 
		{
			$str = str_replace($delimiter, '-', $str);
		}
		return is_datetime($str);
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 日期格式 Y-m-d
 *
 * @param           $str                string          日期
 * @param           $delimiter          string          定界符，默认为-
 * @return                              bool            
 */
if ( ! function_exists('check_date'))
{
	function check_date($str, $delimiter = '')
	{
		$CI = & get_instance();
		$CI->load->helper('date');

		$str = strval($str);
		$delimiter = strval($delimiter);
		if (!empty($delimiter)) 
		{
			$str = str_replace($delimiter, '-', $str);
		}
		return is_date($str);
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 时间格式 H:i:s
 *
 * @param           $str                string          时间
 * @param           $second             bool            $str是否包括秒
 * @return                              bool            
 */
if ( ! function_exists('check_time'))
{
	function check_time($str, $second = true)
	{
		$CI = & get_instance();
		$CI->load->helper('date');

		$str = strval($str);
		$second = (bool)$second;
		if (!$second) 
		{
			$str .= ':00';
		}
		return is_time($str);
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 日期范围
 * 2012-09-01 在 2012-08-31和2012-09-03内
 *
 * @param           $date               string          日期
 * @param           $min                string          最小日期
 * @param           $max                string          最大日期
 * @return                              bool           
 */
if ( ! function_exists('check_date_between'))
{
	function check_date_between($date, $min, $max)
	{
		$CI = & get_instance();
		$CI->load->helper('date');
		$date = to_date($date);
		$min = to_date($min);
		$max = to_date($max);

		if (!check_date($date) || !check_date($min) || !check_date($max)) 
		{
			return false;
		}

		$date = date2datetime($date);
		$min = date2datetime($min);
		$max = date2datetime($max);

		if (!$date || !$min || !$max) 
		{
			return false;
		}

		return check_datetime_between($date, $min, $max);
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 时间范围
 * 12:00:00 在 00:05:04和20:14:13内
 *
 * @param           $time               string          时间
 * @param           $min                string          最小时间
 * @param           $max                string          最大时间
 * @return                              bool              
 */
if ( ! function_exists('check_time_between'))
{
	function check_time_between($time, $min, $max)
	{
		$CI = & get_instance();
		$CI->load->helper('date');
		$time = to_time($time);
		$min = to_time($min);
		$max = to_time($max);

		if (!check_time($time) || !check_time($min) || !check_time($max)) 
		{
			return false;
		}

		$time = time2datetime($time);
		$min = time2datetime($min);
		$max = time2datetime($max);

		if (!$time || !$min || !$max) 
		{
			return false;
		}

		return check_datetime_between($time, $min, $max);
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 日期时间范围
 * 2012-06-30 23:59:59 在 2012-01-01 00:00:00和 2012-12-31 23:59:59内 
 *
 * @param           $datetime           string          日期时间
 * @param           $min                string          最小日期时间
 * @param           $max                string          最大日期时间
 * @return                              bool            
 */
if ( ! function_exists('check_datetime_between'))
{
	function check_datetime_between($datetime, $min, $max)
	{
		if (!check_datetime($datetime) || !check_datetime($min) || !check_datetime($max)) 
		{
			return false;
		}

		$CI = & get_instance();
		$CI->load->helper('date');
		$datetime = parse_datetime($datetime);
		$min = parse_datetime($min);
		$max = parse_datetime($max);

		if (!$datetime || !$min || !$max) 
		{
			return false;
		}

		$datetime = mktime($datetime[3], 
						   $datetime[4], $datetime[5], $datetime[1], $datetime[2], $datetime[0]);
		$min = mktime($min[3], $min[4], $min[5], $min[1], $min[2], $min[0]);
		$max = mktime($max[3], $max[4], $max[5], $max[1], $max[2], $max[0]);
		if (false === $datetime || false === $min || false === $max) 
		{
			return false;
		}

		if ($datetime >= $min && $datetime <= $max) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 日期范围
 *
 * @param           $min                string           开始日期
 * @param           $max                string           结束日期
 * @return                              bool            
 */
if ( ! function_exists('check_date_range'))
{
	function check_date_range($min, $max)
	{
		$min = to_date($min);
		$max = to_date($max);
		if (false === $min || false === $max) //格式错误
		{
			return false;
		}
		return strcmp($min, $max) <= 0 ? true : false;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 时间范围
 *
 * @param           $min                string           开始时间
 * @param           $max                string           结束时间
 * @return                              bool            
 */
if ( ! function_exists('check_time_range'))
{
	function check_time_range($min, $max)
	{
		$min = to_time($min);
		$max = to_time($max);
		if (false === $min || false === $max) //格式错误
		{
			return false;
		}
		return strcmp($min, $max) <= 0 ? true : false;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 数值范围
 *
 * @param           $num                float           数值
 * @param           $min                float           最小数值
 * @param           $max                float           最大数值
 * @return                              bool            
 */
if ( ! function_exists('check_number_between'))
{
	function check_number_between($num, $min, $max)
	{
		if (false === filter_var($num, FILTER_VALIDATE_FLOAT) ||
			false === filter_var($min, FILTER_VALIDATE_FLOAT) ||
			false === filter_var($max, FILTER_VALIDATE_FLOAT)) 
		{
			return false;
		}

		$num = floatval($num);
		$min = floatval($min);
		$max = floatval($max);

		if ($num >= $min && $num <= $max) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 整数范围
 *
 * @param           $num                integer           数值
 * @param           $min                integer           最小数值
 * @param           $max                integer           最大数值
 * @return                              bool            
 */
if ( ! function_exists('check_integer_between'))
{
	function check_integer_between($num, $min, $max)
	{
		if (false === filter_var($num, FILTER_VALIDATE_INT) ||
			false === filter_var($min, FILTER_VALIDATE_INT) ||
			false === filter_var($max, FILTER_VALIDATE_INT)) 
		{
			return false;
		}

		$num = intval($num);
		$min = intval($min);
		$max = intval($max);

		return check_number_between($num, $min, $max);
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 无符号64位整数范围
 *
 * @param           $num                string           数值
 * @param           $min                string           最小数值
 * @param           $max                string           最大数值
 * @return                              bool            
 */
if ( ! function_exists('check_uint64_between'))
{
	function check_uint64_between($num, $min = '0', $max = '18446744073709551615')
	{
		if (!is_numeric($num) ||
			!is_numeric($min) ||
			!is_numeric($max)) 
		{
			return false;
		}

		$len = 20;
		$num = str_pad($num, $len, '0', STR_PAD_LEFT);
		$min = str_pad($min, $len, '0', STR_PAD_LEFT);
		$max = str_pad($max, $len, '0', STR_PAD_LEFT);

		if (strcmp($min, $num) <= 0 && strcmp($num, $max) <= 0 ) 
		{
			return true;
		}

		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 文件名称
 *
 * @param           $name               string          文件名
 * @param           $bad_chars          string          非法字符
 * @param           $length             integer         文件名长度
 * @return                              bool            
 */
if ( ! function_exists('check_filename'))
{
	function check_filename($name, $bad_chars = array("/","\\","|","*","?",":",'"',"<",">"), $length = 255)
	{
		$name = strval($name);
		if (empty($name) || mb_strlen($name) > $length) 
		{
			return false;
		}

		$name = str_replace($bad_chars, '', $name, $count);
		if ($count > 0) 
		{
			return false;
		}
		else 
		{
			return true;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 校验是否域名
 *
 * @param           $str                string          域名字符串
 * @return                              bool            
 */
if(!function_exists('check_domain')) {
	function check_domain($str) 
	{
		$str = strval($str);
		if (preg_match('/^[^\'"><&]{1,255}$/', $str)) //WAC标准：无特殊字符限制最大256个字符，js限制成255
		{
			return true;
		}
		return false;

	}
}

// ------------------------------------------------------------------------

/**
 * 校验是否域名
 *
 * @param           $str                string          域名字符串
 * @return                              bool            
 */
if(!function_exists('check_domain')) {
	function check_domain($str) 
	{
		$str = strval($str);
		if (preg_match('/[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?/', $str)) //RFC标准
		{
			return true;
		}
		return false;

	}
}

// ------------------------------------------------------------------------

/**
 * 校验 端口范围
 *
 * @param           $str                string          域名字符串
 * @param           $min                integer         最小端口号
 * @param           $max                integer         最大端口号
 * @return                              bool            
 */
if(!function_exists('check_port_between')) {
	function check_port_between($port, $min = 1, $max = 65535) 
	{
		if (false === filter_var($port, FILTER_VALIDATE_INT) ||
			false === filter_var($min, FILTER_VALIDATE_INT) ||
			false === filter_var($max, FILTER_VALIDATE_INT)) 
		{
			return false;
		}

		$port = intval($port);
		$min = intval($min);
		$max = intval($max);
		
		if ($port >= $min && $port <= $max) 
		{
			return true;
		}
		return false;

	}
}

// ------------------------------------------------------------------------

/**
 * 校验 协议号
 *
 * @param           $str                string          协议号
 * @param           $min                integer         最小协议号
 * @param           $max                integer         最大协议号
 * @return                              bool            
 */
if(!function_exists('check_protocol_between')) {
	function check_protocol_between($protocol, $min = 0, $max = 255) 
	{
		$protocol = intval($protocol);
		$min = intval($min);
		$max = intval($max);
		
		if ($protocol >= $min && $protocol <= $max) 
		{
			return true;
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 IP Range
 * 如(每行一个)：
 *      192.168.0.1
 *      192.168.10.0-200.200.10.254
 *      192.168.7.0/255.255.252.0
 *      192.168.125.0/24
 *      192.168.8.0|100 ---表示从该IP起的100个IP
 *
 * @param           $str                string          IP范围
 * @param           $mark               array|string    定界符(逗号区分多个，只支持-,|,/三种)
 * @param           $ipv                array|string    IP版本(4或6)
 * @param           $delimiter          string          换行符
 * @return                              bool            
 */
if(!function_exists('check_ip_range')) {
	function check_ip_range($str, $mark = '|,/,-', $ipv = array(4,6), $delimiter = "\n") 
	{
		$mark = (!is_array($mark)) ? unjoin(',', $mark) : $mark;
		$ipv = (!is_array($ipv)) ? array($ipv) : $ipv;
		$str = trim(strval($str));
		$CI = & get_instance();
		$CI->load->helper(array('network', 'array'));
		$arr = array_unique(array_trim(unjoin($delimiter, $str)));
		$tmp = array();
		foreach ($arr as $key=>$val) 
		{
			$val = trim($val);
			if (empty($val)) 
			{
				continue;
			}
			if (in_array('|', $mark) && strrpos($val, '|') > 0) 
			{
				$tmp = explode('|', $val);
				if (!(is_array($tmp) && 2 === count($tmp))) 
				{
					return false;
				}
				if ((in_array(4, $ipv) && check_ipv4($tmp[0]) && calc_ipv4_range($tmp[0], $tmp[1])) || 
					(in_array(6, $ipv) && check_ipv6($tmp[0]) && calc_ipv6_range($tmp[0], $tmp[1]))) //192.168.8.0|100
				{
					continue;
				}
				else 
				{
					return false;
				}
			}
			if (in_array('/', $mark) && strrpos($val, '/') > 0) 
			{
				$tmp = explode('/', $val);
				if (!(is_array($tmp) && 2 === count($tmp))) 
				{
					return false;
				}
				if ((in_array(4, $ipv) && strrpos($tmp[1], '.')) > 0 || 
					(in_array(6, $ipv) &&  strrpos($tmp[1], ':') > 0)) //192.168.7.0/255.255.252.0
				{
					if ((in_array(4, $ipv) && check_ipv4($tmp[0]) && 
						check_netmask($tmp[1]) && check_ipv4_host_full_0($tmp[0], $tmp[1])) || 
						(in_array(6, $ipv) && check_ipv6($tmp[0]) && check_netmask($tmp[1]))) 
					{
						continue;
					}
					else 
					{
						return false;
					}
				}
				else 
				{
					$subnet_to_mask = prefix2netmask($tmp[1], true);
					if ((in_array(4, $ipv) && check_ipv4($tmp[0]) && 
						$subnet_to_mask && check_ipv4_host_full_0($tmp[0],$subnet_to_mask)) || 
						(in_array(6, $ipv) && check_ipv6($tmp[0]) && prefix2netmask($tmp[1], false))) //192.168.125.0/24
					{
						continue;
					}
					else 
					{
						return false;
					}
				}
			}
			if (in_array('-', $mark) && strrpos($val, '-') > 0) 
			{
				$tmp = explode('-', $val);
				if (!(is_array($tmp) && 2 === count($tmp))) 
				{
					return false;
				}
				if ((in_array(4, $ipv) && check_ipv4($tmp[0]) && 
					check_ipv4($tmp[1]) && ip_compare($tmp[0], $tmp[1]) <= 0 ) || 
					(in_array(6, $ipv) && check_ipv6($tmp[0]) && 
					check_ipv6($tmp[1]) && ip_compare($tmp[0], $tmp[1]) <= 0 )) //192.168.10.0-200.200.10.254
				{
					continue;
				}
				else 
				{
					return false;
				}
			}
			if ((in_array(4, $ipv) && check_ipv4($val)) || 
				(in_array(6, $ipv) && check_ipv6($val))) //192.168.0.1
			{
				continue;
			}
			else 
			{
				return false;
			}
		}
		$str = implode($delimiter, $arr);
		return $str ? $str : true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 PORT Range
 * 如(每行一个)：
 *      0-15
 *      25
 *      46-100
 *
 * @param           $str                string          端口范围
 * @param           $mark               array|string    定界符(逗号区分多个，目前只支持-一种)
 * @param           $between			array			端口范围合法区间，默认1-65535
 * @param           $delimiter          string          换行符
 * @return                              bool            
 */
if(!function_exists('check_port_range')) {
	function check_port_range($str, $mark = '-', $between = array(1, 65535), $delimiter = "\n") 
	{
		if (!is_array($mark)) 
		{
			$mark = explode(',', $mark);
		}
		if (!is_array($between)) 
		{
			$between = array(1, 65535);
		}
		else 
		{
			$between = array_pad($between, 2, 65535);
		}
		$between = array_map('intval', $between);
		if ($between[0] > $between[1]) 
		{
			$tmp = $between[0];
			$between[0] = $between[1];
			$between[1] = $tmp;
		}
		$str = strval($str);
		$str = trim($str);
		$CI = & get_instance();
		$CI->load->helper(array('array'));
		$arr = unjoin($delimiter, $str);

		foreach ($arr as $key=>$val) 
		{
			if (false !== strpos($val, $mark[0])) 
			{
				$array = explode($mark[0], $val);
				foreach ($array as $k=>$v) 
				{
					if (false === filter_var($v, FILTER_VALIDATE_INT)) 
					{
						return false;
					}
				}
				$array = array_map('intval', $array);
				if ($array[0] > $array[1]) 
				{
					return false;
				}
				elseif ($array[0] < $between[0] || $array[1] > $between[1]) 
				{
					return false;
				}
			}
			else 
			{
				if (false === filter_var($val, FILTER_VALIDATE_INT)) 
				{
					return false;
				}
				$val = intval($val);
				if ($val < $between[0] || $val > $between[1]) 
				{
					return false;
				}
			}
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验字符串 是否匹配正则表达式
 *
 * @param           $str                string          字符串
 * @param           $length             string          字符串最大长度
 * @param           $regex              string          字符串匹配的正则
 * @return                              bool            
 */
if(!function_exists('check_string')) {
	function check_string($str, $length = 255, $regex = "/[-_0-9a-z ]+/i") 
	{
		$str = strval($str);
//		if (empty($str) || mb_strlen($str) > $length) //改为以字节算长度
		if (empty($str) || strlen($str) > $length) 
		{
			return false;
		}

		if ( ! preg_match($regex, $str))
		{
			return false;
		}

		return  true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验字符串长度
 *
 * @param           $str                string          字符串
 * @param           $min                string          字符串最小长度
 * @param           $max                string          字符串最大长度
 * @return                              bool            
 */
if(!function_exists('check_strlen')) {
	function check_strlen($str, $min = 1, $max = 255) 
	{
		$str = strval($str);
//		$len = mb_strlen($str); //改为以字节算长度
		$len = strlen($str);
		if ($len < $min) 
		{
			return false;
		}

		if ($len > $max)
		{
			return false;
		}

		return  true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验字符串 是否相同
 * 该函数必须调用两次(算一对)，比如检查两次输入的密码是否相同
 *
 * @param           $str                string          字符串
 * @param           $id                 string          检查的对应名称
 * @param           $strict             bool            是否严格比较
 * @return                              bool            
 */
if(!function_exists('check_match')) {
	function check_match($str, $id, $strict = true) 
	{
		$strict = (bool)$strict;
		static $last = null;
		if (!isset($last[$id])) //第一次调用该函数始终返回true 
		{
			$last[$id] = $str;
			return true;
		}
		else //第二次调用做检查
		{
			$tmp = $last[$id];
			unset($last[$id]);
			if ($strict) 
			{
				if ($tmp === $str) 
				{
					return true;
				}
				else 
				{
					return false;
				}
			}
			else 
			{
				if ($tmp == $str) 
				{
					return true;
				}
				else 
				{
					return false;
				}
			}            
		}
	}
}


// ------------------------------------------------------------------------

/**
 * 使用回调函数校验字符串 
 * 
 * @param           $str                string          字符串
 * @param           $fn                 string          方法名称
 * @return                              bool            
 */
if(!function_exists('check_callback')) {
	function check_callback($str, $fn) 
	{
		$str = strval($str);
		$fn = strval($fn);
		$args = func_get_args();
		$args = array_slice($args, 2);
		array_unshift($args, $str);
		$OBJ = & _get_validation_object();
		if (false === $OBJ) 
		{
			return false;
		}
		else 
		{
			return (bool)@call_user_func_array(array($OBJ, $fn), $args);
		}
	}
}


// ------------------------------------------------------------------------

/**
 * 检测含有中文的字符串 
 * 
 * 1. GBK (GB2312/GB18030)
 * \x00-\xff  GBK双字节编码范围
 * \x20-\x7f  ASCII
 * \xa1-\xff  中文
 * \x80-\xff  中文
 * 
 * 2. UTF-8 (Unicode)
 * \x4e00-\x9fa5 (中文，含有繁体) \x4e00-\x9fff (包括其他繁体) \xf900-\xfa2d (输入法打不出来的汉字)
 * \x3130-\x318F (韩文)
 * \xac00-\xd7a3 (韩文)
 * \x0800-\x4e00 (日文)
 * \x2e80-\x9fff (中日韩文)
 * 
 * 补充知识：
 * 2e80～33ffh：中日韩符号区。收容康熙字典部首、中日韩辅助部首、注音符号、日本假名、韩文音符，中日韩的符号、标点、带圈或带括符文数字、月份，以及日本的假名组合、单位、年号、月份、日期、时间等。
 * 3400～4dffh：中日韩认同表意文字扩充a区，总计收容6,582个中日韩汉字。
 * 4e00～9fffh：中日韩认同表意文字区，总计收容20,902个中日韩汉字。
 * a000～a4ffh：彝族文字区，收容中国南方彝族文字和字根。
 * ac00～d7ffh：韩文拼音组合字区，收容以韩文音符拼成的文字。
 * f900～faffh：中日韩兼容表意文字区，总计收容302个中日韩汉字。
 * fb00～fffdh：文字表现形式区，收容组合拉丁文字、希伯来文、阿拉伯文、中日韩直式标点、小符号、半角符号、全角符号等。
 * 
 * 
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('check_chinese')) {
	function check_chinese($str) 
	{
		$str = strval($str);
		if (preg_match("/[\x{4e00}-\x{9fa5}]+/iu", $str)) 
		{
			return true;
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 检测全角字符，匹配双字节字符(包括汉字在内)
 * 
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('check_double_byte')) {
	function check_double_byte($str) 
	{
		$str = strval($str);
		if (preg_match("/[\x80-\xff]+/", $str)) 
		{
			return true;
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 MAC地址
 *
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('check_mac')) {
	function check_mac($str)         
	{
		$str = strval($str);
		if (preg_match('/^[0-9a-f]{2}([-:]{0,1})[0-9a-f]{2}\1[0-9a-f]{2}\1[0-9a-f]{2}\1[0-9a-f]{2}\1[0-9a-f]{2}$/i', $str)) 
		{
			return true;
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 子网掩码
 *
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('check_netmask')) {
	function check_netmask($str)         
	{
		$rs = is_netmask($str, true);
		if ($rs) 
		{
			return true;
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 子网掩码是否0.0.0.0，是返回false
 *
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('check_netmask_full_0')) {
	function check_netmask_full_0($str)         
	{
		$str = trim($str);
		if ('0.0.0.0' === $str) 
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 子网掩码是否255.255.255.255，是返回false
 *
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('check_netmask_full_1')) {
	function check_netmask_full_1($str)         
	{
		$str = trim($str);
		if ('255.255.255.255' === $str) 
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 时区
 *
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('check_timezone')) {
	function check_timezone($str)         
	{
		$arr = timezone_map();
		if (isset($arr[$str])) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 判断升序或降序
 *
 * @param           $str                string          asc 或 desc
 * @return                              bool|string            
 */
if(!function_exists('check_direction')) {
	function check_direction($str) 
	{
		$str = strtolower(strval($str));
		$arr = array('asc', 'desc');

		if (in_array($str, $arr)) 
		{
			return true;
		}
		else 
		{
			return $arr[0];
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 检测数组含有几个元素
 * 
 * @param           $arr                array           数组
 * @param           $count              integer         整数
 * @return                              bool            
 */
if(!function_exists('check_count_limit')) {
	function check_count_limit(array $arr, $count) 
	{
		$count = intval($count);
		if ($count < count($arr)) 
		{
			return false;
		}
		else 
		{
			return true;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 检测国家码
 * 
 * @param           $str        string          国家英文缩写
 * @param           $arr        array           国家英文缩写数组
 * @return                      bool            
 */
if(!function_exists('check_country_code')) {
	function check_country_code($str, array $arr = array('cn', 'us', 'jp', 'kr')) 
	{
		$str = strtolower($str);
		if (in_array($str, $arr, true)) 
		{
			return true;
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 检查是否含有字符串
 * 如：'ABC' in ('ABC', '123') 是成立的
 * 
 * @param           $str                string          字符串
 * @param           $arr                string          字符串(以 , 分隔)
 * @return                              bool            
 */
if(!function_exists('check_in')) {
	function check_in($str, $arr = '') 
	{
		if (empty($arr)) 
		{
			$arr = array();
		}
		else 
		{
			$arr = unjoin(',', $arr);
		}

		if (in_array($str, $arr)) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 使用回调函数校验数组的所有元素
 * 
 * @param           $arr                array           数组
 * @param           $callback           string          函数名称
 * @return                              array            
 */
if(!function_exists('check_array')) {
	function check_array($arr, $callback) 
	{
		if (!is_array($arr)) 
		{
			$arr = $arr;
		}

		foreach ($arr as $key=>$val) 
		{
			$res = $callback($val);
			if (is_bool($res)) 
			{
				if (false === $res) 
				{
					return false;
				}
			}
			else 
			{
				$arr[$key] = $res;
			}
		}
		return $arr;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验网络范围
 * e.g: 有个网络范围 [192.168.1.1, 192.168.2.254]
 *      判断这些数据 192.168.1.100或192.168.1.200-192.168.2.100 是否在该区间
 *
 * @param           $str                string          IP： '192.168.1.100'或'192.168.1.200-192.168.2.100'
 * @param           $start              string          起始IP
 * @param           $end                string          结束IP
 * @return                              bool            
 */
if(!function_exists('check_network_range')) {
	function check_network_range($str, $start, $end) 
	{
		if (ip_compare($start, $end) > 0) 
		{
			return false;
		}

		$rg = interval($str);
		if (count($rg) > 1) 
		{
			if (ip_compare($rg[0], $rg[1]) > 0) 
			{
				return false;
			}

			if (ip_compare($rg[0], $start) < 0 || 
				ip_compare($rg[1], $end) > 0) 
			{
				return false;
			}
		}
		else 
		{
			if (ip_compare($rg[0], $start) < 0 || 
				ip_compare($rg[0], $end) > 0) 
			{
				return false;
			}
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验IPV4是否广播地址(x.x.x.255)，是返回false
 *
 * @param           $str                string          IP
 * @return                              bool            
 */
if(!function_exists('check_ipv4_broadcast')) {
	function check_ipv4_broadcast($str) 
	{
		$ip_segments = explode('.', $str);
		list($a, $b, $c, $d) = $ip_segments;
		if (255 == $d)
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验IPV4是否回环地址(127.x.x.x)，是返回false
 *
 * @param           $str                string          IP
 * @return                              bool            
 */
if(!function_exists('check_ipv4_loopback')) {
	function check_ipv4_loopback($str) 
	{
		$ip_segments = explode('.', $str);
		list($a, $b, $c, $d) = $ip_segments;
		if (127 == $a)
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验IPV4是否保留地址(2.x.x.x)，是返回false
 *
 * @param           $str                string          IP
 * @return                              bool            
 */
if(!function_exists('check_ipv4_reserved')) {
	function check_ipv4_reserved($str) 
	{
		$ip_segments = explode('.', $str);
		list($a, $b, $c, $d) = $ip_segments;
		if (2 == $a)
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验IPV4是否E类保留地址(240.0.0.0-255.255.255.255)，是返回false
 *
 * @param           $str                string          IP
 * @return                              bool            
 */
if(!function_exists('check_ipv4_e_addr')) {
	function check_ipv4_e_addr($str) 
	{
		$ip_segments = explode('.', $str);
		list($a, $b, $c, $d) = $ip_segments;
		if ($a >= 240)
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验IPV4是否组播地址(224.0.0.0-239.255.255.255)，是返回false
 *
 * @param           $str                string          IP
 * @return                              bool            
 */
if(!function_exists('check_ipv4_multicast_addr')) {
	function check_ipv4_multicast_addr($str) 
	{
		$ip_segments = explode('.', $str);
		list($a, $b, $c, $d) = $ip_segments;
		if ($a >= 224 && $a <= 239)
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验IPV4是否直接广播地址255.255.255.255，是返回false
 *
 * @param           $str                string          IP
 * @return                              bool            
 */
if(!function_exists('check_ipv4_full_1')) {
	function check_ipv4_full_1($str) 
	{
		$ip_segments = explode('.', $str);
		list($a, $b, $c, $d) = $ip_segments;
		if (255 == $a && 255 == $b && 255 == $c && 255 == $d)
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验IPV4是否0.0.0.0，是返回false
 *
 * @param           $str                string          IP
 * @return                              bool            
 */
if(!function_exists('check_ipv4_full_0')) {
	function check_ipv4_full_0($str) 
	{
		$ip_segments = explode('.', $str);
		list($a, $b, $c, $d) = $ip_segments;
		if (0 == $a && 0 == $b && 0 == $c && 0 == $d)
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验IPV4是否地址0.开头，是返回false
 *
 * @param           $str                string          IP
 * @return                              bool            
 */
if(!function_exists('check_ipv4_begin_0')) {
	function check_ipv4_begin_0($str) 
	{
		$ip_segments = explode('.', $str);
		list($a, $b, $c, $d) = $ip_segments;
		if (0 == $a)
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验IPV4是否地址.0结尾，是返回false
 *
 * @param           $str                string          IP
 * @return                              bool            
 */
if(!function_exists('check_ipv4_end_0')) {
	function check_ipv4_end_0($str) 
	{
		$ip_segments = explode('.', $str);
		list($a, $b, $c, $d) = $ip_segments;
		if (0 == $d)
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验IPV4是否地址.255结尾，是返回false
 *
 * @param           $str                string          IP
 * @return                              bool            
 */
if(!function_exists('check_ipv4_end_255')) {
	function check_ipv4_end_255($str) 
	{
		$ip_segments = explode('.', $str);
		list($a, $b, $c, $d) = $ip_segments;
		if (255 == $d)
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验IPV4是否地址0.x.x.x(排除0.0.0.0)，是返回false
 *
 * @param           $str                string          IP
 * @return                              bool            
 */
if(!function_exists('check_ipv4_exclude_begin_full_0')) {
	function check_ipv4_exclude_begin_full_0($str) 
	{
		$ip_segments = explode('.', $str);
		list($a, $b, $c, $d) = $ip_segments;
		if (0 == $a)
		{
			if (0 != $b || 0 != $c || 0 != $d) 
			{
				return false;
			}
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验IPV4是否地址x.x.x.0(排除0.0.0.0)，是返回false
 *
 * @param           $str                string          IP
 * @return                              bool            
 */
if(!function_exists('check_ipv4_exclude_end_full_0')) {
	function check_ipv4_exclude_end_full_0($str) 
	{
		$ip_segments = explode('.', $str);
		list($a, $b, $c, $d) = $ip_segments;
		if (0 == $d)
		{
			if (0 != $a || 0 != $b || 0 != $c) 
			{
				return false;
			}
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验IPV4是否地址x.x.x.255(排除255.255.255.255)，是返回false
 *
 * @param           $str                string          IP
 * @return                              bool            
 */
if(!function_exists('check_ipv4_exclude_end_full_1')) {
	function check_ipv4_exclude_end_full_1($str) 
	{
		$ip_segments = explode('.', $str);
		list($a, $b, $c, $d) = $ip_segments;
		if (255 == $d)
		{
			if (255 != $a || 255 != $b || 255 != $c) 
			{
				return false;
			}
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 主机位是否为1，否返回false
 *
 * @param           $str                string          IP
 * @param           $mask               string          子网掩码
 * @return                              bool            
 */
if(!function_exists('check_ipv4_host_full_1')) {
	function check_ipv4_host_full_1($str, $mask) 
	{
		if (false !== filter_var($mask, FILTER_VALIDATE_INT)) 
		{
			$mask = prefix2netmask($mask, true);
		}
		$ip = ip2bin($str);
		$mask = ip2bin($mask);
		$mask_invert = bin_invert($mask);
		return (($ip & $mask_invert) === $mask_invert);
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 主机位是否为0，否返回false
 *
 * @param           $str                string          IP
 * @param           $mask               string          子网掩码
 * @return                              bool            
 */
if(!function_exists('check_ipv4_host_full_0')) {
	function check_ipv4_host_full_0($str, $mask) 
	{
		if (false !== filter_var($mask, FILTER_VALIDATE_INT)) 
		{
			$mask = prefix2netmask($mask, true);
		}
		$ip = ip2bin($str);
		$mask = ip2bin($mask);
		$mask_invert = bin_invert($mask);
		return (($ip & $mask_invert) === str_repeat('0', 32));
	}
}

// ------------------------------------------------------------------------

/**
 * 校验IPV4与子网掩码组合，无效返回false
 *
 * @param           $str                string          IP
 * @param           $mask               string          子网掩码
 * @return                              bool            
 */
if(!function_exists('check_ipv4_mask')) {
	function check_ipv4_mask($ip, $mask) 
	{
		$ip = ip2bin($ip);
		if (false !== filter_var($mask, FILTER_VALIDATE_INT)) 
		{
			$mask = prefix2netmask($mask);
		}
		$mask = ip2bin($mask);
		$rs = $ip & $mask;
		if (str_repeat('0', 32) === $rs) 
		{
			return false;
		}
		else 
		{
			return true;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 00:00:00开头MAC地址，是返回false
 *
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('check_mac_begin_0x000000')) {
	function check_mac_begin_0x000000($str)         
	{
		$str = strval($str);
		if (preg_match('/^[0]{2}([-:]{0,1})[0]{2}\1[0]{2}.*/i', $str)) //00:00:00开头
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 [0-9a-f][13579bdf]:开头MAC地址，是返回false
 *
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('check_mac_begin_0x01')) {
	function check_mac_begin_0x01($str)         
	{
		$str = strval($str);
		if (preg_match('/^[0-9a-f][13579bdf]([-:]{0,1}).*/i', $str)) 
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 全1的MAC地址，是返回false
 *
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('check_mac_full_1')) {
	function check_mac_full_1($str)         
	{
		$str = strval($str);
		if (preg_match('/^[f]{2}([-:]{0,1})[f]{2}\1[f]{2}\1[f]{2}\1[f]{2}\1[f]{2}$/i', $str)) //全1
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 全0的MAC地址，是返回false
 *
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('check_mac_full_0')) {
	function check_mac_full_0($str)         
	{
		$str = strval($str);
		if (preg_match('/^[0]{2}([-:]{0,1})[0]{2}\1[0]{2}\1[0]{2}\1[0]{2}\1[0]{2}$/i', $str)) //全0
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 校验 URL地址
 *
 * @param           $str                string          字符串
 * @return                              string|bool            
 */
if(!function_exists('check_url')) {
	function check_url($str, $min = 1, $max = 2048)         
	{
		$scheme1 = substr($str, 0, 7);
		$scheme2 = substr($str, 0, 8);
		$scheme3 = substr($str, 0, 6);
		if (0 !== strcasecmp('http://', $scheme1) && 
			0 !== strcasecmp('https://', $scheme2) && 
			0 !== strcasecmp('ftp://', $scheme3)) 
		{
			$str = 'http://' . $str;
		}
		$b = filter_var($str, FILTER_VALIDATE_URL);
		if (!$b) 
		{
			$b = preg_match('/(((^https?)|(^ftp)):\/\/(([\-\w]+\.)+\w{2,3}|(\d{1,3}\.){3}\d{1,3})(\/[%\-\w]+(\.\w{2,})?)*(([\w\-\.\?\/\\+@&#;`~=%!]*)(\.\w{2,})?)*\/?)/iu', $str, $match);
		}
		$a = check_strlen($str, $min, $max);
		if ($a && $b) 
		{
			return remove_invisible_characters($str, true);
		}
		return false;
	}
}

// ------------------------------------------------------------------------




/* End of file check_helper.php */
/* Location: ./application/helpers/check_helper.php */