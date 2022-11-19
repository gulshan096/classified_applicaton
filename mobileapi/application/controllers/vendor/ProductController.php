<?php

		defined('BASEPATH') OR exit('No direct script access allowed');

	class ProductController extends CI_Controller
		{

			function __construct()
			{
				parent::__construct();
				$this->load->model('AuthenticationModel');
			    $this->AuthenticationModel->checklogin();
				$this->load->model('vendor/ProductModel');    
			}
				
			public function add_product()
			{	
				echo $this->ProductModel->add_product();
			}
			public function getSellerProductByStatus()
			{
				echo $this->ProductModel->getSellerProductByStatus();
			}
            			
			
		}
	
?>