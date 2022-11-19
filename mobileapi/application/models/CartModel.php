<?php
     
	 Class CartModel extends CI_Model
		{
			
			function __construct()
			{
				parent::__construct();
                $this->load->library('bcrypt');		
			}
					
			public function addCartItems()
			{
				$sendarray = array();
				$cartItem = array();
				
				$token = $this->input->post('Authorization');
				
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
		
		        $product_id = json_decode($this->input->post('pid'), true);
		        $product_qty = json_decode($this->input->post('qty'), true);
				
				
				
				for($i=0; $i < count($product_id); $i++)
				{
					$this->db->select('id as product_id,user_id as seller_id');
					$this->db->where('id',$product_id[$i]);
					$query = $this->db->get('products')->result_array();

					foreach($query as $row)
				    {
						$row['buyer_id'] = $jwtPayload['id'];
						$row['quantity'] =  $product_qty[$i];
					    $cartItem[] = $row;
				    }
				}
				$result =  $this->db->insert_batch('add_to_cart',$cartItem);

				if($result)
				{
					$sendarray['status']  =   1;
					$sendarray['message']  =  "successfully add to cart";
					$sendarray['cart_item'] =  $cartItem;	
				}
				else
				{
				   $sendarray['status']  =   0;
				   $sendarray['message']  =  $this->db->error();	
				}
				return json_encode($sendarray, true);
                				
            }


            public function addCartItemsSingle()
			{
				$sendarray = array();
				$cartItem = array();
				
				$token = $this->input->post('Authorization');
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
		
		        $product_id    = $this->input->post('pid');
		        $product_qty   = $this->input->post('qty');
		        $cart_id       = $this->input->post('cart_id');
				
				$cartItem['product_id'] =  $product_id;
				$cartItem['buyer_id'] = $jwtPayload['id'];
				$cartItem['quantity'] =  $product_qty;
				
				if(!empty($cart_id))
				{
					$cartItem['updated_at'] = date('Y-m-d H:i:s');
					$this->db->where('id', $cart_id);
                    $up = $this->db->update('add_to_cart', $cartItem);
					
					if($up)
					{
						$sendarray['status']  =   1;
						$sendarray['message']  =  "successfully updeted cart"; 
						$sendarray['cart_item'] =  $cartItem;					 
					}
				}
				else
				{
				    $add = $this->db->insert('add_to_cart',$cartItem);
					if($add)
				    {
					 $sendarray['status']  =   1;
					 $sendarray['message']  =  "successfully added to cart"; 
                     $sendarray['cart_item'] =  $cartItem;					 
				    }	
				}
				
				return json_encode($sendarray, true);   				
            }			
            
			public function getCartItem()
			{
				$sendarray  = array();
				$cartItem   = array();
				$finalarray = array();
				
				$token = $this->input->post('Authorization');
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
				
				$this->db->select('id,product_id,quantity');
				$this->db->where('buyer_id',$jwtPayload['id']);
				$query1 = $this->db->get('add_to_cart')->result_array();
				
				foreach($query1 as $row1)
				{
				   $this->db->select('id,user_id as seller_id, price');	
				   $this->db->where('id',$row1['product_id']);
                   $query2 = $this->db->get('products')->result_array();
				   
                   foreach($query2 as $product)
				   {
					 $this->db->select('username');
					 $this->db->where('id',$product['seller_id']);
					 $username = $this->db->get('users')->row_array();
					 
					 $product['seller_name']   = $username['username'];
					 $product['quantity']      = $row1['quantity'];
					 $product['cart_id']      = $row1['id'];
					 $product['total_price']      = $product['price']*$row1['quantity'];
					 
					 $this->db->select('title');
					 $this->db->where('product_id',$product['id']);
					 $product_details = $this->db->get('product_details')->row_array();
					
					 $product['product_title']       =  $product_details['title'];
					
                     $pid =  $product['id'];
					 $sql2 = "select image_default,image_big,image_small,is_main,storage from images where product_id = $pid";
				     $product['product_image']	=	$this->db->query($sql2)->result_array();

					 $finalarray[]  =  $product;
				   }  
				}
				if(!empty($finalarray))
				  {
					$sendarray['status']  = 1;
                    $sendarray['message'] =  'Successfuly get cart items';	
                    $sendarray['data']    =  $finalarray;					
				  }
				  else
				  {
					$sendarray['status']  = 0;
					$sendarray['message'] =  'Oops! cart is empty.';
                    $sendarray['data']    =   array(); 
				  }
				return json_encode($sendarray);
				
				
			}
			
			public function removeCart()
			{
				$sendarray = array();
				
				$token = $this->input->post('Authorization');
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
				
				$product_id    = $this->input->post('pid');
		
				$this->db->where('product_id',$product_id);
				$this->db->where('buyer_id',$jwtPayload['id']);
				$rm = $this->db->delete('add_to_cart');
				
				if($rm)
				{
					 $sendarray['status']  =   1;
					 $sendarray['message']  =  "successfully remove from cart"; 
                    				 
				}	
		        return json_encode($sendarray);
			}
				
		}

?>