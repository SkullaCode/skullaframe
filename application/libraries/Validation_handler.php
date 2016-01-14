<?php

	defined('BASEPATH') OR terminate('No direct script access allowed');

	class Validation_handler
	{
		private $fv;
		private $error;

		public function __construct()
		{
			$ci = get_instance();
			$this->ci->load->library('form_validation');
			$this->fv = $this->ci->form_validation;
			$this->error = &load_class('Ecxeptions', 'core');
			$ci = null;
		}

		public function validate()
		{
			if($this->fv->run() == false)
			{
				foreach($this->fv->error_array() as $key => $val)
				{
					$this->error->handle_error($val, E_USER_ERROR, $key);
				}
				return false;
			}
			else
			{
				return true;
			}
		}

		public function set_data($arr,$rules = array())
		{
			if(is_array($arr))
			{
				$config = array();
				foreach($arr as $key => $val)
				{
					$x = array(
						'field' => $key,
						'label' => ucfirst($key)
					);
					if(is_array($rules) && isset($rules[$key]))
					{
						$x['rules'] = $rules[$key];
					}
					else
					{
						$x['rules'] = 'required';
					}
					array_push($config, $x);
				}
				$this->fv->set_data($arr);
				$this->fv->set_rules($config);
				return true;
			}
			else
			{
				return false;
			}
		}
	}