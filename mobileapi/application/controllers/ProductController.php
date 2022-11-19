<?php

		defined('BASEPATH') OR exit('No direct script access allowed');

	class ProductController extends CI_Controller
		{

			function __construct()
			{
				parent::__construct();
				$this->load->model('Product_model');
				$this->load->model('AuthenticationModel');
				// $this->AuthenticationModel->checklogin();		
			}
			
			public function get_products()
			{
				
				echo $this->Product_model->get_products();
			}
				
			public function get_product_category()
			{
					
				echo $this->Product_model->get_product_category();
			}
			public function get_new_arrival()
			{
				
				echo $this->Product_model->get_new_arrival();
			}
			
           			
			
		}
	
?>