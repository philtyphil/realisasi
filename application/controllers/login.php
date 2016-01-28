<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
	public $raw;
	public function __construct()
	{
		parent::__construct();
		
		header('content-type:text/html;charset=utf-8');
	}
	
	public function index()
	{
		$this->load->library('menuroleaccess');
		$view['template'] = template();
		$this->load->view(template().'/login.html',$view);
	}
	
	public function proses()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('username', 'Username', 'trim|required|alpha_numeric|min_length[3]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		
		if($this->form_validation->run() == FALSE)
		{
			if(validation_errors('username')!=NULL){
				$view['error_username'] = strip_tags(form_error('username'));
			}
			if(validation_errors('password')!=NULL){
				$view['error_password'] = strip_tags(form_error('password'));
			}
			
		}
		else
		{
			$this->load->model('login_model');
			$username = $this->input->post('username');
			$password = $this->input->post('password');
		
			$doLogin  = $this->login_model->doLogin($username,$password);
				
			if($doLogin)
			{
				$this->load->library('sess');
				$this->sess->sess_reg($doLogin);
				$view['success'] = "Success Login";
			}
			else
			{
				$view['error_failed'] = "username or password WRONG!";
			}
		}
		header('content-type:application/json');
		echo json_encode($view);
		exit();
		
	}
	
	function logout()
	{
		$this->load->helper('url');
		$this->session->sess_destroy();
		$view['template'] = template();
		redirect(config_item('base_url'));
	}
	
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */
