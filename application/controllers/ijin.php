<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ijin extends CI_Controller {
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
		$auth_page = $this->menuroleaccess->check_access("adm_kepegawaian");
		if($auth_page) 
		{
			/** Success Login **/
			$data['sForm'] 		= "home";
			$data['title'] 		= "HOME - Bhana Ghana Reksa";
			$data['breadcumb']	= array('home' => "Landing Page");
			
			render('home',$data);
			
		}
		else
		{
			$this->load->library('sess');
			$this->sess->session_destroy();
			redirect(config_item('base_url'));
		}
	}
	
	/**
	* Permohonan Ijin 
	**/
	public function permohonan()
	{
		$this->load->helper('url');
		$this->load->library('menuroleaccess');
		$auth_page = $this->menuroleaccess->check_access("adm_kepegawaian");
		if($auth_page) 
		{
			$this->load->model('absensi_model');
			$this->load->model('ijin_model');
			/** Success Login **/
			$data['sForm'] 		= "ijin_permohonan";
			$data['title'] 		= "HRM: Izin - Permohonan";
			$data['id']			= intval($this->session->userdata('fld_empid'));
			$data['nik']		= $this->session->userdata('fld_empnik');
			$data['username']	= $this->session->userdata('fld_empnm');
			$data['jenis_ijin']	= $this->ijin_model->jenis_ijin();
			$jabatan			= $this->ijin_model->get_jabatan(intval($this->session->userdata('fld_empid')));
			$data['jabatan']	= $jabatan[0]['pos'];
			render('ijin_permohonan',$data,"ijin_permohonan");
			
		}
		else
		{
			$this->load->library('sess');
			$this->sess->session_destroy();
			redirect(config_item('base_url'));
		}
	}
	
	public function persetujuan()
	{
		$this->load->helper('url');
		$this->load->library('menuroleaccess');
		$auth_page = $this->menuroleaccess->check_access("adm_kepegawaian");
		if($auth_page) 
		{
			$data['sForm'] = "persetujuan";
			$data['title'] = "HRM: Izin - Persetujuan";
			render('ijin_persetujuan_content',$data,'ijin_persetujuan');
		}
		else
		{
			$this->load->library('sess');
			$this->sess->session_destroy();
			redirect(config_item('base_url'));
		}
	}
	
	public function proses()
	{
		$this->load->helper('url');
		$this->load->library('menuroleaccess');
		$auth_page = $this->menuroleaccess->check_access("adm_kepegawaian");
		if($auth_page) 
		{
			
			$this->load->library('form_validation');
			$this->form_validation->set_rules('id','ID ','trim|required');
			$this->form_validation->set_rules('nik', 'Nik ','trim|required');
			$this->form_validation->set_rules('nama_pegawai', 'Nama Pegawai ','trim');
			$this->form_validation->set_rules('jenis_ijin', 'Nama Pegawai ','required');
			$this->form_validation->set_rules('tgl_awal', 'Tanggal Awal', 'trim|required');
			$this->form_validation->set_rules('tgl_akhir', 'Tanggal Akhir', 'trim|required');
			$this->form_validation->set_rules('keperluan', 'Tanggal Akhir', 'trim|required');
			$this->form_validation->set_rules('tmp_tujuan', 'Tempat Tujuan', 'trim|required');
			
			if($this->form_validation->run() == FALSE)
			{
				if(validation_errors('id')!=NULL){
					$view['error_id'] = strip_tags(form_error('id'));
				}
				if(validation_errors('nik')!=NULL){
					$view['error_nik'] = strip_tags(form_error('nik'));
				}
				if(validation_errors('nama_pegawai')!=NULL){
					$view['error_nama_pegawai'] = strip_tags(form_error('nama_pegawai'));
				}
				if(validation_errors('jenis_ijin')!=NULL){
					$view['error_jenis_ijin'] = strip_tags(form_error('jenis_ijin'));
				}
				
				if(validation_errors('tgl_awal')!=NULL){
					$view['error_tgl_awal'] = strip_tags(form_error('tgl_awal'));
				}
				
				if(validation_errors('tgl_akhir')!=NULL){
					$view['error_tgl_akhir'] = strip_tags(form_error('tgl_akhir'));
				}
				
				if(validation_errors('keperluan')!=NULL){
					$view['error_keperluan'] = strip_tags(form_error('keperluan'));
				}
				
				if(validation_errors('tmp_tujuan')!=NULL){
					$view['error_tmp_tujuan'] = strip_tags(form_error('tmp_tujuan'));
				}
				
				
			}
			else
			{
				$this->load->model('ijin_model');
				$data = $this->input->post();
				$insert = $this->ijin_model->insert($data);
				if($insert)
				{
					$view['ok'] = "ok";
				}
			}
			header('content-type:application/json');
			echo json_encode($view);
			exit();
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
		$this->load->helper('url');
		$this->load->library('menuroleaccess');
		$auth_page = $this->menuroleaccess->check_access("adm_kepegawaian");
		if($auth_page) 
		{
			$this->load->model('ijin_model');
			$data = $this->ijin_model->json_dt($_GET);
			if(is_array($data))
			{
				header('content-type:application/json');
				echo json_encode($data);
				exit();
			}
		}
	}
	
	function details()
	{
		$id = intval(decode($this->uri->segment(3)));
		$this->load->model('ijin_model');
		$detail 		= $this->ijin_model->get_detail($id);
		$view['detail'] = $detail;
		$view['html']	= $this->create_html($detail);
		header("content-type:application/json");
		echo json_encode($view);
		exit();
	}
	
	private function create_html($data)
	{
		$html = "";
		if(is_array($data))
		{
			$data = $data[0];
			if(isset($data['fld_ijindtakh']) && $data['fld_ijindtakh'] != "")
			{
				$ijin = $data['fld_ijindtawl'] . "  -  " . $data['fld_ijindtakh'];
			}
			else
			{
				$ijin = $data['fld_ijindtawl'];
			}
			$html .= '
			<form role="form" method="post" id="perstujuan_ijin">
				<div class="form-group">
					<label>Nomor Ijin</label>
						<div class="input-group">
							<input type="text" class="form-control" name="no_ijin" placeholder="No Ijin" value="'.$data['fld_ijinno'].'" disabled>
							<span class="input-group-addon"><i class="fa fa-key"></i></span>
						</div>
				</div>
				<div class="form-group">
					<label>Nama</label>
						<div class="input-group">
							<input type="text" class="form-control" name="nama_pegawai" placeholder="Nama" value="'.$data['fld_empnm'].'" disabled>
							<span class="input-group-addon"><i class="fa fa-user"></i></span>
						</div>
				</div>
				<div class="form-group">
					<label>Jenis Ijin</label>
						<div class="input-group">
							<input type="text" class="form-control" placeholder="Jenis Ijin" name="jenis_ijin" value="'.$data['tbl_jnsijinnm'].'" disabled>
							<span class="input-group-addon"><i class="fa fa-info"></i></span>
						</div>
				</div>
				<div class="form-group">
					<label>Ijin Tanggal</label>
						<div class="input-group">
							<input type="text" class="form-control" name="tgl_ijin" placeholder="Nama" value="'.$ijin.'" disabled>
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
				</div>
				
				<div class="form-group">
					<label>Alasan</label><br/>
					<textarea id="maxL-3" class="form-control" name="ijin_perlu" readonly>'.$data['fld_ijinperlu'].'</textarea>
				</div>
				
				<div class="form-group">
					<label>Note</label><br/>
					<textarea id="maxL-3" class="form-control note" name="note" placeholder="Catatan"></textarea>
				</div>
				<input type="hidden" id="id_ijin" name="id_ijin" value="'.$data['fld_ijinid'].'"/>
			
			</form>'
			;
			
		}
		
		return $html;
	}
	
	public function q()
	{
		$appr	= intval($this->uri->segment(3));
		if($appr)
		{
			if($appr === 1)
			{
				$this->load->model('ijin_model');
				$id 	= intval($this->input->post('id_ijin'));
				$note	= strip_tags($this->input->post('note'));
				$data 	= $this->ijin_model->approval($id,$note,1);
				if($data)
				{
					$view['success']	= "ok";
				}
				else
				{
					$view['fail']	= "Fail";
				}
				
			}
			else if($appr === 2)
			{
				$this->load->model('ijin_model');
				$id 	= intval($this->input->post('id_ijin'));
				$note	= strip_tags($this->input->post('note'));
				$data 	= $this->ijin_model->approval($id,$note,2);
				if($data)
				{
					$view['success']	= "ok";
					
				}
				else
				{
					
					$view['fail']	= "Fail";
				}
				
			}
			else
			{
				$view['nothing']	=	"";
			}
			header("content-type:application/json");
			echo json_encode($view);
			exit();
		}
	}
	
	function note()
	{
		$id 	= intval(decode($this->uri->segment(3)));
		$this->load->model('ijin_model');
		$get   	= $this->ijin_model->getnote($id);
		if($get)
		{
			header('content-type:application/json');
			echo json_encode(array("data" => $get[0]));
			exit();
		}
		
	}
	
	private function sendmails($nama,$email,$id)
	{
		
		$data = "";
		$this->load->helper('mail');
		$redirect 		  		= base_url();
		$dbuff['nomor']			= 7;
		$dbuff['nama']			= "BGR Indonesia";
		$dbuff['email']			= $email;
		$dbuff['id']			= $id;
		$data['to']	 	  		= $email;
		$data['subject']  		= 'Registrasi Panselnas Kementerian BUMN';
		$data['to_name']  		= $nama;
		$data['content']  		= $this->parser->parse('verification.html',$dbuff,true);
		sent_mail($data);
	}

}

/* End of file ijin.php */
/* Location: ./application/controllers/ijin.php */
