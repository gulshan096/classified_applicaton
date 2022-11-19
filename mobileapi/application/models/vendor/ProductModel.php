<?php
     
	 Class ProductModel extends CI_Model
		{
			
			function __construct()
				{
					parent::__construct();
					$this->load->library('bcrypt');
					$this->load->library('upload');	
				}
				
            public function add_product()
			{
				
				$sendarray = array();
				
				$token = $this->input->post('Authorization');
				$tokenParts = explode(".", $token);  
				$tokenHeader = base64_decode($tokenParts[0]);
				$tokenPayload = base64_decode($tokenParts[1]);
				$jwtHeader = json_decode($tokenHeader);
				$jwtPayload = json_decode($tokenPayload, true);
				
				$user_id = $jwtPayload['id'];
				
				$data = array(
				
					'slug' => $this->input->post('slug'),
					'product_type' => $this->input->post('product_type'),
					'listing_type' => $this->input->post('listing_type'),
					'sku' =>  $this->input->post('sku'),
					'stock' => $this->input->post('stock'),
					'price' => $this->input->post('price'),
					'discount_rate' => $this->input->post('discount_rate'),
					'vat_rate' => $this->input->post('vat_rate'),
					'user_id' =>  $user_id,
					'demo_url' =>  $this->input->post('demo_url'),
					
					'category_id' => $this->input->post('category_id'),
					'title' => $this->input->post('title'),
                    'description' => $this->input->post('description'),
                    'seo_title' => $this->input->post('seo_title'),
                    'seo_description' => $this->input->post('seo_description'),
                    'seo_keywords' => $this->input->post('seo_keywords'),
					
					
					'image_default' => $this->input->post('image_default'),
					'files_included' => $this->input->post('files_included'),
					'digital_file' => $this->input->post('digital_file'),
					
					'license_key' => $this->input->post('license_key'),

					'status' => 0,
					'currency' => "",
					'is_promoted' => 0,
					'promote_start_date' => date('Y-m-d H:i:s'),
					'promote_end_date' => date('Y-m-d H:i:s'),
					'promote_plan' => "none",
					'promote_day' => 0,
					'visibility' => 1,
					'rating' => 0,
					'pageviews' => 0,
					'external_link' => "",
					'shipping_delivery_time_id' => 0,
					'multiple_sale' => 1,
					'is_deleted' => 0,
					'is_draft' => 1,
					'is_free_product' => 0,
				
                );
		
		            $product = array();
					$product_details = array();
					$product_license_keys = array();
					$digital_files = array();
					$product_images = array();
						
						$product['slug']          =  $data['slug'];
						$product['product_type']  =  $data['product_type'];
						$product['listing_type']  =  $data['listing_type'];
						$product['sku']           =  $data['sku'];
						$product['category_id']   =  $data['category_id'];
						$product['price']         =  $data['price'];
						$product['discount_rate'] =  $data['discount_rate'];
						$product['vat_rate']      =  $data['vat_rate'];
						$product['user_id']       =  $data['user_id'];
						$product['demo_url']      =  $data['demo_url'];
						$product['stock']         =  $data['stock'];
						
						$this->db->insert('products', $product);
						$sendarray['status']      = 1;
						$sendarray['message']     =  'Success! successfully.';
						$product_id = $this->db->insert_id();
						
						$this->db->where('id',$product_id);
						$prod = $this->db->get('products')->row_array();
						
						if(!empty($prod))
						{
						   $sendarray['products']   =  $prod;	
						}
					
					if(!empty($product_id))
					{
						
						// Product details
						
						if(!empty($data['title']))
						{
							$product_details['title']            =  $data['title'];
							$product_details['description']      =  $data['description'];
							$product_details['seo_title']        =  $data['seo_title'];
							$product_details['seo_description']  =  $data['seo_description'];
							$product_details['seo_keywords']     =  $data['seo_keywords'];
							$product_details['product_id']       =  $product_id;
							
							$this->db->insert('product_details', $product_details);
							
							$sendarray['status'] = 1;
							$sendarray['message'] =  'Success! successfully.';
							$sendarray['product_details']   =  $product_details;
						}
						else
						{
							$sendarray['message'] =  'Error! All Fields are required.';
						}
					  
						
						// Licence Key
						
						if(!empty($data['license_key']))
						{
							$product_license_keys['license_key']      =  $data['license_key'];
							$product_license_keys['product_id']       =  $product_id;
							$this->db->insert('product_license_keys', $product_license_keys);
							
							$sendarray['status'] = 1;
							$sendarray['message'] =  'Success! successfully.';
							$sendarray['product_license_keys']   =  $product_license_keys;
						}
						else
						{
							
							$sendarray['message'] =  'Error! All license_key Fields are required.';
						}
						
						
						// digital files  
						if(!empty($_FILES['digital_file']['name']))
						{
							$folderName = date('Ym');
							$pathToUpload = './../uploads/digital-files/' . $folderName;
									
										
							if ( ! file_exists($pathToUpload) )
							{
								$create = mkdir($pathToUpload, 0777);
								$createThumbsFolder = mkdir($pathToUpload . '/thumbs', 0777);
								if ( ! $create || ! $createThumbsFolder)
								return;
							}
							$config['upload_path'] = $pathToUpload;
							$config['allowed_types'] = 'gif|jpg|png|zip|pdf|gzip|jpeg|doc|docx|xls|xlsx|ppt|pptx|rar|iso';
							$this->upload->initialize($config);
							$dfupload = $this->upload->do_upload("digital_file");
							if(!empty($dfupload))
							{
								$filename = $this->upload->data('file_name');
								
								$digital_files['product_id']     =  $product_id;
								$digital_files['storage']        =   'local';
								$digital_files['file_name']      =  $filename;
								$digital_files['user_id']        =  $user_id;
								
								$this->db->insert('digital_files', $digital_files);
								$sendarray['status'] = 1;
								$sendarray['message'] =  'Success! successfully.';
								$sendarray['digital_files']      =  $digital_files;
							}
						}
						else
						{
							
						}
						
						//Product Images
						if(!empty($_FILES['image_default']['name'][0]))
						{
							$filecount = count($_FILES['image_default']['name']);  
						
							for($i = 0; $i < $filecount; $i++)
							{
								$_FILES['file']['name']       =  $_FILES['image_default']['name'][$i];
								$_FILES['file']['type']       = $_FILES['image_default']['type'][$i];
								$_FILES['file']['tmp_name']   = $_FILES['image_default']['tmp_name'][$i];
								$_FILES['file']['error']      = $_FILES['image_default']['error'][$i];
								$_FILES['file']['size']       = $_FILES['image_default']['size'][$i];
								
								$folderName = date('Ym');
								$pathToUpload = './../uploads/images/' . $folderName;
									
										
								if ( ! file_exists($pathToUpload) )
								{
									$create = mkdir($pathToUpload, 0777);
									$createThumbsFolder = mkdir($pathToUpload . '/thumbs', 0777);
									if ( ! $create || ! $createThumbsFolder)
									return;
								}
										
								// File upload configuration
								$config['upload_path'] = $pathToUpload;
								$config['overwrite'] = TRUE;
								$config['allowed_types'] = 'jpg|jpeg|png|gif';

								// Load and initialize upload library
								$this->load->library('upload', $config);
								$this->upload->initialize($config);

								// Upload file to server
								if($this->upload->do_upload('file'))
								{
								  // Uploaded file data
								  $imageData = $this->upload->data();
								  $product_images[$i]['product_id']          =   $product_id;
								  $product_images[$i]['storage']             =   'local';
								  $product_images[$i]['image_default']       =  $folderName.'/'.$imageData['file_name'];
								  $product_images[$i]['image_big']           =  $folderName.'/'.$imageData['file_name'];
								  $product_images[$i]['image_small']         =  $folderName.'/'.$imageData['file_name']; 
								}	
							}
							if(!empty($product_images))
							{
								$this->db->insert_batch('images', $product_images);
								$sendarray['status'] = 1;
								$sendarray['message'] =  'Success! successfully.';
								$sendarray['product_images']   =  $product_images;			   
							}  
						}  								
					}
		        return json_encode($sendarray);
			}
			
			public function getSellerProductByStatus()
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
				
				$status   = $this->input->post('status');
				$is_draft = $this->input->post('is_draft');
				
			    $this->db->where('user_id',$user_id);
				if(!empty($status) == 0)
				{
					// echo "pending";
					$this->db->where('status',$status);
				}
				elseif(!empty($status) == 1)
				{
					// echo "active product";
					$this->db->where('status',$status);
				}
				elseif(!empty($is_draft) == 1)
				{
					// echo "is_draft";
					
					$this->db->where('is_draft',$is_draft);
				}
				elseif(!empty($is_sold)== 1)
				{
					// echo "is_sold";
					$this->db->where('is_sold',$is_sold);
				}
				
				$pacl = $this->db->get('products')->result_array();
				
				foreach($pacl as $product)
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
					$this->db->select('image_default,image_big,image_small,is_main,storage');
					$this->db->where('product_id',$pid);
				    $product['product_image']	=	$this->db->get('images')->result_array();
					
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
					$sendarray['message'] =  'Oops! Product not found.';
                    $sendarray['data']   =   array(); 
				  }
				  
				return json_encode($sendarray);
				
			}
			
			
		}

?>