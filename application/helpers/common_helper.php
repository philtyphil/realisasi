<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function render($view,$data='',$js='',$layout='frame'){
	$CI=& get_instance();
	$layout = template() ."/".$layout.".html";
	if(isset($js) && $js != "" && !empty($js))
	{
		$js = template() ."/js/".$js.".js";
		$CI->data['js']		 = $CI->parser->parse($js, $CI->data,true);
	}
	else
	{
		$CI->data['js']		 = $js;

	}
	
	$persentase 			= $CI->benchmark->elapsed_time();
	$CI->data['bencmark']	= $persentase;
	
	// set Menu *Unlimited Menus*
	$menu = $CI->session->userdata('menus');
	if(empty($menu) || $menu == "")
	{
		$CI->load->model('frame_model');
		$CI->data['menu'] = $CI->frame_model->set_menu();
		$CI->session->set_userdata(array('menus' => $CI->data['menu']));
	}
	else
	{
		$CI->data['menu'] = $menu;
	}
	
	
	if(is_array($data)){
		$CI->data = array_merge($CI->data,$data);
	}
	
	if(!$layout){
		$CI->parser->parse($view.'.html', $CI->data);
	}
	else
	{
		$CI->data['content'] = $CI->parser->parse(template()."/".$view.'.html', $CI->data,true);
		
		$CI->parser->parse($layout,$CI->data);
	}
}



function template()
{
	$CI=& get_instance();
	
	$template = $CI->db->limit('1')->where('status',"Y")->get('template');
	if($template->num_rows() > 0)
	{
		$r	= $template->result_array();
		return $r[0]['template'];
	}
	else
	{
		show_error("We Havent Template","404",$heading="Undefined Template - @philtyphils");
		return false;
	}
	
}
function encode($str)
{
	$CI=& get_instance();
	$CI->load->library('encrypt');
	$str_encode = $CI->encrypt->encode($str);
	$str 		= str_replace(array('+','/','='),array('-','_','.'),$str_encode);
	return $str;
}

function decode($str)
{
	$CI=& get_instance();
	$CI->load->library('encrypt');
	$data = str_replace(array('-','_','.'),array('+','/','='),$str);
	$mod4 = strlen($data) % 4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	$str_decode = $CI->encrypt->decode($data);
	return $str_decode;
}



function cetak_rekap($row) {
	$CI =& get_instance();
	$string = <<<EOT
<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td colspan="2"><div align="center">
			<h6>BHANA GHARA REKSA</h6>    
    </div></td>
  </tr>
  <tr>
    <td width="50%"><small>DATA PEGAWAI BGR</small></td>
    <td width="50%"></td>
  </tr>
  
  
</table>
<table width="100%" border="1" cellspacing="0" cellpadding="3">
  <thead>
EOT;
        $string .= '
<tr>

    <th align="center" width="5%"><small>Gol.</small></th>
    <th align="center" width="10%"><small>NIK</small></th>
    <th align="center" width="15%"><small>Nama Pegawai</small></th>
    <th align="center" width="11%"><small>Tgl. Lahir</small></th>
    <th align="center" width="13%"><small>Jns. Kelamin</small></th>
    <th align="center" width="13%"><small>Sts. Peg.</small></th>
    <th align="center" width="5%"><small>Pend.</small></th>
    <th align="center" width="5%"><small>Sts. Kel.</small></th>
    <th align="center" width="11%"><small>Lokasi</small></th>
    <th align="center" width="11%"><small>Masa Kerja</small></th>
</tr>
';
	$string .= "</thead><tbody>";
	$rows = 1;
	foreach($row as $key=> $value)
	{
		if(isset($value['masa_kerja']))
			{
				$time = explode(".",$value['masa_kerja']);
				if(count($time) > 0)
				{
					$tahun = $time[0] . " Tahun<br/>";
					$bulan = substr($time[1],1,1) . " Bulan";
				}

				$masa_kerja =  $tahun . $bulan;

			}
		$string .= "<tr>
		<td width=\"5%\"><small>".$value['gol']."</small></td>
		<td align=\"center\" width=\"10%\"><small>".$value['fld_empnik']."</small></td>
		<td align=\"center\" width=\"15%\"><small>".$value['fld_empnm']."</small></td>
		<td align=\"center\" width=\"11%\"><small>".$value['fld_empbod']."</small></td>
		<td align=\"center\" width=\"13%\"><small>".$value['sex']."</small></td>
		<td align=\"center\" width=\"13%\"><small>".$value['stspeg']."</small></td>
		<td align=\"center\" width=\"5%\"><small>".$value['pddk']."</small></td>
		<td align=\"center\" width=\"5%\"><small>".$value['stskel']."</small></td>
		<td align=\"center\" width=\"11%\"><small>".$value['loknm']."</small></td>
		<td align=\"center\" width=\"11%\"><small>".$masa_kerja."</small></td>
		</tr>";
	}
  
  
	$string .= "</tbody></table><br/><br/><small>*dicetak pada tanggal: ".date("d M Y H:i:s")."<small>";
 
  
        return $string;
}

