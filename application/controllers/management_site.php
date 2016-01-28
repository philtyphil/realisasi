<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Management_site extends CI_Controller {
	public $data;
	public function __construct()
	{
		parent::__construct();
		$this->data =  array(
			'template' => template(),
			'base_url' => config_item('base_url'),
		
		);
		if(!$this->session->userdata('logged'))
		{
			show_error('Dissalowed Page',"404",$heading = "Autherized is failed");
		}
		header('content-type:text/html;charset=utf-8');
	}
	
	public function index()
	{
		$this->load->helper('url');
		$this->load->library('menuroleaccess');
	
		$auth_page = $this->menuroleaccess->check_access("management_site");
		if($auth_page) 
		{
			/** Success Login **/
			$data['sForm'] 		= "Management Site";
			$data['title'] 		= "Management Site - Menu Role Access";
			render('management_site',$data,"management_site");
			
		}
		else
		{
			$this->load->library('sess');
			$this->sess->session_destroy();
			redirect(config_item('base_url'));
		}
	}
	
	public function menu()
	{
		$this->load->helper('url');
		$this->load->library('menuroleaccess');
	
		$auth_page = $this->menuroleaccess->check_access("management_site");
		if($auth_page) 
		{
			/** Success Login **/
			$data['sForm'] 		= "Management Site: Menu";
			$data['title'] 		= "Management Site Menu - Menu Role Access";
			render('management_site_menu',$data,"management_site");
			
		}
		
	}

}

/* End of file home.php */
/* Location: ./application/controllers/home.php */
