<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {
    function __construct()
	{
		parent::__construct();	
		$this->load->model('AuthenticationModel');
	    $this->AuthenticationModel->checklogin();	
		$this->load->model('OrderModel');
	}
	
	public function ordernow()
	{
	
		echo $this->OrderModel->ordernow();	
	}
}

?>