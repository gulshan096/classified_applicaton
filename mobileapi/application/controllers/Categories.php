<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends CI_Controller {
    function __construct()
	{
		parent::__construct();	
		$this->load->model('CategoriesModel');
	
	}
	
	public function getSubCategories()
	{
		$sendarray = array();
		
		$authorization = $this->input->post('Authorization');
		$parent_id = $this->input->post('parent_id');
		echo $this->CategoriesModel->getSubCategories($parent_id);
			
	}
}
