<?php
class Login_model extends CI_Model{
	private $db;
	function __construct()
	{
		parent::__construct();
		$this->db = $this->load->database('default', TRUE);
		
	}
	
	function doLogin($username,$password)
	{
		
		$crypt = crypt($password,'!@#$%philtyphil;08118779995;philtyphils@gmail.com');
		$this->db->where('username',$username);
		$this->db->where('password',$crypt);
		$this->db->limit('1');
		$this->db->select('*');
		$this->db->from('user');
		$data = $this->db->get()->result_array();
		return $data;
	}
	
	
	
	
}
?>