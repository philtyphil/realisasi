<?php
class Wilayah_model extends CI_Model{
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
	
	function get_master()
	{
		$data = $this->db->get('TS_WILKKKS');
		
		if($data)
		{
			return $data->result_array();
		}
	}
	
	function edit($id)
	{
		$data = $this->db->where('ID_CONFIG',$id)->get('TS_WILKKKS');
	
		if($data)
		{
			return $data->result_array();
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * [[Description]]
	 * @param [[Type]] $lokasi         Lokasi Pegawai
	 * @param [[Type]] $golongan       Golongan pegawai (yg akan di tampilkan)
	 * @param [[Type]] $status_pegawai status Pegawai Contoh Pegawai Tetap
	 * @param [[Type]] $tahun          tahun
	 */
	function json_datatable($request,$lokasi,$golongan,$status_pegawai,$tahun)
	{
		$columns = array('gol','fld_empnik','fld_empnm','fld_empbod','sex','pddk','stskel','loknm','lokkerja');
		
		// order
		$this->db->order_by('gol',"desc");
		$this->db->order_by('fld_empnm',"asc");
		$this->db->limit(0,10);
		//single where
		if(isset($request['search']['value']) && $request['search']['value'] != "")
		{
			for($i=0;$i<count($request['columns']);$i++)
			{
				$this->db->like($columns[$i],$request['search']['value']);
			}
		}
		
		
		$this->db->select($columns);
		$output = $this->db->get('views_data_pegawai');
		foreach($output->result_array() as $key=> $value)
		{
			$row 	= array();
			$row[] 	= $value['gol'];
			$row[] 	= $value['fld_empnik'];
			$row[] 	= $value['fld_empnm'];
			$row[] 	= $value['fld_empbod'];
			$row[] 	= $value['sex'];
			$row[] 	= $value['pddk'];
			$row[] 	= $value['stskel'];
			$row[] 	= $value['loknm'];
			$row[] 	= $value['lokkerja'];
			$set[] 	= $row;
			
		}
		$all = $this->db->count_all_results('view_data_pegawai');
	
		return array(
			"draw"            => intval( $request['draw'] ),
			"recordsTotal"    => intval( $all ),
			"recordsFiltered" => intval( $output->num_rows() ),
			"data"            => $set
		);
		return $output;
		
	}
	
	function json_dt($request,$id_master,$level)
	{
	
		// Define Showing Field - @philtyphils
		$columns = array('ID','ID_CONFIG','NM_CONFIG','GRP_CONFIG','CUID');
		
		// Define Selected Table - @philtyphils
		$sTable  = "TS_WILKKKS";
		
		

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
			
		
		}
		unset($col);
		
		// Define WHERE - @philtyphils
		$where = " WHERE (";
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
		
		// Define Limit - @philtyphils
		if(isset($request['start']) && $request['start'] != "" && $request['length'] != '')
		{
			if($where != " WHERE (")
			{
				$where .= " AND (";
				$where .= " ROWNUM >= ".intval($request['start'])." AND ROWNUM <= ".intval($request['length'] ) . " ) ";
			}
			else
			{
				$where .= " ROWNUM >= ".intval($request['start'])." AND ROWNUM <= ".intval($request['length']) . " ) ";
			}
		}
		
		// Including Condition Lokasi Pegawai - @philtyphils
	
		if(isset($id_master) && $id_master != "")
		{
			
			if($where != " WHERE (")
			{
				$where .= " AND (";
				$where .= " ID_CONFIG = '".$id_master . "') ";
				
			}
			else
			{
				$where .= " ID_CONFIG = '".$id_master . "') ";
			}

		}
	
		
		// Including Condition Status Pegawai - @philtyphils
	
		//if(isset($level) && $level != "")
		//{
		//	if($where != " WHERE (")
		//	{
		//		$where .= " AND (";
		//	}
		//
		//	$exp = explode("|",$level);
        //
		//	for($i=0;$i<count($exp) -1;$i++)
		//	{
		//		
		//		$where .= " GRP_CONFIG = " .$exp[$i].  " OR ";
		//	}
		//	
		//	$where = substr_replace( $where, "", -4 );
		//	$where .= " ) ";
		//	
		//}
		
 		//Execution Query
		unset($exp);
		$query = "SELECT ".str_replace(" , ", " ", implode(", ", $columns))." FROM ".$sTable." 
			$where
			$order
			";
		$execution = $this->db->query($query);
		$set = array();
		$no = 1;
		foreach($execution->result_array() as $key=> $value)
		{
			$row 	= array();
			$row[] 	= "<small>" . $no++ . "</small>";
			$row[] 	= "<small>" . $value['ID_CONFIG'] . "</small>";
			$row[] 	= "<small>" . $value['NM_CONFIG'] . "</small>";
			$row[] 	= "<small>" . $value['GRP_CONFIG'] . "</small>";
			$row[] 	= "<small>" . $value['CUID'] . "</small>";
			$row[] 	= "<small><a href='".base_url()."wilayah/edit/".encode($value['ID_CONFIG'])."'><button title='Edit Master' class='btn btn-info btn-xs btn-circle btn-line' type='button'><i class='fa fa-pencil icon-only'> </i></button></a> | <button title='Delete Pegawai' class='btn btn-primary btn-xs btn-circle btn-line' type='button'><i class='fa fa-trash-o icon-only'></i></button></small>";
			$set[] 	= $row;
			
		}
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
			if($tahun[0] != "null" && $tahun[1] != "null")
			{
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
	
	
	
	
}
?>