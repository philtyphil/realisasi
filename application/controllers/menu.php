<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends CI_Controller {
	public $raw;
	public function __construct()
	{
		parent::__construct();
		$this->data = array(
			'base_url' => config_item('base_url')
		);
		$this->raw = '';
		header('content-type:text/html;charset=utf-8');
	}
	
	public function index()
	{
		$auth = $this->menuroleaccess->check_access('management_site');
		if($auth)
		{
			$this->load->model('menu_model');
			$data['template']		= template();
			$data['title'] 			= "Manage Menu";
			$data['menu_parent'] 	= $this->menu_model->get_parent_menu();
			
			/*
				:Payload render (view name,data,js name)
			*/
			render('menu',$data,'menu');
		}
	}
	
	public function akun_list()
	{
		$auth = $this->menuroleaccess->check_access('management_site');
		if($auth)
		{
			$buff['base_url']			= base_url();
			
			$buff['template'] 			= template();
		
			$html['table_list_akun'] 	= $this->parser->parse(template().'/jLoadpage/manage_content.html',$buff);
		}
	}
	
	public function json()
	{
		$this->load->model('menu_model');
		$data = $this->menu_model->json_dt($_GET);
		
		if(is_array($data))
		{
		
			header('content-type:application/json');
			echo json_encode($data);
			exit();
		}
		else
		{
			show_error("Data Return Isn't Array Value","501",$heading="Eror Load Datatable - @philtyphils");
			return false;
		}
	}
	
	public function delete_akun()
	{
		$id = decode($this->input->post('id'));
		if(intval($id))
		{
			$data = $this->db->where('fld_menuid',$id)->delete('tbl_menu');
			if($data)
			{
				$e = "";
				$e['success'] = "ok";
				echo json_encode($e);
			}
			else
			{
				
				return false;
			}
		}
	}
	
	public function action_akun()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('nama_akun', 'Nama Akun ', 'trim|required|min_length[2]');
		$this->form_validation->set_rules('parent_akun', 'Parent Akun', 'trim|required|numeric');
		$this->form_validation->set_rules('descr', 'Description', 'trim|min_length[1]');
		$this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[1]|numeric');
		$this->form_validation->set_rules('url', 'Menu Url', 'trim');
		$this->form_validation->set_rules('action', 'Action', 'trim|required');
		$this->form_validation->set_rules('id', 'Primary Key', 'trim');
		
		
		if($this->form_validation->run() == FALSE)
		{
			if(validation_errors('nama_akun')!=NULL){
				$view['error_nama_akun'] = strip_tags(form_error('nama_akun'));
			}
			if(validation_errors('parent_akun')!=NULL){
				$view['error_parent_akun'] = strip_tags(form_error('parent_akun'));
			}
			if(validation_errors('descr')!=NULL){
				$view['error_descr'] = strip_tags(form_error('descr'));
			}
			if(validation_errors('url')!=NULL){
				$view['error_url'] = strip_tags(form_error('url'));
			}
			if(validation_errors('status')!=NULL){
				$view['error_status'] = strip_tags(form_error('status'));
			}
			if(validation_errors('action')!=NULL){
				$view['error_action'] = strip_tags(form_error('action'));
			}
			if(validation_errors('id')!=NULL){
				$view['error_id'] = strip_tags(form_error('id'));
			}
			
		}
		else
		{
			$insert = array(
				"fld_menunm" 	=> $this->input->post('nama_akun'),
				"fld_menuidp" 	=> $this->input->post('parent_akun'),
				"fld_menudsc"	=> $this->input->post('descr'),
				"fld_menuurl"	=> $this->input->post('url'),
				"fld_menusts"	=> $this->input->post('status'),
				"action"		=> $this->input->post('action'),
				"fld_menuid"	=> $this->input->post('id')
			);
		
			$this->load->model('menu_model');
			$data = $this->menu_model->action($insert);
			if($data)
			{
				$view = "";
				$view['sukses'] = "ok";
				
			}
			else
			{
				return false;
			}
		}
		
		header('content-type:application/json');
		echo json_encode($view);
		exit();
	}
	
	function valid_url($url)
	{
		$pattern = "|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i";
        if (!preg_match($pattern, $url)){
            $this->form_validation->set_message('valid_url', 'The URL you entered is not correctly formatted.');
            return FALSE;
        }
 
        return TRUE;
		
	}
	
	function get_descr()
	{
		$id = $this->uri->segment(3);
		$this->load->model('menu_model');
		$data = $this->menu_model->get_descr($id);
	
		if($data)
		{
			$view = array();
			$data = $data->result_array();
			
			$view['descr'] = $data[0]['fld_menudsc'];
			$view['id'] = $data[0]['fld_menuidp'];
			
			header('content-type:application/json');
			echo json_encode($view);
			exit();
		}
	}
	
	function edit_akun()
	{	
		$id = $this->uri->segment(3);
		
		$this->load->model('menu_model');
		$data = $this->menu_model->edit_akun($id);
		if($data)
		{
			$view = array();
			$view['data'] = $data->result_array();
			$view['id'] = $id;
			unset($data);
			header('content-type:application/json');
			echo json_encode($view);
			exit();
		}
	}


	
	
	
	
}

/* End of file manage.php */
/* Location: ./application/controllers/manage.php */
