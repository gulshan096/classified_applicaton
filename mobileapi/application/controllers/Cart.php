<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller {
    function __construct()
	{
		parent::__construct();
        $this->load->model('AuthenticationModel');
	    $this->AuthenticationModel->checklogin();		
		$this->load->model('CartModel');
		// $this->load->library('session');
	}
	
	public function addtocart()
	{
		echo $this->CartModel->addCartItems();		
	}
	
	public function addToCartSingle()
	{
		echo $this->CartModel->addCartItemsSingle();
	}
	
	public function getCartItem()
	{
		echo $this->CartModel->getCartItem();
	}
	
	public function removeCart()
	{
		echo $this->CartModel->removeCart();
	}
}
