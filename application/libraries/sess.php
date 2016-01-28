<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed - philtyphil');

class Sess
{
	public function sess_reg($user)
	{
		$CI=& get_instance();
			
		$CI->load->library('user_agent','encrypt');
		$user = $user[0];
		
		$data = $CI->db->where("id",$user['group'])->limit(1)->get("user_group")->result_array();
		$data = $data[0];		
		$session =  array(
			"logged"			=> 1,
			"group"				=> $data['id'],
			"_i"				=> encode($user['id']),
			"username"			=> $user['username'],
			"add"				=> $data['add'],
			"edit"				=> $data['edit'],
			"delete"			=> $data['delete'],
			"view"				=> $data['view'],
			"browser"			=> $CI->agent->browser(),
			"ip"				=> $_SERVER['REMOTE_ADDR'],
			"last_login"	 	=> date("Y-m-d H:i:s")
		);
		
		$r = $this->update_login($user['id'],$session);
		
		if($r)
		{
			$CI->session->set_userdata($session);
			/*
			*	Unset useless variable to handlin xss injection (I think); :p
			*/
			unset($user);unset($data);
			return true;
		}
		else
		{
			show_error("Error Update","404",$heading="Update Session To Database Failed");
			return false;
		}
	}
	
	public function session_destroy()
	{
		$CI=& get_instance();
		$newdata = array(
		"ip" 		=> "",
		"browser" 	=> "",
		);
		$CI->session->sess_destroy();
		$CI->db->where('id',$CI->session->userdata(decode($CI->session->userdata('id'))))->update('user',$newdata);
		return false;
	}
	
	public function update_login($id,$var)
	{
	
		$CI=& get_instance();
		
		$set_session = array(
			"ip" 			=> $var['ip'],
			"browser" 		=> $var['browser']
		);
		$update = $CI->db->where("id",$id)->update('user',$set_session);
		
		if($update)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
}