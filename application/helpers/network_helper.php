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
 * 判断是否域名
 *
 * @param           $str                string          域名字符串
 * @return                              bool            
 */
if(!function_exists('is_domain')) {
	function is_domain($str) {
		$str = strval($str);
		if (preg_match('/[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?/', $str)) 
		{
			return true;
		}
		return false;

	}
}

// ------------------------------------------------------------------------

/**
 * 校验是否为IPV6
 *
 * @param           $ip                 string          IPV6
 * @return                              bool            
 */
if(!function_exists('is_ipv6')) {
	function is_ipv6($ip) 
	{ 
		$ip = strval($ip);
		return (bool)preg_match('/\A
			(?:
			(?:
			(?:[a-f0-9]{1,4}:){6}
			|
			::(?:[a-f0-9]{1,4}:){5}
			|
			(?:[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){4}
			|
			(?:(?:[a-f0-9]{1,4}:){0,1}[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){3}
			|
			(?:(?:[a-f0-9]{1,4}:){0,2}[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){2}
			|
			(?:(?:[a-f0-9]{1,4}:){0,3}[a-f0-9]{1,4})?::[a-f0-9]{1,4}:
			|
			(?:(?:[a-f0-9]{1,4}:){0,4}[a-f0-9]{1,4})?::
			)
			(?:
			[a-f0-9]{1,4}:[a-f0-9]{1,4}
			|
			(?:(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.){3}
			(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])
			)
			|
			(?:
			(?:(?:[a-f0-9]{1,4}:){0,5}[a-f0-9]{1,4})?::[a-f0-9]{1,4}
			|
			(?:(?:[a-f0-9]{1,4}:){0,6}[a-f0-9]{1,4})?::
			)
			)\Z/ix',
			$ip 
		);
	}
}

// ------------------------------------------------------------------------

/**
 * 校验是否为IPV4
 *
 * @param           $ip                 string          IPV4
 * @return                              bool            
 */
if(!function_exists('is_ipv4')) {
	function is_ipv4($ip) 
	{ 
		$ip = strval($ip);
		$pattan = '/\A(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d{2}|2[0-5]\d|25[0-5])\.(\d|[1-9]\d|1\d{2}|2[0-5]\d|25[0-5])\.(\d|[1-9]\d|1\d{2}|2[0-5]\d|25[0-5])\Z/i';
		return (bool)preg_match($pattan, $ip);
	}
}



// ------------------------------------------------------------------------

/**
 * IPV4转为长整型
 *
 * @param           $ip                 string          IPV4
 * @return                              string|bool            
 */
if(!function_exists('iptolong')) {
	function iptolong($ip) 
	{
		if (is_ipv4($ip)) 
		{
			list($a, $b, $c, $d) = preg_split("/\./", $ip);
			return sprintf('%u', (($a * 256 + $b) * 256 + $c) * 256 + $d);
		}
		else 
		{
			return false;
		}
	}
}


// ------------------------------------------------------------------------


/**
 * 将IPV6的地址展开
 *
 * @param           $ip                 string          IPV6
 * @return                              string|false            
 */
if(!function_exists('expand_ipv6')) {
	function expand_ipv6($ip) 
	{
		if (!is_ipv6($ip)) 
		{
			return false;
		}

		$ip = strval($ip);
		if (strpos($ip, '::') !== false)
		{
			$ip = str_replace('::', str_repeat(':0', 8 - substr_count($ip, ':')).':', $ip);
		}
		if (strpos($ip, ':') === 0) 
		{
			$ip = '0'.$ip;
		}
		return $ip;
	}
}


// ------------------------------------------------------------------------

/**
 * 计算IPV6的地址范围
 *
 * @param           $ip                 string              IPV6
 * @param           $count              string|numeric      个数
 * @return                              string|false            
 */
if(!function_exists('calc_ipv6_range')) {
	function calc_ipv6_range($ip, $count) 
	{
		if (!is_ipv6($ip) || pow(2, 46) < $count || $count < 1) // 因为默认精度为14位，故最大支持 70368744177664 个
		{
			return false;
		}
		$ip = expand_ipv6($ip);
		$arr = explode(':', $ip);
		foreach ($arr as $key=>$val) 
		{
			$arr[$key] = strval(hexdec($val));
		}
		$count = strval(bcsub($count, 1, 0)); // 起始IP算一个

		$base = '65536';
		$tmp = array();
		$next = bcadd($arr[7], $count, 0);
		for ($i = 7; $i >=0 ; $i--) 
		{
			$tmp[$i][0] = bcmod($next, $base);
			$tmp[$i][1] = bcdiv($next, $base);
			if ($i > 0) 
			{
				$next = bcadd($arr[$i - 1], $tmp[$i][1]);
			}
		}
		if ($tmp[0][1] > 0) // 溢出
		{
			return false;
		}

		foreach ($arr as $k => &$v) 
		{
			$v = dechex($tmp[$k][0]);
		}
		return implode(':', $arr);

	}
}


// ------------------------------------------------------------------------

/**
 * 计算IPV4的地址范围
 *
 * @param           $ip                 string              IPV4,起始IP
 * @param           $count              string|numeric      个数
 * @return                              string|false            
 */
if(!function_exists('calc_ipv4_range')) {
	function calc_ipv4_range($ip, $count) 
	{
		if (!is_ipv4($ip) || !(pow(2, 32) > $count) || $count < 1)
		{
			return false;
		}
		$arr = explode('.', $ip);
		foreach ($arr as $key=>$val) 
		{
			$arr[$key] = strval($val);
		}
		$count = strval(bcsub($count, 1, 0)); // 起始IP算一个

		$base = '256';
		$tmp = array();
		$next = bcadd($arr[3], $count, 0);
		for ($i = 3; $i >=0 ; $i--) 
		{
			$tmp[$i][0] = bcmod($next, $base);
			$tmp[$i][1] = bcdiv($next, $base);
			if ($i > 0) 
			{
				$next = bcadd($arr[$i - 1], $tmp[$i][1]);
			}
		}
		if ($tmp[0][1] > 0) // 溢出
		{
			return false;
		}

		foreach ($arr as $k => &$v) 
		{
			$v = $tmp[$k][0];
		}
		return implode('.', $arr);

	}
}


// ------------------------------------------------------------------------

/**
 * 校验IP是否为子网掩码
 *
 * @param           $ip                 string          IP
 * @param           $all_zero			string          是否允许全0
 * @return                              bool            
 */
if(!function_exists('is_netmask')) {
	function is_netmask($ip, $all_zero = false)
	{ 
		$ip = strval($ip);
		if (!is_ipv4($ip) && !is_ipv6($ip)) 
		{
			return false;
		}
		
		$str = ip2bin($ip);
		$pos = strrpos($str, '1'); // 最后一个1的位置
		if (false === $pos) // 没有1，判断是否全0
		{
			$str = str_replace('0', '', $str);
			if ('' === $str) //全0
			{
				return (bool)$all_zero;
			}
			else 
			{
				return false;
			}
		}
		else // 有1
		{
			$pos++;
			$left = substr($str, 0, $pos);
			$right = substr($str, $pos, strlen($str));
			$left = str_replace('1', '', $left);
			$right = str_replace('0', '', $right);
			if ('' === $left && '' === $right) // 连续的1与连续的0
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

// ------------------------------------------------------------------------

/**
 * 校验是否为子网前缀
 *
 * @param           $prefix             integer         子网前缀
 * @param           $is_ipv4            bool            是否IPV4
 * @return                              bool            
 */
if(!function_exists('is_prefix')) {
	function is_prefix($prefix, $is_ipv4 = true)
	{ 
		$is_ipv4 = (bool)$is_ipv4;
		if ($is_ipv4 && $prefix > 0 && $prefix < 32) 
		{
			return true;
		}
		if (!$is_ipv4 && $prefix > 0 && $prefix < 128) 
		{
			return true;
		}
		return false;
	}
}

// ------------------------------------------------------------------------


/**
 * IP转为二进制字符串
 *
 * @param           $ip                 string          IP
 * @return                              false|string    二进制字符串            
 */
if(!function_exists('ip2bin')) {
	function ip2bin($ip) 
	{
		$ip = strval($ip);
		if (is_ipv4($ip)) 
		{
			$arr = explode('.', $ip);
			foreach ($arr as $key=>$val) 
			{
				$arr[$key] = str_pad(decbin($val), 8, "0", STR_PAD_LEFT);  
			}
			return implode('', $arr);
		}

		if (is_ipv6($ip)) 
		{
			$ip = expand_ipv6($ip);
			$arr = explode(':', $ip);
			foreach ($arr as $key=>$val) 
			{
				$arr[$key] = str_pad(decbin(hexdec($val)), 16, "0", STR_PAD_LEFT);  
			}
			return implode('', $arr);
		}
			
		return false;

	}
}

// ------------------------------------------------------------------------


/**
 * 二进制字符串转为IP
 *
 * @param           $ip                 string          IP
 * @return                              false|string    二进制字符串            
 */
if(!function_exists('bin2ip')) {
	function bin2ip($bin, $is_ipv4 = true) 
	{
		if ($is_ipv4) 
		{
			$bin = str_pad($bin, 32, '0', STR_PAD_LEFT);
			$arr = mb_str_split($bin, 8);
			foreach ($arr as $key=>$val) 
			{
				$arr[$key] = bindec($val);
			}
			$separator = '.';
		}
		else 
		{
			$bin = str_pad($bin, 128, '0', STR_PAD_LEFT);
			$arr = mb_str_split($bin, 16);
			foreach ($arr as $key=>$val) 
			{
				$val = bindec($val);
				$arr[$key] = dechex($val);
			}
			$separator = ':';
		}
		return implode($separator, $arr);
	}
}

// ------------------------------------------------------------------------


/**
 * 前缀转为二进制字符串
 * 192.168.1.0/24 ，24被称为prefix，表示子网掩码(255.255.255.0)
 * IPV6同上
 *
 * @param           $count              int             整数
 * @param           $is_ipv4            bool            是否IPV4
 * @return                              false|string                
 */
if(!function_exists('prefix2bin')) {
	function prefix2bin($count, $is_ipv4 = true) 
	{
		$count = intval($count);
		$mask = array();
		for ($i=0; $i<$count; $i++) 
		{
			array_push($mask, 1);
		}

		if ($is_ipv4) 
		{
			if ($count > 32) 
			{
				return false;
			}

			for ($i=$count; $i<32; $i++) 
			{
				array_push($mask, 0);
			}
		}
		else 
		{
			if ($count > 128) 
			{
				return false;
			}

			for ($i=$count; $i<128; $i++) 
			{
				array_push($mask, 0);
			}
		}
		return implode('', $mask);
	}
}


// ------------------------------------------------------------------------

/**
 * 前缀转为子网掩码
 *
 * @param           $count              int             整数
 * @param           $is_ipv4            bool            是否IPV4
 * @return                              false|string                
 */
if(!function_exists('prefix2netmask')) {
	function prefix2netmask($count, $is_ipv4 = true) 
	{
		$count = intval($count);
		$str = prefix2bin($count, $is_ipv4);
		if (false === $str) 
		{
			return false;
		}

		if ($is_ipv4) 
		{
			$arr = str_split($str, 8);
			foreach ($arr as $key=>$val) 
			{
				$arr[$key] = bindec($val);
			}
			$str = implode('.', $arr);
		}
		else 
		{
			$arr = str_split($str, 16);
			foreach ($arr as $key=>$val) 
			{
				$arr[$key] = dechex(bindec($val));
			}
			$str = implode(':', $arr);
		}

		if (!is_netmask($str, true)) 
		{
			return false;
		}
		return $str;
	}
}


// ------------------------------------------------------------------------

/**
 * 子网掩码转为前缀
 *
 * @param           $ip                 string          IP
 * @return                              false|int            
 */
if(!function_exists('netmask2prefix')) {
	function netmask2prefix($ip)
	{
		$ip = strval($ip);
		if (!is_netmask($ip, true)) 
		{
			return false;
		}

		$str = ip2bin($ip);
		if (false === $str) 
		{
			return false;
		}

		$arr = str_split($str);
		$arr = array_count_values($arr);
		return isset($arr[1]) ? $arr[1] : 0;
	}
}


// ------------------------------------------------------------------------


/**
 * 子网掩码转为IP数
 *
 * @param           $ip                 string          IP
 * @param           $netmask            string          子网掩码
 * @return                              false|float     IP个数            
 */
if(!function_exists('netmask2count')) {
	function netmask2count($ip, $netmask) 
	{
		$ip = strval($ip);
		$netmask = strval($netmask);

		$ip = ip2bin($ip);
		if (false === $ip) 
		{
			return false;
		}

		$netmask = ip2bin($netmask);
		if (false === $netmask) 
		{
			return false;
		}
		$netmask = str_replace(array(1, 0, 'k'), array('k', 1, 0), $netmask);

		$start = bindec($ip & $netmask);
		$end = bindec($netmask) + 1;
		if ($start > 0) 
		{
			$count = $end - $start;
		}
		else if (0 === $start)
		{
			$count = $end;
		}
		else 
		{
			$count = 0;
		}
		
		return $count;
	}
}

// ------------------------------------------------------------------------

/**
 * 前缀转为IP数
 *
 * @param           $ip                 string          IP
 * @param           $prefix             int             前缀
 * @param           $is_ipv4            bool            是否IPV4
 * @return                              false|float     IP个数   
 */
if(!function_exists('prefix2count')) {
	function prefix2count($ip, $prefix, $is_ipv4 = true) 
	{
		$netmask = prefix2netmask($prefix, $is_ipv4);
		return netmask2count($ip, $netmask);
	}
}

// ------------------------------------------------------------------------

/**
 * 加法运算
 *
 * @param           $a                  numeric         左操作数
 * @param           $b                  numeric         右操作数
 * @return                              string          浮点数
 */
if(!function_exists('bcadd')) {
	function bcadd($a, $b) 
	{
		$a = floatval($a);
		$b = floatval($b);
		return sprintf("%u", $a + $b);
	}
}

// ------------------------------------------------------------------------

/**
 * 除法运算
 *
 * @param           $a                  numeric         左操作数
 * @param           $b                  numeric         右操作数
 * @return                              string          浮点数
 */
if(!function_exists('bcdiv')) {
	function bcdiv($a, $b) 
	{
		$a = floatval($a);
		$b = floatval($b);
		return sprintf("%u", $a / $b);
	}
}


// ------------------------------------------------------------------------

/**
 * 取模运算
 *
 * @param           $a                  numeric         左操作数
 * @param           $b                  numeric         右操作数
 * @return                              integer         浮点数
 */
if(!function_exists('bcmod')) {
	function bcmod($a, $b) 
	{
		$a = floatval($a);
		$b = floatval($b);
		return $a % $b;
	}
}


// ------------------------------------------------------------------------

/**
 * 减法运算
 *
 * @param           $a                  numeric         左操作数
 * @param           $b                  numeric         右操作数
 * @return                              integer         浮点数
 */
if(!function_exists('bcsub')) {
	function bcsub($a, $b) 
	{
		$a = floatval($a);
		$b = floatval($b);
		return $a - $b;
	}
}


// ------------------------------------------------------------------------

/**
 * 获取系统的可用网口名称
 *
 * @return                              array         
 */
if(!function_exists('netport')) {
	function netport() 
	{
		$ret = array();
		exec('mii-tool', $output, $retval);

		if (0 === $retval && is_array($output)) 
		{
			foreach ($output as $key=>$val) 
			{
				$pos = strpos($val, ':');
				if (false !== $pos) 
				{
					array_push($ret, substr($val, 0, $pos));
				}
			}
		}
		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取IP个数
 *
 * @param           $start              string          起始IP
 * @param           $end                string          结束IP
 * @return                              float|false         
 */
if(!function_exists('ipcount')) {
	function ipcount($start, $end) 
	{
		if (is_ipv4($start) && is_ipv4($end)) 
		{
			return ipv4count($start, $end);
		}
		if (is_ipv6($start) && is_ipv6($end)) 
		{
			return ipv6count($start, $end);
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取IPV4个数
 *
 * @param           $start              string          起始IP
 * @param           $end                string          结束IP
 * @return                              float|false         
 */
if(!function_exists('ipv4count')) {
	function ipv4count($start, $end) 
	{
		$start = strval($start);
		$end = strval($end);
		if (!is_ipv4($start) || !is_ipv4($end)) 
		{
			return false;
		}

		$start_section = explode('.', $start);
		$end_section = explode('.', $end);
		$unit = array(pow(2,24), pow(2,16), pow(2,8), 1);
		$sub_section = array(
			$unit[0]*($end_section[0] - $start_section[0]),
			$unit[1]*($end_section[1] - $start_section[1]),
			$unit[2]*($end_section[2] - $start_section[2]),
			$unit[3]*($end_section[3] - $start_section[3]),
		);

		$count = 1; // 如果两个IP相等，至少IP数为1
		foreach ($sub_section as $key=>$val) 
		{
			$count += $val;
		}
		if ($count < 0) 
		{
			return false;
		}
		return $count;
	}
}


// ------------------------------------------------------------------------

/**
 * 获取IPV6个数
 *
 * @param           $start              string          起始IP
 * @param           $end                string          结束IP
 * @return                              float|false         
 */
if(!function_exists('ipv6count')) {
	function ipv6count($start, $end) 
	{
		$start = strval($start);
		$end = strval($end);
		if (!is_ipv6($start) || !is_ipv6($end)) 
		{
			return false;
		}
		$start = expand_ipv6($start);
		$end = expand_ipv6($end);

		$start_section = explode(':', $start);
		$start_section = array_map('hexdec', $start_section);
		$end_section = explode(':', $end);
		$end_section = array_map('hexdec', $end_section);
		$unit = array(pow(2,112), pow(2, 96), pow(2, 80), pow(2, 64), 
					  pow(2, 48), pow(2, 32), pow(2, 16), 1);
		$sub_section = array(
			$unit[0]*($end_section[0] - $start_section[0]),
			$unit[1]*($end_section[1] - $start_section[1]),
			$unit[2]*($end_section[2] - $start_section[2]),
			$unit[3]*($end_section[3] - $start_section[3]),
			$unit[4]*($end_section[4] - $start_section[4]),
			$unit[5]*($end_section[5] - $start_section[5]),
			$unit[6]*($end_section[6] - $start_section[6]),
			$unit[7]*($end_section[7] - $start_section[7]),
		);

		$count = 1; // 如果两个IP相等，至少IP数为1
		foreach ($sub_section as $key=>$val) 
		{
			$count += $val;
		}
		if ($count < 0) 
		{
			return false;
		}
		return $count;
	}
}

// ------------------------------------------------------------------------

/**
 * 规范MAC地址
 * 00fafadd4e57和00-fa-fa-dd-4e-57 变成 00:FA:FA:DD:4E:57
 *
 * @param           $str                string          字符串
 * @param           $separator          string          分隔符
 * @return                              string|false            
 */
if(!function_exists('to_mac')) {
	function to_mac($str, $separator = ':') 
	{
		$str = strval($str);
		$pattern = '/^[0-9a-f]{2}([-:]{0,1})[0-9a-f]{2}\1[0-9a-f]{2}\1[0-9a-f]{2}\1[0-9a-f]{2}\1[0-9a-f]{2}/i';
		if (preg_match($pattern, $str, $matches)) 
		{
			if ('' === $matches[1]) 
			{
				$arr = str_split($str, 2);
				
			}
			else 
			{
				$arr = explode($matches[1], $str);
			}

			$str = implode($separator, $arr);
			return strtoupper($str);
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 规范IP地址
 * 02.2.2.02 变成 2.2.2.2
 *
 * @param           $str                string          字符串
 * @return                              string            
 */
if(!function_exists('to_ipv4')) {
	function to_ipv4($str) 
	{
		$token = unjoin('.', $str);
		foreach ($token as $key=>$val) 
		{
			$val = intval($val);
			$token[$key] = strval($val);
		}
		return implode('.', $token);
	}
}

// ------------------------------------------------------------------------

/**
 * 64位整数转为MAC地址，只在64位设备有效
 * '12375687646' 变成 00:02:E1:A6:01:DE
 *
 * @param           $str                string          字符串
 * @param           $separator          string          分隔符
 * @return                              string            
 */
if(!function_exists('int2mac')) {
	function int2mac($str, $separator = ':') 
	{
		$str_sc = gmp_init ( $str );
		$str = gmp_strval($str_sc,16);
		$str = str_pad($str, 16, '0', STR_PAD_LEFT);
		$arr = mb_str_split($str, 4); //大端MAC，需切除末尾的4个0
		array_pop($arr);
		$str = implode('', $arr);
		$str = hex2mac($str, $separator);
		return strtoupper($str);
	}
}

// ------------------------------------------------------------------------

/**
 * MAC地址统一化，把AA-BB-CC-DD-EE-FF 转为 0xAABBCCDDEEFF0000
 *
 * @param       $str        string          字符串
 * @return					string
 */
if(!function_exists('macval')) {
	function macval($str)
	{
		$str = mac2hex($str);
		$str = '0x' . strtoupper($str) . '0000'; //大端64位MAC
		return machex2int($str);
	}
}

// ------------------------------------------------------------------------

/**
 * 把0xAABBCCDDEEFF 转为 187723572702975
 *
 * @param       $str        string          字符串
 * @return					string
 */
if(!function_exists('machex2int')) {
	function machex2int($str)
	{
		$r = gmp_init($str, 16);
		$str = gmp_strval($r);
		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * IP大小比较
 * 
 *
 * @param           $ip1                string          IP
 * @param           $ip2                string          IP
 * @return                              bool|integer            
 */
if(!function_exists('ip_compare')) {
	function ip_compare($ip1, $ip2) 
	{
		if (is_ipv4($ip1) && is_ipv4($ip2)) 
		{
			$ip1_segments = explode('.', $ip1);
			$ip1_segments = array_map('intval', $ip1_segments);
			$ip2_segments = explode('.', $ip2);
			$ip2_segments = array_map('intval', $ip2_segments);
		}

		if (is_ipv6($ip1) && is_ipv6($ip2)) 
		{
			$ip1_segments = explode(':', $ip1);
			$ip1_segments = array_map('hexdec', $ip1_segments);
			$ip2_segments = explode(':', $ip2);
			$ip2_segments = array_map('hexdec', $ip2_segments);
		}

		if (!isset($ip2_segments)) 
		{
			return false;
		}

		foreach ($ip2_segments as $key=>$val) 
		{
			if ($ip1_segments[$key] > $val) 
			{
				return 1;
			}
			elseif ($ip1_segments[$key] === $val) 
			{
				continue;
			}
			else 
			{
				return -1;
			}
		}
		return 0;
	}
}


// ------------------------------------------------------------------------

/**
 * IP网段，第一个IP会比第二个IP小
 * 
 *
 * @param           $ip1                string          IP
 * @param           $ip2                string          IP         
 */
if(!function_exists('ip_segment')) {
	function ip_segment(&$ip1, &$ip2) 
	{
		if (1 === ip_compare($ip1, $ip2)) 
		{
			swap($ip1, $ip2);
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 路由排序
 * 按子网掩码排序，IP与子网掩码相同的按度量值排序
 *
 * @param           $a				array			数组(ip,netmask,metric)
 * @param           $b				array			数组(ip,netmask,metric)
 * @return                              bool|integer            
 */
if(!function_exists('route_sort')) {
	function route_sort(array $a, array $b) 
	{
		if (!isset($a['ip'], $a['netmask'], $a['metric'], $b['ip'], $b['netmask'], $b['metric'])) 
		{
			bug('illegal_parameter');
		}

		if (0 !== ($netmask = ip_compare($b['netmask'], $a['netmask']))) //子网掩码不等
		{
			return $netmask;
		}

		if (0 !== ($ip = ip_compare($a['ip'], $b['ip']))) //IP不等
		{
			return $ip;
		}
		else //IP相等，子网掩码相等
		{
			$metric = $a['metric'] - $b['metric'];
			if ($metric < 0) 
			{
				return -1;
			}
			elseif ($metric > 0) 
			{
				return 1;
			}
			else 
			{
				return 0;
			}
		}
	}
}

// ------------------------------------------------------------------------

/**
 * MAC地址转为16进制
 *
 * @param		$str		string		MAC地址
 * @return					string              
 */
if(!function_exists('mac2hex')) {
	function mac2hex($str)
	{
		$str = strval($str);
		$str = str_replace(array('-', ':'), '', $str);
		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * 十六进制转为MAC地址
 *
 * @param		$str			string		十六进制
 * @param		$separator		string		分隔符
 * @return						string            
 */
if(!function_exists('hex2mac')) {
	function hex2mac($str, $separator = '-')
	{
		$str = strval($str);
		$str = str_pad($str, 12, 0, STR_PAD_LEFT);
		$arr = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);
		$str = '';
		foreach ($arr as $key=>$val) 
		{
			if (0 === $key) 
			{
				$str .= $val;
			}
			else 
			{
				if (0 === $key % 2) 
				{
					$str .= $separator . $val;
				}
				else 
				{
					$str .= $val;
				}
			}
		}
		return $str;
	}
}


// ------------------------------------------------------------------------

/**
 * 转为IP范围，一个起始IP，一个结束IP
 * 支持:192.168.0.1
 *      192.168.10.0-200.200.10.254
 *      192.168.7.0/255.255.252.0
 *      192.168.125.0/24
 *      192.168.8.0|100 ---表示从该IP起的100个IP
 *
 * @param           $str                string          字符串
 * @param           $from               string          起始IP
 * @param           $to                 string          结束IP
 * @return                              bool            
 */
if(!function_exists('to_ip_range')) {
	function to_ip_range($str, &$from = null, &$to = null) 
	{
		$from = $to = null;
		$str = trim(strval($str));
		if (is_empty($str)) 
		{
			return false;
		}

		if (strrpos($str, '|') > 0) //如:192.168.8.0|100
		{
			$arr = unjoin_pad('|', $str, 2);
			if ((is_ipv4($arr[0]) && ($end = calc_ipv4_range($arr[0], $arr[1]))) || 
				(is_ipv6($arr[0]) && ($end = calc_ipv6_range($arr[0], $arr[1])))) 
			{
				$from = $arr[0];
				$to = $end;
				return true;
			}
		}
		elseif (strrpos($str, '/') > 0) 
		{
			$arr = unjoin_pad('/', $str, 2);
			$is_ipv4 = is_ipv4($arr[0]);
			$is_ipv6 = is_ipv6($arr[0]);
			if (is_netmask($arr[1])) //如:192.168.7.0/255.255.252.0
			{
				$netmask = $arr[1];
				$arr[1] = netmask2count($arr[0], $arr[1]);
				if (($is_ipv4 && ($end = calc_ipv4_range($arr[0], $arr[1]))) || 
					($is_ipv6 && ($end = calc_ipv6_range($arr[0], $arr[1])))) 
				{
					$from = network_segment($arr[0], $netmask);
					$to = $end;
					return true;
				}
			}
			else //如:192.168.125.0/24
			{
				$netmask = prefix2netmask($arr[1], $is_ipv4);
				$arr[1] = prefix2count($arr[0], $arr[1], $is_ipv4);
				if (($is_ipv4 && ($end = calc_ipv4_range($arr[0], $arr[1]))) || 
					($is_ipv6 && ($end = calc_ipv6_range($arr[0], $arr[1])))) 
				{
					$from = network_segment($arr[0], $netmask);
					$to = $end;
					return true;
				}
			}
		}
		elseif (strrpos($str, '-') > 0) //如:192.168.10.0-200.200.10.254
		{
			$arr = unjoin_pad('-', $str, 2);
			if ((is_ipv4($arr[0]) && is_ipv4($arr[1])) || 
				(is_ipv6($arr[0]) && is_ipv6($arr[1]))) 
			{
				$from = $arr[0];
				$to = $arr[1];
				if (1 === ip_compare($from, $to)) 
				{
					swap($from, $to);
				}
				return true;
			}
		}
		else 
		{
			if (is_ipv4($str) || is_ipv6($str)) //如:192.168.0.1
			{
				$from = $str;
				return true;
			}
			return false;
		}
	}
}


// ------------------------------------------------------------------------

/**
 * 是否匹配IP范围
 * 支持:192.168.0.1
 *      192.168.10.0-200.200.10.254
 *      192.168.7.0/255.255.252.0
 *      192.168.125.0/24
 *      192.168.8.0|100 ---表示从该IP起的100个IP
 *
 * @param           $ip                 string          IP
 * @param           $range              string|array    IP范围
 * @return                              bool            
 */
if(!function_exists('is_match_ip_range')) {
	function is_match_ip_range($ip, $range) 
	{
		if (is_array($range)) 
		{
			foreach ($range as $key=>$val) 
			{
				$rs = is_match_ip_range($ip, $val);
				if ($rs) 
				{
					return true;
				}
			}
		}
		else 
		{
			if (to_ip_range($range, $from, $to))
			{
				if (is_empty($to)) //单个IP
				{
					if ($from === $ip) 
					{
						return true;
					}
				}
				else //IP网段
				{
					if (ip_compare($from, $ip) <= 0 && 
						ip_compare($ip, $to) <= 0) 
					{
						return true;
					}
				}
			}
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取网段
 *
 * @param		$ip				string			IP地址
 * @param		$netmask		string			子网掩码
 * @return						false|string	网段
 */
if(!function_exists('network_segment')) {
	function network_segment($ip, $netmask) 
	{
		if (is_ipv4($ip)) 
		{
			$is_ipv4 = true;
		}
		elseif (is_ipv6($ip)) 
		{
			$is_ipv4 = false;
		}
		else 
		{
			return false;
		}

		if (false !== filter_var($netmask, FILTER_VALIDATE_INT)) 
		{
			$netmask = prefix2netmask($netmask, $is_ipv4);
		}
		$netmask = ip2bin($netmask);
		$ip = ip2bin($ip);
		$net = bin2ip($netmask & $ip, $is_ipv4);
		return $net;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取广播地址
 *
 * @param		$ip				string			IP地址
 * @param		$netmask		string			子网掩码
 * @return						false|string	广播IP
 */
if(!function_exists('broadcast_ip')) {
	function broadcast_ip($ip, $netmask) 
	{
		$net = network_segment($ip, $netmask);
		if ($net) 
		{
			$net = ip2bin($net);
			$netmask = ip2bin($netmask);
			$netmask = bin_invert($netmask);
			$ret = bin2ip($net | $netmask);
			return $ret;
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 按位取反，如：'101'返回'010'
 *
 * @param		$bin			string			二进制字符串
 * @return						string	
 */
if(!function_exists('bin_invert')) {
	function bin_invert($bin) 
	{
		$bin = strval($bin);
		$arr = preg_split('//', $bin);
		array_shift($arr);
		array_pop($arr);
		$ret = array();
		foreach ($arr as $key=>$val) 
		{
			if ('1' === $val) 
			{
				$ret[] = '0';
			}
			else 
			{
				$ret[] = '1';
			}
		}
		$ret = implode('', $ret);
		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 按位与
 *
 * @param		$bin1			string			二进制字符串
 * @param		$bin2			string			二进制字符串
 * @return						string	
 */
if(!function_exists('bin_and')) {
	function bin_and($bin1, $bin2) 
	{
		$bin1 = strval($bin1);
		$bin2 = strval($bin2);
		$arr1 = preg_split('//', $bin1);
		$arr2 = preg_split('//', $bin2);
		array_shift($arr1);
		array_shift($arr2);
		array_pop($arr1);
		array_pop($arr2);

		$max_len = count($arr1) > count($arr2) ? count($arr1) : count($arr2);
		$arr1 = array_pad($arr1, -$max_len, '0');
		$arr2 = array_pad($arr2, -$max_len, '0');
		$ret = array();
		foreach ($arr1 as $key=>$val) 
		{
			if ('1' === $val && '1' === $arr2[$key]) 
			{
				$ret[] = '1';
			}
			else 
			{
				$ret[] = '0';
			}
		}
		$ret = implode('', $ret);
		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 按位或
 *
 * @param		$bin1			string			二进制字符串
 * @param		$bin2			string			二进制字符串
 * @return						string	
 */
if(!function_exists('bin_or')) {
	function bin_or($bin1, $bin2) 
	{
		$bin1 = strval($bin1);
		$bin2 = strval($bin2);
		$arr1 = preg_split('//', $bin1);
		$arr2 = preg_split('//', $bin2);
		array_shift($arr1);
		array_shift($arr2);
		array_pop($arr1);
		array_pop($arr2);

		$max_len = count($arr1) > count($arr2) ? count($arr1) : count($arr2);
		$arr1 = array_pad($arr1, -$max_len, '0');
		$arr2 = array_pad($arr2, -$max_len, '0');
		$ret = array();
		foreach ($arr1 as $key=>$val) 
		{
			if ('1' === $val || '1' === $arr2[$key]) 
			{
				$ret[] = '1';
			}
			else 
			{
				$ret[] = '0';
			}
		}
		$ret = implode('', $ret);
		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 返回字节值，把270M转成283115520
 *
 * @param		$str			string		字符串
 * @param		$base			integer		基数
 * @return						float	
 */
if(!function_exists('byteval')) {
	function byteval($str, $base = 1024)
	{
		$val = floatval($str);
		$unit = 'B';
		if (preg_match('/.*(K|KB|M|MB|G|GB|T|TB|P|PB|E|EB)$/i', $str, $match)) 
		{
			$unit = strtoupper($match[1]);
		}

		$arr = array('B','K','M','G','T','P','E');
		$pos = array_search($unit, $arr);
		$val *= pow($base, $pos);
		return $val;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取本地所有IP
 *
 * @return						array	
 */
if(!function_exists('get_local_ips')) {
	function get_local_ips()
	{
		$handle = popen("ifconfig | grep 'inet addr' | awk -F: '{print $2}' | awk -F' ' '{print $1}' | grep -v '127.0.0.1'", 'r');
		$ips = array();
		if ($handle) 
		{
			while ( ! feof($handle) ) 
			{
				$buffer = trim(fgets($handle, 4096));
				if ( '' == $buffer)
				{
					continue;
				}
				$ips[] = $buffer;
			}
			pclose($handle);
		}
		return $ips;
	}
}

// ------------------------------------------------------------------------

/**
 * 判断是否是IP前缀
 */
if(!function_exists('is_preip')) {
	function is_preip($ip) 
	{
		return preg_match(
			'/^(([1-9]?\d)|(1\d\d)|(2[0-4]\d)|(25[0-5]))(\.(([1-9]?\d)|(1\d\d)|(2[0-4]\d)|(25[0-5]))){0,3}$/',
			$ip) || preg_match('/^((([1-9]?\d)|(1\d\d)|(2[0-4]\d)|(25[0-5]))\.){1,3}$/', $ip); //允许后台带.
	}
}

// ------------------------------------------------------------------------

/**
 * 由ip前缀获取ip范围
 * @param $preip
 */
if(!function_exists('preip2segment')) {
	function preip2segment($preip) 
	{
		if (substr($preip, -1) == '.') //判断最后一个是不是'.',如果是，就去掉它
		{
			$preip = substr($preip, 0, -1);
		}
	
		$vs = explode('.', $preip);
		$ip_beg = $preip;
		$ip_end = $preip;
		for ($i = count($vs); $i < 4; $i++) 
		{
			$ip_beg .= '.0';
			$ip_end .= '.255';
		}
		return array($ip_beg, $ip_end);
	}
}


/* End of file network_helper.php */
/* Location: ./application/helpers/network_helper.php */