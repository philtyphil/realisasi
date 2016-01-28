<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Pegawai extends CI_Controller {
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
			show_error('Dissalowed Page',"404",$heading = "Autherized is failed. No Page Found!");
		}
		header('content-type:text/html;charset=utf-8');
	}
	
	
	/**
	 * Landing Page Menu Master > Pegawai | @philtyphils
	 */
	public function index()
	{
		$this->load->helper('url');
		$this->load->library('menuroleaccess');
		$auth_page = $this->menuroleaccess->check_access("pegawai");
		if($auth_page) 
		{
			$this->load->model('laporan_model');
			/** Success Login **/
			$data['sForm'] 			= "Laporan";
			$data['title'] 			= "HRM: Pegawai";
			$data['lokasi']			= $this->laporan_model->get_lokasi();
			$data['lok']			= encode($this->session->userdata('fld_emplokcd'));
			$data['nik']			= encode($this->session->userdata('fld_empnik'));
			$data['nama']			= encode(ucfirst($this->session->userdata('fld_empnm')));
			render('pegawai',$data,"pegawai");
		}
		else
		{
			$this->load->library('sess');
			$this->sess->session_destroy();
			redirect(config_item('base_url'));
		}
		
	}
	
	public function json()
	{
		$lokasi			= intval($this->uri->segment(3));
		$nik			= strtolower($this->uri->segment(4));
		$nama			= strtolower($this->uri->segment(5));
		// Prepare Data to Load Datatable
		$this->load->model('pegawai_model');
		$data = $this->pegawai_model->json_data_pegawai($_GET,$lokasi,$nik,$nama);
		
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
	
	public function edit()
	{
		
		$this->load->helper('url');
		$this->load->library('menuroleaccess');
		$auth_page = $this->menuroleaccess->check_access("pegawai");
		if($auth_page) 
		{
			$this->load->model('pegawai_model');
			/** Success Login **/
			$data['sForm'] 			= "Edit Pegawai";
			$data['title'] 			= "HRM: Edit Pegawai";
			$data['nik']			= intval(decode($this->uri->segment(3)));
			$data['agama']			= $this->pegawai_model->get_agama();
			$data['pendidikan']		= $this->pegawai_model->get_pendidikan_pegawai();
			$data['kota']			= $this->pegawai_model->get_kota();
			$data['provinsi']		= $this->pegawai_model->get_provinsi();
			$data['status_pegawai']	= $this->pegawai_model->get_status_pegawai();
			$data['golongan']		= $this->pegawai_model->get_golongan();
			$data['status_keluarga']= $this->pegawai_model->get_keluarga_pegawai();
			$data['lokasi']= $this->pegawai_model->get_lokasi_peg();
			render('pegawai_act',$data,"pegawai");
		}
		else
		{
			$this->load->library('sess');
			$this->sess->session_destroy();
			redirect(config_item('base_url'));
		}
		
	}
	
	
}
/* End of file pegawai.php */
/* Location: ./application/controllers/pegawai.php */
/* Contact : philtyphils@gmail.com;08118779995 */