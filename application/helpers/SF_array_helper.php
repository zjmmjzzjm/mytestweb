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
 * CodeIgniter Array Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/array_helper.html
 */

// ------------------------------------------------------------------------

/**
 * 清除数组中满足empty()的元素
 *
 * @param           $arr                array           数组
 * @return                              array           数组
 */
if(!function_exists('clear_empty_element')) {
	function clear_empty_element(array $arr) 
	{
		foreach ($arr as $key=>$val) 
		{
			if (is_null($val) || '' === $val || false === $val) 
			{
				unset($arr[$key]);
			}
		}
		return $arr;
	}
}

// ------------------------------------------------------------------------

/**
 * 对数组中每个元素执行trim
 *
 * @param           $arr                array           数组
 * @return                              array           数组
 */
if(!function_exists('array_trim')) {
	function array_trim(array $arr) 
	{
		$arr = array_map("trim", $arr);
		return $arr;
	}
}

// ------------------------------------------------------------------------

/**
 * explode的替代函数
 *
 * @param		$separator			string			分隔符
 * @param		$string				string			字符串
 * @return							array			数组
 */
if(!function_exists('unjoin')) {
	function unjoin($separator , $string)
	{
		$separator = strval($separator);
		$string = strval($string);
		$arr = explode($separator, $string);
		return clear_empty_element($arr);
	}
}

// ------------------------------------------------------------------------

/**
 * 自定义array_combine函数
 *
 * @param		$keys			array			键数组
 * @param		$vals			array			值数组
 * @param		$key_val		array			键名=>键值
 * @return						false|array		数组
 */
if(!function_exists('array_ucombine')) {
	function array_ucombine(array $keys, array $vals, array $key_val = array())
	{
		$equal = count($keys) === count($vals) ? true : false;
		if (!$equal) 
		{
			return false;
		}

		if (empty($key_val)) 
		{
			return array_combine($keys, $vals);;
		}
		else
		{
			$fn = function($k, $v) use ($key_val)
			{
				$rs = array();
				foreach ($key_val as $key=>$val) 
				{
					$rs[$key] = $k;
					$rs[$val] = $v;
				}
				return $rs;
			};
			return array_map($fn, $keys, $vals);
		}
	}
}

// ------------------------------------------------------------------------

/**
 * array_pad和array_slice的合并函数
 *
 * @param		$arr				array			数组
 * @param		$size				integer			数组元素个数
 * @param		$value				mixed			值
 * @param		$length				integer			返回数组的元素个数
 * @param		$offset				integer			返回数组的起始偏移
 * @param		$preserve_keys		bool			保留键名
 * @return							array
 */
if(!function_exists('array_pad_slice')) {
	function array_pad_slice(array $arr, $size, $value, $length, $offset = 0, $preserve_keys = false) 
	{
		$arr = array_pad($arr, $size, $value);
		return array_slice($arr, $offset, $length, $preserve_keys);
	}
}

// ------------------------------------------------------------------------

/**
 * array_map和array_pad的合并函数
 *
 * @param		$callback			string|array	回调函数
 * @param		$arr				array			数组
 * @param		$size				integer			数组元素个数
 * @param		$value				mixed			值
 * @return							array
 */
if(!function_exists('array_map_pad')) {
	function array_map_pad($callback, array $arr, $size, $value = null) 
	{
		$arr = array_map($callback, $arr);
		$arr = array_pad($arr, $size, $value);
		return $arr;
	}
}

// ------------------------------------------------------------------------

/**
 * unjoin和array_pad的合并函数
 *
 * @param		$separator			string			分隔符
 * @param		$str				string			字符串
 * @param		$size				integer			数组元素个数
 * @param		$value				mixed			值
 * @return							array
 */
if(!function_exists('unjoin_pad')) {
	function unjoin_pad($separator, $str, $size, $value = null) 
	{
		$arr = unjoin($separator, $str);
		$arr = array_pad($arr, $size, $value);
		return $arr;
	}
}

// ------------------------------------------------------------------------

/**
 * 将对象转为数组
 *
 * @param		$obj				object|array	变量
 * @return							array
 */
if(!function_exists('object2array')) {
	function object2array($obj) 
	{
		$obj = json_encode($obj);
		$arr = json_decode($obj, true);
		return $arr;
	}
}

// ------------------------------------------------------------------------

/**
 * 替换数组中某个元素的值
 *
 * @param		$replace_pairs		array	待替换元素对
 * @param		$arr				array	待替换数组
 * @param		$strict				bool	是否严格替换
 * @return							array
 */
