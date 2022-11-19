<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Slider extends CI_Controller {
     function __construct()
				{
					parent::__construct();
				    
					$this->load->model('Slider_model');
					$this->load->model('AuthenticationModel');
					// $this->AuthenticationModel->checklogin();
					
					
				}
	
	public function get_slider()
	{
		echo $this->Slider_model->get_slider_items_all();
	}
}
