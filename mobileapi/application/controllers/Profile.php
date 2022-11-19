<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {
    function __construct()
	{
		parent::__construct();
		$this->load->model('Profile_model');
		$this->load->model('AuthenticationModel');
	   // $this->AuthenticationModel->checklogin();				
	}
	
	public function getOneUserProfile()
	{
		echo $this->Profile_model->getOneUserProfile();
	}
	
	public function updateUserProfile()
	{
		echo $this->Profile_model->updateUserProfile();
	}
	public function cover_image()
	{
		echo $this->Profile_model->cover_image();
	}
	
	public function add_shipping_address()
	{
		echo $this->Profile_model->add_shipping_address();
	}
	
	public function get_shipping_address()
	{
		echo $this->Profile_model->get_shipping_address();
	}
	
	public function add_social_media()
	{
		echo $this->Profile_model->add_social_media();
	}
	
	public function add_review() 
	{
		echo $this->Profile_model->add_review();
	}
	
	public function get_review() 
	{
		echo $this->Profile_model->get_review();
	}
	public function get_state() 
	{
		echo $this->Profile_model->get_state();
	}
	public function get_city() 
	{
		echo $this->Profile_model->get_city();
	}
	
}
