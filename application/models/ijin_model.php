<?php
class Ijin_model extends CI_Model{
	private $db;
	function __construct()
	{
		parent::__construct();
		$this->db = $this->load->database('default', TRUE);
	}
	
	function get_jabatan($id)
	{
		$data = $this->db->where('fld_empid',$id)->limit(1)->get('view_data_pegawai');
		if($data->num_rows > 0)
		{
			return $data->result_array();
		}
	}
	
	function jenis_ijin()
	{
		$data = $this->db->get('tbl_jenisijin');
		if($data->num_rows > 0)
		{
			return $data->result_array();
		}
	}
	
	function insert($arr)
	{
		$jenis_ijin = decode($arr['jenis_ijin']);
	
		$ExpJns		= explode("|",$jenis_ijin);
		$last		= $this->db->select('fld_ijinid')->limit(1)->order_by('fld_ijinid',"DESC")->get('tbl_ijin')->result_array();
		$no			= intval($last[0]['fld_ijinid']) + 1;
		$newdata = array(
			"fld_empid" 	=> $arr['id'],
			"fld_ijinno"	=> $no ."/".trim($ExpJns[1])."/".date("m")."/".date("Y"),
			"fld_ijindt" 	=> date("Y-m-d"),
			"fld_ijinutk" 	=> $ExpJns[0],
			"fld_ijinperlu" => $ExpJns[1]."  -  ".$arr['keperluan'],
			"fld_ijinalm" 	=> $arr['tmp_tujuan'],
			"fld_ijindtawl" => tgl_to_db($arr['tgl_awal']),
			"fld_ijindtakh" => tgl_to_db($arr['tgl_akhir']),
			"fld_ijinjam" 	=> date("H:i:s"),
			"fld_lup"		=> date("Y-m-d H:i:s")
			
		);
		
		$_init_insert = $this->db->insert('tbl_ijin',$newdata);
		if($_init_insert)
		{
			return true;
		}
		else
		{
			return false;
		}	
	
	}
	
	function json_dt($request)
	{
		// Define Showing Field - @philtyphils
		$columns = array('fld_ijinno','fld_ijindtawl','fld_ijinjam','fld_ijinperlu','fld_ijinsts','fld_ijinutk','fld_ijindtakh','fld_ijinid');
		
		// Define Selected Table - @philtyphils
		$sTable  = "tbl_ijin";
		
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
				$order = " ORDER BY fld_ijinid desc";
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
			$row 	= array();
			$row[] 	= "<small>" . $value['fld_ijinno'] . "</small>";
			//:set Tanggal Ijin
			if($value['fld_ijinutk'] == 3) // Jenis Ijin sakit
			{
				$tanggal_ijin = $value['fld_ijindtawl'] . "<i>&nbsp;&nbsp;<b>s/d</b>&nbsp;&nbsp;</i>". $value['fld_ijindtakh'];
			}
			else if($value['fld_ijinutk'] == 1 || $value['fld_ijinutk'] == 2) // Jenis TMB or PSW
			{
				$tanggal_ijin = $value['fld_ijindtawl'] . "<br/>". $value['fld_ijinjam'];
			}
			else
			{
				$tanggal_ijin = $value['fld_ijindtawl'] . "&nbsp;". $value['fld_ijinjam'];
				$tanggal_ijin .= "<i>&nbsp;&nbsp;<b>s/d</b>&nbsp;&nbsp;</i>";
				$tanggal_ijin .= $value['fld_ijindtawl'] . "&nbsp;". $value['fld_ijinjam'];
			}
			$row[] 	= "<small>" . $tanggal_ijin . "</small>";
			
			$explode = explode("-",$value['fld_ijinperlu']);
			$jenis_ijin = trim($explode[0]);
			$alasan		= trim($explode[1]);
			$row[]	= "<small>" . $jenis_ijin . "</small>";
			$row[]	= "<small>" . $alasan . "</small>";
			if($value['fld_ijinsts'] == 0)
			{
				$status = '<span class="label label-pending arrowed-in-right arrowed-in">Diajukan</span>';
			}
			else if($value['fld_ijinsts'] == 1)
			{
				$status = '<span class="label label-paid arrowed-in-right arrowed-in">Disetujui</span>';
			}
			else
			{
				$status = '<span class="label label-inverse arrowed-in-right arrowed-in">Ditolak</span>';
			}
			$row[]	= "<small>" . $status . "</small>";
			
			if($value['fld_ijinsts'] == 0)
			{
				$button = '<button type="button" onclick="view_detail(\''.encode($value['fld_ijinid']).'\');" title="Detail Ijin" class="btn btn-success btn-xs btn-circle btn-line"><i class="fa fa-search icon-only"></i></button>';
				$button .= ' | <button type="button" title="Approve Ijin" class="btn btn-info btn-xs btn-circle btn-line"><i class="fa fa-check icon-only"></i></button>';
				$button .= ' | <button type="button" title="Ijin Ditolak" class="btn btn-inverse btn-xs btn-circle btn-line"><i class="fa fa-ban icon-only"></i></button>';
			}
			else
			{
				$button = '<button type="button" onClick="getnote(\''.encode($value['fld_ijinid']).'\')" title="Approve Ijin" class="btn btn-info btn-xs btn-circle btn-line">
							<i class="fa fa-info icon-only"></i>
						  </button>';
			}
			$row[]	= "<small>".$button."</small>";
			
			
			$set[] 	= $row;
			unset($jenis_ijin);
			
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
	
	public function get_detail($id)
	{
		$data = $this->db->where("fld_ijinid",$id)->limit("1")->join('tbl_emp','tbl_emp.fld_empid=tbl_ijin.fld_empid')->join('tbl_jenisijin','tbl_jenisijin.tbl_jnsijinid=tbl_ijin.fld_ijinutk')->get('tbl_ijin');
		if($data->num_rows > 0)
		{
			return $data->result_array();
		}
	}
	
	public function approval($id,$note,$status)
	{
		$data	= array(
			"fld_ijinrem"	=> $note,
			"fld_ijinsts"	=> $status
		);
		$update = $this->db->where('fld_ijinid',$id)->update('tbl_ijin',$data);
		if($update)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function getnote($id)
	{
		$data = $this->db->where('fld_ijinid',$id)->get('tbl_ijin');
		if($data->num_rows() > 0)
		{
			return $data->result_array();
		}
		else
		{
			return false;
		}
	}
	
	
	
	
	
}
?>