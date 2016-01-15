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

if(!function_exists('str_startwith')) {
	//第一个是原串,第二个是 部份串
	function str_startwith($str, $needle) {

			return strpos($str, $needle) === 0;


	}

}

if(!function_exists('str_endwith')) {
//第一个是原串,第二个是 部份串
 function str_endwith($haystack, $needle) {   

      $length = strlen($needle);  
      if($length == 0)
      {    
	           return true;  
	        }  
      return (substr($haystack, -$length) === $needle);
  }
}
// ------------------------------------------------------------------------

/**
 * 把字符串中的关键字高亮. 
 * @param           $val            string          字符串
 */
if(!function_exists('highlight_keywords')) {
	function highlight_keywords($str, $words) 
	{
		foreach($words as $v)
		{
			$str = str_replace($v, '<b>'.$v.'</b>', $str);
		}
		return $str;

	}    
}

/**
 *获取扩展名.
 */
if(!function_exists('get_extension')) {
	function get_extension($file) 
	{ 
		$pos = strrpos($file, '.');
		if($pos === FALSE)
			return FALSE;

		return strtolower(substr($file, $pos)); 
	} 
}

/**
 * 判断文件类型. 
 * @param           $val            string          字符串
 */
if(!function_exists('guess_file_type')) {
	function guess_file_type($file) 
	{
		static $res = array(
			".rmvb"=>"视频",
			".mkv"=>"视频",
			".avi" => "视频",
			".rm" => "视频",
			".mp4" => "视频",
			".mpeg" => "视频",
			".mpg" => "视频",
			".mov" => "视频",
			".wmv" => "视频",
			".3gp" => "视频",
			".asf" => "视频",
			".flv" => "视频",
			".mpe" => "视频",
			".vob" => "视频",

			".mp3" => "音频",
			".mid" => "音频",
			".ape" => "音频",
			".cda" => "音频",
			".au" => "音频",
			".mac" => "音频",
			".aac" => "音频",
			".wma" => "音频",
			".ogg" => "音频",
			".wav" => "音频",


			".pdf" => "文档",
			".doc" => "文档",
			".ppt" => "文档",
			".xls" => "文档",
			".xlsx" => "文档",
			".txt" => "文档",
			".md" => "文档",
			".html" => "文档",
			".xml" => "文档",

			".exe" => "程序",
			".lib" => "程序",
			".msi" => "程序",
			".dmg" => "程序",
			".bat" => "程序",
			".sh" => "程序",
			".py" => "程序",
			".js" => "程序",
			".php" => "程序",

			".zip" => "压缩文件",
			".bz2" => "压缩文件",
			".rar" => "压缩文件",
			".7z" => "压缩文件",
			".iso" => "压缩文件",
			".gz" => "压缩文件",
			".tgz" => "压缩文件",

			".jpg" => "图片",
			".png" => "图片",
			".bmp" => "图片",
			".gif" => "图片",
			".tga" => "图片",
		);

		$ext =get_extension($file);
		if($ext === FALSE)
		{

		}
		else if(array_key_exists($ext, $res))
			return $res[$ext];
		return "未知文件";
		
	}    
}
/**
 * 判断种子类型. 
 * @param           $val            string          字符串
 */
if(!function_exists('guess_torrent_type')) {
	function guess_torrent_type($files) 
	{
		$res = array(
			"视频" => 0,
			"音频" => 0,
			"文档" => 0,
			"压缩文件"=>0,
			"程序"=>0,
			"图片" => 0,
			"未知文件"=>0,
		);
		foreach($files as $f)
		{
			$fn = $f['file'];
			$res[guess_file_type($fn)]++;
		}

		if($res["视频"] > 0 && $res['视频'] + 5 > $res['音频'] )
			return "视频";
		if($res["音频"] > 0)
			return "音频";
		if($res["程序"] > 0)
			return "程序";
		if($res["压缩文件"] > 0)
			return "压缩文件";

		if($res["图片"] > 10)
			return "图片";
		if($res["文档"] > 0)
			return "文档";
		return  "未知文件";

	}    
}
// ------------------------------------------------------------------------


/* End of file view_helper.php */
/* Location: ./application/helpers/view_helper.php */
