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
 * CodeIgniter Path Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/xml_helper.html
 */


// ------------------------------------------------------------------------

/**
 * Set Realpath
 *
 * @param           $path               string          路径
 * @param           $check_existance    bool            检测路径是否存在
 * @return                              string          
 */
if ( ! function_exists('set_realpath'))
{
    function set_realpath($path, $check_existance = FALSE)
    {
        // Security check to make sure the path is NOT a URL.  No remote file inclusion!
        $pattern = "#^(http:\/\/|https:\/\/|www\.|ftp|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})#i";
        if (preg_match($pattern, $path))
        {
            show_page(__('无效URL'));
        }

        // Resolve the path
        if ($check_existance == TRUE AND function_exists('realpath') AND 
            @realpath($path) !== FALSE)
        {
            $path = realpath($path).'/';
        }

        // Add a trailing slash
        $path = preg_replace("#([^/])/*$#", "\\1/", $path);

        // Make sure the path exists
        if ($check_existance == TRUE)
        {
            if ( ! is_dir($path))
            {
                show_page(__('无效路径 {0}', $path));
            }
        }

        return $path;
    }
}

// ------------------------------------------------------------------------

/**
 * 正常化路径
 *
 * @param           $path               string          路径
 * @param           $relative_path      bool            是否允许相对路径
 * @return                              string          正常化后的路径
 */
if(!function_exists('normalize_path')) {
    function normalize_path($path, $relative_path = FALSE) 
    {
        $path = strval($path);
        $path = trim($path);
        $path = remove_invisible_characters($path, false);
        $path = str_replace("\\", '/', $path);

        $CI = & get_instance();
        $CI->load->helper(array('string', 'path'));
        $path = reduce_double_slashes($path);

        $bad = array();
        if (!$relative_path) 
        {
            $bad = array('../', './');
        }
        $path = str_replace($bad, '', $path);

        $path = set_realpath($path);
        $path = rtrim($path, '/');
        return $path;
    }
}

// ------------------------------------------------------------------------

/**
 * 返回相对第二个参数的子路径
 *
 * @param           $path               string          路径
 * @param           $prefix             string          路径
 * @param           $root               string          根路径
 * @return                              string          
 */
if(!function_exists('subpath')) {
    function subpath($path, $prefix = '', $root = '/') 
    {
        $root = strval($root);
        $root = trim($root);
        $path = strval($path);
        $path = trim($path);

        $prefix = strval($prefix);
        $prefix = trim($prefix);
        $prefix = rtrim($prefix, '/') . '/';
        $len = mb_strlen($prefix);

        if (0 === mb_strpos($path, $prefix)) 
        {
            $str = mb_substr($path, $len);
            if (!empty($root)) 
            {
                $str = $root .'/'. $str;
            }
            return normalize_path($str);
        }
        else 
        {
            return false;
        }
        
    }
}


// ------------------------------------------------------------------------

/**
 * escapeshellarg的替代方案
 *
 * @param               $str            string              字符串
 * @param               $lang           string              linux支持的语言
 * @return                              bool            
 */
if(!function_exists('escape_shell_arg')) {
    function escape_shell_arg($str, $lang = '')
    {
        $str = strval($str);
        $lang = strval($lang);
        if (!empty($lang)) 
        {
            setlocale(LC_CTYPE, $lang); // "zh_CN.UTF-8"
            return escapeshellarg($str);
        }
        
        $str = explode("'", $str);
        return "'" . implode("\\'", $str) . "'";
    }
}


// ------------------------------------------------------------------------

/**
 * 过滤路径
 *
 * @param               $arr            array               数组
 * @param               $str            string              查询字符串
 * @return                              array            
 */
