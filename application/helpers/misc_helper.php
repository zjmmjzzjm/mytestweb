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
 * 特殊字符串转为布尔值
 * @param           $val            string          字符串
 */
if(!function_exists('str_to_bool')) {
	function str_to_bool($val) 
	{

		$val = (string)$val;
		switch (strtolower($val))
		{
			case '':
			case '0':
			case 'false':
			case 'off':
			case 'null':
			case 'no':
				return false;
			default:
				return true;
		}
	}    
}

// ------------------------------------------------------------------------

/**
 * 特殊字符串转为整数值
 * @param           $val            string          字符串
 */
if(!function_exists('str_to_int')) {
	function str_to_int($val) 
	{
		$val = (string)$val;
		switch (strtolower($val))
		{
			case '':
			case '0':
			case 'false':
			case 'off':
			case 'null':
			case 'no':
				return 0;
			default:
				return 1;
		}
	}    
}

// ------------------------------------------------------------------------

/**
 * 布尔值转为字符串
 * @param           $val            bool            布尔值
 */
if(!function_exists('to_string')) {
	function to_string($val) 
	{

		if (is_null($val))
		{
			return 'null';
		}

		if (is_array($val))
		{
			return '';
		}

		if (is_object($val) && method_exists($val, '__toString'))
		{
			return $val->__toString();
		}

		if (!is_bool($val))
		{
			return (string)$val;
		}
		
		if (true === $val)
		{
			return 'true';
		}
		else
		{
			return 'false';
		}
	}    
}

// ------------------------------------------------------------------------

/**
 * 保存为 ini 格式的文件 
 * @param           $arr            array           数组
 * @param           $path           string          文件保存路径
 * @param           $with_main      bool            是否带 [main] 计数
 * @return                          int             写入文件的字节数
 */
if(!function_exists('save_ini_file')) {
	function save_ini_file(array $arr, $path, $with_main = false) 
	{
		$path = normalize_path(strval($path));

		clearstatcache();
		if (file_exists($path))
		{
			unlink($path);
		}

		$data = array();
		$count = 0;
		foreach($arr as $key=>$val) 
		{
			$key = (string)$key;
//			if (!preg_match("/[\{\}\|&~\!\[\]\(\)\"]/", $key)) // section没有非法字符
			if (!preg_match("/[\]]/", $key)) // section没有非法字符
			{
				array_push($data, '[' . $key . ']');

				if (!is_array($val))
				{
					$val = array();
				}
				foreach($val as $k=>$v) 
				{
					$bak = $v;
					$v = to_string($v); // todo
					$k = (string)$k;
//					if (!preg_match("/[\{\}\|&~\!\[\]\(\)\"]/", $k)) // keyname没有非法字符
					if (!preg_match("/[=]/", $k)) // keyname没有非法字符
					{
						if (is_string($bak) && is_numeric($bak)) 
						{
							array_push($data, sprintf('%s = "%s"', $k, $v));
							continue;
						}
						
						$v = str_replace(array("\\", '"'), array("\\\\", "\\\""), $v);
						if (false !== strpos($v, "\n")) //存在换行
						{
							$v = str_replace(array("\r\n", "\n"), array("\r\n", "\r\n"), $v);
							$v = '"""' . $v . '"""';
						}
						array_push($data, $k . ' = ' . $v);
					}
					 
				}

				$count++;
				array_push($data, "\r\n\r\n");
			}
		}

		$data = implode("\r\n", $data);
		if ($with_main) 
		{
			$data = "[main]\r\ncount = {$count}\r\n\r\n\r\n" . $data;
		}
		
		$CI = & get_instance();
		$CI->load->helper('file');
		return write_file($path, $data);
	}
}

// ------------------------------------------------------------------------

/**
 * 读取 ini 格式的文件 ，兼容rwini
 *
 * @param           $path           string          文件路径
 * @return                          array
 */
