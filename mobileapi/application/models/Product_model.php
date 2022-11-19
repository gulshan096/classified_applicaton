<?php
     
	 Class Product_model extends CI_Model
		{
			
			function __construct()
		    {
				parent::__construct();
				$this->load->library('bcrypt');
					
			}
				
            public function get_products()
			{
				
				$sendarray  = array();
				$finalarray = array();
				
				$category_id = $this->input->post('category_id');
				$search_item = $this->input->post('search_item');
				
				if(!empty($category_id))
				{
					$sql1 = "select  * from products where category_id = $category_id and status=1";
				}
				elseif(!empty($search_item))
				{
					$sql1 = "select  * from products where  slug LIKE '%$search_item%' or product_type LIKE '%$search_item%' or listing_type LIKE '%$search_item%' ";
				}
				
				$pacl	=	$this->db->query($sql1)->result_array();
				
				foreach($pacl as $product)
				{
					
					$this->db->select('username');
					$this->db->where('id',$product['user_id']);
					$username = $this->db->get('users')->row_array();
					$product['seller_name']   = $username['username'];
					
					$pid =  $product['id'];
					$sql2 = "select image_default,image_big,image_small,is_main,storage from images where product_id = $pid";
				    $product['product_image']	=	$this->db->query($sql2)->result_array();
					
					$finalarray[]  =  $product;
				}
				
				  if(!empty($finalarray))
				  {
					$sendarray['status']  = 1;
                    $sendarray['message'] =  'Successfuly get all products';	
                    $sendarray['data']    =  $finalarray;					
				  }
				  else
				  {
					$sendarray['status']  = 0;
					$sendarray['message'] =  'Oops! This category product not found.';
                    $sendarray['data']    =   array(); 
				  }
				return json_encode($sendarray);
			}
			
			
			public function get_product_category()
			{
				$sendarray = array();
				$this->db->select('id,slug,image');
				$categories = $this->db->get('categories')->result_array();
				
		        if(!empty($categories))
			    {
					$sendarray['status']   =    1;
					$sendarray['message']  =   "get successfully all categories";
					$sendarray['data']     =   $categories;
				}
				else
				{
				   	$sendarray['status']   =    0;
					$sendarray['message']  =   "record not found";
					$sendarray['data']   =   array();
				}
				return json_encode($sendarray);
			}
			
			public function get_new_arrival()
			{
			
				$sendarray  = array();
				$finalarray = array();
			
			    $sql1 = "select  * from products where status=1 ORDER BY id DESC LIMIT 5";
				$pacl	=	$this->db->query($sql1)->result_array();
				
				foreach($pacl as $product)
				{
					$this->db->select('username');
					$this->db->where('id',$product['user_id']);
					$username = $this->db->get('users')->row_array();
					$product['seller_name']   = $username['username'];
					
					$pid =  $product['id'];
					$sql2 = "select image_default,image_big,image_small,is_main,storage from images where product_id = $pid";
				    $product['product_image']	=	$this->db->query($sql2)->result_array();
					
					$finalarray[]  =  $product;
				}
				
				  if(!empty($finalarray))
				  {
					$sendarray['status'] = 1;
                    $sendarray['message'] =  'Successfuly get all products';	
                    $sendarray['data'] =  $finalarray;					
				  }
				  else
				  {
					$sendarray['status'] = 0;
					$sendarray['message'] =  'Oops! This category product not found.';
                    $sendarray['data']   =   array(); 
				  }
				  
				return json_encode($sendarray);
			}
			          
			
		}

?>