<?php
	defined('BASEPATH') OR terminate('No direct script access allowed');

	class CI_Data
	{
		public $data_count;
		public $data_array;
		private $error;

		public function __construct()
		{
			$this->data_count = 0;
			$this->data_array = array();
			$this->error = load_class('Exceptions', 'core');
		} 

		public function handle_data($action,$name,$value = '')
		{
			switch($action)
			{
				case 'add':
				{
					$this->add_data($name,$value);
					break;
				}
				case 'remove':
				{
					$this->remove_data($name);
					break;
				}
			}
		}

		public function add_data($name,$obj)
		{
			$this->data_array[$name] = $obj;
			$this->data_count++;
		}

		public function remove_data($name)
		{
			$this->data_count--;
			unset($this->data_array[$name]);
		}

		public function get_data()
		{
			$params = new StdClass();
			$params->data_count = $this->data_count;
			$params->data = $this->data_array;
			return $params;
		}

		public function get_data_count()
		{
			return $this->data_count;
		}

		public function get_data_array()
		{
			return $this->data_array;
		}

		public function send_request($arr,$config = array())
		{
			if(empty($config))
			{
				$options = array(
					'http' => array(
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($arr['data'])
					),
				);
			}
			else
			{
				$headers = "";
				foreach($config['headers'] as $key => $val) 
				{
    				$headers .= $key.': '.$val."\r\n";
  				}

  				$options = array(
  					'http' => array(
  						'header' => $headers,
  						'method' => $config['method'],
  						'content' => http_build_query($arr['data'])
  					)
  				);
			}

			$context  = stream_context_create($options);
			$result = trim(file_get_contents($arr['url'], false, $context));
			if(!$result === false)
			{
				$this->add_data($arr['id'],json_decode($result));
				return true;
			}
			else
			{
				$this->error->handle_error('Error occured while retrieving '.$arr['id']);
				return false;
			}
		}
	}