<?php

	defined('BASEPATH') OR terminate('No direct script access allowed');

	$output = compact('http_code','content_type','error_count','errors','data_count','data');
	echo json_encode($output);