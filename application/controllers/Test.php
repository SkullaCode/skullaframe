<?php
	defined('BASEPATH') OR terminate('No direct script access allowed to '.pathinfo(__FILE__,PATHINFO_FILENAME));

	class  Test extends CI_Controller
	{

		public function index()
		{
			terminate('testing terminate_script '.pathinfo(__FILE__,PATHINFO_FILENAME),401);
		}

		public function api()
		{
		
		}

		public function stream()
		{
			
		}
	}
