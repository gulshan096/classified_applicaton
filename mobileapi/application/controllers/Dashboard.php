<?php

		defined('BASEPATH') OR exit('No direct script access allowed');

	class Dashboard extends CI_Controller
		{

			function __construct()
			{
				parent::__construct();
			    $this->load->model('AuthenticationModel');
			    $this->AuthenticationModel->checklogin();
				$this->load->model('Dashboard_model');
			}
				
			public function  get_all_count()
			{	
				echo $this->Dashboard_model->get_all_count();
			}
			
			public function popular_ads()
			{
				echo $this->Dashboard_model->popular_ads();
			}
			public function new_arrival()
			{
				echo $this->Dashboard_model->new_arrival();
			}
           	public function addWishlist()
			{
				echo $this->Dashboard_model->addWishlist();
			}
			public function getWishlist()
			{
				echo $this->Dashboard_model->getWishlist();
			}
            public function removeWishlist()
			{
				echo $this->Dashboard_model->removeWishlist();
			
			}
            public function getSalesByStatus()
			{
				echo $this->Dashboard_model->getSalesByStatus();
			}
            public function getOldSales()
			{
				echo $this->Dashboard_model->getOldSales();
			}
            public function earning()
			{
				echo $this->Dashboard_model->earning();
			}			
			
		}
	
?>