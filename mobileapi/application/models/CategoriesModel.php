<?php
     
	 Class CategoriesModel extends CI_Model
		{
			
			function __construct()
			{
				parent::__construct();
                $this->load->library('bcrypt');
					
			}
					
			public function getSubCategories($parent_id)
			{
				$sendarray = array();
				
				$this->db->select('id,slug,parent_id,image');
				$this->db->where('parent_id',$parent_id);
				$subcategories = $this->db->get('categories')->result_array();
               
			
				if(!empty($subcategories))
				{
					$sendarray['status']  =   1;
					$sendarray['message']  =  "successfully get product sub categories";
					$sendarray['data'] =  $subcategories;	
				}
				else
				{
				   $sendarray['status']  =   0;
				   $sendarray['message']  =  "opps can't find any sub category";
                   $sendarray['data'] =  [];				   
				}
				return json_encode($sendarray, true);
                				
            }
            		
		}

?>