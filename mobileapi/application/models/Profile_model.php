<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile_model extends CI_Model
{
    function __construct()
	{
		parent::__construct();
					
		$this->load->library('upload');
		$this->load->library('bcrypt');
					
	}
    //get user profile
    public function getOneUserProfile()
    {
		$jwt = new JWT();
		$sendarray  = array();
		
		$token = $this->input->post('Authorization');
		$dtoken = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))), true);
		$userId = $dtoken['id'];
		
		
        $this->db->where('id', $userId);
        $query = $this->db->get('users')->row_array();
		
		if(!empty($query))
		{
			$sendarray['status'] = 1;
            $sendarray['status'] =  'Successfuly get user profile';	
            $sendarray['data'] =  $query;					
		}
	    else
		{
		    $sendarray['status'] = 0;  
		    $sendarray['data']   =   array();
		}	
			return json_encode($sendarray);
       
    }

    //update user profile
    
	public function updateUserProfile()
	{
	    $sendarray  = array();	
		
		$token = $this->input->post('Authorization');
		$dtoken = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))), true);
		$userId = $dtoken['id'];
		
		if(!empty($_FILES['avatar']['name']))
			{			
				$upload_path = "../uploads/profile/";
				$config['allowed_types'] = 'jpg|png';
		        $config['upload_path'] =  $upload_path;
			    $this->load->library('upload', $config);
			    $this->upload->initialize($config);
				
				if($this->upload->do_upload("avatar"))
                {
					$filename = $this->upload->data('file_name');
	            }
			}
		    
			$userData['email'] = $this->input->post('email');
			$userData['username'] = $this->input->post('username');
			$userData['slug'] = $this->input->post('slug');
			$userData['first_name'] = $this->input->post('first_name');
			$userData['last_name'] = $this->input->post('last_name');
			$userData['phone_number'] = $this->input->post('phone_number');
			$userData['send_email_new_message'] = $this->input->post('send_email_new_message');
			$userData['show_email'] = $this->input->post('show_email');
		    $userData['show_phone'] = $this->input->post('show_phone');
			$userData['show_location'] = $this->input->post('show_location');
		    $userData['avatar'] = $filename;
		
			$this->db->where('id',$userId);
			$this->db->update('users',$userData);
			
	        $this->db->where('id', $userId);
			$updatedUser = $this->db->get('users')->row_array();
			
			$sendarray['status'] = 1;
            $sendarray['message'] =  'Successfuly updated user profile';
			$sendarray['data'] = $updatedUser;
		
		
		return json_encode($sendarray);
		
	}
	
	
	public function cover_image()
	{
	
		// Cover Image
		
		$sendarray  = array();	
		
	
		$token = $this->input->post('Authorization');
		$dtoken = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))), true);
		$userId = $dtoken['id'];
		
		$cover_image_type   =   $this->input->post('cover_image_type');
		
		if(!empty($_FILES['cover_image']['name']))
		{
			$upload_path =  './uploads/profile/';
		    $config['allowed_types'] = 'jpg|png';
		    $config['upload_path'] =  $upload_path;
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			
			if($this->upload->do_upload("cover_image"))
            {
			    $filename = $this->upload->data('file_name');   			
	        }	
	    }
		             
		    $userData['cover_image_type'] = $cover_image_type;
            $userData['cover_image'] = 'uploads/profile/cover_image_'.$filename;			   
			
			$this->db->where('id',$userId);
			$this->db->update('users',$userData);
			  
			$this->db->select('id,cover_image,cover_image_type');
			$this->db->where('id', $userId);
			$updatedUser = $this->db->get('users')->row_array();
				
			$sendarray['status'] = 1;
            $sendarray['message'] =  'Successfuly updated Cover image';
			$sendarray['data'] = $updatedUser;
		
		
		return json_encode($sendarray);
	}
	
	public function add_shipping_address()
	{
		
		$sendarray  = array();
        $data  =  array();		
		
		$id   =  $this->input->post('id');
		$token = $this->input->post('Authorization');
		$dtoken = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))), true);
		$userId = $dtoken['id'];
		$email = $dtoken['email'];
		$phone_number = $dtoken['phone_number'];
		
		$data['user_id']   =  $userId;
	    $data['title']  =   $this->input->post('title');
		$data['first_name']  =   $this->input->post('first_name');
		$data['last_name']  =   $this->input->post('last_name');
		$data['email']  =   $email;
		$data['phone_number']  =   $phone_number;
		$data['address']  =   $this->input->post('address');
		$data['country_id']  =   $this->input->post('country_id');
		$data['state_id']  =   $this->input->post('state_id');
		$data['city']  =   $this->input->post('city');
		$data['zip_code']  =   $this->input->post('zip_code');
		
		
		if(!empty($data['user_id']) && !empty($data['title']) && !empty($data['first_name']) && !empty($data['last_name']) &&
		
     		!empty($data['email']) && !empty($data['phone_number'])  && !empty($data['address']) && !empty($data['country_id']) && !empty($data['state_id']) && !empty($data['city']) && !empty($data['zip_code']))
		{
			    if(!empty($id))
				{
					$this->db->where('id',$id);
				    $this->db->update('shipping_addresses',$data);
			
					$this->db->where('id', $id);
					$updated_data = $this->db->get('shipping_addresses')->row_array();
					
					$sendarray['status']  = 1;
			        $sendarray['message']  = "success! shipping address updated successfully";	
					$sendarray['data']  =  $updated_data;
				}
				else
				{
				    $this->db->insert('shipping_addresses',$data);
					
					$lastid  = $this->db->insert_id();
					$this->db->where('id', $lastid);
					$inserted_data = $this->db->get('shipping_addresses')->row_array();
					
					$sendarray['status']  = 1;
			        $sendarray['message']  = "success! shipping address added successfully";
					$sendarray['data']  =  $inserted_data;
					
				}
		}
		else
        {
			$sendarray['status']  = 0;
			$sendarray['message']  = "error! all field are required";
			$sendarray['data']  =   array();
		}		
		return json_encode($sendarray);	
	}
	
	
	public function get_shipping_address()
	{
		      $sendarray  = array();
			  
			  $token = $this->input->post('Authorization');
		      $dtoken = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))), true);
		      $userId = $dtoken['id'];
			  
			  
			  
			  if(!empty($userId))
			  {
				  $this->db->select('*');  
				  $this->db->where('user_id', $userId);
				  $query = $this->db->get('shipping_addresses')->result_array();
				  
				  $sendarray['status']  = 1;
			      $sendarray['message']  = "success! Your all shipping address"; 
				  $sendarray['data']    =  $query;
			  }
			  else
			  {
				    $sendarray['status']  = 0;
			        $sendarray['message']  = "Oops! all field are required"; 
			  }
			  return json_encode($sendarray);	
	}
	
    
	
	public function add_social_media()
	{
	    $sendarray  = array();
        $data  =  array();	
		
		$token = $this->input->post('Authorization');
		$dtoken = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))), true);
		$userId = $dtoken['id'];
		

		$data['personal_website_url']   =  $this->input->post('personal_website_url');
	    $data['facebook_url']  =   $this->input->post('facebook_url');
		$data['twitter_url']  =   $this->input->post('twitter_url');
		$data['instagram_url']  =   $this->input->post('instagram_url');
		$data['pinterest_url']  =   $this->input->post('pinterest_url');
		$data['linkedin_url']  =   $this->input->post('linkedin_url');
		$data['vk_url']  =   $this->input->post('vk_url');
		$data['youtube_url']  =   $this->input->post('youtube_url');
		$data['whatsapp_url']  =   $this->input->post('whatsapp_url');
		$data['telegram_url']  =   $this->input->post('telegram_url');
	
		
		if(!empty($userId))
				{
					$this->db->where('id',$userId);
				    $this->db->update('users',$data);
			
					$this->db->where('id', $userId);
					$updated_data = $this->db->get('users')->row_array();
					
					$sendarray['status']  = 1;
			        $sendarray['message']  = "success! Social media updated successfully";	
					$sendarray['data']  =  $updated_data;
				}
				else
				{
				    $sendarray['status']  = 0;
			        $sendarray['message']  = "Error! Oops we cant find user";	
				
					$sendarray['data']  =  array();
					
				}
				
			return json_encode($sendarray);	
		
	}
	
	public function add_review()
	{
		$sendarray  = array();
        $data  =  array();	
		
		$rid   =  $this->input->post('review_id');
		
		$token = $this->input->post('Authorization');
		$dtoken = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))), true);
		$userId = $dtoken['id'];
		
		$data['user_id']   =  $userId;
	    $data['product_id']  =   $this->input->post('product_id');
		$data['rating']  =   $this->input->post('rating');
		$data['review']  =   $this->input->post('review');
		$data['ip_address']  =  $this->input->ip_address();

		if(!empty($rid))
				{
					$data['id']  =  $rid;
					$this->db->where('id',$rid);
				    $this->db->update('reviews',$data);
					
					$this->db->where('id', $rid);
					$updated_data = $this->db->get('reviews')->row_array();
			
					$this->db->where('id', $rid);
					$sendarray['status']  = 1;
			        $sendarray['message']  = "success! Review and ratiing updated successfully";	
					$sendarray['updated_data']  =    $updated_data; 
				
				}
				else
				{
				    
				    $this->db->insert('reviews',$data);
					$lastid  = $this->db->insert_id();
					$this->db->where('id', $lastid);
					$inserted_data = $this->db->get('reviews')->row_array();
					
					$sendarray['status']  = 1;
			        $sendarray['message']  = "success! Review and ratiing added  successfully";	
					$sendarray['inserted_data']  =    $inserted_data; 

					
				}
				
			return json_encode($sendarray);	
	}
	
	public function get_review()
	{
		  $sendarray  = array();
		  
		  $productId  =  $this->input->post('product_id');
		  $rating  =  $this->input->post('rating');
		           
		  $this->db->select('reviews.*, users.avatar, users.username');
		  $this->db->join('users','users.id=reviews.user_id','left');
		  $this->db->where('reviews.product_id',$productId);
		 
		  if(!empty($rating))
		  {
			  
			 $this->db->where('reviews.rating',$rating); 
		  }
		  
		  $query =  $this->db->get('reviews')->result_array();
				   
		 if(!empty($query))
		 {
			   $sendarray['status']  = 1;
			   $sendarray['message']  = "success!  successfully get all reviews.";	 
			   $sendarray['data']  =  $query;
		 }
		 else
         {
			  $sendarray['status']  = 0;
			  $sendarray['message']  = "Error! We can't find any review.";	
			  $sendarray['data']  =  array();
		 }		 
		 return json_encode($sendarray);
	}
	
	
	public  function get_state()
	{
		 $sendarray  = array();
		 $state =   $this->db->get('tbl_states')->result_array();
		 
		 if(!empty($state))
		 {
			  $sendarray['status']  = 1;
			  $sendarray['message']  = "success!  successfully get all state.";	 
			  $sendarray['data']  =  $state;
		 }
		 else
         {
			  $sendarray['status']  = 0;
			  $sendarray['message']  = "Error! We can't find any state.";	
			  $sendarray['data']  =  array();
		 }		 
		 return json_encode($sendarray);
	}
	
	public  function get_city()
	{
		$sendarray  = array();
		$state_id = $this->input->post('state_id');
		$this->db->where('state_id',$state_id);
		$cities =   $this->db->get('cities')->result_array();
		 
		 if(!empty($cities))
		 {
			   $sendarray['status']  = 1;
			   $sendarray['message']  = "success!  successfully get all cities.";	 
			   $sendarray['data']  =  $cities;
		 }
		 else
         {
			  $sendarray['status']  = 0;
			  $sendarray['message']  = "Error! We can't find any cities.";	
			  $sendarray['data']  =  array();
		 }		 
		 return json_encode($sendarray);
	}

}

?>