function cetak_absen($nip,$bulan,$tahun,$row) {

	$CI =& get_instance();
	$tgl  = date('M',strtotime($tahun."-".$bulan."-01"));
	$data = $CI->db->where('fld_empnik',$nip)->limit("1")->get('tbl_emp')->result_array();
	$data = $data[0];
        $string = <<<EOT
<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td colspan="2"><div align="center">
			<h6>BHANA GHARA REKSA</h6>    
    </div></td>
  </tr>
  <tr>
    <td width="10%"><small>NIP</small></td>
    <td width="90%"><small>$data[fld_empnik]</small></td>
  </tr>
  <tr>
    <td width="10%"><small>Nama</small></td>
    <td width="90%"><small>$data[fld_empnm]</small></td>
  </tr>
  <tr>
    <td width="10%"><small>Absensi Bulan</small></td>
    <td width="90%"><small>$tgl</small></td>
  </tr>
</table>
<table width="100%" border="1" cellspacing="0" cellpadding="3">
  <thead>
EOT;
        $string .= '
<tr>

    <th align="center" width="10%" rowspan="2"><small>Hari, Tanggal</small></th>
    <th align="center" width="10%" rowspan="2"><small>Absen Masuk</small></th>
    <th align="center" width="10%" rowspan="2"><small>Absen Keluar</small></th>
    <th align="center" width="35%" colspan="5"><small>Jenis Pelanggaran</small></th>
    <th align="center" width="18%" rowspan="2"><small>Keterangan</small></th>
</tr>
<tr>
    <th align="center" width="7%"><small>Cuti</small></th>
    <th align="center" width="7%"><small>TL</small></th>
    <th align="center" width="7%"><small>PSW</small></th>
    <th align="center" width="7%"><small>TMB</small></th>
    <th align="center" width="7%"><small>Total</small></th>
</tr>';
	$string .= "</thead><tbody>";
	$rows = 1;
	foreach($row as $key=> $value)
	{
		
		$string .= "<tr>
		<td width=\"10%\"><small>".hari_indo($value['date']).",<br/>".tanggal_to_read($value['date'])."</small></td>
		<td align=\"center\" width=\"10%\"><small>".$value['jam_datang']."</small></td>
		<td align=\"center\" width=\"10%\"><small>".$value['jam_pulang']."</small></td>
		<td align=\"center\" width=\"7%\"><small>".$value['TL']."</small></td>
		<td align=\"center\" width=\"7%\"><small>".$value['PSW']."</small></td>
		<td align=\"center\" width=\"7%\"></td>
		<td align=\"center\" width=\"7%\"></td>
		<td align=\"center\" width=\"7%\"></td>
		<td align=\"center\" width=\"18%\"></td>
		</tr>";
	}
  
  
	$string .= "</tbody></table><br/><br/><small>*dicetak pada tanggal: ".date("d M Y H:i:s")."<small>";
 
  
        return $string;
 }
function hari_indo($time)
{
	$date_list = array('Sunday' => "Minggu",'Monday' => "Senin", 'Tuesday'=>"Selasa",'Wednesday' => "Rabu",'Thursday' => "Kamis", 'Friday' => "Jumat",'Saturday' => "Sabtu");
	return $date_list[date('l',strtotime($time))];
}
function tanggal_to_read($time)
{
	return date('d M Y',strtotime($time));
}
function bulan_indonesia($tgl)
{
	$bln = array(''=>'','01' => "Januari",'02' => "Febuari", '03' => "Maret", '04' => "April",'05' => "Mei", '06' => "Juni", '07' => "July", '08'=> "Agustus", '09' => "September", '10' => "Oktober", '11' => "November", '12' => "Desember");
	
	return $bln[$tgl];
}

function tgl_to_db($tgl)
{
	$tgl = explode("/",$tgl);
	$thn = $tgl[0];
	$bln = $tgl[1];
	$day = $tgl[2];
	$tanggal = (string) $day . "/" .$bln."/".$thn;
	return $tanggal;
}
function bug($var)
{
	echo "<pre>";print_r($var);die(" <<<");
}

