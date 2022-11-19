<?php
     
	 Class QuotationModel extends CI_Model
		{
			
			function __construct()
			{
				parent::__construct();		
			}
					
			public function sendQuotation()
			{
				$sendarray = array();
				$token = $this->input->post('Authorization');
				
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
				
				$product_id  = $this->input->post('product_id');
				$buyer_id = $jwtPayload['id'];
				$seller_id = $this->input->post('seller_id');
				
				
				$this->db->where('buyer_id',$buyer_id);
				$this->db->where('product_id',$product_id);
			    $already = $this->db->get('quote_requests')->row_array();

                if($seller_id == $buyer_id)
				{
					$sendarray['status'] = 0 ;
					$sendarray['message']  =   'You cannot request a quote for your own item';	
				}
				elseif(!empty($already['status'] == "new_quote_request"))
				{
					$sendarray['status'] = 0 ;
					$sendarray['message']  =   'You already have an active request for this product';	
				}
				else
				{
				   $quotation['product_id']        = $product_id;
				   $quotation['product_title']     = $this->input->post('product_title');
				   $quotation['product_quantity']  = $this->input->post('product_quantity');
				   $quotation['seller_id ']        = $seller_id;
				   $quotation['buyer_id']          = $buyer_id;
				   $quotation['status']            = "new_quote_request";
				   
				   $this->db->insert('quote_requests',$quotation);
				   $sendarray['status']  =   1;
				   $sendarray['message']  =  "successfully send quotation";
				   $sendarray['data'] =  $quotation;	
				}

				return json_encode($sendarray, true);   				
            }

            public function getQuotation()
			{
			    $sendarray = array();
				$token = $this->input->post('Authorization');
				
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
				
				$seller_id = $jwtPayload['id'];	
				
				if(!empty($seller_id))
				{
					$this->db->where('seller_id',$seller_id);
					$quotation_req = $this->db->get('quote_requests')->result_array();
					
					if(!empty($quotation_req))
					{
						$sendarray['status']  =   1;
				        $sendarray['message']  =  "successfully get quotation request";
				        $sendarray['data'] =  $quotation_req;
					}
					else
					{
						$sendarray['status'] = 0 ;
					    $sendarray['message']  =   'empty quotation request';
					}	
				}
				else
				{
					$sendarray['status'] = 0 ;
					$sendarray['message']  =   'all fiels are required';
				}
				return json_encode($sendarray, true);
			}					
		}

?>