<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Perusahaan extends CI_Controller {
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
	
	
	public function index()
	{
		$this->load->helper('url');
		$this->load->library('menuroleaccess');
		$auth_page = $this->menuroleaccess->check_access("perusahaan");
		if($auth_page) 
		{
			$this->load->model('perusahaan_model');
			/** Success Login **/
			$data['sForm'] 			= "Perusahaan";
			$data['title'] 			= "Perusahaan";
			$data['master']			= $this->perusahaan_model->get_master();
			$data['group']			= $this->perusahaan_model->get_group();
			
			
			render('perusahaan',$data,"master");
		}
		else
		{
			$this->load->library('sess');
			$this->sess->session_destroy();
			redirect(config_item('base_url'));
		}
		
	}
	
	public function edit()
	{
		$id		 = htmlentities(decode($this->uri->segment(3)));
		$buff	 = array();
		$this->load->model('master_model');
		$data 	 = $this->master_model->edit($id);
	
		if($data)
		{
			$data = $data[0];
			$buffer['id'] 		= $data['ID_MASTER'];
			$buffer['title']	= "Master Edit";
			$buffer['level']	= $data['LEVELS'];
			$buffer['code']		= $data['CODE'];
			$buffer['name']		= $data['NAME'];
			$buffer['desc']		= $data['DESCRIPTION'];
		}
		
		render('mstr_edit',$buffer,'mstr_edit');
	}
	
	public function search()
	{
		$data['data'] 	= ""; 
		$this->load->library('form_validation');
		$this->form_validation->set_rules('lokasi', 'Lokasi ', 'trim|required|alpha_numeric|min_length[3]');
		$this->form_validation->set_rules('tahun', 'Tahun', 'trim|required|max_length[4]');
		
		if($this->form_validation->run() == FALSE)
		{
			if(validation_errors('lokasi')!=NULL){
				$view['error_lokasi'] = strip_tags(form_error('lokasi'));
			}
			if(validation_errors('bulan')!=NULL){
				$view['error_tahun'] = strip_tags(form_error('tahun'));
			}
		}
		else
		{
			$buff['lokasi'] 	= encode($this->input->post('lokasi'));
			$buff['tahun']		= encode($this->input->post('tahun'));
			$buff['base_url']	= config_item('base_ur');
			$buff['template']	= template();
			$this->load->model('laporan_model');
			$buff['data']		= $this->laporan_model->get_laporan_2_dimensi($this->input->post('lokasi'),$this->input->post('tahun'));
			$view['table_absensi']  = $this->parser->parse(template().'/jLoadpage/laporan_2_dimensi_content.html',$buff);
			
		}
	
		header('content-type:application/json');
		echo json_encode($view);
		exit();
	}
	
	/**
	 * Get Laporan Dari Datatable
	 */
	public function get_laporan_datatable()
	{
		$lokasi = decode($this->uri->segment(3));
		$tahun	= decode($this->uri->segment(4));
		$this->load->model('laporan');
		$data 	= $this->laporan->data_pegawai_datatable($lokasi,$tahun);
		bug($data);
	}	
	
	public function insert()
	{
		$this->load->library('menuroleaccess');
		$auth_page = $this->menuroleaccess->check_access("laporan");
		if($auth_page && $this->input->post())
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('group', 'Group', 'trim|required');
			$this->form_validation->set_rules('code', 'Tanggal', 'trim|required');
			$this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[3]');
			$this->form_validation->set_rules('description', 'Description', 'trim|required|min_length[2]');
			
			if($this->form_validation->run() == FALSE)
			{
				if(validation_errors('group')!=NULL){
					$view['error_group'] = strip_tags(form_error('nip'));
				}
				
				if(validation_errors('code')!=NULL){
					$view['error_code'] = strip_tags(form_error('code'));
				}
				
				if(validation_errors('Name')!=NULL){
					$view['error_name'] = strip_tags(form_error('name'));
				}
				
				if(validation_errors('description')!=NULL){
					$view['error_description'] = strip_tags(form_error('description'));
				}
			}
			else
			{
				$this->load->model('master_model');
				$insert = $this->master_model->insert($this->input->post());
				$view['status']			= '400';
				if($insert)
				{
					$view['status'] 	= '200';
				}
			}
			
			header('content-type:application/json');
			echo json_encode($view);
			exit();
			
		}
		
	}
	
	public function p_edit_master()
	{
		$this->load->library('menuroleaccess');
		$auth_page = $this->menuroleaccess->check_access("laporan");
		if($auth_page && $this->input->post())
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('group', 'Group', 'trim|required');
			$this->form_validation->set_rules('code', 'Tanggal', 'trim|required');
			$this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[3]');
			$this->form_validation->set_rules('description', 'Description', 'trim|required|min_length[2]');
			
			if($this->form_validation->run() == FALSE)
			{
				if(validation_errors('group')!=NULL){
					$view['error_group'] = strip_tags(form_error('nip'));
				}
				
				if(validation_errors('code')!=NULL){
					$view['error_code'] = strip_tags(form_error('code'));
				}
				
				if(validation_errors('Name')!=NULL){
					$view['error_name'] = strip_tags(form_error('name'));
				}
				
				if(validation_errors('description')!=NULL){
					$view['error_description'] = strip_tags(form_error('description'));
				}
			}
			else
			{
				$this->load->model('master_model');
				$insert = $this->master_model->p_edit($this->input->post());
				$view['status']			= '400';
				if($insert)
				{
					$view['status'] 	= '200';
				}
			}
			
			header('content-type:application/json');
			echo json_encode($view);
			exit();
			
		}
		
	}
	
	public function delete()
	{
		$data	= array();
		$id = htmlentities(trim($this->uri->segment(3)));
		$this->load->model('master_model');
		$delete = $this->master_model->delete($id);
		$data['status'] 	= "404";
		if($delete)
		{
			$data['status'] = "200";
		}
		
		header('content-type:application/json');
		echo json_encode($data);
		exit();
		
	}
	
	public function json()
	{
		$id_master		= decode($this->uri->segment(3));
		$level 			= decode($this->uri->segment(4));
		
		// Prepare Data to Load Datatable
		$this->load->model('master_model');
		$data = $this->master_model->json_dt($_GET,$id_master,$level);
		
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
	
	/**
	 * Validasi input jam
	 * @param  string $str ex 08:00
	 * @return boolean  true/false
	 */
	function check_time($str)
	{
		$data = preg_match('/^([0-9]{2}):([0-9]{2})/',$str);
		if($data)
		{
			return true;
		}
		else
		{
			$this->form_validation->set_message('check_time', 'Format Jam Yang Anda Masukan Tidak Sesuai.');
			return false;
		}
	}

	
	/**
	 * Fungsi Menampilkan Data Table (tablenya aja!)
	 */
	public function rekap()
	{
		$this->load->library('menuroleaccess');
		$auth_page = $this->menuroleaccess->check_access();
		
		if($auth_page && $this->input->post())
		{
		
			$this->load->library('form_validation');
			$this->form_validation->set_rules('id_master', 'Code/Nama', 'trim|strip_tags');
			$this->form_validation->set_rules('level[]', 'Level', 'trim');
		
			if($this->form_validation->run() == FALSE)
			{
				$view = "";
				if(validation_errors('id_master')!=NULL){
					$view['error_id_master'] = strip_tags(form_error('id_master'));
				}
				if(validation_errors('level')!=NULL){
					$view['error_level'] = strip_tags(form_error('level'));
				}
			}
			else
			{
				// Ubah Array Golongan Menjadi String - @philtyphils
				$level = $this->input->post('level');
				if(is_array($this->input->post('level')))
				{
					$level = "";
					foreach($this->input->post('level') as $key => $value)
					{
						$level .= $value ."|";
					}
				}
		
				
		
				
				$buff['id_master']				= encode($this->input->post('id_master'));
				
				$buff['level']					= encode($level);
				$buff['base_url']				= config_item('base_url');
				$buff['template']				= template();
			
				$view['table_rekap_pegawai']	= $this->parser->parse(template().'/jLoadpage/master_content.html',$buff);
				$view['success']				= "UYEAY";
				
			}
			header('content-type:application/json');
			echo json_encode($view);
			exit();
			
		}
		else
		{
			show_error("Auth Failed To Access This URL","501",$heading="Auth Access Failed");
		}
	}
	
	/**
	 * Fungsi untuk cetak file format excel!
	 */
	public function print_excel()
	{
		$lokasi 		= $this->uri->segment(3);
		$golongan 		= $this->uri->segment(4);
		$status_pegawai = $this->uri->segment(5);
		$awal_masa_ker 	= $this->uri->segment(6);
		$akhir_masa_ker = $this->uri->segment(7);
		$pendidikan 	= $this->uri->segment(8);
		$status_keluarga= $this->uri->segment(9);
		$usia			= $this->uri->segment(10);
		$this->load->library('menuroleaccess');
		$auth_page = $this->menuroleaccess->check_access("laporan");
		if($auth_page) 
		{
			//$masa_kerja = ($akhir_masa_ker == "null" && $awal_masa_ker != "null") ? $awal_masa_ker."-".$this->input->post('awal') : $this->input->post('awal')."-".$akhir_masa_ker;
			
			$this->load->model('laporan_model');
			$data	= $this->laporan_model->get_data_cetak($lokasi,$golongan,$status_pegawai,$awal_masa_ker,$akhir_masa_ker,$pendidikan,$status_keluarga,$usia);
			
			$this->load->library('Excel');  
			// Create new PHPExcel object  
			$objPHPExcel = new PHPExcel();  
			/* Set Width column */
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(26);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(6);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(7);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(19);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(17);
			$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2',"DATA PEGAWAI BGR INDONESIA");
			
			 
			
			$rowdefault = 3;
			/* :set GOLONGAN HEADER */
			if(isset($golongan) && $golongan != '' && $golongan != "ALL-")
			{
				$defGol = $this->laporan_model->get_golongan($golongan);
				$golongan = "";
				foreach($defGol as $key => $value)
				{
					$golongan = $golongan.", ".$value['fld_tyvalnm'];
				}
				unset($defGol);
				$golongan = substr($golongan,1);
				
				// Bold It!
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rowdefault)->getFont()->setBold(true);
				// Write It To Excel
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$rowdefault++,'GOLONGAN: '.$golongan);
			}
			
			/* :set Status Pegawai HEADER */
			if(isset($status_pegawai) && $status_pegawai != '' && $status_pegawai != "ALL-")
			{
				$defGol = $this->laporan_model->get_status_pegawai($status_pegawai);
				
				$status_pegawai = "";
				foreach($defGol as $key => $value)
				{
					$status_pegawai = $status_pegawai.", ".$value['fld_tyvalnm'];
				}
				unset($defGol);
				$status_pegawai = substr($status_pegawai,1);
				
				// Bold It!
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rowdefault)->getFont()->setBold(true);
				//Write It To Excel
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$rowdefault++,'Status Pegawai: '.$status_pegawai);
			}
			
			/* :set Pendidikani HEADER */
			if(isset($pendidikan) && $pendidikan != '' && $pendidikan != "ALL-")
			{
				$defGol = $this->laporan_model->special_pendidikan_pegawai($pendidikan);
				$pendidikan = "";
				foreach($defGol as $key => $value)
				{
					$pendidikan = $pendidikan.", ".$value['fld_tyvalnm'];
				}
				unset($defGol);
				$pendidikan = substr($pendidikan,1);
				
				// Bold It!
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rowdefault)->getFont()->setBold(true);
				//Write It To Excel
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$rowdefault++,'Pendidikan: '.$pendidikan);
			}
			
			$col = "A";$rowdefault++;
			$styleCenter = array(
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					)
				);

			
			// :set It To Center On Vertical Or Horizontal - @philtyphils
			$objPHPExcel->getActiveSheet()->getStyle("A".$rowdefault.":J".$rowdefault)->applyFromArray($styleCenter);
			// :set Row Height - @philtyphils
			$objPHPExcel->getActiveSheet()->getRowDimension($rowdefault)->setRowHeight(35);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.$rowdefault,"Golongan");
			$objPHPExcel->getActiveSheet()->getStyle($col.$rowdefault)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col.$rowdefault,"NIK");
			$objPHPExcel->getActiveSheet()->getStyle($col.$rowdefault)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col.$rowdefault,"Nama Pegawai");
			$objPHPExcel->getActiveSheet()->getStyle($col.$rowdefault)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col.$rowdefault,"Tgl Lahir");
			$objPHPExcel->getActiveSheet()->getStyle($col.$rowdefault)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col.$rowdefault,"Jns. Kelamin");
			$objPHPExcel->getActiveSheet()->getStyle($col.$rowdefault)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col.$rowdefault,"Sts. Pegawai");
			$objPHPExcel->getActiveSheet()->getStyle($col.$rowdefault)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col.$rowdefault,"Pend.");
			$objPHPExcel->getActiveSheet()->getStyle($col.$rowdefault)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col.$rowdefault,"Sts. Kel");
			$objPHPExcel->getActiveSheet()->getStyle($col.$rowdefault)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col.$rowdefault,"Lokasi");
			$objPHPExcel->getActiveSheet()->getStyle($col.$rowdefault)->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col.$rowdefault,"Masa Kerja");
			$objPHPExcel->getActiveSheet()->getStyle($col.$rowdefault)->getFont()->setBold(true);
			$col = "A";$rowdefault++;
				//* Freeze **//
			$objPHPExcel->getActiveSheet()->freezePane($col.$rowdefault);
			
			foreach($data as $key => $value)
			{
				$time = explode(".",$value['masa_kerja']);
				if(count($time) > 0)
				{
					$tahun = $time[0] . " Tahun ";
					$bulan = substr($time[1],1,1) . " Bulan";
				}
				$masa_kerja =  $tahun . $bulan;
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col .$rowdefault, "GOL. ".$value['gol']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col . $rowdefault,$value['fld_empnik']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col . $rowdefault,$value['fld_empnm']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col . $rowdefault,$value['fld_empbod']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col . $rowdefault,$value['sex']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col . $rowdefault,$value['stspeg']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col . $rowdefault,$value['pddk']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col . $rowdefault,$value['stskel']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col . $rowdefault,$value['loknm']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$col . $rowdefault,$masa_kerja);
				$col="A";$rowdefault++;unset($masa_kerja);
				
			}
			
		
			/** Set Default style **/
			$styleArray = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			
			// Set document properties  
			$objPHPExcel->getProperties()->setCreator("Sulistyo Nur Anggoro - @philtyphils")  
				->setLastModifiedBy("philtyphils@gmail.com")  
				->setTitle("DATA PEGAWAI BGR")  
				->setSubject("Office 2007 XLSX Test Document")  
				->setDescription("Absensi Pegawai")  
				->setKeywords("BGR - Report Absnesi")  
				->setCategory("Report");  
			
			
			// Rename worksheet (worksheet, not filename)  
			$objPHPExcel->getActiveSheet()->setTitle('DATA PEGAWAI');  
			
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet  
			$objPHPExcel->setActiveSheetIndex(0); ob_end_clean();   
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment;filename="DATA PEGAWAI BGT.xlsx"');  
			header('Cache-Control: max-age=0');  
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  
			$objWriter->save('php://output');  
			exit();
		}
		else
		{
			show_error("Auth Failed To Print",'502',$heading = "AUTH PRINT EXCEL FAILED");
		}
	}
	
	/**
	 * Print to pdf with PHP tcpdf
	 */
	public function print_pdf()
	{	
		$this->load->library('menuroleaccess');
		$auth_page = $this->menuroleaccess->check_access("laporan");
		if($auth_page)
		{
			$this->load->library('outpdf');$this->load->model('absensi_model');
			$lokasi 		= $this->uri->segment(3);
			$golongan 		= $this->uri->segment(4);
			$status_pegawai = $this->uri->segment(5);
			$awal_masa_ker 	= $this->uri->segment(6);
			$akhir_masa_ker = $this->uri->segment(7);
			$pendidikan 	= $this->uri->segment(8);
			$status_keluarga= $this->uri->segment(9);
			$usia			= $this->uri->segment(10);
			//$masa_kerja 	= ($this->input->post('akhir') == "null" && $this->input->post('awal') != "null") ? $this->input->post('awal')."-".$this->input->post('awal') : $this->input->post('awal')."-".$this->input->post('akhir');
			
			$this->load->model('laporan_model');
			$data		= $this->laporan_model->get_data_cetak($lokasi,$golongan,$status_pegawai,$awal_masa_ker,$akhir_masa_ker,$pendidikan,$status_keluarga,$usia);
			$cetak 		= cetak_rekap($data);
			$pdf 		= new Outpdf();
			$pdf->out($cetak, FALSE, 'laporan_bulanan.pdf', 'P');
			exit();
		}
		else
		{
			show_error("Auth Failed To Print",'502',$heading = "AUTH PRINT PDF FAILED");
		}
	}
	
	/**
	 * Print HTML
	 */
	public function print_html()
	{		
		$lokasi 		= $this->uri->segment(3);
		$golongan 		= $this->uri->segment(4);
		$status_pegawai = $this->uri->segment(5);
		$awal_masa_ker 	= $this->uri->segment(6);
		$akhir_masa_ker = $this->uri->segment(7);
		$pendidikan 	= $this->uri->segment(8);

		$masa_kerja = ($this->input->post('akhir') == "null" && $this->input->post('awal') != "null") ? $this->input->post('awal')."-".$this->input->post('awal') : $this->input->post('awal')."-".$this->input->post('akhir');

		$this->load->model('laporan_model');
		$data		= $this->laporan_model->get_data_cetak($lokasi,$golongan,$status_pegawai,$awal_masa_ker,$akhir_masa_ker,$pendidikan);
		$cetak 		= cetak_rekap($data);
		echo $cetak;
		exit();
	}
}
/* End of file laporan.php */
/* Location: ./application/controllers/laporan.php */
/* Contact : philtyphils@gmail.com;08118779995 */