<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR terminate('No direct script access allowed');

/**
 * Exceptions Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Exceptions
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/exceptions.html
 */
class CI_Exceptions {

	/**
	 * Nesting level of the output buffering mechanism
	 *
	 * @var	int
	 */
	public $ob_level;

	/**
	 * List of available error levels
	 *
	 * @var	array
	 */
	public $levels = array(
		E_ERROR			=>	'Error',
		E_WARNING		=>	'Warning',
		E_PARSE			=>	'Parsing Error',
		E_NOTICE		=>	'Notice',
		E_RECOVERABLE_ERROR => 'Recoverable',
		E_CORE_ERROR		=>	'Core Error',
		E_CORE_WARNING		=>	'Core Warning',
		E_COMPILE_ERROR		=>	'Compile Error',
		E_COMPILE_WARNING	=>	'Compile Warning',
		E_USER_ERROR		=>	'User Error',
		E_USER_WARNING		=>	'User Warning',
		E_USER_NOTICE		=>	'User Notice',
		E_STRICT		=>	'Runtime Notice'
	);

	private $err_count = 0;
	private $errors = array();
	private $http_code = 200;

	private function err_count_inc()
	{
		$this->err_count++;
	}

	private function err_count_dec()
	{
		$this->err_count--;
	}

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->ob_level = ob_get_level();
		// Note: Do not log messages from this constructor.
	}

	// --------------------------------------------------------------------

	/**
	 * Exception Logger
	 *
	 * Logs PHP generated error messages
	 *
	 * @param	int	$severity	Log level
	 * @param	string	$message	Error message
	 * @param	string	$filepath	File path
	 * @param	int	$line		Line number
	 * @return	void
	 */
	public function log_exception($severity, $message, $filepath, $line)
	{
		$message = trim($message);
		$severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
		log_message('error', 'Severity::['.$severity.' --> '.$message.']    Filepath::['.$filepath.']    Line::['.$line.']');
	}

	// --------------------------------------------------------------------

	/**
	 * General Error Page
	 *
	 * Takes an error message as input (either as a string or an array)
	 * and displays it using the specified template.
	 *
	 * @param	string		$heading	Page heading
	 * @param	string|string[]	$message	Error message
	 * @param	string		$template	Template name
	 * @param 	int		$status_code	(default: 500)
	 *
	 * @return	string	Error page output
	 */
	public function terminate($status_code = 500)
	{
		if (is_cli())
		{
			exit(1);
		}
		else
		{
			$stati = array(
				100	=> 'Continue',
				101	=> 'Switching Protocols',

				200	=> 'OK',
				201	=> 'Created',
				202	=> 'Accepted',
				203	=> 'Non-Authoritative Information',
				204	=> 'No Content',
				205	=> 'Reset Content',
				206	=> 'Partial Content',

				300	=> 'Multiple Choices',
				301	=> 'Moved Permanently',
				302	=> 'Found',
				303	=> 'See Other',
				304	=> 'Not Modified',
				305	=> 'Use Proxy',
				307	=> 'Temporary Redirect',

				400	=> 'Bad Request',
				401	=> 'Unauthorized',
				402	=> 'Payment Required',
				403	=> 'Forbidden',
				404	=> 'Not Found',
				405	=> 'Method Not Allowed',
				406	=> 'Not Acceptable',
				407	=> 'Proxy Authentication Required',
				408	=> 'Request Timeout',
				409	=> 'Conflict',
				410	=> 'Gone',
				411	=> 'Length Required',
				412	=> 'Precondition Failed',
				413	=> 'Request Entity Too Large',
				414	=> 'Request-URI Too Long',
				415	=> 'Unsupported Media Type',
				416	=> 'Requested Range Not Satisfiable',
				417	=> 'Expectation Failed',
				422	=> 'Unprocessable Entity',

				500	=> 'Internal Server Error',
				501	=> 'Not Implemented',
				502	=> 'Bad Gateway',
				503	=> 'Service Unavailable',
				504	=> 'Gateway Timeout',
				505	=> 'HTTP Version Not Supported'
			);
			
			$status_code = (int)$status_code;
			if (isset($stati[$status_code]))
			{
				$text = $stati[$status_code];
			}
			else
			{
				$text = 'Unknown Status';
			}

			$server_protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
			header($server_protocol.' '.$status_code.' '.$text, TRUE, $status_code);
			$templates_path = config_item('error_views_path');
			if (empty($templates_path))
			{
				$templates_path = VIEWPATH.'errors'.DIRECTORY_SEPARATOR;
			}
			$template = 'html'.DIRECTORY_SEPARATOR.'error_'.$status_code;
			if (ob_get_level() > $this->ob_level + 1)
			{
				ob_end_flush();
			}
			ob_start();
			if(!file_exists($templates_path.$template.'.php'))
			{
				$file = substr((string)$status_code,0,1);
				if($file == '4' || $file == '5')
				{
					include(VIEWPATH.'errors'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'error_'.$file.'00.php');	
				}
				else
				{
					include(VIEWPATH.'errors'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'error_500.php');		
				}
			}
			else
			{
				include($templates_path.$template.'.php');
			}
			$buffer = ob_get_contents();
			ob_end_clean();
			echo $buffer;
			exit(1);
		}
	}

	// --------------------------------------------------------------------

	public function code_to_name($code)
	{
		$x = get_defined_constants();
		foreach($x as $key => $val)
		{
			if($code === $val)
			{
				return $key;
				break;
			}
		}
		return "ERR_ERROR";
	}

	public function name_to_code($name)
	{
		if(defined($name))
		{
			return constant($name);
		}
		else
		{
			return ERR_ERROR;
		}
	}

	public function is_error($code)
	{
		
	}

	public function get_error_level($code)
	{
		if(substr($code,0,4) == "E_US")
		{
			return "USER";
		}
		else
		{
			return "SYSTEM";
		}
	}

	public function get_error_count()
	{
		return $this->err_count;
	}

	public function get_error_array()
	{
		return $this->errors;
	}

	public function get_errors()
	{
		$params = new StdClass();
		$params->error_count = $this->err_count;
		$params->errors = $this->errors;
		return $params;
	}
	
	public function handle_error($message, $code = E_ERROR, $locale = 'system')
	{
		$code = abs($code);
		$code = $this->code_to_name($code);
		$level = $this->get_error_level($code);
		$err = new StdClass();
		$err->error_code = $code;
		$err->error_level = $level;
		$err->error_message = $message;
		$err->error_locale = $locale;
		$this->err_count_inc();
		array_push($this->errors, $err);
	}

	public function handle_output()
	{
		//create a html page to display the information
		//used for debugging purposes.
	}
}
