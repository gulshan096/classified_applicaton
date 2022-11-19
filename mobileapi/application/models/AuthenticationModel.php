<?php
     
	    Class AuthenticationModel extends CI_Model
		{
			
			function __construct()
			{
				parent::__construct();
				$this->load->library('bcrypt');	
			}
					
			public function dologin()
			{
                $sendarray = array();
			    $jwt = new JWT();
				$jwtsecretkey = time();
				$email	    =	$this->input->post('email');
				$password	=   $this->input->post('password');	
					
				if(!empty($email) && !empty($password))
				{
					$this->db->select('id,username,email,email_status,password,phone_number,role_id,avatar,user_type');
					$this->db->where('email',$email); 
			
					$logincheck	=	$this->db->get('users')->row_array();	
					if(!empty($logincheck) && $this->bcrypt->check_password($password, $logincheck['password']))
					{
						if(!empty($logincheck['email_status'] == 1))
						{
							$logincheck['Authorization'] =  $jwt->encode($logincheck,$jwtsecretkey,'HS256');
							$sendarray['status'] = 1 ;
							$sendarray['message'] =  'Success! You are logged in successfully.';
							$sendarray['data'] =  $logincheck;
						} 
						else
						{
							$sendarray['status'] = 0 ;
							$sendarray['message'] =  'Error! In order to login to the site, you must confirm your email address..';   
						}
					} 
					else 
					{
						$sendarray['status'] = 0 ;
						$sendarray['message'] =  'Error! Incorrect Login details.';
					}
				} 
				else 
				{
					$sendarray['status'] = 0 ;
					$sendarray['message'] =  'Error! All Fields are required.';  
				}
				return json_encode($sendarray);
            }	


            public function doregister()
			{
				$sendarray = array();
				$jwt = new JWT();
				$jwtsecretkey = time();
		     
				$username        =    $this->input->post('username');
				$email           =    $this->input->post('email');
				$phone_number    =    $this->input->post('phone_number');
				$password        =    $this->bcrypt->hash_password($this->input->post('password'));
				$token1          =    uniqid("", TRUE);
                $token2          =    str_replace(".", "-", $token1);
                $token           =    $token2 . "-" . rand(10000000, 99999999);
				
				if(!empty($username) && !empty($email) && !empty($phone_number) && !empty($password) && !empty($token))
				{
				
                    $this->db->select('email,phone_number, username');
					$this->db->where('email',$email);
					$check_mail = $this->db->get('users')->row_array();
						
					if(!empty($check_mail['email']))
					{
						$sendarray['status'] = 0 ;
						$sendarray['message']  =   'oops mail are already register';	
					}
					elseif(!empty($check_mail['phone_number']))
					{
						$sendarray['status'] = 0 ;
						$sendarray['message']  =   'oops This Mobile Number are already register';
					}
					elseif(!empty($check_mail['username']))
					{
						$sendarray['status'] = 0 ;
						$sendarray['message']  =   'oops This Username are already register';
					}
					else
					{
						$data['username'] = $username;
						$data['slug'] = $username;
						$data['email'] = $email;
						$data['phone_number'] = $phone_number;
						$data['password'] = $password;
							
						$data['token'] = $jwt->encode($data,$jwtsecretkey,'HS256');	
						$this->db->insert('users', $data);
						$lid   =   $this->db->insert_id();
						    
						$this->db->select('id,username,email,email_status,password,phone_number,role_id,token');
						$query = $this->db->get_where('users', array('id' => $lid))->result_array();
							
						$sendarray['message'] =  'Success! You are register  successfully.';
						$sendarray['status'] = 1 ;
						$sendarray['data'] =  $query;					 
					}
				}
				else
				{
					$sendarray['message'] =  'Error! All Fields are required.';
				    $sendarray['status'] = 0 ;
				}
				return json_encode($sendarray);	
			}
			
			public function checklogin()
			{
				$sendarray = array();
				$token = $this->input->post('Authorization');
				if(empty($token))
				{
					$sendarray['status']  =  0 ;
					$sendarray['message'] =   'info! You are not login';
                    $sendarray['data']  =  array();					
				}
				else
				{
				    $tokenParts = explode(".", $token);  
					$tokenHeader = base64_decode($tokenParts[0]);
					$tokenPayload = base64_decode($tokenParts[1]);
					$jwtHeader = json_decode($tokenHeader);
					$jwtPayload = json_decode($tokenPayload, true);
				}	
				if(  !empty($jwtPayload['username']) && !empty($jwtPayload['role_id'])  && !empty($jwtPayload['email']) )
				{
					return true;	
				}        
				$sendarray['status']  =  0 ;
				$sendarray['message'] =   'info! You are not login';	
                 $sendarray['data']  =  array();				
				echo json_encode($sendarray);
				exit(0);				
			}
			
			public function changePassword()
			{
				$sendarray = array();
				$jwt = new JWT();
				$jwtsecretkey = time();
				
				$user_id            =    $this->input->post('user_id');
				$old_password       =    $this->input->post('old_password');
				$new_password       =    $this->input->post('new_password');
				$confirm_password   =    $this->input->post('confirm_password');
				
		
				if(!empty($old_password) && !empty($new_password) && !empty($confirm_password) &&  !empty($user_id))
				{
					$this->db->select('id,password');
					$this->db->where('id',$user_id); 
					$userinfo	=	$this->db->get('users')->row_array();
                    
					if(!empty($userinfo) && $this->bcrypt->check_password($old_password, $userinfo['password']))
					{
						if($new_password == $confirm_password)
						{
							$update_rows = array('password' => $this->bcrypt->hash_password($new_password));
		                    $this->db->where('id', $user_id);
		                    $this->db->update('users', $update_rows);	
					
							
							$this->db->select('id,username,email,email_status,password,phone_number,role_id,avatar');
					        $this->db->where('id',$user_id); 
					        $updatedrow	=	$this->db->get('users')->row_array();
							$updatedrow['Authorization'] =  $jwt->encode($updatedrow,$jwtsecretkey,'HS256');
							
							$sendarray['status'] = 1 ;
							$sendarray['message'] =  'Success! You are changed password successfully.';
							$sendarray['data'] =  $updatedrow;
						} 
						else
						{
							$sendarray['status'] = 0 ;
							$sendarray['message'] =  'Error! new password and confirm password are not match.';   
						}
					} 
					else 
					{
						$sendarray['status'] = 0 ; 
						$sendarray['message'] =  'Error! Incorrect Old Password.';
					}
				} 
				else 
				{
					$sendarray['status'] = 0 ;
					$sendarray['message'] =  'Error! All Fields are required.';  
				}
				return json_encode($sendarray);
	
	        }
			
			public function resetPassword()
			{
				$sendarray = array();
				
				$username_email               =    $this->input->post('username_email');
				if(!empty($username_email))
				{
					$this->db->select('id,username,email,password');
					$this->db->where('username',$username_email);
                    $this->db->or_where('email',$username_email);					
					$userinfo	=	$this->db->get('users')->row_array();
                    
					if(!empty($userinfo))
					{
						$sendarray['status'] = 1 ; 
						$sendarray['message'] =  'Success! user verified successfully. reset link sended in your mail id. please login';
						$sendarray['data'] = $userinfo; 
					} 
					else 
					{
						$sendarray['status'] = 0 ; 
						$sendarray['message'] =  'Error! Incorrect Emai lD .';
					}
				} 
				else 
				{
					$sendarray['status'] = 0 ;
					$sendarray['message'] =  'Error! All Fields are required.';  
				}
				return json_encode($sendarray);
	
	        }
			   			
		}

?>