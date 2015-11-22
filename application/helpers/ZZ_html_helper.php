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
 * Convert special characters to HTML entities
 *
 * @access	public
 * @param	mixed
 * @return	mixed
 */
if ( ! function_exists('html_encode'))
{
	function html_encode($var)
	{
		return html_escape($var);
	}
}

// ------------------------------------------------------------------------

/**
 * Convert special HTML entities back to characters 
 *
 * @access	public
 * @param	mixed
 * @return	mixed
 */
if ( ! function_exists('html_decode'))
{
	function html_decode($var)
	{
		if (is_array($var))
		{
			return array_map('html_decode', $var);
		}
		else
		{
			return htmlspecialchars_decode($var, ENT_QUOTES);
		}
	}
}

/* End of file html_helper.php */
/* Location: ./application/helpers/SF_html_helper.php */