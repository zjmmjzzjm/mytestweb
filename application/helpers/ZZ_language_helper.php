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
 * CodeIgniter Language Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/language_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Lang
 *
 * Fetches a language variable and translate it
 *
 * @access	public
 * @param	string	the language line
 * @param	string	parameters
 * @return	string
 */
if ( ! function_exists('__'))
{
	function __($line)
	{
		$argc = func_num_args();
		$CI =& get_instance();
		$bak = $line;
		$line = $CI->lang->line($line);
		if (false === $line) 
		{
			if ($argc > 1) 
			{
				$line = $bak;
			}
			else 
			{
				return $bak;
			}
		}

		if ($argc > 1) 
		{
			$args = func_get_args();
			array_shift($args);
			array_unshift($args, $line);
			
			if (preg_match('/{\d+}/', $line)) 
			{
				return call_user_func_array('tr', $args);
			}

			$line = call_user_func_array('sprintf', $args);
			return $line;
		}
		else 
		{
			return $line;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * Translate
 *
 * Fetches a language variable and translate it
 *
 * @access	public
 * @param	string	the language line
 * @param	string	parameters
 * @return	string
 */
if ( ! function_exists('tr'))
{
	function tr($line)
	{
		$args = func_get_args();
		$line = array_shift($args);
		$pos = 0;
		$pairs = array();
		foreach ($args as $val) 
		{
			$pairs[sprintf('{%s}', $pos++)] = strval($val);
		}
		$line = strtr($line, $pairs);
		return $line;
	}
}

// ------------------------------------------------------------------------

/**
 * Lang
 *
 * Fetches a language variable and optionally outputs a form label
 * @return	string
 */
if ( ! function_exists('lang'))
{
	function lang()
	{
		return call_user_func_array('__', func_get_args());
	}
}

// ------------------------------------------------------------------------

/**
 * BIG5 转 GBK
 *
 * @param	$instr		string		字符串
 * @return				string
 */
if ( ! function_exists('b2g'))
{
	function b2g( $instr ) 
	{
		$fp = fopen(APPPATH . 'language/big5-gb.tab', 'r' );

		$len = strlen($instr);
		for( $i = 0 ; $i < $len ; $i++ ) {
			$h = ord($instr[$i]);
			if( $h >= 160 ) {
				$l = ($i+1 >= $len) ? 32 : ord($instr[$i+1]);
				if( $h == 161 && $l == 64 )
					$gb = '  ';
				else {
					fseek( $fp, (($h-160)*255+$l-1)*3 );
					$gb = fread( $fp, 2 );
				}
				$instr[$i] = $gb[0];
				$instr[$i+1] = $gb[1];
				$i++;
			}
		}
		fclose($fp);
		return $instr;
	}
}


// ------------------------------------------------------------------------

/**
 * GBK 转 BIG5
 *
 * @param	$instr		string		字符串
 * @return				string
 */
if ( ! function_exists('g2b'))
{
	function g2b( $instr ) 
	{
		$fp = fopen(APPPATH . 'language/gb-big5.tab', 'r' );

		$len = strlen($instr);
		for( $i = 0 ; $i < $len ; $i++ ) {
			$h = ord($instr[$i]);
			if( $h > 160 && $h < 248 ) {
				$l = ($i+1 >= $len) ? 32 : ord($instr[$i+1]);
				if( $l > 160 && $l < 255 ) {
					fseek( $fp, (($h-161)*94+$l-161)*3 );
					$bg = fread( $fp, 2 );
				}
				else
					$bg = '  ';
				$instr[$i] = $bg[0];
				$instr[$i+1] = $bg[1];
				$i++;
			}
		}
		fclose($fp);
		return $instr;
	}
}

// ------------------------------------------------------------------------

/**
 * BIG5 转 UTF-8
 *
 * @param	$instr		string		字符串
 * @return				string
 */
if ( ! function_exists('b2u'))
{
	function b2u( $instr ) 
	{
		$fp = fopen(APPPATH . 'language/big5-unicode.tab', 'r' );
		$len = strlen($instr);
		$outstr = '';
		for( $i = $x = 0 ; $i < $len ; $i++ ) {
			$h = ord($instr[$i]);
			if( $h >= 160 ) {
				$l = ( $i+1 >= $len ) ? 32 : ord($instr[$i+1]);
				if( $h == 161 && $l == 64 )
					$uni = '  ';
				else {
					fseek( $fp, ($h-160)*510+($l-1)*2 );
					$uni = fread( $fp, 2 );
				}
				$codenum = ord($uni[0])*256 + ord($uni[1]);
				if( $codenum < 0x800 ) {
					$outstr[$x++] = chr( 192 + $codenum / 64 );
					$outstr[$x++] = chr( 128 + $codenum % 64 );
	#				printf("[%02X%02X]<br>\n", ord($outstr[$x-2]), ord($uni[$x-1]) );
				}
				else {
					$outstr[$x++] = chr( 224 + $codenum / 4096 );
					$codenum %= 4096;
					$outstr[$x++] = chr( 128 + $codenum / 64 );
					$outstr[$x++] = chr( 128 + ($codenum % 64) );
	#				printf("[%02X%02X%02X]<br>\n", ord($outstr[$x-3]), ord($outstr[$x-2]), ord($outstr[$x-1]) );
				}
				$i++;
			}
			else
				$outstr[$x++] = $instr[$i];
		}
		fclose($fp);
		if( $instr != '' )
			return join( '', $outstr);
	}
}

// ------------------------------------------------------------------------

/**
 * UTF-8 转 BIG5
 *
 * @param	$instr		string		字符串
 * @return				string
 */
if ( ! function_exists('u2b'))
{
	function u2b( $instr ) 
	{
		$fp = fopen(APPPATH . 'language/unicode-big5.tab', 'r' );
		$len = strlen($instr);
		$outstr = '';
		for( $i = $x = 0 ; $i < $len ; $i++ ) {
			$b1 = ord($instr[$i]);
			if( $b1 < 0x80 ) {
				$outstr[$x++] = chr($b1);
	#			printf( "[%02X]", $b1);
			}
			elseif( $b1 >= 224 ) {	# 3 bytes UTF-8
				$b1 -= 224;
				$b2 = ord($instr[$i+1]) - 128;
				$b3 = ord($instr[$i+2]) - 128;
				$i += 2;
				$uc = $b1 * 4096 + $b2 * 64 + $b3 ;
				fseek( $fp, $uc * 2 );
				$bg = fread( $fp, 2 );
				$outstr[$x++] = $bg[0];
				$outstr[$x++] = $bg[1];
	#			printf( "[%02X%02X]", ord($bg[0]), ord($bg[1]));
			}
			elseif( $b1 >= 192 ) {	# 2 bytes UTF-8
				printf( "[%02X%02X]", $b1, ord($instr[$i+1]) );
				$b1 -= 192;
				$b2 = ord($instr[$i]) - 128;
				$i++;
				$uc = $b1 * 64 + $b2 ;
				fseek( $fp, $uc * 2 );
				$bg = fread( $fp, 2 );
				$outstr[$x++] = $bg[0];
				$outstr[$x++] = $bg[1];
	#			printf( "[%02X%02X]", ord($bg[0]), ord($bg[1]));
			}
		}
		fclose($fp);
		if( $instr != '' ) {
	#		echo '##' . $instr . " becomes " . join( '', $outstr) . "<br>\n";
			return join( '', $outstr);
		}
	}
}

// ------------------------------------------------------------------------

/**
 * GBK 转 UTF-8
 *
 * @param	$instr		string		字符串
 * @return				string
 */
if ( ! function_exists('g2u'))
{
	function g2u( $instr ) 
	{
		$fp = fopen(APPPATH . 'language/gb-unicode.tab', 'r' );
		$len = strlen($instr);
		$outstr = '';
		for( $i = $x = 0 ; $i < $len ; $i++ ) {
			$h = ord($instr[$i]);
			if( $h > 160 ) {
				$l = ( $i+1 >= $len ) ? 32 : ord($instr[$i+1]);
				fseek( $fp, ($h-161)*188+($l-161)*2 );
				$uni = fread( $fp, 2 );
				$codenum = ord($uni[0])*256 + ord($uni[1]);
				if( $codenum < 0x800 ) {
					$outstr[$x++] = chr( 192 + $codenum / 64 );
					$outstr[$x++] = chr( 128 + $codenum % 64 );
	#				printf("[%02X%02X]<br>\n", ord($outstr[$x-2]), ord($uni[$x-1]) );
				}
				else {
					$outstr[$x++] = chr( 224 + $codenum / 4096 );
					$codenum %= 4096;
					$outstr[$x++] = chr( 128 + $codenum / 64 );
					$outstr[$x++] = chr( 128 + ($codenum % 64) );
	#				printf("[%02X%02X%02X]<br>\n", ord($outstr[$x-3]), ord($outstr[$x-2]), ord($outstr[$x-1]) );
				}
				$i++;
			}
			else
				$outstr[$x++] = $instr[$i];
		}
		fclose($fp);
		if( $instr != '' )
			return join( '', $outstr);
	}
}

// ------------------------------------------------------------------------

/**
 * UTF-8 转 GBK
 *
 * @param	$instr		string		字符串
 * @return				string
 */
if ( ! function_exists('u2g'))
{
	function u2g( $instr ) 
	{
		$fp = fopen(APPPATH . 'language/unicode-gb.tab', 'r' );
		$len = strlen($instr);
		$outstr = '';
		for( $i = $x = 0 ; $i < $len ; $i++ ) {
			$b1 = ord($instr[$i]);
			if( $b1 < 0x80 ) {
				$outstr[$x++] = chr($b1);
	#			printf( "[%02X]", $b1);
			}
			elseif( $b1 >= 224 ) {	# 3 bytes UTF-8
				$b1 -= 224;
				$b2 = ($i+1 >= $len) ? 0 : ord($instr[$i+1]) - 128;
				$b3 = ($i+2 >= $len) ? 0 : ord($instr[$i+2]) - 128;
				$i += 2;
				$uc = $b1 * 4096 + $b2 * 64 + $b3 ;
				fseek( $fp, $uc * 2 );
				$gb = fread( $fp, 2 );
				$outstr[$x++] = $gb[0];
				$outstr[$x++] = $gb[1];
	#			printf( "[%02X%02X]", ord($gb[0]), ord($gb[1]));
			}
			elseif( $b1 >= 192 ) {	# 2 bytes UTF-8
				printf( "[%02X%02X]", $b1, ord($instr[$i+1]) );
				$b1 -= 192;
				$b2 = ($i+1>=$len) ? 0 : ord($instr[$i+1]) - 128;
				$i++;
				$uc = $b1 * 64 + $b2 ;
				fseek( $fp, $uc * 2 );
				$gb = fread( $fp, 2 );
				$outstr[$x++] = $gb[0];
				$outstr[$x++] = $gb[1];
	#			printf( "[%02X%02X]", ord($gb[0]), ord($gb[1]));
			}
		}
		fclose($fp);
		if( $instr != '' ) {
	#		echo '##' . $instr . " becomes " . join( '', $outstr) . "<br>\n";
			return join( '', $outstr);
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 获取转换编码转换函数
 *
 * @param	$original		string		源编码
 * @param	$preferred		string		目标编码
 * @return					array
 */
if ( ! function_exists('get_conversion'))
{
	function get_conversion($original, $preferred) 
	{
		$charset_alias = array(
				'big5'       => 'big5',
				'gb'         => 'gb2312',
				'gbk'        => 'gb2312',
				'gb2312'     => 'gb2312',
				'utf-8'      => 'utf-8',
				'utf8'       => 'utf-8',
				'iso-8859-1' => 'iso-8859-1',
				'iso-8859-2' => 'iso-8859-2',
				'iso-8859-15'=> 'iso-8859-15',
				'de-ascii'   => 'iso-8859-15',
				'us-ascii'   => 'iso-8859-15',
				'fr-ascii'   => 'iso-8859-15',
				'it-ascii'   => 'iso-8859-15',
				'ascii'      => 'iso-8859-15' 
		);

		$original = $charset_alias[trim(strtolower($original))];
		$preferred = $charset_alias[trim(strtolower($preferred))];

		if ( ( $preferred == 'big5' ) && ( $original == 'gb2312' ) ) {
			$convert['to'] = 'g2b';
			$convert['back'] = 'b2g';
			$convert['source'] = 'GB2312';
			$convert['result'] = 'BIG5';
		}
		elseif ( ( $preferred == 'gb2312' ) && ( $original == 'big5' ) ) {
			$convert['to'] = 'b2g';
			$convert['back'] = 'g2b';
			$convert['source'] = 'BIG5';
			$convert['result'] = 'GB2312';
		}
		elseif ( ( $preferred == 'utf-8' ) && ( $original == 'big5' ) ) {
			$convert['to'] = 'b2u';
			$convert['back'] = 'u2b';
			$convert['source'] = 'BIG5';
			$convert['result'] = 'UTF-8';
		}
		elseif ( ( $preferred == 'big5' ) && ( $original == 'utf-8' ) ) {
			$convert['to'] = 'u2b';
			$convert['back'] = 'b2u';
			$convert['source'] = 'UTF-8';
			$convert['result'] = 'BIG5';
		}
		elseif ( ( $preferred == 'utf-8' ) && ( $original == 'gb2312' ) ) {
			$convert['to'] = 'g2u';
			$convert['back'] = 'u2g';
			$convert['source'] = 'GB2312';
			$convert['result'] = 'UTF-8';
		}
		elseif ( ( $preferred == 'gb2312' ) && ( $original == 'utf-8' ) ) {
			$convert['to'] = 'u2g';
			$convert['back'] = 'g2u';
			$convert['source'] = 'UTF-8';
			$convert['result'] = 'GB2312';
		}
		else {
			$convert['to'] = null;
			$convert['back'] = null;
		}
		return($convert);
	}
}

// ------------------------------------------------------------------------

/**
 * 获取浏览器语言，准确性不高
 *
 * @return	string
 */
if ( ! function_exists('get_browser_language'))
{
	function get_browser_language() 
	{
		$lang_option = array(	
				'en'         => 'English',
				'zh-tw'      => 'Chinese (BIG5)',
				'zh-cn'      => 'Chinese (GB)',
				'unicode'    => 'Unicode (UTF-8)',
				'fr'         => 'Fran&ccedil;ais',
				'fi'         => 'Finnish',
				'de'         => 'German',
				'it'         => 'Italiano',
				'sk'         => 'Slovak' 
		);
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) 
		{
			$lang_prefer = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
			$ml = strtolower($lang_prefer[0]);
//			$ml = substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 );
			if( isset($lang_option[$ml]) ) 
			{
				$curr_language = $ml;
			}
			else 
			{
				$curr_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
			}
			return $curr_language;
		}
		else 
		{
			return 'unicode';
		}
	}
}

// ------------------------------------------------------------------------

/**
 * 获取浏览器编码
 *
 * @return	string
 */
if ( ! function_exists('get_browser_charset'))
{
	function get_browser_charset() 
	{
		$lang_coding = array(	
			'en'         => 'iso-8859-1',
			'zh-tw'      => 'BIG5',
			'zh-cn'      => 'GB2312',
			'unicode'    => 'UTF-8',
			'fr'         => 'iso-8859-15',
			'fi'         => 'iso-8859-15',
			'de'         => 'iso-8859-15',
			'it'         => 'iso-8859-15',
			'sk'         => 'iso-8859-2' 
		);
		$curr_language = get_browser_language();
		$curr_charset = $lang_coding[$curr_language];
		return $curr_charset;
	}
}

/* End of file SF_language_helper.php */
/* Location: ./system/helpers/SF_language_helper.php */