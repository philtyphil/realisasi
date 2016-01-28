<?php
class Pegawai_model extends CI_Model{
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
	
	function get_pegawai($where = "")
	{
		if($where == "" || empty($where))
		{
			$this->db->where('fld_emplokcd',$this->session->userdata('fld_emplokcd'));
		}
		$data = $this->db->order_by('fld_empnm',"asc")->get('view_data_absensi');
		if($data->num_rows() > 0)
		{
			return $data->result_array();
		}
		else
		{
			return false;
		}
	}
	
	function get_lokasi()
	{
		$data = $this->db->order_by('loknm',"asc")->get('tbl_lokasi');
		if($data->num_rows() > 0)
		{
			return $data->result_array();
		}
		else
		{
			return false;
		}	
		
		
	}
	
	function json_data_pegawai($request,$lokasi,$nik = "",$nama ="")
	{
		// Define Showing Field - @philtyphils
		$columns = array('id_golongan','fld_empnik','fld_empnm','fld_empbod','unit_kerja','lokkerja','pos','loknm','stspeg','id_lokasi','gol');
		
		// Define Selected Table - @philtyphils
		$sTable  = "view_data_pegawai";
		// Define Limit - @philtyphils
		if(isset($request['start']) && $request['start'] != "" && $request['length'] != '')
		{
			$limit = " LIMIT ".intval($request['start']).", ".intval($request['length']);
		}

		// Define Order - @philtyphils
		$order = "";
		if ( isset($request['order']) && count($request['order']) ) 
		{
			$order = " ORDER BY ";
			for($i=0;$i<count($request['order']);$i++)
			{
				
				$col 	= $request['order'][$i]['column'];
				$type	= $request['order'][$i]['dir'];
				$order .= $columns[$col] . " " . $type .", ";
			}
			$order = substr_replace( $order, "", -2 );
			
			if(trim($order) == "ORDER BY")
			{
				$order = " ORDER BY fld_empnm asc ";
			}
		}
		unset($col);
		
		// Define WHERE - @philtyphils
		$where = "";
		if(isset($request['search']['value']) && $request['search']['value'] != '')
		{
			$where = " WHERE (";
			
			
			// Multiple Where - @philtyphils
			for($i=0;$i<count($request['columns']);$i++)
			{
				$str = $request['columns'][$i]['searchable'];
				
				if($str == "true")
				{
					$col	= $request['columns'][$i]['data'];
					$val	= $request['search']['value'];
					$where .= $columns[$col] ." LIKE '%".mysql_real_escape_string($val)."%' OR ";
				}
			}
			$where = substr_replace( $where, "", -3 );
			$where .= " ) ";
			
		}
		
		
		
		/*
		* Extra Condition From Selection of Form - @philtyphils
		*/
		if($where == "") $where =  " WHERE (";$filter = false;
		
		$where_is = $request['columns'];
		foreach($where_is as $key => $value)
		{
			$search = $value['search'];
			if($search['value'] != "" || !empty($search['value']))
		   {
				$filter = true;
				if($where != " WHERE (")
				{
					$where .= " AND (";
					$where .= $columns[$value['data']] . " LIKE '%".$search['value'] . "%' OR";
				}
				else
				{
					$where .= $columns[$value['data']] . " LIKE '%".$search['value']  . "%' OR ";
				}
				
		   }
		}
		if($filter)
		{
			$where = substr_replace( $where, "", -3 );
			$where .= " ) ";
			unset($filter);
		}
		
		
		// Including Condition Lokasi Pegawai - @philtyphils
		if(isset($lokasi) && $lokasi != "")
		{
			if($where != " WHERE (")
			{
				$where .= " AND (";
				$where .= "id_lokasi = ".$lokasi . ") ";
			}
			else
			{
				$where .= " id_lokasi = ".$lokasi . ") ";
			}

			
		}
		
		if(isset($nik) && $nik != "")
		{
			if($where != " WHERE (")
			{
				$where .= " AND (";
				$where .= " fld_empnik LIKE '$".$nik . "%') ";
			}
			else
			{
				$where .= " fld_empnik LIKE '%". $nik . "%') ";
			}
		}
		
		if(isset($nama) && $nama != "")
		{
			if($where != " WHERE (")
			{
				$where .= " AND (";
				$where .= " fld_empnm LIKE '$".$nama . "%') ";
			}
			else
			{
				$where .= " fld_empnm LIKE '%". $nama . "%') ";
			}
		}
		
	
 		//Execution Query
		unset($exp);
		$query = "SELECT ".str_replace(" , ", " ", implode(", ", $columns))." FROM ".$sTable." 
			$where
			$order
			$limit
			";
		$execution = $this->db->query($query);
		$set = array();
		foreach($execution->result_array() as $key=> $value)
		{
			// Prepare For Count Masa Kerja - @philtyphils
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


			$row 	= array();
			$row[] 	= "<small>" ."Gol. ". $value['gol'];
			$row[] 	= "<small>" . $value['fld_empnik'] . "</small>";
			$row[] 	= "<small>" . $value['fld_empnm'] . "</small>";
			$row[] 	= "<small>" . $value['fld_empbod'] . "</small>";
			$row[] 	= "<small>" . $value['unit_kerja'] . "</small>";
			$row[] 	= "<small>" . $value['lokkerja'] . "</small>";
			$row[] 	= "<small>" . $value['pos'] . "</small>";
			$row[] 	= "<small>" . $value['stspeg'] . "</small>";
			
			// :set Edit Button - @Philtyphils
			$button	= '<a href='.config_item('base_url').'pegawai/edit/'.encode($value['fld_empnik']).'/" ><button type="button" title="Edit Pegawai" class="btn btn-info btn-xs btn-circle btn-line"><i class="fa fa-pencil icon-only"> </i></button></a>&nbsp;|&nbsp;';
			
			// :set Delete Button - @Philtyphils
			$button	.= '<a href="#" ><button type="button"  title="Delete Pegawai" class="btn btn-primary btn-xs btn-circle btn-line"><i class="fa fa-trash-o icon-only"></i></button></a>'; 
			$row[]  = $button;
			$set[] 	= $row;
			
		}
		unset($masa_kerja);
		$iTotal = $execution->num_rows();
      
		$sQueryTotal = $query = "SELECT ".str_replace(" , ", " ", implode(", ", $columns))." FROM ".$sTable."  $where";
        $FetchsQuery = $this->db->query($sQueryTotal);
        $iFilteredTotal = $FetchsQuery->num_rows();
      
		unset($where);
		return array(
			"draw"            => intval( $request['draw'] ),
			"recordsTotal"    => intval( $iTotal ),
			"recordsFiltered" => intval( $iFilteredTotal ),
			"data"            => $set
		);
		
	}
	/**
	 * Menampilkan pegawai dari table view_data_pegawai
	 * @param [[Type]] $lokasi lokasinya
	 * @param [[Type]] $tahun  tahunNya
	 * @philtyphils - philtyphils@gmail.com;08118779995
	 */
	function get_laporan_2_dimensi($lokasi,$tahun)
	{
		$the_Query = "SELECT 
						A.fld_tyvalnm AS golongan,
						(SELECT COUNT(*) FROM view_data_pegawai WHERE FLOOR(masa_kerja) = 55 AND id_status_peg != 185 AND id_golongan = A.fld_tyvalid AND id_lokasi = '$lokasi') as pegawai_mpp,
						(SELECT COUNT(*) FROM view_data_pegawai WHERE id_status_peg = 185 AND id_golongan = A.fld_tyvalid AND id_lokasi = '$lokasi') as pensiun,
						(SELECT COUNT(*) FROM view_data_pegawai WHERE id_golongan = A.fld_tyvalid AND id_lokasi = '$lokasi') AS jml_golongan,
						(SELECT COUNT(*) FROM view_data_pegawai WHERE id_golongan = A.fld_tyvalid AND id_status_keluarga = '94' AND id_lokasi = '$lokasi') AS TK,
						(SELECT COUNT(*) FROM view_data_pegawai WHERE id_golongan = A.fld_tyvalid AND id_status_keluarga = '95' AND id_lokasi = '$lokasi' ) AS K0,
						(SELECT COUNT(*) FROM view_data_pegawai WHERE id_golongan = A.fld_tyvalid AND id_status_keluarga = '96' AND id_lokasi = '$lokasi' ) AS K1,
						(SELECT COUNT(*) FROM view_data_pegawai WHERE id_golongan = A.fld_tyvalid AND id_status_keluarga = '97' AND id_lokasi = '$lokasi') AS K2,
						(SELECT COUNT(*) FROM view_data_pegawai WHERE id_golongan = A.fld_tyvalid AND id_status_keluarga = '98' AND id_lokasi = '$lokasi') AS K3,
						(SELECT COUNT(*) FROM view_data_pegawai WHERE id_golongan = A.fld_tyvalid AND TIMESTAMPDIFF(YEAR,fld_empddwrk,now()) <= 10  AND id_lokasi = '$lokasi') AS dibwh10thn,
						(SELECT COUNT(*) FROM view_data_pegawai WHERE id_golongan = A.fld_tyvalid AND TIMESTAMPDIFF(YEAR,fld_empddwrk,now()) > 10   AND id_lokasi = '$lokasi') AS diats10thn
						
						FROM
						(
						
						SELECT fld_tyvalid,fld_tyvalnm FROM tbl_tyval
						WHERE fld_tyid= '24'
						) AS A";
		
		$db	= $this->db->query($the_Query);
		if($db->num_rows() > 0)
		{
			return $db->result_array();
		}
		else
		{
			return false;
		}
		
	}
	