if(!function_exists('array_tr')) {
	function array_tr(array $replace_pairs, array $arr, $strict = true) 
	{
		$strict = (bool)$strict;
		foreach ($replace_pairs as $search=>$replace) 
		{
			$pos = array_keys($arr, $search, $strict);
			foreach ($pos as $key=>$val) 
			{
				$arr[$val] = $replace;
			}
		}
		return $arr;
	}
}

// ------------------------------------------------------------------------

/**
 * 删除数组中某个元素
 *
 * @param		$els				array	待删除元素集
 * @param		$arr				array	待删除数组
 * @param		$strict				bool	是否严格删除
 * @return							array
 */
if(!function_exists('array_rm')) {
	function array_rm(array $els, array $arr, $strict = true) 
	{
		foreach ($els as $search) 
		{
			$pos = array_keys($arr, $search, $strict);
			foreach ($pos as $key=>$val) 
			{
				unset($arr[$val]);
			}
		}
		return $arr;
	}
}


// ------------------------------------------------------------------------

/**
 * 只复制source数组中与数组target有相同键名的元素给数组target
 *
 * @param       $target         array           目标数组
 * @param       $source         array           源数组
 * @return      void
 */
if(!function_exists('array_merge_intersect')) {
	function array_merge_intersect(array $target, array $source)
	{
		$keys = array_intersect_key($target, $source);
		foreach ($keys as $key=>$val) 
		{
			$target[$key] = $source[$key];
		}
		return $target;
	}
}

// ------------------------------------------------------------------------

/**
 * 只复制source数组中与数组target没有相同键名的元素给数组target
 *
 * @param       $target         array           目标数组
 * @param       $source         array           源数组
 * @return      void
 */
if(!function_exists('array_merge_diff')) {
	function array_merge_diff(array $target, array $source)
	{
		$keys = array_diff_key($source, $target);
		foreach ($keys as $key=>$val) 
		{
			$target[$key] = $source[$key];
		}
		return $target;
	}
}

// ------------------------------------------------------------------------

/**
 * 获取一维数组中的最大值
 *
 * @param       $target         array           数组
 * @return      mixed
 */
if(!function_exists('array_max')) {
	function array_max(array $arr, $flag = SORT_NUMERIC)
	{
		 sort($arr, $flag);
		 return array_pop($arr);
	}
}

// ------------------------------------------------------------------------

/**
 * 获取一维数组中的最小值
 *
 * @param       $target         array           数组
 * @return      mixed
 */
if(!function_exists('array_min')) {
	function array_min(array $arr, $flag = SORT_NUMERIC)
	{
		 sort($arr, $flag);
		 return array_shift($arr);
	}
}

// ------------------------------------------------------------------------

/**
 * 移动$recordset中指定的记录集
 * 上移优先级会减1，下移优先级会加1，移动到需要指定优先级
 * 优先级根据记录集的顺序依次递减排列，不支持多条记录集有相同优先级
 * 
 * 注：函数太长，申请免检
 * 
 * @param		$recordset	array		已根据优先级排好序的记录集，下标从0开始，依次递增
 * @param		$idx		array		待移动记录的下标
 * @param		$column		string		优先级字段名称，优先级从1开始，越大优先级越低
 * @param		$dir		string		上移:up,下移:down,移动到:to
 * @param		$priority	integer		指定移动到的优先级，当且仅当to时有效
 * @return					false|bool
 */
