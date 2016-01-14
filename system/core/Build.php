<?php
	defined('BASEPATH') OR terminate('No direct script access allowed');

	class CI_Build
	{
		private $load = null;
		private $data = null;
		private $error = null;

		public function __construct()
		{
			$this->load = &load_class('Loader', 'core');
			$this->data = &load_class('Data', 'core');
			$this->error = &load_class('Exceptions', 'core');
		}

		public function meta($folder)
		{
			$meta = $this->load->view('global_meta','',true);
			$meta .= $this->load->view($folder.'/meta','',true);
			return $meta;
		}

		public function links($folder)
		{
			$links = $this->load->view('global_links','',true);
			$links .= $this->load->view($folder.'/links','',true);
			return $links;
		}

		public function scripts($folder)
		{
			$scripts = $this->load->view('global_scripts','',true);
			$scripts .= $this->load->view($folder.'/scripts','',true);
			return $scripts;
		}

		public function navigation()
		{
			return $this->load->view('global_navigation','',true);
		}

		public function body($folder,$params)
		{
			return $this->load->view($folder.'/body',$params,true);
		}

		public function json($code = 200)
		{
			$params['http_code'] = $code;
			$params['content_type'] = 'application/json';
			$params['error_count'] = $this->error->get_error_count();
			$params['errors'] = $this->error->get_error_array();
			$params['data_count'] = $this->data->get_data_count();
			$params['data'] = $this->data->get_data_array();
			return $this->load->view('build_json_page',$params,true);
		}

		public function build_template($locale)
		{
			$main['html_properties'] = config_item('html_properties');
			$main['title'] = config_item('site_title').'Home';
			$main['meta'] = $this->meta($locale);
			$main['links'] = $this->links($locale);
			$main['scripts'] = $this->scripts($locale);
			$body['navigation'] = $this->navigation();
			$main['body'] = $this->body($locale);
			return $main;
		}
	}