if(!function_exists('read_ini_file')) {
	function read_ini_file($path, $process_sections = false) 
	{
		if (!is_file($path)) 
		{
			return false;
		}

		$arr = file($path);
		$res = $ret = array();
		if (is_array($arr)) 
		{
			$current_section = null;
			foreach ($arr as $key=>$val) 
			{
				if (empty($multi)) 
				{
					$val = ltrim($val);
					if ('' === $val || in_array($val[0], array('#', ';'), true)) //空行或注释
					{
						continue;
					}
					elseif ('[' === $val[0] && preg_match("/^\[([^\]]+)\][\s]*$/", $val, $match)) //section
					{
						$match[1] = trim($match[1]);
						$res[$match[1]] = array();
						$current_section = &$res[$match[1]];
					}
					elseif ('[' !== $val[0] && preg_match("/^([^=]+)[\s]*=(.*)$/", $val, $match)) 
					{
						$match[1] = trim($match[1]);
						if (is_null($current_section)) //文件格式可能为UTF8+BOM，应该变成UTF8
						{
							return false;
						}
						else 
						{
							$match[2] = trim($match[2]);
							if (preg_match('/^"""(.*)/', $match[2], $matching)) //被"""括起
							{
								$multi = true;
								$current_section[$match[1]] = $matching[1] . "\n";
								continue;
							}

							if (preg_match("/^\"(.*)\"$/", $match[2], $matchs)) //被双引号括起
							{
								$current_section[$match[1]] = $matchs[1];
							}
							else 
							{
								$current_section[$match[1]] = $match[2];
								if (is_numeric($current_section[$match[1]])) //值为数字
								{
									$current_section[$match[1]] = floatval($current_section[$match[1]]);
								}
							}

							if (in_array(strtolower($current_section[$match[1]]), 
								array('true', 'false', 'on', 'off', 'yes', 'no', 'null'), true)) 
							{
								$current_section[$match[1]] = boolval($current_section[$match[1]]);
							}
						}
					}
				}
				else 
				{
					if (preg_match('/(.*)"""\s*$/', $val, $matching)) //结束"""
					{
						$current_section[$match[1]] .= $matching[1];
						$multi = false;
					}
					else 
					{
						$current_section[$match[1]] .= $val;
					}
				}
			}

			if (!$process_sections) 
			{
				foreach ($res as $key=>$val) 
				{
					foreach ($val as $k=>$v) 
					{
						if (is_string($v)) 
						{
							$v = str_replace(array("\\\"", "\\\\"), array('"', "\\"), $v);
						}
						$ret[$k] = $v; 
					}
				}
			}
			else 
			{
				foreach ($res as $key=>$val) 
				{
					foreach ($val as $k=>$v) 
					{
						if (is_string($v)) 
						{
							$v = str_replace(array("\\\"", "\\\\"), array('"', "\\"), $v);
						}
						$ret[$key][$k] = $v; 
					}
				}
			}
		}
		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 通用ajax请求，返回JSON格式的数据给前端
 *
 * @param           $success            bool            请求是否成功
 * @param           $message            string          消息
 * @param           $data               mixed           数据
 * @param           $options            array           其他
 */
if(!function_exists('echo_json')) {
	function echo_json($success, $message = null, $data = null, array $options = array()) 
	{
		$ret = array();

		$success = (bool)$success;
		$ret['success'] = $success;
		
		if (!is_null($message))
		{
			$ret['msg'] = (string)$message;
		}

		if (!is_null($data))
		{
			$ret['data'] = $data;
		}

		if (!empty($options))
		{
			foreach($options as $key=>$val) 
			{
				$ret[$key] = $val;
			}
		}
		
//        header('Content-Type: application/json'); //上传文件时该项会影响结果
		echo json_encode($ret);
		exit(0);
	}
}

// ------------------------------------------------------------------------

/**
 * 扫描所给目录下的所有文件或目录
 *
 * @param           $dir                string          目录
 * @param           $sort               int             排序，1为降序
 * @param           $exclude            array           排除的文件或目录
 */
if(!function_exists('scan_dir')) {
	function scan_dir($dir, $sort = null, array $exclude = array('.', '..')) 
	{
		
		$dir = (string)$dir;
		$dir = trim($dir);
		if (!is_dir($dir))
		{
			return array();
		}

		if (is_null($sort))
		{
			$list = @scandir($dir); // 升序
		}
		else
		{
			$list = @scandir($dir, 1); // 降序
		}

		if (false === $list)
		{
			return false;
		}

		$list = array_diff($list, $exclude);
		return $list;
		
	}

}

// ------------------------------------------------------------------------

/**
 * 对非关联数组排序
 *
 * @param           $array              array           数组
 * @param           $column             string          列名
 * @param           $dir                integer         0：升序或1：降序
 * @return                              array|false     
 */
//if(!function_exists('array_sort')) {
//	function array_sort(array $array, $column, $dir = 'asc', $sort_flags = SORT_STRING) 
//	{
//		$dir = strtolower($dir);
//		$arr = array();
//		foreach ($array as $key=>$val) 
//		{
//			if (!isset($val[$column])) 
//			{
//				return false;
//			}
//			$arr[$val[$column]] = $val; //bug:字段不唯一
//		}
//
//		if (ksort($arr, $sort_flags))
//		{
//			if (in_array($dir, array('0', 'asc'))) 
//			{
//				return $arr;
//			}
//			else 
//			{
//				return array_reverse($arr, true);
//			}
//		}
//		else 
//		{
//			return false;
//		}
//	}
//}

// ------------------------------------------------------------------------

/**
 * 打印变量，调试用
 *
 * @param           $val                string          变量
 */
if(!function_exists('dump_var')) {
	function dump_var($val) 
	{
		$CI = & get_instance();
		if (!(@$CI->debug || 'development' === ENVIRONMENT)) 
		{
			return;
		}

		$val = func_get_args();
		@header('Content-Type: text/html;charset=utf-8');
		var_dump($val);
	}
}

// ------------------------------------------------------------------------

/**
 * 打印变量到文件，调试用，开发时使用
 *
 */
if(!function_exists('dump_once')) {
	function dump_once() 
	{
		$CI = & get_instance();
		if (!(@$CI->debug || 'development' === ENVIRONMENT)) 
		{
			return;
		}

		$file = config_item('temp_folder') . '/' . __FUNCTION__ . '.txt';
		$file = normalize_path($file);
		if (func_num_args() <= 0) 
		{
			if (is_file($file)) 
			{
				unlink($file);
			}
			return ;
		}
		ob_start();
		debug_print_backtrace();
		$header = ob_get_contents();
		ob_end_clean();
		$val = func_get_args();
		$text = "\n" . str_repeat("> >", 80) . "\n" . $header . "\n" . var_export($val, true);
		error_log($text, 3, $file);
	}
}


// ------------------------------------------------------------------------

/**
 * 打印变量到文件，调试用，开发时使用
 *
 */
if(!function_exists('dump_file')) {
	function dump_file() 
	{
		$CI = & get_instance();

		$file = config_item('temp_folder') . '/' . __FUNCTION__ . '.txt';
		$file = normalize_path($file);
		if (func_num_args() <= 0) 
		{
			@unlink($file);
			return ;
		}
		ob_start();
		debug_print_backtrace();
		$header = ob_get_contents();
		ob_end_clean();
		$val = func_get_args();
		$text = "\n" . str_repeat("> >", 80) . "\n" . $header . "\n" . var_export($val, true);
		error_log($text, 3, $file);
	}
}

// ------------------------------------------------------------------------

/**
 * 净化 HTML 中的 innerText
 *
 * @param           $str                string          字符串
 * @return                              string          字符串
 */
if(!function_exists('sanitize_text')) {
	function sanitize_text($str) 
	{
		$str = (string)$str;
		$str = remove_invisible_characters($str);
		$str = html_escape($str);
		return $str;
	}
}


// ------------------------------------------------------------------------

/**
 * 非UTF-8的中文字符串转为UTF-8
 *
 * @param           $str                string          字符串
 * @return                              string          字符串
 */
if(!function_exists('gbk_to_utf8')) {
	function gbk_to_utf8($str) 
	{
		$str = (string)$str;
		$str = mb_convert_encoding($str, 'UTF-8', 'GBK');
		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * 循环切换 error_reporting
 *
 * @param		$level			integer		错误级别
 * @return						void
 */
if(!function_exists('toggle_error_reporting')) {
	function toggle_error_reporting($level = null) 
	{
		static $errorlevel = null;

		if (!is_null($level)) 
		{
			$errorlevel = error_reporting();
			error_reporting($level);
		}
		else 
		{
			if (is_null($errorlevel)) 
			{
				$errorlevel = error_reporting();
			}
			error_reporting($errorlevel);
		}
	}
}

// ------------------------------------------------------------------------

/**
 * UTF-8的中文字符串转为GBK
 *
 * @param           $str                string          字符串
 * @return                              string          字符串
 */
if(!function_exists('utf8_to_gbk')) {
	function utf8_to_gbk($str) 
	{
		$str = (string)$str;
		$str = mb_convert_encoding($str, 'GBK', 'UTF-8');
		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取最近的错误级别信息，不包括warning、notice
 *
 * @param           $key                string          错误字段
 * @return          string|array|false
 */
if(!function_exists('get_last_error')) {
	function get_last_error($key = '') 
	{
		$key = strval($key);
		$e = error_get_last();
		if (isset($e[$key])) 
		{
			return $e[$key];
		}
		elseif (empty($key)) 
		{
			return $e;
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 构建路径
 *
 * @param           $path               string          路径
 * @param           $mode               int             目录权限
 * @return                              bool            布尔值
 */
if(!function_exists('build_dir')) {
	function build_dir($path, $mode = 0777) 
	{
		$CI = & get_instance();
		$CI->load->helper('path');

		$path = set_realpath($path);
		if (!empty($path))
		{
			$str = __('no_file_system_permissions');
			if (!is_dir($path) && !@mkdir($path, $mode ,true))
			{
				bug($str);
			}
			return true;
		}

		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 复制文件
 *
 * @param           $from               string          路径
 * @param           $to                 string          路径
 * @return                              bool            布尔值
 */
if(!function_exists('copy_file')) {
	function copy_file($from, $to) 
	{
		$from = strval($from);
		$to = strval($to);
		clearstatcache();

		if (is_dir($to) || !is_file($from)) 
		{
			return false;
		}

		$dir = mb_pathinfo($to, PATHINFO_DIRNAME);
		$res = build_dir($dir);
		if (false === $res) 
		{
			return false;
		}

		if (copy($from, $to)) 
		{
			return true;
		}
		return false;
	}
}


// ------------------------------------------------------------------------

/**
 * 递归复制文件
 *
 * @param           $from               string          路径
 * @param           $to                 string          路径
 * @return                              bool            布尔值
 */
if(!function_exists('copy_files')) {
	function copy_files($from, $to) 
	{
		$from = strval($from);
		$to = strval($to);
		clearstatcache();

		if (!is_dir($from) || !is_dir($to)) 
		{
			return false;
		}

		$list = scan_dir($from);
		if (is_array($list)) 
		{
			$res = true;
			foreach ($list as $key=>$val) 
			{
				$src = normalize_path($from . DIRECTORY_SEPARATOR . $val);
				$dst = normalize_path($to . DIRECTORY_SEPARATOR . $val);
				if (is_dir($src)) 
				{
					$res = copy_files($src, $dst);
				}
				elseif (is_file($src)) 
				{
					$res = copy_file($src, $dst);
				}

				if (!$res) 
				{
					break;
				}
			}

			if (!$res) 
			{
				delete_files($to, true);
				return false;
			}
			else 
			{
				return true;
			}
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
 * @return                              bool            
 */
if(!function_exists('is_direction')) {
	function is_direction($str) 
	{
		$str = strtolower(strval($str));
		$arr = array('asc', 'desc');

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
 * 判断是否可用网口
 *
 * @param           $str                string          字符串
 * @return                              bool            
 */
if(!function_exists('is_eth_port')) {
	function is_eth_port($str) 
	{
		$str = strval($str);
		$ports = get_all_eth_port();
		if (in_array($str, $ports)) 
		{
			return true;
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 判断是否合法网络模式
 *
 * @param           $val                integer|string          网络模式代号
 * @return                              bool            
 */
if(!function_exists('is_network_mode')) {
	function is_network_mode($val) 
	{
		$val = intval($val);
		$arr = get_whole_network_mode();
		if (false === array_search($val, $arr)) 
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 判断session文件存在否
 *
 * @param           $cookie             string       session文件cookie值
 * @return                              string|bool  为true返回session文件路径或false
 */
if(!function_exists('is_session_file')) {
	function is_session_file($cookie) 
	{
		$path = session_save_path();
		$sess_dir = $path ? $path : config_item('linux_tmp_folder');
		$sess_file = sprintf('%s/sess_%s', rtrim($sess_dir, '/'), $cookie);
		return is_file($sess_file) ? $sess_file : false;
	}
}

// ------------------------------------------------------------------------

/**
 * 限制每行字符数，并添加后缀
 *
 * @param           $str                $str            字符串
 * @param           $rowlen             integer         字符数
 * @param           $suffix             string          字符串
 * @return                              string            
 */
if(!function_exists('linebreak')) {
	function linebreak($str, $rowlen, $suffix = '<br>') 
	{
		$str = strval($str);
		$suffix = strval($suffix);
		$rowlen = intval($rowlen);
		if (0 === $rowlen) 
		{
			return '';
		}
		$num = ceil(mb_strlen($str,"UTF-8")/$rowlen);
		$strs = array();
		for($j=0;$j<$num;$j++)
		{
			$strs[$j] = mb_substr($str, ($j * $rowlen), $rowlen, "UTF-8");
		}
		$str = implode($suffix, $strs);
		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * 布尔字符串变成布尔值
 *
 * @param           $str                string          布尔字符串
 * @return                              bool            
 */
if ( ! function_exists('boolval'))
{
	function boolval($str)
	{
		$str = strtolower(trim(strval($str)));
		$allows = array('1', 'true', 'false', 'on', 'off', 'yes', 'no', 'y', 'n');
		if (in_array($str, $allows, true)) 
		{
			if (in_array($str, array('1', 'true', 'on', 'yes', 'y'), true)) 
			{
				return true;
			}
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 变量转为数组
 *
 * @param           $mix                string          变量值
 * @return                              mixed            
 */
if ( ! function_exists('to_array'))
{
	function to_array($mix)
	{
		if (is_null($mix) || false == $mix) 
		{
			return array();
		}
		elseif (is_array($mix)) 
		{
			return $mix;
		}
		else 
		{
			return array($mix);
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 生成一个值或范围，如：1-255或65535
 *
 * @param           $mix                array|string          数据
 * @return                              string            
 */
if ( ! function_exists('rangeval'))
{
	function rangeval($mix, $delimiter = '-')
	{
		if (is_array($mix)) 
		{
			$mix = array_values($mix);
			if (count($mix) > 1) 
			{
				return sprintf('%s%s%s', $mix[0], $delimiter, $mix[1]);
			}
			$mix = $mix[0];
		}
		return sprintf('%s', $mix);
	}
}

// ------------------------------------------------------------------------

/**
 * 生成一个区间数组，如：1-255变成array($from=>1, $to=>255)
 *
 * @param           $str                string          字符串
 * @param           $from               string          起始键名
 * @param           $to                 string          结束键名
 * @param           $delimiter          string          分界符
 * @return                              array            
 */
if ( ! function_exists('interval'))
{
	function interval($str, $from = 0, $to = 1, $delimiter = '-')
	{
		$str = strval($str);
		$arr = unjoin($delimiter, $str);
		if (count($arr) > 1) 
		{
			return array($from => $arr[0], $to => $arr[1]);
		}
		return array($from => $arr[0]);
	}
}

// ------------------------------------------------------------------------

/**
 * 统一流量值为指定单位
 *
 * @param           $str                string          流量值(带单位)
 * @param           $unit               string          新单位
 * @param           $base               string          基数
 * @return                              string            
 */
if ( ! function_exists('fluxval'))
{
	function fluxval($str, $unit = 'K', $base = 1024)
	{
		$str = strval($str);
		$v = 0;
		static $units = array();
		if (empty($units)) 
		{
			$units['K'] = pow($base, 1);
			$units['KB'] = $units['K'];
			$units['M'] = pow($base, 2);
			$units['MB'] = $units['M'];
			$units['G'] = pow($base, 3);
			$units['GB'] = $units['G'];
			$units['T'] = pow($base, 4);
			$units['TB'] = $units['T'];
			$units['P'] = pow($base, 5);
			$units['PB'] = $units['P'];
			$units['E'] = pow($base, 6);
			$units['EB'] = $units['E'];
		}

		foreach ($units as $key=>$val) 
		{
			if (false !== stripos($str, $key)) 
			{
				$v = floatval($str) * $val; //转为单位B
				break;
			}
		}

		if (!isset($units[$unit])) 
		{
			return $v . 'B';
		}
		return $v / $units[$unit] . $unit;
	}
}

// ------------------------------------------------------------------------

/**
 * Form Value
 *
 * @param           $field              string          域名字符串
 * @param           $default            mixed           默认值
 * @return                              mixed            
 */
if ( ! function_exists('set_value'))
{
	function set_value($field = '', $default = '', $form_prep = false)
	{
		$CI = & get_instance();
		$CI->load->helper('form');

		$ret = null;
		if (FALSE === ($OBJ =& _get_validation_object()))
		{
			if (!$form_prep) 
			{
				$ret = $_POST[$field];
			}
			else 
			{
				$ret = form_prep($_POST[$field], $field); //HTML转换
			}
		}

		if (!$form_prep) 
		{
			$ret = $OBJ->set_value($field, $default);
		}
		else 
		{
			$ret = form_prep($OBJ->set_value($field, $default), $field);
		}

		if (is_array($default)) //期望是数组
		{
			if (is_string($ret)) 
			{
				return str_to_array($ret);
			}
			else if (is_array($ret)) 
			{
				return $ret;
			}
		}

		if (is_bool($default)) //期望是布尔值 
		{
			return boolval($ret);
		}

		if (is_null($ret)) 
		{
			$ret = $default;
		}

		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * CRC
 *
 * @param           $str                string          字符串
 * @return                              integer|string            
 */
if(!function_exists('crc')) {
	function crc($str) 
	{
		$str = strval($str);
		return sprintf('%u', crc32($str));

	}
}

// ------------------------------------------------------------------------

/**
 * Unique Id
 *
 * @param           $pre                string          前缀
 * @return                              integer|string            
 */
if(!function_exists('uniqueid')) {
	function uniqueid($pre = '') 
	{
		$pre = strval($pre) . getmypid();
		return sprintf('%s', uniqid($pre));

	}
}

// ------------------------------------------------------------------------

/**
 * 获取有效会话在线用户数
 *
 * @param           $second             integer     多少秒内有更新
 * @return                              integer            
 */
if(!function_exists('get_online_clients')) {
	function get_online_clients($second = 30) 
	{
		$dir = '/tmp/';
		$now = exec('date +%s') - intval($second);
		$cnt = 0;
		foreach (glob($dir . 'sess_*') as $path)
		{
			if (is_file($path)) 
			{
				$mtime = filemtime($path);
				if ($mtime > $now) 
				{
					++$cnt;
				}
			}
		}
		return $cnt;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取设备所有网口
 *
 * @return                              array            
 */
if(!function_exists('get_all_eth_port')) {
	function get_all_eth_port() 
	{
		$ret = array();
		exec('ifconfig -a | grep Ethernet', $output, $retval);

		if (0 === $retval && is_array($output)) 
		{
			foreach ($output as $key=>$val) 
			{
				$pos = strpos($val, ' ');
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
 * 获取设备的管理口
 *
 * @return                              string|false            
 */
if(!function_exists('get_dmz_eth_port')) {
	function get_dmz_eth_port() 
	{
		$file0 = '/ac/etc/config/gwmode.conf';
		$file1 = '/etc/sinfor/gwmode.conf';
		$arr = array();
		$ret = '';

		if (is_file($file0)) 
		{
			$arr = @parse_ini_file($file0, true);
		}
		elseif (is_file($file1)) 
		{
			$arr = @parse_ini_file($file1, true);
		}
		else 
		{
			return false;
		}

		if (is_array($arr) && isset($arr['ManageInterface']) && 
			isset($arr['ManageInterface']['ManageEth'])) 
		{
			$ret = $arr['ManageInterface']['ManageEth'];
		}
		else 
		{
			return false;
		}

		return '' === $ret ? false : $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取设备所有网络模式
 *
 * @return                              array            
 */
if(!function_exists('get_all_network_mode')) {
	function get_all_network_mode() 
	{
		$ret = array(
			'网桥或旁路模式' => 1, 
			'路由模式' => 2
		);
		return $ret;
	}
}


// ------------------------------------------------------------------------

/**
 * 获取设备全部网络模式
 *
 * @return                              array            
 */
if(!function_exists('get_whole_network_mode')) {
	function get_whole_network_mode() 
	{
		$ret = array(
			'网桥模式' => 1, 
			'旁路模式' => 3, 
			'路由模式' => 2
		);
		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取设备当前的网络模式
 *
 * @return                              string|false            
 */
if(!function_exists('get_network_mode')) {
	function get_network_mode() 
	{
		$file0 = '/ac/etc/config/gwmode.conf';
		$file1 = '/etc/sinfor/gwmode.conf';
		$arr = array();
		$ret = array();

		if (is_file($file0)) 
		{
			$arr = @parse_ini_file($file0, true);
		}
		elseif (is_file($file1)) 
		{
			$arr = @parse_ini_file($file1, true);
		}
		else 
		{
			return false;
		}

		if (is_array($arr) && isset($arr['Gateway Mode']) && 
			isset($arr['Gateway Mode']['Mode'])) 
		{
			$mode = $arr['Gateway Mode']['Mode'];
			$key = array_search($mode, get_all_network_mode());
			if (false !== $key) 
			{
				array_push($ret, array($key => $mode));
			}
		}
		else 
		{
			return false;
		}

		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 计算时间戳间的时长
 *
 * @param               $ts1            numeric             时间戳
 * @param               $ts2            numeric             时间戳
 * @param               $unit           array               单位
 * @return                              string            
 */
if(!function_exists('time_interval')) {
	function time_interval($ts1, $ts2, $unit = array()) 
	{
		$label = array('day' => '天', 'hour' => '时', 
					   'minute' => '分', 'second' => '秒');
		$unit = array_merge($label, $unit);
		$ts1 = floatval($ts1);
		$ts2 = floatval($ts2);
		if ($ts2 < $ts1) 
		{
			$tmp = $ts1;
			$ts1 = $ts2;
			$ts2 = $tmp;
		}
		$interval = $ts2 - $ts1;

		$second = $interval % 60; // 秒
		$interval = intval($interval / 60); 

		$minute = $interval % 60; // 分
		$interval = intval($interval / 60); 

		$hour = $interval % 24; // 时
		$interval = intval($interval / 24); // 天

		return sprintf('%d%s%d%s%d%s%d%s', 
			$interval, $unit['day'], 
			$hour, $unit['hour'], 
			$minute, $unit['minute'], 
			$second, $unit['second']
		);
	}
}

// ------------------------------------------------------------------------

/**
 * 转义字符串
 *
 * @param               $str            array|string        字符串
 * @param               $like           bool                是否转义 %和_
 * @return                              string            
 */
if(!function_exists('escape')) {
	function escape($str, $like = FALSE)
	{
		$like = str_to_int($like) ? TRUE : FALSE;
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = escape($val, $like);
			}

			return $str;
		}

		if (function_exists('mysql_real_escape_string'))
		{
			$str = mysql_real_escape_string($str);
		}
		else
		{
			$str = addslashes($str);
		}

		// escape LIKE condition wildcards
		if ($like === TRUE)
		{
			$str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * 转义指定字符集
 *
 * @param               $str            array|string        字符串
 * @param               $chars          string              待转义的字符集
 * @param               $escape         string              转义符
 * @return                              string            
 */
if(!function_exists('escape_sqlstr')) {
	function escape_sqlstr($str, $chars = '"\'\\', $escape = '\\')
	{
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = escape_sqlstr($val, $chars, $escape);
			}
			return $str;
		}

		$arr = preg_split('//', $chars);
		array_shift($arr);
		array_pop($arr);
		$arr = $bak = array_unique($arr);
		$idx = array_search($escape, $arr);
		if (false !== $idx) 
		{
			unset($arr[$idx], $bak[$idx]);
			array_unshift($bak, $escape);
		}
		foreach ($arr as $key=>$val) 
		{
			$arr[$key] = $escape . $val;
		}
		if (false !== $idx) 
		{
			array_unshift($arr, str_repeat($escape, 2));
		}
		$str = str_replace($bak, $arr, $str);

		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * 转义sqlite参数
 *
 * @param		$str		string		字符串
 * @param		$str		string		是否为like语句中的字符串
 * @return					string		
 */
if(!function_exists('escape_sqlite_str')) {
	function escape_sqlite_str($str, $like = false) 
	{
		$str = strval($str);
		if (is_empty($str)) 
		{
			return $str;
		}
		else 
		{
			$str = escape_sqlstr($str, '"\'\\', "'");
			if ($like) 
			{
				$str = escape_sqlstr($str, '%_', "'");
			}
			return $str;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 删除目录或文件
 *
 * @param               $path           string              路径
 * @return                              bool            
 */
if(!function_exists('delete_file')) {
	function delete_file($path)
	{
		$path = normalize_path($path);
		$cmd = sprintf('rm -rf %s', escape_shell_arg($path));
		@exec($cmd, $output, $retval);
		if (0 !== $retval) 
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 字符串转为数组，如 1,2,3 转为 array(1,2,3)
 *
 * @param               $str            string              字符串
 * @param               $separator      string              分隔符
 * @return                              array            
 */
if(!function_exists('str_to_array')) {
	function str_to_array($str, $separator = "\n")
	{
		$str = strval($str);
		$separator = strval($separator);
		if (empty($separator)) 
		{
			return array($str);
		}

		$arr = @explode($separator, $str);
		if (empty($str) || !is_array($arr)) 
		{
			$arr = array();
		}
		return $arr;
	}
}


// ------------------------------------------------------------------------

/**
 * 计算文件大小，默认单位为KB
 *
 * @param               $path           string              路径
 * @param               $unit           string              单位
 * @return                              string            
 */
if(!function_exists('file_size')) {
	function file_size($path, $unit = '')
	{
		$unit = strtoupper($unit);
		$path = realpath(normalize_path(strval($path)));
		$res = 0;
		if (file_exists($path)) 
		{
			$cmd = sprintf("du -s %s | awk -F' '  '{print $1}'", escape_shell_arg($path));
			$res = exec($cmd, $output, $retval);
			if (0 !== $retval) 
			{
				return 0;
			}
		}

		switch ($unit) 
		{
			case 'B':
				$res = sprintf('%.2f', $res * 1024);
				break;
			case 'KB':
				$res = sprintf('%.2f', $res);
				break;
			case 'MB':
				$res = sprintf('%.2f', $res/1024);
				break;
			case 'GB':
				$res = sprintf('%.2f', $res/1048576);
				break;
			case 'TB':
				$res = sprintf('%.2f', $res/1073741824);
				break;
			default:
				break;
		}
		return floatval($res);
	}
}

// ------------------------------------------------------------------------

/**
 * 判断是否服务中心端
 *
 * @return                              bool            
 */
if(!function_exists('is_server')) {
	function is_server()
	{
		$flag = '/flow_library/';
		$pos = strrpos(mb_pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME), $flag);
		if (false === $pos) 
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
 * 判断是否可见变量
 *
 * @param		$var		mixed		变量值
 * @return                  bool            
 */
if(!function_exists('is_visible')) {
	function is_visible($var)
	{
		if (!in_array($var, array('', null, false), true)) 
		{
			return true;
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 计算文件(夹)个数
 *
 * @param               $path           string              路径
 * @param               $recursive      bool                是否递归
 * @return                              integer|bool            
 */
if(!function_exists('file_count')) {
	function file_count($path, $recursive = false)
	{
		$path = normalize_path(strval($path));
		$recursive = (bool)$recursive;
		if (is_dir($path)) 
		{
			$path = escape_shell_arg($path);
			if ($recursive) 
			{
				$cmd = sprintf('ls -lR %s | grep "^[d-]" | wc -l', $path);
			}
			else 
			{
				$cmd = sprintf('ls -l %s | grep "^[d-]" | wc -l', $path);
			}

			$res = exec($cmd, $output, $retval);
			if (0 !== $retval) 
			{
				return false;
			}
			return intval($res);
			
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 计算子文件夹的个数（不包含文件）
 *
 * @param               $path           string              路径
 * @param               $recursive      bool                是否递归
 * @return                              integer|bool            
 */
if(!function_exists('directory_count')) {
	function directory_count($path, $recursive = false)
	{
		$path = normalize_path(strval($path));
		$recursive = (bool)$recursive;
		if (is_dir($path)) 
		{
			$path = escape_shell_arg($path);
			if ($recursive) 
			{
				$cmd = sprintf('ls -lR %s | grep "^d" | wc -l', $path);
			}
			else 
			{
				$cmd = sprintf('ls -l %s | grep "^d" | wc -l', $path);
			}

			$res = exec($cmd, $output, $retval);
			if (0 !== $retval) 
			{
				return false;
			}
			return intval($res);
			
		}
		return false;
	}
}


// ------------------------------------------------------------------------

/**
 * 判断文件(夹)是否存在，支持中文名称，
 *
 * @param               $path           string              路径
 * @param               $is_file        null|bool           是否为文件路径
 * @return                              bool            
 */
if(!function_exists('file_exist')) {
	function file_exist($path, $is_file = null)
	{
		$path = normalize_path(strval($path));
		if (!empty($path)) 
		{
			if (true === $is_file && is_file($path))
			{
				return true;
			}
			elseif (false === $is_file && is_dir($path))
			{
				return true;
			}
			elseif (is_null($is_file) && file_exists($path)) 
			{
				return true;
			}
		}
		return false;
	}
}


// ------------------------------------------------------------------------

/**
 * 自定义字符串序列化
 *
 * @param               $arr            array               数组
 * @return                              string            
 */
if(!function_exists('str_serialize')) {
	function str_serialize(array $arr)
	{
		$str = urlencode(strval(@serialize($arr)));
		return (string)$str;
	}
}


// ------------------------------------------------------------------------

/**
 * 自定义字符串反序列化
 *
 * @param               $str            string              字符串
 * @return                              array|false            
 */
if(!function_exists('str_unserialize')) {
	function str_unserialize($str)
	{
		$str = strval($str);
		$str = @unserialize(urldecode($str));
		if (!is_array($str)) 
		{
			return false;
		}
		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * 调试跟踪
 *
 * @param               $limit          intval              堆栈深度
 * @param               $isreal         bool                是否真实内存峰值
 */
if(!function_exists('debug_trace')) {
	function debug_trace($arg, $limit = 255, $isreal = true) 
	{
		$CI = & get_instance();
		if (!(@$CI->debug || 'development' === ENVIRONMENT)) 
		{
			return;
		}

		$file = config_item('temp_folder') . '/' . __FUNCTION__ . '.txt';
		$file = normalize_path($file);
		if (func_num_args() <= 0) 
		{
			@unlink($file);
			return ;
		}

		$limit = intval($limit);
		$isreal = (bool)$isreal;
		$trace = debug_backtrace();
//        $trace = array_slice($trace, 2);

		$trace = array_reverse($trace);
		$str = array('[' . $CI->input->ip_address() . ']' . 
			(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'sh') . 
			' ' . 
			(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/index.php'));
		foreach ($trace as $key=>$val) 
		{
			if (isset($val['class'])) 
			{
				$rc = new ReflectionClass($val['class']);
				$val['file'] = $rc->getFileName();
				$rc = $rc->getMethod($val['function']);
				$val['line'] = $rc->getStartLine();
			}
			$usage = sprintf("%.3fM", memory_get_usage()/1048576); 
			$top = sprintf("%.3fM", memory_get_peak_usage($isreal)/1048576); 
			$val['file'] = isset($val['file']) ? $val['file'] : '';
			$val['line'] = isset($val['line']) ? $val['line'] : '';
			$val['class'] = isset($val['class']) ? $val['class'] : '';
			$val['type'] = isset($val['type']) ? $val['type'] : '';
			$str[] = '[' . $val['file'] . ':' . $val['line'] . ']' . $val['class'] . 
				$val['type'] . $val['function'] . '<' . $usage . '~' . $top . '>';

			if ($key == $limit) 
			{
				break;
			}
		}

		$str[] = var_export($arg, true);

		static $last_ts = null;
		$current_ts = microtime(true);
		static $first_ts = null;
		if (!is_null($last_ts)) 
		{
			$interval = $current_ts - $last_ts;
			$length = $current_ts - $first_ts;
			$str[] = sprintf("<%.6fs~%.6fs>: %s", $interval, $length, date('Y-m-d H:i:s'));
		}
		else 
		{
			if (is_null($first_ts)) 
			{
				$first_ts = $current_ts;
			}
			$str[] = "(0s): " . date('Y-m-d H:i:s');
		}
		$last_ts = $current_ts;

		$str = implode("\r\n", $str) . "\r\n\r\n";
		error_log($str, 3, $file);
	}
}


// ------------------------------------------------------------------------

/**
 * 计算最大单位值
 *
 * @param               $arr            array               数据源
 * @param               $units          array               单位列表
 * @param               $base           integer             单位基数
 * @return                              string              最大单位
 */
if(!function_exists('top_flux_unit')) {
	function top_flux_unit(array $arr, 
						   array $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'), 
						   $base = 1024) 
	{
		if (!is_numeric($base) || $base <= 0) 
		{
			$base = 1024;
		}
		$tmp = $arr;
		sort($tmp, SORT_NUMERIC);
		$tmp = array_reverse($tmp);
		$i = 0;
		if (isset($tmp[0])) 
		{
			$max = floatval($tmp[0]);
			while ($max > $base) 
			{
				$max /= $base;
				$i++;
			}
		}

		$unit = @$units[$i];
		return $unit;
	}
}

// ------------------------------------------------------------------------

/**
 * 将数据转为人类较可观的单位
 *
 * @param               $arr            array               数据源
 * @param               $unit           string              单位
 * @param               $units          array               单位列表
 * @param               $base           integer             单位基数
 * @return                              array|false
 */
if(!function_exists('human_unit_flux')) {
	function human_unit_flux(array $arr, 
							 $unit, 
							 array $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'), 
							 $base = 1024) 
	{
		if (!is_numeric($base) || $base <= 0) 
		{
			$base = 1024;
		}
		$unit = strval($unit);
		$i = array_search($unit, $units);
		if (false === $i) 
		{
			return false;
		}
		$tmp = pow($base, $i);
		foreach ($arr as $key=>$val) 
		{
			$arr[$key] = round(floatval($val/$tmp), 2);
		}
		return $arr;
	}
}

// ------------------------------------------------------------------------

/**
 * 自动检测数值并设置单位，自动设置单位B,KB，MB，GB等的
 *
 * @param               $arr            array               数据源
 * @param               $unit           string              单位
 * @param               $units          array               单位列表
 * @param               $base           integer             单位基数
 * @return                              array|false
 */
if(!function_exists('get_auto_unit')) {
	function get_auto_unit($val ) 
	{
		if(!is_numeric($val))
		{
			return '';
		}
		if($val >= 1024*1024*1024*1024)
			return "TB";

		if($val >= 1024*1024*1024)
			return "GB";

		if($val >= 1024*1024)
			return "MB";

		if($val >= 1024)
			return "KB";

		if($val >= 0)
			return "B";

		return '';

	}
}

// ------------------------------------------------------------------------

/**
 * 将数值转化成易读形式，自动设置单位B,KB，MB，GB等的
 *
 * @param               $arr            array               数据源
 * @param               $unit           string              单位
 * @param               $units          array               单位列表
 * @param               $base           integer             单位基数
 * @return                              array|false
 */
if(!function_exists('auto_unit_flux')) {
	function get_num_by_unit($val, $unit) 
	{
		if(!is_numeric($val))
			return $val;
		switch($unit)
		{
		case 'TB':
			return round($val/(floatval(pow(1024,4))), 2);
			break;
		case 'GB':
			return round($val/(floatval(pow(1024,3))), 2);
			break;
		case 'MB':
			return round($val/(floatval(pow(1024,2))), 2);
			break;
		case 'KB':
			return round($val/(floatval(1024)), 2);
			break;
		case 'B':
			return $val;
			break;
		default:
			return $val;
			break;
			
		}
	}
}

// ------------------------------------------------------------------------

/**
* Error Handler
*
* This function lets us invoke the exception class and
* display errors using the standard error template located
* in application/errors/errors.php
* This function will send the error page directly to the
* browser and exit.
*
* @access	public
* @return	void
*/
if ( ! function_exists('show_page'))
{
	function show_page($message, $status_code = 500, $heading = 'Tip Message', $page = 'tip_general')
	{
		$heading = __($heading);
		$_error =& load_class('Exceptions', 'core');
		echo $_error->show_error($heading, $message, $page, $status_code);
		exit;
	}
}

// ------------------------------------------------------------------------

/**
 * 调试日志格式
 *
 * @param               $file           string              文件
 * @param               $fn             string              函数
 * @param               $line           string              行号
 * @param               $msg            string              消息
 * @return                              string
 */
if(!function_exists('debug_msg')) {
	function debug_msg($file, $fn, $line, $msg) 
	{
		$msg = sprintf('File:%s, Function:%s, Line:%s, Msg:%s', $file, $fn, $line, $msg);
		return $msg;
	}
}

// ------------------------------------------------------------------------

/**
 * 转义xml特殊字符
 *
 * @param               $str            string              字符串
 * @return                              string
 */
if(!function_exists('xmlentities')) {
	function xmlentities($str)
	{
		return str_replace(array('&', '"', "\r", '<', '>' ),
						   array('&amp;', '&quot;', '&#13;', '&lt;', '&gt;'),
						   $str);
	}
}


// ------------------------------------------------------------------------

/**
 * 判断是否empty，如 false, '', null, 0
 *
 * @param               $val            mixed              任何值
 * @return                              string
 */
if(!function_exists('is_empty')) {
	function is_empty($val)
	{
		if (empty($val)) 
		{
			if ('0' === $val) 
			{
				return false;
			}
			return true;
		}
		return false;
	}
}


// ------------------------------------------------------------------------

/**
 * 判断是否为LINUX设备
 *
 * @return          bool
 */
if(!function_exists('work_on_linux')) {
	function work_on_linux()
	{
		return 0 === stripos(PHP_OS, 'WIN') ? false : true;
	}
}

// ------------------------------------------------------------------------

/**
 * 偏移转页码
 *
 * @param       $start      integer         偏移(从0开始)
 * @param       $pagesize   integer         每页记录数
 * @return      页码
 */
if(!function_exists('offset2pageindex')) {
	function offset2pageindex($start, $pagesize = 20) 
	{
		$start = intval($start);
		$pagesize = intval($pagesize);
		if ($start < 0 || $pagesize <= 0) 
		{
			return false;
		}

		$page = 1;
		if ($start < $pagesize) 
		{
			return $page;
		}

		$start++;
		$page = intval($start / $pagesize);
		if ($start % $pagesize > 0) 
		{
			$page++;
		}
		return $page;
	}
}

// ------------------------------------------------------------------------


/**
 * 页码转偏移
 *
 * @param       $pageindex  integer         页码(从1开始)
 * @param       $pagesize   integer         每页记录数
 * @return      偏移
 */
if(!function_exists('pageindex2offset')) {
	function pageindex2offset($pageindex, $pagesize = 20) 
	{
		$pageindex = intval($pageindex);
		$pagesize = intval($pagesize);
		if ($pageindex < 1 || $pagesize <= 0) 
		{
			return false;
		}

		$offset = ($pageindex - 1) * $pagesize;
		return $offset;
	}
}

// ------------------------------------------------------------------------

/**
 * 优化多天查询，只支持升序
 *
 * @param       $recordset  array           每天的记录总数列表(如：array(array('total'=>100),array('total'=>200,...)))
 * @param       $date_from  string          起始日期
 * @param       $date_to    string          结束日期
 * @param       $start      integer         起始记录索引
 * @param       $limit      integer         每页记录数
 * @return      bool                        是否有优化
 */
if(!function_exists('optimize_multiday_query')) {
	function optimize_multiday_query(array $recordset, & $date_from, & $date_to, & $start = 0, $limit = 20) 
	{
		$total = 0;
		$arr = array();
		foreach ($recordset as $key=>$val) 
		{
			$val = (array)$val;
			array_push($arr, sprintf('%u', reset($val)));
			$total = array_sum($arr);
		}
		unset($recordset);

		$range = day_range($date_from, $date_to);
		if (false === $range || count($arr) !== count($range)) 
		{
			return false;
		}

		$sum = 0; //当前日期之前的记录总数
		foreach ($range as $key=>$val) 
		{
			if (!isset($flag)) 
			{
				$sum += $arr[$key];
				$sub = $sum - $start;
				if ($sub > 0) 
				{
					$date_from = $val;
					$start = $arr[$key] - $sub;
					$sum = $sub;
					$flag = true;
				}
			}

			if (isset($flag)) 
			{
				$sub = $sum - $limit;
				if ($sub >= 0)
				{
					$date_to = $val;
					break;
				}
				$sum += $arr[$key];
			}
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 缓存变量
 *
 * @param       $name       string          变量名称
 * @param       $value      string          变量值
 * @param       $ttl        integer         有效期,单位秒
 * @return      mixed|void
 */
if(!function_exists('cache_var')) {
	function cache_var($name, $value = null, $ttl = 900)
	{
		$CI = & get_instance();
		$CI->load->driver('cache', array('adapter' => 'file'));
		$name = strval($name);
		$ttl = intval($ttl);
		if ($ttl <= 0) //删
		{
			$CI->cache->delete($name);
		}
		else if (!is_null($value)) // 写
		{
			$CI->cache->save($name, $value, $ttl);
		}
		else // 读
		{
			$v = $CI->cache->get($name);
			if (false === $v)
			{
				return null;
			}
			else 
			{
				return $v;
			}
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 缓存下载链接，注意文件存储路径不能包含符号‘|’
 *
 * @param       $seed       string          种子
 * @param       $value      array           array('path'=>'文件存储路径', 'filename'=>'下载文件名')
 * @param       $ttl        integer         有效期,单位秒，默认15分钟
 * @return      mixed|void
 */
if(!function_exists('cache_link')) {
	function cache_link($seed, array $value = array(), $ttl = 900)
	{
		if (empty($value)) 
		{
			$value = null;
		}
		elseif (isset($value['path'], $value['filename']))
		{
			$value = sprintf('%s|%s', $value['path'], $value['filename']);
		}
		return cache_var($seed, $value, $ttl);
	}
}

// ------------------------------------------------------------------------

/**
 * 解析缓存的下载链接
 *
 * @param       $seed       string          种子
 * @return      mixed						array('path'=>'文件存储路径', 'filename'=>'下载文件名')
 */
if(!function_exists('uncache_link')) {
	function uncache_link($seed)
	{
		$value = cache_link($seed);
		if ($value) 
		{
			return array_combine(array('path', 'filename'), explode('|', $value));
		}
		return null;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取无符号整数
 *
 * @param       $var        mixed           数值(忽略小数位)
 * @return      float
 */
if(!function_exists('uint')) {
	function uint($var)
	{
		return (float)sprintf('%u', $var);
	}
}


// ------------------------------------------------------------------------

/**
 * bug报错
 *
 * @param       $str        string          消息
 * @return      void
 */
if(!function_exists('bug')) {
	function bug($str)
	{
		$arr = debug_backtrace();
		$arr = $arr[1];
		if (isset($arr['object'])) 
		{
			unset($arr['object']);
		}
		$arr = array('bug'=>$str, 'detail'=>$arr);
		$CI = & get_instance();
		$CI->log->add('debug', $arr);
		$msg = @json_encode($arr); //禁止递归报错
		log_message('error', $msg);
		throw new Exception($str);
	}
}


// ------------------------------------------------------------------------

/**
 * 如果键名key在数组source中存在则复制给数组target
 *
 * @param       $target         array           目标数组
 * @param       $source         array           源数组
 * @param       $key            string          源数组索引
 * @return      void
 */
if(!function_exists('applyif')) {
	function applyif(array &$target, array $source, $key = null)
	{
		if (is_null($key)) 
		{
			foreach ($source as $k=>$v) 
			{
				if (!isset($target[$k]) || (isset($target[$k]) && is_empty($target[$k]))) 
				{
					$target[$k] = $v;
				}
			}
		}
		else 
		{
			if (isset($source[$key]) && 
				(!isset($target[$key]) || (isset($target[$key]) && is_empty($target[$key])))) 
			{
				$target[$key] = $source[$key];
			}
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 获取根据字母自然序号获取对应字母
 *
 * @param       $num            integer         1-26对应A-Z
 * @return      string|false
 */
if(!function_exists('get_letter')) {
	function get_letter($num) 
	{
		static $letters = null;
		if (empty($letters)) 
		{
			$letters = array(1 => chr(65));
			for ($i=66; $i<91; $i++) 
			{
				array_push($letters, chr($i));
			}
		}
		return isset($letters[$num]) ? $letters[$num] : false;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取excel文件的字母列名
 *
 * @param       $numcol         integer         列序号(从1~256)
 * @return      string|false
 */
if(!function_exists('get_excel_column_name')) {
	function get_excel_column_name($numcol) 
	{
		$numcol = intval($numcol);
		if ($numcol > 256 || $numcol <= 0) 
		{
			return false;
		}
		elseif ($numcol > 26) 
		{
			$a = intval($numcol / 26);
			$b = intval($numcol % 26);
			return get_letter($a) . get_letter($b);
		}
		else
		{
			return get_letter($numcol);
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 获取指定目录的磁盘空间占用百分比
 *
 * @param		$dir		string		目录
 * @return      integer|false
 */
if(!function_exists('get_disk_percent')) {
	function get_disk_percent($dir) 
	{
		if (!is_dir($dir)) 
		{
			return false;
		}
		$cmd = sprintf("df %s | tail -n1 | awk '{print $5}'", escape_shell_arg($dir));
		$ret = exec($cmd, $output, $code);
		return intval($ret);
	}
}

// ------------------------------------------------------------------------

/**
 * 查看PHP进程内存限制
 *
 * @return      array
 */
if(!function_exists('memory_limit')) {
	function memory_limit() 
	{
		$ret = array();
		$ret['php'] = ini_get('memory_limit');
		exec('ulimit -a', $output);
		$ret['server'] = $output;
		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 字符串转16进制
 *
 * @return      string
 */
if(!function_exists('str_to_hex')) {
	function str_to_hex($string)
	{
		$hex = '';
		$len = strlen($string);
		for ($i = 0; $i < $len; $i++)
		{
			$hex .= dechex(ord($string[$i]));
		}
		$hex = strtoupper($hex);
		return $hex;
	}
}

// ------------------------------------------------------------------------

/**
 * 16进制转字符串
 *
 * @return      string
 */
if(!function_exists('hex_to_str')) {
	function hex_to_str($hex)
	{
		$string = '';
		$len = strlen($hex) - 1;
		for ($i = 0;$i < $len;$i += 2)
		{
			$string .= chr(hexdec($hex[$i] . $hex[$i+1]));
		}
		return $string;
	}
}

// ------------------------------------------------------------------------

/**
 * 加上UTF8文件BOM头部
 *
 * @param       $handle         resource            文件句柄
 * @return                      integer|false
 */
if(!function_exists('add_utf8_bom')) {
	function add_utf8_bom(& $handle)
	{
		return fwrite($handle, hex_to_str('EFBBBF'));
	}
}

// ------------------------------------------------------------------------

/**
 * 剔除UTF8文件BOM头部
 *
 * @param       $handle         resource            文件句柄
 * @return      void
 */
if(!function_exists('remove_utf8_bom')) {
	function remove_utf8_bom(& $handle)
	{
		if (0 === ftell($handle)) //首次调用
		{
			$head = fread($handle, 3);
			if ($head) 
			{
				if ('EFBBBF' !== str_to_hex($head)) //不是UTF-8 + BOM
				{
					rewind($handle);
				}
			}
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 解析一行csv字符串为数组
 *
 * @param       $handle         resource            文件句柄
 * @param       $length         integer             每行读取长度
 * @param       $d              string              定界符
 * @param       $e              string              包裹符
 * @return      array
 */
if(!function_exists('mb_fgetcsv')) {
	function mb_fgetcsv(& $handle, $length = null, $d = ',', $e = '"') 
	{
		remove_utf8_bom($handle);

		$d = preg_quote($d);
		$e = preg_quote($e);
		$_line = "";
		$eof=false;
		while ($eof != true) 
		{
			$_line .= (empty ($length) ? fgets($handle) : fgets($handle, $length));
			$itemcnt = preg_match_all('/' . $e . '/', $_line, $dummy);
			if ($itemcnt % 2 == 0)
			{
				$eof = true;
			}
		}
		$_csv_line = preg_replace('/(?: |[ ])?$/', $d, trim($_line));
		$text = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . 
			 ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
		$_csv_pattern = $text;
		preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
		$_csv_data = $_csv_matches[1];
		for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) 
		{
			$text = '/^' . $e . '(.*)' . $e . '$/s';
			$_csv_data[$_csv_i] = preg_replace($text, '$1', $_csv_data[$_csv_i]);
			$_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
		}

		if (empty ($_line)) 
		{
			return false;
		}

		foreach ($_csv_data as $key=>$val) 
		{
			$_csv_data[$key] = iconv2utf8($val);
		}
		return $_csv_data;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取UTF8编码的字符串
 * PS:比较担心big5编码的字符串简体繁体中文混用
 *
 * @param       $str            string          字符串
 * @param       $charset        string          字符串的编码
 * @return      string
 */
if(!function_exists('iconv2utf8')) {
	function iconv2utf8($str, & $charset = array('ASCII', 'GBK', 'UTF-8', 'big5')) 
	{
		 $str = strval($str);
		 if (!is_array($charset)) 
		 {
			$charset = array($charset);
		 }

		 foreach ($charset as $key=>$val) //自动识别
		 {
			$tmp = @iconv($val, 'UTF-8', $str);
			if (false !== $tmp && $str === iconv('UTF-8', $val, $tmp)) 
			{
				$charset = $val;
				return $tmp;
			}
		 }

		 //无法识别
		 return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * 百分比统一化，不带符号%
 * PS: 弊端是会四舍五入
 *
 * @param       $part			float			部分
 * @param       $total			float			全部
 * @param       $precision		integer			小数点后保留位数
 * @return						float
 */
if(!function_exists('percent')) {
	function percent($part, $total, $precision = 1)
	{
		$part = floatval($part);
		$total = floatval($total);
		$percent = $part / $total;
		return round($percent, $precision);
	}
}

// ------------------------------------------------------------------------

/**
 * 将数值转为以秒为单位的整数
 *
 * @param       $val			integer			数值
 * @param       $unit			string			单位，可取值：d(天)、h(时)、m(分)、s(秒)
 * @return						integer
 */
if(!function_exists('secondval')) {
	function secondval($val, $unit = 's')
	{
		$val = intval($val);
		$unit = strtolower(strval($unit));
		switch ($unit) 
		{
			case 'd':
			case 'day':
				$val *= 86400;
				break;
			case 'h':
			case 'hour':
				$val *= 3600;
				break;
			case 'm':
			case 'min':
			case 'minute':
				$val *= 60;
				break;
			case 's':
			case 'sec':
			case 'second':
			default:
				break;
		}
		return $val;
	}
}

// ------------------------------------------------------------------------

/**
 * 满足is_empty()的变量转为NULL
 *
 * @param       $val			mixed			变量
 * @return						mixed
 */
if(!function_exists('to_null')) {
	function to_null($val)
	{
		if (in_array($val, array('', 0, null, false), true)) 
		{
			return null;
		}
		return $val;
	}
}

// ------------------------------------------------------------------------

/**
 * 交互两个变量的值
 *
 * @param       $val1			mixed			变量1
 * @param       $val2			mixed			变量2
 * @return						void
 */
if(!function_exists('swap')) {
	function swap(& $val1, & $val2)
	{
		$tmp = $val1;
		$val1 = $val2;
		$val2 = $tmp;
	}
}

// ------------------------------------------------------------------------

/**
 * str_split的替代函数
 *
 * @param       $str			string			字符串
 * @param       $limit			integer			截取字符个数
 * @return						array
 */
if(!function_exists('mb_str_split')) {
	function mb_str_split($str, $limit) 
	{
		$i = 0;
		$ret = array();
		$s = mb_substr($str, $i, $limit);
		while ('' !== $s) 
		{
			array_push($ret, $s);
			$i += $limit;
			$s = mb_substr($str, $i, $limit);
		}
		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 判断utf-8字符的编码位数
 *
 * @param       $char			string			utf-8字符
 * @return						integer
 */
if(!function_exists('lenofchar')) {
	function lenofchar($char) 
	{
		$char = ord($char);
		$lenofbit = 0;
		if(($char & 0x80) == 0x00)
		{
			//一位的编码
			$lenofbit = 1;
		}
		else if(($char & 0xE0) == 0xC0)
		{
			//二位的编码
			$lenofbit = 2;
		}
		else if(($char & 0xF0) == 0xE0)
		{
			//三位的编码
			$lenofbit = 3;
		}
		else if(($char & 0xF8) == 0xF0)
		{
			//四位编码
			$lenofbit = 4;
		}
		
		return $lenofbit;
	}
}

// ------------------------------------------------------------------------

/**
 * 判断utf-8字符串的长度
 *
 * @param       $str			string			utf-8字符串
 * @return						integer
 */
if(!function_exists('mb_strlen')) {
	function mb_strlen($str) 
	{
		$len = strlen($str);
		$i = 0;
		$ret = 0;
		while ($i < $len) 
		{
			$char = substr($str, $i, 1);
			$bit = lenofchar($char);
			++$ret;
			$i += $bit;
		}
		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 替换mb_convert_encoding函数不存在时的实现
 *
 * @param           $str                string          字符串
 * @param           $to_charset         string          目标编码
 * @param           $from_charset       string          源编码
 * @return                              string          字符串
 */
if(!function_exists('mb_convert_encoding')) {
	function mb_convert_encoding($str, $to_charset, $from_charset) 
	{
		$str = (string)$str;
		$str = @iconv($from_charset, $to_charset, $str);
		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * 把字符串切成数组，每个元素表示一个UTF8字符
 *
 * @param           $str                string          字符串
 * @return                              array           字符串
 */
if(!function_exists('mb_strarr')) {
	function mb_strarr($str) 
	{
		$len = strlen($str);
		$i = 0;
		$ret = array();
		while ($i < $len) 
		{
			$char = substr($str, $i, 1);
			$bit = lenofchar($char);
			$ret[] = substr($str, $i, $bit);
			$i += $bit;
		}
		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 替换mb_substr函数不存在时的实现
 *
 * @param           $str                string          字符串
 * @param           $start              string          起始偏移
 * @param           $length             string          字符长度
 * @return                              string          字符串
 */
if(!function_exists('mb_substr')) {
	function mb_substr($str, $start, $length) 
	{
		$arr = mb_strarr($str);
		$arr = array_slice($arr, $start, $length);
		return implode('', $arr);
	}
}

// ------------------------------------------------------------------------

/**
 * 拼音排序回调函数
 *
 * @param       $a				string			字符串
 * @param       $b				string			字符串
 * @return						integer
 */
if(!function_exists('pinyin_sort_cmp')) {
	function pinyin_sort_cmp($a, $b) 
	{
		$a = strval($a);
		$b = strval($b);
		$a = @iconv('utf-8', 'GB18030', $a);
		$b = @iconv('utf-8', 'GB18030', $b);
		return strnatcmp($a, $b);
	}
}

// ------------------------------------------------------------------------

/**
 * 密码转义
 *
 * @param		$str			string		字符串
 * @return						string
 */
if(!function_exists('escape_pwd')) {
	function escape_pwd($str) 
	{
		$str = strval($str);
		$str = str_replace(array("\\", '"', '`', '$'), array("\\\\", "\\\"", "\\`", "\\$"), $str);
		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * 字符串转义
 *
 * @param		$str			string		字符串
 * @return						string
 */
if(!function_exists('escape_str')) {
	function escape_str($str) 
	{  
		$str = strval($str);
		$reg = "/(\"|'|\\\\)/";
		$rep = '\\\\$1';
		$str = preg_replace($reg, $rep, $str);
		return $str;
	} 
}

// ------------------------------------------------------------------------

/**
 * 正则字符串转义
 *
 * @param		$str			string		字符串
 * @return						string
 */
if(!function_exists('escape_regex')) {
	function escape_regex($str) 
	{  
		$str = strval($str);
		$reg = "/([\-\.\*\+\?\^\$\{\}\(\)\|\[\]\/\\\\])/";
		$rep = '\\\\$1';
		$str = preg_replace($reg, $rep, $str);
		return $str;
	} 
}

// ------------------------------------------------------------------------

/**
 * 判断是否WAC主机还是备机
 *
 * @return						bool
 */
if(!function_exists('is_slave_wac')) {
	function is_slave_wac() 
	{
		clearstatcache();
		if (is_file('/tmp/slave_master_mode')) //存在此文件则认为是备机
		{
			return true;
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 判断是否精简版WAC
 *
 * @return						bool
 */
if(!function_exists('is_lite_wac')) {
	function is_lite_wac() 
	{
		//exec('head /app/appversion | grep -e "WAC.*-xp-Build.*"', $output, $code);
		exec('head /app/appversion | grep -e "^MINIWAC"',$output,$code);
		if (0 === $code) 
		{
			if ($output > 0) 
			{
				return true;
			}
		}
		return false;
	}
}

// ------------------------------------------------------------------------

/**
 * 当AP名称为MAC地址时，转换AP名称
 *
 * @param		$name		string			AP名称
 * @return					string			
 */
if(!function_exists('ap_mac_name')) {
	function ap_mac_name($name) 
	{
		$name = str_replace(array('-', ':'), '_', $name); //因为名称校验不支持-、:等符号
		return $name;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取树节点
 *
 * @param		$tree			array			EXTJS树
 * @param		$id				integer			节点ID
 * @param		$fields			array			字段名
 * @param		$callback		array|string	回调函数，参数($node<当前正在遍历的节点>)
 * @return						array|null		节点
 */
if(!function_exists('get_tree_node')) {
	function get_tree_node(array $tree, 
						   $id, 
						   $fields = array('id' => 'id', 'name' => 'name', 'children' => 'children'), 
						   $callback = null) 
	{
		static $match = false;
		static $level = 1;
		if (1 === $level) 
		{
			$tree = object2array($tree);
		}
		foreach ($tree as $key=>$val) 
		{
			if (is_empty($callback)) 
			{
				if (0 === strcmp($val[$fields['id']], $id)) 
				{
					$match = true;
				}
			}
			else 
			{
				if (call_user_func_array($callback, array($val, $tree, $id, $fields, $callback))) 
				{
					$match = true;
				}
			}

			if ($match) 
			{
				$match = false;
				return $val;
			}
			elseif (isset($val[$fields['children']]) && 
				is_array($val[$fields['children']])) 
			{
				++$level;
				$ret = get_tree_node($val[$fields['children']], $id, $fields, $callback);
				--$level;
				if (!is_null($ret))
				{
					return $ret;
				}
			}
		}
		$match = false;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取树节点路径
 *
 * @param		$tree			array			EXTJS树
 * @param		$id				integer			节点ID
 * @param		$fields			array			字段名
 * @param		$callback		array|string	回调函数，参数($node<当前正在遍历的节点>)
 * @return						string			路径
 */
if(!function_exists('get_tree_path')) {
	function get_tree_path(array $tree, 
						   $id, 
						   $fields = array('id' => 'id', 'name' => 'name', 'children' => 'children'), 
						   $callback = null) 
	{
		static $paths = array();
		static $match = false;
		static $level = 1;
		if (1 === $level) 
		{
			$tree = object2array($tree);
		}
		foreach ($tree as $key=>$val) 
		{
			array_push($paths, $val[$fields['name']]);
			if (is_empty($callback)) 
			{
				if (0 === strcmp($val[$fields['id']], $id)) 
				{
					$match = true;
				}
			}
			else 
			{
				if (call_user_func_array($callback, array($val, $tree, $id, $fields, $callback))) 
				{
					$match = true;
				}
			}

			if (!$match && isset($val[$fields['children']]) && 
				is_array($val[$fields['children']])) 
			{
				++$level;
				get_tree_path($val[$fields['children']], $id, $fields, $callback);
				--$level;
			}

			if (!$match) 
			{
				array_pop($paths);
			}
			else 
			{
				$ret = '/' . trim(implode('/', $paths), '/');
				if (1 === $level) 
				{
					$paths = array();
					$match = false;
				}
				return $ret;
			}
		}

		if (1 === $level) 
		{
			$paths = array();
			$match = false;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 获取树节点的某个值
 *
 * @param		$tree			array			EXTJS树
 * @param		$id				integer			节点ID
 * @param		$catch			string			提取字段名
 * @param		$fields			array			字段名
 * @param		$callback		array|string	回调函数，参数($node<当前正在遍历的节点>)
 * @return						string|null		值
 */
if(!function_exists('get_tree_value')) {
	function get_tree_value(array $tree, 
							$id, 
							$catch = 'name',
							$fields = array('id' => 'id', 'name' => 'name', 'children' => 'children'), 
							$callback = null) 
	{
		static $level = 1;
		if (1 === $level) 
		{
			$tree = object2array($tree);
		}
		foreach ($tree as $key=>$val) 
		{
			if ($val[$fields['id']] === $id) 
			{
				if ($callback) 
				{
					return call_user_func_array($callback, 
						array($val, $tree, $id, $catch, $fields, $callback));
				}
				return $val[$catch];
			}
			else 
			{
				if (isset($val[$fields['children']]) && 
					is_array($val[$fields['children']])) 
				{
					++$level;
					$ret = get_tree_value(
						$val[$fields['children']], 
						$id, 
						$catch, 
						$fields, 
						$callback
					);
					--$level;
					if (!is_null($ret))
					{
						return $ret;
					}
				}
			}
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 获取图表的某个轴数据，左补点
 *
 * @param		$dataset		array|object	数据源
 * @param		$ts_field		string			时间戳字段
 * @param		$start_ts		integer			起始时间戳
 * @param		$interval		integer			数据点间跨度，每隔多少秒一个点
 * @param		$zero_field		string|array	数据字段
 * @return						array
 */
if(!function_exists('left_pad_chart_axis')) {
	function left_pad_chart_axis($dataset, $ts_field, $start_ts, $interval, $zero_field = array()) 
	{
		$interval = intval($interval);
		$dataset = object2array($dataset);
		if (empty($dataset)) 
		{
			return $dataset;
		}

		if (!is_array($zero_field)) 
		{
			$zero_field = array($zero_field);
		}
		$first = current($dataset);
		if (!isset($first[$ts_field])) 
		{
			$first[$ts_field] = $start_ts;
		}

		$first[$ts_field] -= $interval;
		while ($start_ts < $first[$ts_field]) 
		{
			$row = array($ts_field => $first[$ts_field]);
			foreach ($zero_field as $key=>$val) 
			{
				$row[$val] = 0;
			}
			array_unshift($dataset, $row);
			$first[$ts_field] -= $interval;
		}
		return $dataset;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取图表的某个轴数据，可根据参数合并点数及补点
 *
 * @param		$dataset		array|object	数据源
 * @param		$field			string			数据字段
 * @param		$step			integer			要合并点数
 * @param		$num			integer			数据总点数
 * @param		$precision		integer			数据精度
 * @param		$top			bool			取峰值
 * @return						array
 */
if(!function_exists('get_chart_axis')) {
	function get_chart_axis($dataset, $field, $step = 1, $num = null, $precision = null, $top = false) 
	{
		$ret = array();
		$step = intval($step);
		$dataset = object2array($dataset);
		if (!is_null($num)) //补点
		{
			$num = $num - count($dataset);
			if ($num > 0) 
			{
				while ($num--) 
				{
					$dataset[] = array($field => 0);
				}
			}
		}

		$count = count($dataset);
		$mod = $count % $step;
		if (0 !== $mod)  //分组补点
		{
			$cnt = $step - $mod;
			while ($cnt--) 
			{
				$dataset[] = array($field => 0);
			}
		}

		$offset = 1;
		foreach ($dataset as $key=>$val) 
		{
			if (1 === $step) 
			{
				if (isset($val[$field])) 
				{
					array_push($ret, $val[$field]);
				}
				else 
				{
					return array();
				}
			}
			elseif (0 === $offset % $step) 
			{
				$grp = array_slice($dataset, $offset - $step, $step);
				$points = array();
				foreach ($grp as $k=>$v) 
				{
					if (isset($v[$field])) 
					{
						array_push($points, $v[$field]);
					}
					else 
					{
						return array();
					}
				}

				if (!$top) //求平均值
				{
					if (is_null($precision)) 
					{
						array_push($ret, array_sum($points) / $step);
					}
					else 
					{
						array_push($ret, round(array_sum($points) / $step, $precision));
					}
				}
				else //取峰值
				{
					if (is_null($precision)) 
					{
						array_push($ret, array_max($points));
					}
					else 
					{
						array_push($ret, round(array_max($points), $precision));
					}
				}
			}
			++$offset;
		}
		if (!in_array($field, array('i_noise'), true)) //i_noise可以为负数
		{
			$ret = balance_chart_axis($ret, $precision);
		}
		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 平衡图表的某个轴数据，避免后台服务挂了无数据
 * PS: 此函数是为解决用户体验问题，辅助底层数据异常，防止暴涨，平滑暴跌
 *
 * @param		$dataset		array|object	数据源
 * @param		$precision		integer			数据精度
 * @return						array
 */
if(!function_exists('balance_chart_axis')) {
	function balance_chart_axis(array $dataset, $precision = null) 
	{
		$prev = null; //前点
		foreach ($dataset as $key=> &$val) 
		{
			if ($val > 0) 
			{
				if (($prev > 100) && ($val > $prev) && ($val / $prev > 5)) //针对100以上的数，判断当前点是前一个点的5倍，表示暴涨
				{
					$backup = $val;
					$val = $prev * 5;
					if (!is_null($precision)) 
					{
						$val = round($val, $precision);
					}
					$prev = $backup;
					continue;
				}
				$prev = $val;
				continue;
			}
			else //发生下跌，猜测系统繁忙或服务挂了
			{
				//大于10才做异常处理
				if ($prev > 10)
				{
					$val = $prev / 2;
					if (!is_null($precision)) 
					{
						$val = round($val, $precision);
					}
					$prev = $val;
				}
			}
		}
		return $dataset;
	}
}

// ------------------------------------------------------------------------

/**
 * and so on
 *
 * @param		$arr			array		对象
 * @param		$limit			integer		数量显示上限
 * @return						array
 */
if(!function_exists('and_so_on')) {
	function and_so_on(array $arr, $limit = 10) 
	{
//		$cnt = config_item('logobj_limit');
//		if (!$cnt) 
//		{
//			$cnt = $limit;
//		}
//
//		if(count($arr) > $cnt) {
//			$arr = array_slice($arr, 0, $cnt);
//			$string = implode(__('comma'), $arr) . '...';
//		} else {
//			$string = implode(__('comma'), $arr);
//		}

		$string = implode(__('comma'), $arr);
		return $string;
	}
}

// ------------------------------------------------------------------------

/**
 * 文本末尾追加...
 *
 * @param		$str			string		字符串
 * @param		$len			integer		字符长度
 * @return						string
 */
if(!function_exists('textwrap')) {
	function textwrap($str, $len = 45) 
	{
		$str = strval($str);
		$length = strlen($str);
		if ($length > $len) 
		{
			$exceed = '...';
			$len -= strlen($exceed);
			
		}
		$str = mb_substr($str, 0, $len);
		if (!empty($exceed)) 
		{
			$str .= $exceed;
		}
		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取图表的时间轴数据，可根据参数合并点数及补点
 *
 * @param		$dataset		array|object	数据源
 * @param		$field			string			数据字段
 * @param		$start_ts		integer			起始时间戳
 * @param		$interval		integer			数据点间跨度，每隔多少秒一个点
 * @param		$step			integer			要合并点数
 * @param		$num			integer			数据总点数
 * @return						array
 */
if(!function_exists('get_chart_tsaxis')) {
	function get_chart_tsaxis($dataset, $field, $start_ts, $interval, $step = 1, $num = null) 
	{
		$ret = array();
		$step = intval($step);
		$interval = intval($interval);
		$dataset = object2array($dataset);
		$end = end($dataset);
		if (!is_null($num)) //补点
		{
			$num = $num - count($dataset);
			if ($num > 0) 
			{
				for ($i=1; $i<=$num; $i++) 
				{
					if (!isset($end[$field])) 
					{
						$end[$field] = $start_ts;
					}
					$dataset[] = array($field => $end[$field] + ($interval * $i));
				}
			}
		}

		$count = count($dataset);
		$mod = $count % $step;
		if (0 !== $mod)  //分组补点
		{
			$cnt = $step - $mod;
			$end = end($dataset);
			for ($i=1; $i<=$cnt; $i++) 
			{
				if (!isset($end[$field])) 
				{
					$end[$field] = $start_ts;
				}
				$dataset[] = array($field => $end[$field] + ($interval * $i));
			}
		}

		$offset = 1;
		foreach ($dataset as $key=>$val) 
		{
			if (1 === $step) 
			{
				if (isset($val[$field])) 
				{
					array_push($ret, $val[$field]);
				}
				else 
				{
					return array();
				}
			}
			elseif (0 === $offset % $step) 
			{
				$grp = array_slice($dataset, $offset - $step, $step);
				$points = array();
				foreach ($grp as $k=>$v) 
				{
					if (isset($v[$field])) 
					{
						array_push($points, $v[$field]);
					}
					else 
					{
						return array();
					}
				}
				array_push($ret, round(array_sum($points) / $step));
			}
			++$offset;
		}
		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 触发代码覆盖率分析
 *
 * 可用webgrind查看代码覆盖率文件
 */
if(!function_exists('toggle_code_coverage')) {
	function toggle_code_coverage() 
	{
		if(!extension_loaded('xdebug')) 
		{
			return;
		}

		static $enable = false;
		if (!$enable) 
		{
			xdebug_start_code_coverage();
			$enable = true;
		}
		else 
		{
			xdebug_stop_code_coverage();
			$enable = false;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 裁剪图片
 *
 * @param		$dst_im		string		生成的文件路径
 * @param		$src_im		string		源文件路径
 * @param		$src_x		integer		源文件起点X坐标
 * @param		$src_y		integer		源文件起点Y坐标
 * @param		$src_w		integer		源文件宽
 * @param		$src_h		integer		源文件高
 * @return					bool
 */
if(!function_exists('clip_image')) {
	function clip_image($dst_im , $src_im , $src_x , $src_y , $src_w , $src_h) 
	{
		$picinfo = getimagesize($src_im);
		$suffix = strtolower(mb_pathinfo($src_im, PATHINFO_EXTENSION));
		switch($picinfo['mime']) {
		    case 'image/jpeg':
	    		$source = imagecreatefromjpeg($src_im);
			$suffix = 'jpeg';
		
		    
		    	break;
		    case 'image/png':
		    	$source = imagecreatefrompng($src_im);
			$suffix = 'png';
		    	break;
		    case 'image/gif':
		    	$source = imagecreatefromgif($src_im);
			$suffix = 'gif';
		    	break;
		    case 'image/bmp':
		    	$source = imagecreatefromwbmp($src_im);
			$suffix = 'wbmp';
		    	break;
		    default:
		    	return false;
		}
	/*	switch ($suffix)
		{
			case 'jpg':
			case 'jpeg':
				$source = imagecreatefromjpeg($src_im);
				$suffix = 'jpeg';
				break;
			case 'png':
				$source = imagecreatefrompng($src_im);
				$suffix = 'png';
				break;
			case 'gif':
				$source = imagecreatefromgif($src_im);
				$suffix = 'gif';
				break;
			case 'bmp':
			case 'wbmp':
				$source = imagecreatefromwbmp($src_im);
				$suffix = 'wbmp';
				break;
			default:
				return false;
		}*/
        		$croped = imagecreatetruecolor($src_w, $src_h);
	//	$croped = imagecreate($src_w, $src_h);
		if (!$croped)
		{
			return false;
		}
		//分配颜色
	/*	imagealphablending($croped, true);
		imagesavealpha($croped, true);
		$trans_colour = imagecolorallocatealpha($croped, 0, 0, 0, 127);
    		imagefill($croped, 0, 0, $trans_colour);*/
		$white= imagecolorallocate($croped,255,255,255);//拾取白色
		imagefill($croped,0,0,$white);//把画布染成白色
		imagecolortransparent($croped ,$white );//把图片中白色设置为透明色
		if (!imagecopy($croped, $source, 0, 0, $src_x, $src_y, $src_w, $src_h))
		{
			return false;
		}
		$fn = 'image' . $suffix;
		if (!$fn($croped, $dst_im))
		{
			return false;
		}
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * 判断一个地址是不是mac地址
 * Enter description here ...
 * @param unknown_type $mac
 * @param unknown_type $d
 */
if(!function_exists('is_real_mac'))
{
	function is_real_mac($mac, $d = '\-') 
	{
		return preg_match("/^(([0-9|a-f|A-F]){2}$d){5}([0-9|a-f|A-F]){2}$/", $mac);
	}
}

// ------------------------------------------------------------------------

/**
 * 根据长度字节数限制截断字符串(支持UTF8中文)
 * @param $in_str: 输入的字符串
 * @param $bytes_limit: 字符串长度字节数限制
 */
if(!function_exists('cutstr_bytes'))
{
    function cutstr_bytes($in_str, $bytes_limit) {
    	$rules = array(
    		array(0x80, 0x00),
    		array(0xE0, 0xC0),
    		array(0xF0, 0xE0),
    		array(0xF8, 0xF0),
    	);
    	
    	$in_len = strlen($in_str);
    	//原始数据比限制的要小，无需截断
    	if ($in_len <= $bytes_limit) {
    		return $in_str;
    	}
    	
    	$i = 0;
    	$c = count($rules);
    	$cut_pos = -1;
    	while ($i < $bytes_limit && $cut_pos < 0) {
    		for ($j = 0; $j < $c; $j++) {
    			$r = $rules[$j];
    			$char_ascii = ord($in_str[$i]);
    			//如果ascii码符合utf8规则
    			if (($char_ascii & $r[0]) == $r[1]) {
    				//如果加上utf8字符长度超过限制
    				//这里比真实长度小1，因为最下面还要++，所以判断时要+1
    				if ($i + $j +1 > $bytes_limit) {
    					$cut_pos = $i;
    				}
    				$i += $j;
    				break; //跳出for循环，回到while循环
    			}
    		}
    		$i++;
    	}
    	
    	if ($cut_pos < 0) {
    		$cut_pos = $bytes_limit;
    	}
    
    	return substr($in_str, 0, $cut_pos);
    }
}

// ------------------------------------------------------------------------


/* End of file misc_helper.php */
/* Location: ./application/helpers/misc_helper.php */