if(!function_exists('filter_path_info')) {
    function filter_path_info(array $arr, $str)
    {
        $buffer = array();
        $str = strval($str);

        if (!empty($arr) && !empty($str)) 
        {
            foreach ($arr as $key=>$val) 
            {
                if (false !== strpos($key, $str)) 
                {
                    $tmp_array = explode('/', $key);
                    if (isset($tmp_array[0]) && '' === $tmp_array[0]) 
                    {
                        $tmp_array[0] = '/';
                    }
                    $index = '';
                    foreach ($tmp_array as $k=>$v) 
                    {
                        $index = $index . $v;
                        if (isset($arr[$index]) && !isset($buffer[$index])) 
                        {
                            $buffer[$index] = $arr[$index];
                        }
                        if ('/' !== $index) 
                        {
                            $index .= '/';
                        }
                    }
                }
            }
            return $buffer;
        }
        return $arr;
    }
}


// ------------------------------------------------------------------------

/**
 * pathinfo的替代方案，支持中文
 * 选项支持：PATHINFO_BASENAME、PATHINFO_EXTENSION、
 *           PATHINFO_DIRNAME、PATHINFO_FILENAME
 *
 * @param               $path           string              路径
 * @param               $options        integer             选项
 * @return                              array|string            
 */
if(!function_exists('mb_pathinfo')) {
    function mb_pathinfo($path, $options = null)
    {
        $path = explode(DIRECTORY_SEPARATOR, $path);
        $path = array_map('urlencode', $path);
        $path = implode(DIRECTORY_SEPARATOR, $path);

        if (is_null($options)) 
        {
            $arr = pathinfo($path);
        }
        else 
        {
            $v = pathinfo($path, $options);
            return urldecode($v);
        }

        $arr = array_map('urldecode', $arr);
        return $arr;
    }
}

// ------------------------------------------------------------------------

/**
 * 判断文件后缀是否等价
 * 后缀格式(点号不能少)：.txt，.zip
 *
 * @param               $a              string              后缀或路径
 * @param               $b              string              后缀或路径
 * @return                              bool            
 */
if(!function_exists('is_equal_suffix')) {
    function is_equal_suffix($a, $b)
    {
        $a = trim(strval($a), ' ');
        $b = trim(strval($b), ' ');

        if ('' === mb_pathinfo($a, PATHINFO_FILENAME) || 
            '' === mb_pathinfo($b, PATHINFO_FILENAME)) 
        {
            $a = mb_pathinfo($a, PATHINFO_EXTENSION);
            $b = mb_pathinfo($b, PATHINFO_EXTENSION);
        }

        $a = trim($a, '. ');
        $b = trim($b, '. ');
        if ($a === $b) 
        {
            return true;
        }
        return false;
    }
}

// ------------------------------------------------------------------------

/**
 * 净化路径字符串，将特殊字符串替换为空
 * 禁止路径存在 ../ 或 ./ 或 非法命名字符 等
 *
 * @param           $str                string          路径
 * @param           $relative_path      bool            是否相对路径
 * @return                              string          净化后的路径
 */
if(!function_exists('sanitize_path')) {
    function sanitize_path($str, $relative_path = FALSE) 
    {
        
        $str = (string)$str;
        $relative_path = (bool)$relative_path;

        $bad = array(
                        "<!--",
                        "-->",
                        "<",
                        ">",
                        "'",
                        '"',
                        '&',
                        '$',
                        '#',
                        '{',
                        '}',
                        '[',
                        ']',
                        '=',
                        ';',
                        '?',
                        "%20",
                        "%22",
                        "%3c",      // <
                        "%253c",    // <
                        "%3e",      // >
                        "%0e",      // >
                        "%28",      // (
                        "%29",      // )
                        "%2528",    // (
                        "%26",      // &
                        "%24",      // $
                        "%3f",      // ?
                        "%3b",      // ;
                        "%3d"       // =
                    );

        if ( ! $relative_path)
        {
            $bad[] = '../';
            $bad[] = './';
        }

        $str = remove_invisible_characters($str, FALSE);
        return stripslashes(str_replace($bad, '', $str));
    }
}

/* End of file path_helper.php */
/* Location: ./system/helpers/path_helper.php */