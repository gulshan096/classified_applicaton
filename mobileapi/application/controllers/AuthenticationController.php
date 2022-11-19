<?php

		defined('BASEPATH') OR exit('No direct script access allowed');

	class AuthenticationController extends CI_Controller
		{

			function __construct()
			{
				parent::__construct();
				$this->load->model('AuthenticationModel');			
			}
				
			public function dologin()
			{	
		
				echo $this->AuthenticationModel->dologin();
			}
			
            public function doregister()
			{   
				echo $this->AuthenticationModel->doregister();
			}

            public  function reset()
			{
			    echo $this->AuthenticationModel->resetpassword();	
			}
			public function logout()
			{
				$sendarray = array();
				$this->session->sess_destroy();
		
				$sendarray['message']   =  'you are logout successfully.';
				echo json_encode($sendarray);
				
			}
			
			public function changePassword()
			{
				echo $this->AuthenticationModel->changePassword();
			}
			
			public function resetPassword()
			{
				echo $this->AuthenticationModel->resetPassword();
			}
				
		}
	
?>