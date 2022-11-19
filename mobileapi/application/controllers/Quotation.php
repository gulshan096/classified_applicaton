<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation extends CI_Controller {
    function __construct()
	{
		parent::__construct();
        $this->load->model('AuthenticationModel');
	    $this->AuthenticationModel->checklogin();		
		$this->load->model('QuotationModel');
	}
	
	public function sendQuotation()
	{
		echo $this->QuotationModel->sendQuotation();	
	}
	
    public function getQuotation()
	{
		echo $this->QuotationModel->getQuotation();
	}
}
