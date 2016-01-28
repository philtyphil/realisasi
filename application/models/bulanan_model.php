<?php
class Bulanan_model extends CI_Model{
	private $db;
	function __construct()
	{
		parent::__construct();
		$this->db = $this->load->database('default', TRUE);
		
		if(!$this->session->userdata('logged'))
		{
			show_error('Dissalowed Page',"404",$heading = "Autherized is failed. No Page Found!");
		}

	}
	
	function getDaPe()
	{
		$data = $this->db->from("TS_WILKKKS")->get();
		return $data;
	}
	

}
?>