<?php
     
	 Class Dashboard_model extends CI_Model
		{
			
			function __construct()
			{
				parent::__construct();
			}
			
			public function get_all_count()
			{
				$counting = array();
		        $sendarray = array();
				
				$token = $this->input->post('Authorization');
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
				$user_id = $jwtPayload['id'];
				
				
				
				
				// Total sales count
				$status= array('1', '0');
				$this->db->distinct();
			    $this->db->select('orders.id');
				$this->db->select('orders.price_total');
				$this->db->join('order_products', 'order_products.order_id=orders.id', 'left');
				$this->db->where('order_products.seller_id',$user_id);
				$this->db->where('orders.payment_status','payment_received');
				$this->db->where_in('orders.status',$status);
		
				$total_sales_count = $this->db->get('orders')->num_rows();
				$counting['total_sales_count'] =  $total_sales_count;
				
				
				
				
				// Total balance
				$this->db->distinct();
			    $this->db->select('orders.id');
				$this->db->select('orders.price_total');
				$this->db->join('order_products', 'order_products.order_id=orders.id', 'left');
				$this->db->where('order_products.seller_id',$user_id);
				$this->db->where('orders.status',1);
				$orders = $this->db->get('orders')->result_array();
				
				$total_balance = 0;
			    foreach($orders as $item)
				{
					$total_balance+= $item['price_total']/100; 
				}
				$counting['total_balance'] =  $total_balance;
				
				
				
				 
			   // Total page views 
				$this->db->select_sum('pageviews');
				$this->db->where('user_id',$user_id);
				$query = $this->db->get('products')->row_array();
				$counting['page_view_count'] =  $query['pageviews'];
				
				
				
				
			
			    // Total product count
				$this->db->where('user_id',$user_id);
				$this->db->where('status',1);
				$query2 =  $this->db->get('products')->num_rows();
				$counting['product_count'] =  $query2;
				
				
			    $sendarray['data'] = $counting;
				return json_encode($sendarray);
	  
			}
			
			public function popular_ads()
			{
				$sendarray  = array();
				
				$token = $this->input->post('Authorization');
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
				
				$user_id = $jwtPayload['id'];
				
				$this->db->select('id,slug,pageviews');
				$this->db->where('user_id',$user_id);
				$this->db->order_by("pageviews", "desc");
				$this->db->limit(5); 
				$query = $this->db->get('products')->result_array();
				
				if(!empty($query))
				{
				    $sendarray['status'] = 1;
                    $sendarray['message'] =  'Successfuly get popular products';	
                    $sendarray['data'] =  $query;		
				}
				else
				{
					$sendarray['status'] = 0;
					$sendarray['message'] =  'Oops! product not found.';
                    $sendarray['data']   =   array(); 
				}
				return json_encode($sendarray);
			}
			
			public function new_arrival()
			{
				$sendarray  = array();
				$finalarray  = array();
				
				$token = $this->input->post('Authorization');
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
				
				$user_id = $jwtPayload['id'];
				
				$this->db->select('*');
				$this->db->where('user_id',$user_id);
				$this->db->where('status',1);
				$this->db->order_by("id", "desc");
				$this->db->limit(5); 
				$query1 = $this->db->get('products')->result_array();
				
				foreach($query1 as $product)
				{
					
					$this->db->select('username');
					$this->db->where('id',$product['user_id']);
					$username = $this->db->get('users')->row_array();
					$product['seller_name']   = $username['username'];
					
					$this->db->select('slug as subcategory,parent_id');
					$this->db->where('id',$product['category_id']);
					$subcategory = $this->db->get('categories')->result_array();
					
					foreach($subcategory as $item)
					{
					   $product['sub_category']   = $item['subcategory'];

                       $this->db->select('slug as category');
					   $this->db->where('id',$item['parent_id']);
					   $category = $this->db->get('categories')->row_array();
					   $product['category']   = $category['category'];					   
					}
					
					$pid =  $product['id'];
					$sql2 = "select image_default,image_big,image_small,is_main,storage from images where product_id = $pid";
				    $product['product_image']	=	$this->db->query($sql2)->result_array();
					
					$finalarray[]  =  $product;
				}
				
				if(!empty($finalarray))
				{
				    $sendarray['status'] = 1;
                    $sendarray['message'] =  'Successfuly get new products';	
                    $sendarray['data'] =  $finalarray;		
				}
				else
				{
					$sendarray['status'] = 0;
					$sendarray['message'] =  'Oops! product not found.';
                    $sendarray['data']   =   array(); 
				}
				return json_encode($sendarray);
			}
			
			public function addWishlist()
			{
				$sendarray  = array();
				
				$token = $this->input->post('Authorization');
				
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
				
				$user_id = $jwtPayload['id'];
				
				$wishlist['product_id'] = $this->input->post('product_id');
				$wishlist['user_id'] = $user_id;
				
				$this->db->insert('wishlist',$wishlist);
				$id = $this->db->insert_id();
				$this->db->where('id',$id);
				$data = $this->db->get('wishlist')->row_array();
				
				if(!empty($data))
				{
				  	$sendarray['status'] = 1;
                    $sendarray['message'] =  'Successfuly add wishlist';	
                    $sendarray['data'] =  $data;
				}
				else
				{
				    $sendarray['status'] = 0;
					$sendarray['message'] =  'Oops! not add wishlist.';
                    $sendarray['data']   =   array();	
				}
				
				return json_encode($sendarray);
			}
            public function getWishlist()
			{
				$sendarray  = array();
				$finalarray = array();
				
				$token = $this->input->post('Authorization');
				
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
				
				$user_id = $jwtPayload['id'];
				
				$this->db->select('*');
				$this->db->where('user_id',$user_id);
				$query1 = $this->db->get('wishlist')->result_array();
				
				foreach($query1 as $row)
				{
					
					$this->db->select('username');
					$this->db->where('id',$product['user_id']);
					$username = $this->db->get('users')->row_array();
					$product['seller_name']   = $username['username'];
					
					$pid =  $row['product_id'];
					$this->db->select('*');
					$this->db->where('id',$pid);
					$query2 = $this->db->get('products')->result_array();
					
					foreach($query2 as $product)
				    {
					  $this->db->select('username');
					  $this->db->where('id',$product['user_id']);
					  $username = $this->db->get('users')->row_array();
					  $product['seller_name']   = $username['username'];
					  
					  $this->db->select('slug as subcategory,parent_id');
					  $this->db->where('id',$product['category_id']);
					  $subcategory = $this->db->get('categories')->result_array();
					
					foreach($subcategory as $item)
					{
					   $product['sub_category']   = $item['subcategory'];

                       $this->db->select('slug as category');
					   $this->db->where('id',$item['parent_id']);
					   $category = $this->db->get('categories')->row_array();
					   $product['category']   = $category['category'];					   
					}
					  
					  $pid =  $product['id'];
					  $sql2 = "select image_default,image_big,image_small,is_main,storage from images where product_id = $pid";
				      $product['product_image']	=	$this->db->query($sql2)->result_array();
					
					  $finalarray[]  =  $product;
				    }
				}
				
			
				if(!empty($finalarray))
				{
				    $sendarray['status'] = 1;
                    $sendarray['message'] =  'Successfuly get wishlist';	
                    $sendarray['data'] =  $finalarray;		
				}
				else
				{
					$sendarray['status'] = 0;
					$sendarray['message'] =  'Oops! wishlist not found.';
                    $sendarray['data']   =   array(); 
				}
				return json_encode($sendarray);
			}
            public function removeWishlist()
			{
		
				$token = $this->input->post('Authorization');
				
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
				
				$user_id = $jwtPayload['id'];
				$product_id = $this->input->post('product_id');
			
				$this->db->where('user_id',$user_id);
				$this->db->where('product_id',$product_id);
				return $this->db->delete('wishlist');
		
			}
			public function getSalesByStatus()
			{
				
				$sendarray = array();
				$order = array();
				$finalarray = array();
				
				$token = $this->input->post('Authorization');
				$status = $this->input->post('status');
				
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
				
				$saller_id = $jwtPayload['id'];
				
				$this->db->distinct();
			    $this->db->select('orders.id,orders.order_number,orders.price_total,orders.created_at,order_products.order_status');
				$this->db->join('order_products', 'order_products.order_id=orders.id', 'left');
				$this->db->where('order_products.seller_id',$saller_id);
				
				
				if($status == "shipped" || $status == "payment_received")
				{
				   $st = array('shipped','payment_received');	
				   $this->db->where_in('order_products.order_status', $st);
				   			   
				   $message =  'active';
				}
				
				elseif($status == "completed")
				{
					$this->db->where('order_products.order_status', $status);
					$message =  'Completed';
				}
				elseif($status ==  "cancelled")
				{
					$this->db->where('order_products.order_status', $status);
					$message =  'cancelled';
				}
				
				
				if($status == "")
				{
					$this->db->limit(5);
					$message =  'latest sales';
				}
			    $this->db->order_by('orders.id','desc');
				$latest_sales = $this->db->get('orders')->result_array();
				
				
               
			    foreach($latest_sales as $item)
				{
					if($item['order_status'] == "")
					{
						$item['status'] = "processing";
					}
					elseif($item['order_status'] == "shipped")
					{
						$item['status'] = "processing";
					}
					elseif($item['order_status'] == "payment_received")
					{
						$item['status'] = "processing";
					}
                    elseif($item['order_status'] == "cancelled")
					{
						$item['status'] = "cancelled"; 
					}
                    elseif($item['order_status'] == "completed")
					{
						$item['status'] = "completed"; 
					}
					
					
					if($item['order_status'] == "")
					{
						$item['payment'] = "Awaiting Payment";
					}
					
					elseif($item['order_status'] == "cancelled")
					{
						$item['payment'] = $item['order_status'];
					}
					elseif($item['order_status'] == "payment_received")
					{
						$item['payment'] = "Payment Received";
					}
					elseif($item['order_status'] == "shipped")
					{
						$item['payment'] = "Payment Received";
					}
                    elseif($item['order_status'] == "completed")
					{
						$item['payment'] = "Payment Received";
					}					
					 $finalarray[] =       $item;
				}
				
				  if(!empty($finalarray))
				  {
					$sendarray['status']  = 1;
                    $sendarray['message'] =  $message;	
                    $sendarray['data']    =  $finalarray;					
				  }
				  else
				  {
					$sendarray['status']  = 0;
					$sendarray['message'] =  'Oops! Product not found';
                    $sendarray['data']    =   array(); 
				  }
				  
				return json_encode($sendarray);
			}
			
			public function getOldSales()
			{
				
				$sendarray = array();
				$order = array();
				$finalarray = array();
				
				$token = $this->input->post('Authorization');
				$status = $this->input->post('status');
				
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);    
				
				$saller_id = $jwtPayload['id'];
				 
				$this->db->distinct();
			    $this->db->select('orders.id,orders.order_number,orders.price_total,orders.created_at,order_products.order_status');
				$this->db->join('order_products', 'order_products.order_id=orders.id', 'left');
				$this->db->where('order_products.seller_id',$saller_id);
				
				
			    $this->db->order_by('orders.id','asc');
			    $this->db->limit(2);

				$old_sales = $this->db->get('orders')->result_array();
				
			    foreach($old_sales as $item)
				{
					if($item['order_status'] == "")
					{
						$item['status'] = "processing";
					}
					elseif($item['order_status'] == "shipped")
					{
						$item['status'] = "processing";
					}
					elseif($item['order_status'] == "payment_received")
					{
						$item['status'] = "processing";
					}
                    elseif($item['order_status'] == "cancelled")
					{
						$item['status'] = "cancelled"; 
					}
                    elseif($item['order_status'] == "completed")
					{
						$item['status'] = "completed"; 
					}
					
					
					if($item['order_status'] == "")
					{
						$item['payment'] = "Awaiting Payment";
					}
					
					elseif($item['order_status'] == "cancelled")
					{
						$item['payment'] = $item['order_status'];
					}
					elseif($item['order_status'] == "payment_received")
					{
						$item['payment'] = "Payment Received";
					}
					elseif($item['order_status'] == "shipped")
					{
						$item['payment'] = "Payment Received";
					}
                    elseif($item['order_status'] == "completed")
					{
						$item['payment'] = "Payment Received";
					}					
					 $finalarray[] =       $item;
				}
				
				  if(!empty($finalarray))
				  {
					$sendarray['status']  = 1;
                    $sendarray['message'] =  "old sales";	
                    $sendarray['data']    =  $finalarray;					
				  }
				  else
				  {
					$sendarray['status']  = 0;
					$sendarray['message'] =  'Oops! Product not found';
                    $sendarray['data']    =   array(); 
				  }
				  
				return json_encode($sendarray);
			}
			
			public function earning()
			{
				$sendarray = array();
			    $token = $this->input->post('Authorization');
				
				
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
				$saller_id = $jwtPayload['id'];	
				
				$this->db->distinct();
			    $this->db->select('orders.id,orders.order_number,orders.price_total,order_products.commission_rate,orders.coupon_discount,order_products.seller_shipping_cost,orders.created_at');
				$this->db->join('order_products', 'order_products.order_id=orders.id', 'left');
				$this->db->where('order_products.seller_id',$saller_id);
				
				$this->db->where('order_products.order_status', "completed");
				$this->db->order_by('orders.id','desc');
			    // $this->db->limit(5);
				$earning = $this->db->get('orders')->result_array();
				
				
				
				// Total balance
				$this->db->distinct();
			    $this->db->select('orders.id');
				$this->db->select('orders.price_total');
				$this->db->join('order_products', 'order_products.order_id=orders.id', 'left');
				$this->db->where('order_products.seller_id',$saller_id);
				$this->db->where('orders.status',1);
				$orders = $this->db->get('orders')->result_array();
				
				$total_balance = 0;
			    foreach($orders as $item)
				{
					$total_balance+= $item['price_total']/100; 
				}
			
				if(!empty($earning))
				  {
					$sendarray['status']        =  1;
                    $sendarray['message']       =  "earning";	
					$sendarray['total_balance'] =  $total_balance;
                    $sendarray['data']          =  $earning;					
				  }
				  else
				  {
					$sendarray['status']  = 0;
					$sendarray['message'] =  'Oops! empty record';
                    $sendarray['data']    =   array(); 
				  }
				  
				return json_encode($sendarray);
				
				
			}
			
		}

?>