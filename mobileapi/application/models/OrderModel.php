<?php
     
	Class OrderModel extends CI_Model
	{
			
		function __construct()
		{
			parent::__construct();       			
		}			
        public function ordernow()
	    {
		
			$sendarray = array();
			$finalarray = array();
			$order_products = array();
			$total = 0;
			$subtotal = 0;
			$shipping_order = array();
			$payments = array();
		
			$token             = $this->input->post('Authorization');
			$tokenParts        = explode(".", $token);  
            $tokenHeader       = base64_decode($tokenParts[0]);
		    $tokenPayload      = base64_decode($tokenParts[1]);
			$jwtHeader         = json_decode($tokenHeader);
			$jwtPayload        = json_decode($tokenPayload, true);
			
			$today = date("Ymd");
            $rand = sprintf("%04d", rand(0,9999));
            $order_number = $today . $rand;
		
			$pid                = json_decode($this->input->post('product_id'), true);
			$product_quantity   = json_decode($this->input->post('product_quantity'), true);
			$product_unit_price = json_decode($this->input->post('product_unit_price'), true);
			
			$shipping_id     =  $this->input->post('shipping_id');
			$payment_method  =  $this->input->post('payment_method');
			$payment_id      =  $this->input->post('payment_id');
			$purchased_plan  =  $this->input->post('purchased_plan');
			$payment_status  =  $this->input->post('payment_status');
			
			for($i=0; $i< count($pid); $i++)
			{
				$total += $product_quantity[$i]*$product_unit_price[$i];
				$subtotal += $product_quantity[$i]*$product_unit_price[$i];
			}
			
			$order['order_number']    = $order_number;
			$order['buyer_id']        = $jwtPayload['id'];
			$order['buyer_type']      = $jwtPayload['user_type'];
			$order['price_subtotal']  = $subtotal;
			// $order['price_vat']       = '';
			$order['price_shipping']  =  100;
			$order['price_total']     = $total;
			$order['price_subtotal']  = $subtotal;
			$order['price_currency '] = "AED";
			
			// $order['payment_method']       = '';
			// $order['payment_status']       = '';
			// $order['coupon_discount_rate'] = '';
			// $order['coupon_code']          = '';
			// $order['coupon_discount']      = '';
			// $order['coupon_seller_id']     = '';		
			
            $this->db->insert('orders',$order);
            $order_id = $this->db->insert_id();	
            
			$this->db->select('first_name as shipping_first_name,last_name as shipping_last_name, email as shipping_email,  
			phone_number as shipping_phone_number, address as shipping_address,country_id as shipping_country, state_id as shipping_state, 
			city as shipping_city, zip_code as shipping_zip_code,
			first_name as billing_first_name,last_name as billing_last_name, email as billing_email,  
			phone_number as billing_phone_number, address as billing_address,country_id as billing_country, state_id as billing_state, 
			city as billing_city, zip_code as billing_zip_code,
			');
			$this->db->where('id',$shipping_id);
			$result5 = $this->db->get('shipping_addresses')->result_array();
			
			foreach($result5 as $sorder)
			{
				$sorder['order_id']  =  $order_id;    
				$shipping_order[]    = $sorder;
			}
	
			$this->db->insert_batch('order_shipping',$shipping_order);
			for($i=0; $i< count($pid); $i++)
			{
				$this->db->select('id as product_id ,user_id as seller_id, slug as product_slug,vat_rate as product_vat_rate,currency as product_currency, listing_type, product_type');
				$this->db->where('id',$pid[$i]);
				$query = $this->db->get('products')->result_array();
				
				foreach($query as $row)
				{
					$this->db->select('title');
					$this->db->where('product_id',$row['product_id']);
					$product_details = $this->db->get('product_details')->row_array();
					
					$row['product_title']       =  $product_details['title'];
					$row['order_id']            =  $order_id;
					$row['buyer_id']            =  $jwtPayload['id'];
					$row['buyer_type']          =  $jwtPayload['user_type'];
					$row['product_quantity']    = $product_quantity[$i];
				    $row['product_unit_price']  = $product_unit_price[$i];
					$row['product_total_price'] =  $product_quantity[$i]*$product_unit_price[$i];  
					$order_products[] = $row;	
				}  	    
			}
			
            $result =  $this->db->insert_batch('order_products',$order_products);	
			for($i=0; $i< count($pid); $i++)
			{
				$this->db->select('id as product_id ,currency');
				$this->db->where('id',$pid[$i]);
				$query2 = $this->db->get('products')->result_array();
				
				foreach($query2 as $row2)
				{
					$row2['user_id']             =  $jwtPayload['id'];
				    $row2['payer_email']         =  $jwtPayload['email'];
				    $row2['payment_method']      =  $payment_method;
				    $row2['payment_id']          =  $payment_id;
				    $row2['purchased_plan']      =  $purchased_plan;
				    $row2['payment_status']      =  $payment_status;
					$row2['payment_amount']      =  $product_quantity[$i]*$product_unit_price[$i];  
					$payments[] = $row2;	
				} 	    
			}
			$result2 =  $this->db->insert_batch('payments',$payments);
			
			if($result)
			{
				$sendarray['status']  =   1;
				$sendarray['message']  =  "successfully order Placed";
				$sendarray['order_product'] =  $order_products;
			}
			else
			{
				$sendarray['status']  =   1;
				$sendarray['message']  =  "error something wrong";
				$sendarray['order_product'] =   [];	
			}
		    return json_encode($sendarray, true);	
		}
	}
?>