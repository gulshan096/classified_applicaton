<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Slider_model extends CI_Model
{
   

    //get slider item
    public function get_slider_item($id)
    {
        $id = clean_number($id);
        $this->db->where('id', $id);
        $query = $this->db->get('slider');
        return $query->row();
    }

    //get slider items
    public function get_slider_items()
    {
        $this->db->where('slider.lang_id', $this->selected_lang->id);
        $this->db->order_by('item_order');
        $query = $this->db->get('slider');
        return $query->result();
    }

    //get all slider items
    public function get_slider_items_all()
    {
		$sendarray  = array();
		
        $this->db->order_by('item_order');
        $query = $this->db->get('slider')->result_array();
		
		if(!empty($query))
				  {
					$sendarray['status'] = 1;
                    $sendarray['status'] =  'Successfuly get all Sliders';	
                    $sendarray['data'] =  $query;					
				  }
				  else
				  {
					$sendarray['status'] = 0;
                    $sendarray['data']   =   array();
				  }
				
				return json_encode($sendarray);
       
    }

    //update slider settings
    

}

?>