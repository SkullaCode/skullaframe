<?php
	defined('BASEPATH') OR terminate('No direct script access allowed');

	class Home extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();
			define('_VIEW_DIR_','home');
		}

		public function index()
		{
			$main['doctype'] = doctype('html5');
			$main['html_properties'] = config_item('html_properties');
			$main['title'] = config_item('site_title').'Home';
			$main['meta'] = $this->build->meta(_VIEW_DIR_);
			$main['links'] = $this->build->links(_VIEW_DIR_);
			$main['scripts'] = $this->build->scripts(_VIEW_DIR_);
			$body['navigation'] = $this->build->navigation();
			$main['body'] = $this->build->body(_VIEW_DIR_,$body);
			$this->load->view('global_main',$main);
		}
	}