if(!function_exists('array_move')) {
	function array_move(array $recordset, array $idx, $column, $dir, $priority = null) 
	{
		$total = count($recordset);
		$count = count($idx);
		if ($total <= 0 || $count <= 0) //没记录，无法移动
		{
			return $recordset;
		}
		$dir = strtolower($dir);
		if (!in_array($dir, array('up', 'down', 'to'), true)) //方向不对
		{
			return false;
		}
		if (!is_null($priority)) //有移动到的优先级
		{
			$priority = intval($priority);
			if ($priority < 1) //越界，优先级重置
			{
				$priority = 1;
			}
			elseif ($priority > $total) 
			{
				$priority = $total;
			}
		}
		foreach ($idx as $key=>$val) 
		{
			if (!isset($recordset[$val])) //记录不存在，剔除
			{
				unset($idx[$key]);
			}
		}

		$i = $priority; //移动到的每条记录的当前优先级
		$pos = ($total - $count) + 1; //可移动到的最后一个位置
		$has_moved = array(); //移动过的记录集
		if ('down' === $dir) 
		{
			$idx = array_reverse($idx);
		}
		foreach ($idx as $key=>$val) //检查移动合法性
		{
			$row = & $recordset[$val];
			switch ($dir) 
			{
				case 'up':
					--$row[$column];
					if ($row[$column] < 1) 
					{
						++$row[$column]; //第一个无法上移就不用移
						$has_moved[$val] = true;
						break;
					}
					else 
					{
						if (isset($has_moved[$val - 1])) //上一个已移过
						{
							++$row[$column]; 
							$has_moved[$val] = true;
							break;
						}
						++$recordset[$val - 1][$column];
					}
					swap($row, $recordset[$val - 1]);
					$has_moved[$val - 1] = true;
					break;
				case 'down':
					++$row[$column];
					if ($row[$column] > $total) 
					{
						--$row[$column]; //最后一个无法下移就不用移
						$has_moved[$val] = true;
						break;
					}
					else 
					{
						if (isset($has_moved[$val + 1])) //下一个已移过
						{
							--$row[$column]; 
							$has_moved[$val] = true;
							break;
						}
						--$recordset[$val + 1][$column];
					}
					swap($row, $recordset[$val + 1]);
					$has_moved[$val + 1] = true;
					break;
				case 'to': 
					if ($priority > $pos) //修正移动到的优先级
					{
						$i = $priority = $pos;
					}
					$row[$column] = $i++; //下一笔待移动到的记录的优先级
					break;
				default:
					break;
			}
		}
		if (is_null($priority)) //完成上移、下移
		{
			return $recordset;
		}

		$j = 1;
		foreach ($recordset as $key=>$val) //梳理优先级
		{
			if (!in_array($key, $idx, true)) //排除被移动的记录
			{
				$recordset[$key][$column] = $j++;
			}
		}

		$new_recordset = array(); //移动后的新纪录集
		$j = 1; //重置优先级
		foreach ($recordset as $key=>$val) 
		{
			if (!in_array($key, $idx, true)) //排除被移动的记录
			{
				if ($val[$column] === $priority) //遇到要移动到的位置
				{
					$inserted = true;
					foreach ($idx as $k=>$v) //插入待移动记录
					{
						array_push($new_recordset, $recordset[$v]);
					}
					$j += $count; //下移待移动的记录数
				}
				$val[$column] = $j++;
				array_push($new_recordset, $val);
			}
		}

		if (empty($inserted)) 
		{
			foreach ($idx as $k=>$v) //插入待移动记录
			{
				array_push($new_recordset, $recordset[$v]);
			}
		}
		return $new_recordset;
	}
}

// ------------------------------------------------------------------------

/**
 * 解析出数组的索引路径
 *
 * @param       $field      string          字段(如：data[smtp][address])
 * @return      string                      如：['data']['smtp']['address']
 */
if(!function_exists('array_index')) {
	function array_index($field) 
	{
		if (preg_match('/([^\[\]]+)(\[.*\])/', $field, $matches)) 
		{
			$matches[2] = str_replace("[", "['", $matches[2]);
			$matches[2] = str_replace("]", "']", $matches[2]);
			$field = sprintf("['%s']%s", $matches[1], $matches[2]);
		}
		else 
		{
			$field = sprintf("['%s']", $field);
		}

		return $field;
	}
}

// ------------------------------------------------------------------------

/**
 * 一维数组转换成多维数组
 *
 * @param		$array			array			一维数组
 * @return						array			多维数组
 */