	/**
	 * Get Data Untum Menampilkan Excel (Data Filter Di dapat dari form DATA PEGAWAI)
	 * @param int $lokasi         Lokasi Pegawai Ex: 201,202,203
	 * @param array $status_pegawai Status Pegawai (bisa Lebih dari Satu)
	 * @param numeric $awal_masa_ker  Filter Awal Masa Kerja
	 * @param numeric $akhir_masa_ker Filter Akhir Masa Kerja
	 * @param array $pendidikan     Pendidikan (bisa Lebih Dari Satu)
	 */
	function get_data_cetak($lokasi,$golongan,$status_pegawai,$awal_masa_ker,$akhir_masa_ker,$pendidikan,$status_keluarga,$usia = "")
	{
		
		$where = " WHERE (";
		// Including Condition Golongan - @philtyphils
		if(isset($golongan) && $golongan != "" && strpos($golongan,"ALL") === false)
		{
			if($where != " WHERE (")
			{
				$where .= " AND (";
			}
			
			$exp = explode("-",$golongan);
			
			for($i=0;$i<count($exp) - 1;$i++)
			{
				$gol	= explode("-",$exp[$i]);
				$where .= " id_golongan = ".$gol[0] . " OR ";
			}
			$where = substr_replace( $where, "", -4 );
			$where .= " ) ";
		}
		
		// Including Condition Status Pegawai - @philtyphils
		if(isset($status_pegawai) && $status_pegawai != "" && strpos($status_pegawai,"ALL") === false)
		{
			if($where != " WHERE (")
			{
				$where .= " AND (";
			}
		
			$exp = explode("-",$status_pegawai);

			for($i=0;$i<count($exp) -1;$i++)
			{
				
				$where .= "id_status_peg = " .$exp[$i].  " OR ";
			}
			$where = substr_replace( $where, "", -4 );
			$where .= " ) ";
			
		}
		
		// Including Condition Status Keluarga - @philtyphils
		if(isset($status_keluarga) && $status_keluarga != "" && strpos($status_keluarga,"ALL") === false)
		{
			if($where != " WHERE (")
			{
				$where .= " AND (";
			}
		
			$exp = explode("-",$status_keluarga);

			for($i=0;$i<count($exp) -1;$i++)
			{
				
				$where .= "id_status_keluarga = " .$exp[$i].  " OR ";
			}
			$where = substr_replace( $where, "", -4 );
			$where .= " ) ";
			
		}
		
		// Including Condition Lokasi Pegawai - @philtyphils
		if(isset($lokasi) && $lokasi != "")
		{
			if($where != " WHERE (")
			{
				$where .= " AND (";
				$where .= "id_lokasi = ".$lokasi . ") ";
			}
			else
			{
				$where .= " id_lokasi = ".$lokasi . ") ";
			}

			
		}

		// Including Condition Pendidikan Pegawai - @philtyphils
		if(isset($pendidikan) && $pendidikan != "" && strpos($pendidikan,"ALL") === false)
		{
			if($where != " WHERE (")
			{
				$where .= " AND (";
			}
			
			$exp = explode("-",$pendidikan);
		
			for($i=0;$i<count($exp) - 1;$i++)
			{
				
				$where .= "id_pendidikan = " .$exp[$i].  " OR ";
			}
			$where = substr_replace( $where, "", -4 );
			$where .= " ) ";
			
		}
		
		$masa_kerja = ($akhir_masa_ker == "null" && $awal_masa_ker != "null") ? $awal_masa_ker."-".$awal_masa_ker : $awal_masa_ker."-".$akhir_masa_ker;
		// Including Condition Masa Kerja Pegawai - @Philtyphils
		if(isset($masa_kerja) && trim($masa_kerja) != "-")
		{
			$tahun = explode("-",$masa_kerja);
			
			if((isset($tahun[0]) && $tahun[0] != "") && (isset($tahun[1]) && $tahun[1] != ""))
			{
				if($where != " WHERE (")
				{
					$where .= " AND (";
				}
				$where .= " FLOOR(masa_kerja) >= ".$tahun[0]." AND FLOOR(masa_kerja) <= ".$tahun[1];
				$where .= " ) ";
			}
 		}
		
		// Including Condition Usia Pegawai - @philtyphils
		if(isset($usia) && $usia != "" && $usia != "null")
		{
			if($where != " WHERE (")
			{
				$where .= " AND (";
				$where .= "TIMESTAMPDIFF(YEAR,fld_empbod,NOW()) = ".$usia . ") ";
			}
			else
			{
				$where .= "TIMESTAMPDIFF(YEAR,fld_empbod,NOW()) = ".$usia . ") ";
			}

		}
		$columns = 'id_golongan,fld_empnik,fld_empnm,fld_empbod,sex,id_status_peg,id_pendidikan,id_status_keluarga,loknm,masa_kerja,gol,stskel,stspeg,pddk';
		$order = " ORDER BY id_golongan ASC, fld_empnm ASC ";
		$query = "SELECT * FROM view_data_pegawai ".$where.$order;
		
		$data = $this->db->query($query);
		
		if($data->num_rows() > 0)
		{
			return $data->result_array();
		}
		else
		{
			return array();
		}
	}
	
	
	function get_golongan($where = "")
	{
		// Add Kondisi Untuk Where Print Excel - @Philtyphils
		if($where != "")
		{
			$exp = explode("-",$where);
			for($i = 0;$i<count($exp)-1;$i++)
			{
				$this->db->or_where('fld_tyvalid',$exp[$i]);
			}
		}
		
		$data = $this->db->select('fld_tyvalid,fld_tyvalnm')->where('fld_tyid',"24")->order_by('fld_tyvalid',"ASC")->get('tbl_tyval');
		
		if($data->num_rows > 0)
		{
			return $data->result_array();
		}
		else
		{
			return array();
		}
	}
	
