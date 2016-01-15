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
 * 计算流量最大单位值
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
 * 计算流速最大单位值
 *
 * @param               $arr            array               数据源
 * @param               $units          array               单位列表
 * @param               $base           integer             单位基数
 * @return                              string              最大单位
 */
if(!function_exists('top_trend_unit')) {
	function top_trend_unit(array $arr, 
							array $units = array('bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps', 'Pbps', 'Ebps'), 
							$base = 1000) 
	{
		return top_flux_unit($arr, $units, $base);
	}
}

// ------------------------------------------------------------------------

/**
 * 将数据自适应为人类较可观的流量单位
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
			$arr[$key] = round(floatval($val/$denominator), 2);
		}
		return $arr;
	}
}

// ------------------------------------------------------------------------

/**
 * 将数据自适应为人类较可观的流速单位
 *
 * @param               $arr            array               数据源
 * @param               $unit           string              单位
 * @param               $units          array               单位列表
 * @param               $base           integer             单位基数
 * @return                              array|false
 */
if(!function_exists('human_unit_trend')) {
	function human_unit_trend(array $arr, 
							  $unit, 
							  array $units = array('bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps', 'Pbps', 'Ebps'), 
							  $base = 1000) 
	{
		return human_unit_flux($arr, $unit, $units, $base);
	}
}

// ------------------------------------------------------------------------

/**
 * 调整流量
 *
 * @param               $arr            array               二维数据源,如[[123,123434],[533,456],...]
 * @param               $unit           string              单位
 * @return                              array
 */
if(!function_exists('adjust_flux_recordset')) {
	function adjust_flux_recordset(array $arr, $unit = 'KB') 
	{
		$unit = strtoupper(strval($unit));
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
		$i = array_search($unit, $units);
		if (false === $i) 
		{
			return $arr;
		}
		$tmp = pow(1024, $i);
		foreach ($arr as $key=>$val) 
		{
			foreach ($val as $k=>$v) 
			{
				$res = round(floatval($v/$tmp), 0); //流量不要小数点
				if ($res < 1) //不足一个单位的都设为1个单位
				{
					$res = 1;
				}
				$arr[$key][$k] = $res;
			}
		}
		return $arr;
	}
}

// ------------------------------------------------------------------------

/**
 * 调整流速
 *
 * @param               $arr            array               二维数据源,如[[123,123434],[533,456],...]
 * @param               $unit           string              单位
 * @return                              array
 */
if(!function_exists('adjust_trend_recordset')) {
	function adjust_trend_recordset(array $arr) 
	{
		//先算出自适应单位
		$units = array('bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps', 'Pbps', 'Ebps');
		$maxs = array();
		foreach ($arr as $k=>$v) 
		{
			array_push($maxs, array_max($v));
		}
		$unit = top_trend_unit($maxs, $units, 1000);
		
		$i = array_search($unit, $units);
		if (false === $i) 
		{
			return $arr;
		}
		$tmp = pow(1000, $i);
		foreach ($arr as $key=>$val) 
		{
			foreach ($val as $k=>$v) 
			{
				$res = round(floatval($v/$tmp), 2);
				$arr[$key][$k] = $res;
			}
		}
		return $arr;
	}
}

// ------------------------------------------------------------------------

/**
 * 调整单个流量值
 *
 * @param               $value			float               数据
 * @param               $unit           string              单位
 * @return                              float
 */
if(!function_exists('adjust_flux_value')) {
	function adjust_flux_value($value, $unit = 'KB') 
	{
		$unit = strtoupper(strval($unit));
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
		$i = array_search($unit, $units);
		if (false === $i) 
		{
			return $value;
		}

		$tmp = pow(1024, $i);
		$value = round(floatval($value/$tmp), 0); //流量不要小数点
		if (0 != $value) 
		{
			if ($value < 1) //不足一个单位的都设为1个单位
			{
				$value = 1;
			}
		}
		return $value;
	}
}

// ------------------------------------------------------------------------

/**
 * 调整单个流速值
 *
 * @param               $value			float               数据
 * @param               $unit           string              单位
 * @return                              float
 */
if(!function_exists('adjust_trend_value')) {
	function adjust_trend_value($value, $unit = '') 
	{
		$unit = strval($unit);
		if (empty($unit)) 
		{
			$unit = top_trend_unit(array($value));
		}
		
		$units = array('bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps', 'Pbps', 'Ebps');
		$i = array_search($unit, $units);
		if (false === $i) 
		{
			return $value;
		}

		$tmp = pow(1000, $i);
		$value = round(floatval($value/$tmp), 2);
		return $value;
	}
}

/* End of file report_helper.php */
/* Location: ./application/helpers/report_helper.php */