if(!function_exists('to_multiarray')) {
	function to_multiarray(array $array) 
	{
		$ret = array();
		foreach ($array as $key=>$val) 
		{
			$str = array_index($key);
			eval("\$ret{$str} = \$val;");
		}
		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 对数组分页
 *
 * @param           $array              array           待分段的数组
 * @param           $start              int             起始元素的键
 * @param           $exclude            array           排除的文件或目录
 * @return                              array|false     
 */
if(!function_exists('array_paging')) {
	function array_paging(array $array, $start, $limit) 
	{
		if (is_null($start) || is_null($limit)) 
		{
			return $array;
		}

		$array = array_values($array);
		if (empty($array))
		{
			return array();
		}

		$start = (int)$start;
		$limit = (int)$limit;
		if ($start < 0)
		{
			$start = 0;
		}
		if ($limit <= 0)
		{
			return array();
		}

		$ret = array_slice($array, $start, $limit);
		return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * 判断数组元素是否都为空
 *
 * @param           $array              array           数组
 * @return                              bool 
 */
if(!function_exists('array_empty')) {
	function array_empty(array $array) 
	{
		$empty = true;
		foreach ($array as $key=>$val) 
		{
			if (!is_empty($val)) 
			{
				$empty = false;
				break;
			}
		}
		return $empty;
	}
}

// ------------------------------------------------------------------------

/**
 * 对数组的某列进行排序
 *
 * @param           $array              array           待排序的数组
 * @param           $column             string          列名称    
 * @param           $direction          string          asc:升序或desc:降序
 * @param           $cmp                string          比较函数
 * @return                              array     
 */
if(!function_exists('array_usort')) {
	function array_usort(array $array, $column, $direction = 'desc', $cmp = 'strnatcmp') 
	{
		$array = array_values($array);
		$array = array_reverse($array);
		if (empty($array))
		{
			return array();
		}
		//无需排序
		if ($column == '') {
			return $array;
		}

		$direction = strtolower($direction);
		if (!is_direction($direction)) {
			$direction = 'desc';
		}
		
		//判断该列是否全整型或者全IP地址
		$col_is_num = true;
		$col_is_ipv4 = true;
		foreach($array as &$r) {
			if (is_numeric($r[$column])) {
				$col_is_ipv4 = false;
			}
			elseif (is_ipv4($r[$column])) {
				//将IP转换为整型，方便排序
				$r['_tmp_ipv4_int'] = ip2bin($r[$column]);
				$col_is_num = false;
			} elseif ($cmp === 'pinyin_sort_cmp'){
				//将字符串转为GBK格式，方便拼音
				$r['_tmp_gbk_str'] = @iconv('utf-8', 'GB18030', $r[$column]);
				$col_is_num = false;
				$col_is_ipv4 = false;
			}
		}
		unset($r);
		
		//选择排序比较函数
		if ($col_is_num) {
			$sort_func = function($a, $b) use ($column) {
							if ($a[$column] === $b[$column]) {
								return 0;
							}
							return $a[$column] > $b[$column] ? 1 : -1;
						};
		} elseif ($col_is_ipv4) {
			$sort_func = function($a, $b) {
							if ($a['_tmp_ipv4_int'] === $b['_tmp_ipv4_int']) {
								return 0;
							}
							return $a['_tmp_ipv4_int'] > $b['_tmp_ipv4_int'] ? 1 : -1;
						};
		} elseif ($cmp === 'pinyin_sort_cmp') {
			$sort_func = function($a, $b) use ($column){
							$a1 = isset($a['_tmp_gbk_str'])? $a['_tmp_gbk_str'] : $a[$column];
							$b1 = isset($b['_tmp_gbk_str'])? $b['_tmp_gbk_str'] : $b[$column];
							if ($a1 === $b1) {
								return 0;
							}
							return strnatcmp($a1, $b1);
						};
		} else {
			$sort_func = $cmp;
		}
		//用来做asc和desc取反处理
		$reverse = 1;
		if ('desc' === $direction) {
			$reverse = -1;
		}

		usort($array, function($a, $b) use ($sort_func, $reverse) {
							return $reverse * $sort_func($a, $b);
						}
		);
		//清除临时变量
		foreach ($array as &$r) {
			unset($r['_tmp_gbk_str']);
			unset($r['_tmp_ipv4_int']);
		}
		unset($r);
		return $array;
	}
}

// ------------------------------------------------------------------------

/**
 * 对数组进行搜索
 *
 * @param           $array              array           数组
 * @param           $columns            array			搜索列名
 * @param           $keyword            string			搜索关键字     
 */
if(!function_exists('array_query')) {
	function array_query(array &$array, array $columns, $keyword) 
	{
		$keyword = strval($keyword);
		if (is_empty($keyword)) 
		{
			return;
		}
		$arr = array();

		foreach ($array as $key=>$val) 
		{
			$match = false;
			foreach ($columns as $k=>$v) 
			{
				if (isset($val[$v])) 
				{
					if (is_array($val[$v])) //为数组
					{
						foreach ($val[$v] as $idx=>$value) 
						{
							if (false !== stripos((string)$value, $keyword)) 
							{
								$match = true;
								break;
							}
						}

						if ($match) 
						{
							break;
						}
					}
					elseif (false !== stripos((string)$val[$v], $keyword)) 
					{
						$match = true;
						break;
					}
				}
				else 
				{
					$match = false;
				}
			}

			if ($match) 
			{
				$arr[] = $val;
			}
		}
		$array = $arr;
	}
}

/* End of file SF_array_helper.php */
/* Location: ./system/helpers/SF_array_helper.php */