	function get_status_pegawai($where = "")
	{
		// Add Kondisi Untuk Where Print Excel - @Philtyphils
		if($where != "")
		{
			$exp = explode("-",$where);
			for($i = 0;$i<count($exp)-1;$i++)
			{
				$this->db->or_where('fld_tyvalid',$exp[$i]);
			}
		}
		
		$data = $this->db->where('fld_tyid',"19")->order_by('fld_tyvalid',"ASC")->get('tbl_tyval');
		if($data->num_rows > 0)
		{
			return $data->result_array();
		}
		else
		{
			return array();
		}
	}

	function get_pendidikan_pegawai()
	{
		$data  = 	$this->db->where('fld_tyid',"13")->order_by('fld_tyvalcd',"ASC")->get('tbl_tyval');
		
		if($data->num_rows > 0)
		{
			return $data->result_array();
		}
		else
		{
			return array();
		}
	}
	
	function special_pendidikan_pegawai($where)
	{
		if($where != "ALL-" && isset($where))
		{
			$exp = explode("-",$where);
			$where = "WHERE fld_tyid = 13 AND (";
			for($i = 0;$i<count($exp)-1;$i++)
			{
				$where .= "fld_tyvalcd = ".$exp[$i]." OR ";
			}
		
			$where = substr_replace( $where, "", -4 );
			$where .= ")";
		}
		else
		{
			$where = " WHERE fld_tyid = 13 ";
		}
		$query = "SELECT fld_tyvalnm FROM tbl_tyval ".$where;
		unset($where);
		$data  = $this->db->query($query);
		if($data->num_rows() > 0)
		{
			return $data->result_array();
		}
		else
		{
			return false;
		}
	}
	
