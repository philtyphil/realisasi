<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed - philtyphil');

class Menuroleaccess
{
	function __construct()
	{
		
	}
	
	function check_access()
	{
		$CI=& get_instance();
		$CI->load->library('user_agent');
		$CI->load->library('session');
		try{
			$data = $CI->db->limit("1")->where('id',decode($CI->session->userdata('_i')))->get('user');
			
		
			
			if($data->num_rows() > 0)
			{
				$data 	= $data->result_array();
				$sess 	= $CI->session->userdata();
				$group	= $CI->db->where('id',$sess['group'])->limit(1)->get('user_group');
			
				if($group->num_rows() > 0)
				{
					$group = $group->result_array();
				
					if($CI->agent->browser() == $data[0]['browser'] &&  $_SERVER['REMOTE_ADDR'] == $data[0]['ip'])
					{
						return true;
					}
					else
					{
						$time = $sess['__ci_last_regenerate']- strtotime(date("Y-m-d H:i:s"));
						if($time > 7200)
						{
							$CI->session->sess_destroy();
							show_error($e->getMessage(),"500",$header="Auth Page Failed");
						}
						return false;
					}
				}
				else
				{
					
					return false;
				}
			}
			else
			{
				return false;
			}
			
			
			
		} catch (Exception $e) {
			show_error($e->getMessage(),"404",$header="Error 500");
		}
		
		
	}

	
}