	function get_keluarga_pegawai()
	{
		$data = $this->db->where('fld_tyid',"23")->get('tbl_tyval');
		if($data->num_rows > 0)
		{
			return $data->result_array();
		}
		else
		{
			return array();
		}
	}
	
	function get_agama()
	{
		$data = $this->db->where('fld_tyid',"16")->get('tbl_tyval');
		if($data->num_rows > 0)
		{
			return $data->result_array();
		}
		else
		{
			return array();
		}
	}
	
	function get_kota()
	{
		$data = $this->db->get('tbl_city');
		if($data->num_rows > 0)
		{
			return $data->result_array();
		}
		else
		{
			return array();
		}
	}
	
	function get_provinsi()
	{
		$data = $this->db->order_by('fld_pulau',"ASC")->order_by('fld_provenm',"ASC")->get('tbl_prove');
		if($data->num_rows > 0)
		{
			return $data->result_array();
		}
		else
		{
			return array();
		}
		
	}
	
	function get_lokasi_peg()
	{
		$sql 	= "SELECT concat(loktp, ' - ', loknm) as `lokasi`, `lokcd` FROM `tbl_lokasi`";
		$data = $this->db->query($sql);
		if($data->num_rows > 0)
		{
			return $data->result_array();
		}
		else
		{
			return array();
		}
	}
	
	
	
	
}
?>