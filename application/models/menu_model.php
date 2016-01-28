<?php
class Menu_model extends CI_Model{
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
	
	function get_parent_menu()
	{
		
			$data = $this->db->get('view_menu');
			if($data->num_rows() > 0)
			{
				return $data->result_array();
			}
			else
			{
				return array();
			}
		
	}
	
	function json_dt($request)
	{
		// Define Showing Field - @philtyphils
		$columns = array('fld_menunm','parent','fld_menuurl','fld_menudsc','fld_menuorder','status','fld_menuid');
		
		// Define Selected Table - @philtyphils
		$sTable  = "view_menu";
		
		// Define Limit - @philtyphils
		if(isset($request['start']) && $request['start'] != "" && $request['length'] != '')
		{
			$limit = " LIMIT ".intval($request['length'])." OFFSET ".intval($request['start']);
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
				$order = " ORDER BY fld_menuidp ASC, fld_menuorder desc ";
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
			$row[] 	= "<small>" . $value['fld_menunm'] . "</small>";
			$row[] 	= "<small>" . $value['parent'] . "</small>";
			$row[] 	= "<small>" . $value['fld_menuurl'] . "</small>";
			$row[] 	= "<small>" . $value['fld_menudsc'] . "</small>";
			$row[] 	= "<small>" . $value['fld_menuorder'] . "</small>";
			
			// :set Status Aktif;
			$status = (trim($value['status']) == "1") ? '<span class="label label-paid arrowed-in-right arrowed-in">Aktif</span>' : '<span class="label arrowed-in-right arrowed-in">Tidak Aktif</span>';
			$row[] 	= "<small>" . $status . "</small>";
			
			// :set Edit Button - @Philtyphils
			$button	= '<a href="#" onClick="edit_akun(\''.$value['fld_menuid'] .'\')"><button type="button" title="Edit Akun" class="btn btn-info btn-xs btn-circle btn-line"><i class="fa fa-pencil icon-only"> </i></button></a>&nbsp;|&nbsp;';
			
			// :set Delete Button - @Philtyphils
			$button	.= '<a href="#" onclick="delete_akun(\''.$value['fld_menuid'].'\')"><button type="button"  title="Delete Akun" class="btn btn-inverse btn-xs btn-circle btn-line"><i class="fa fa-trash-o icon-only"></i></button></a>'; 
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
	
	function action($arr)
	{
		
		if($arr['action'] == "add" && $arr['action'] != "edit")
		{
			$new_set_insert = array(
				"fld_menunm" 	=> $arr['fld_menunm'],
				"fld_menuidp" 	=> $arr['fld_menuidp'],
				"fld_menudsc"	=> $arr['fld_menudsc'],
				"fld_menuurl"	=> $arr['fld_menuurl'],
				"fld_menusts"	=> $arr['fld_menusts']
			);
			$data = $this->db->insert('tbl_menu',$new_set_insert);
			if($data)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			$new_set_insert = array(
				"fld_menunm" 	=> $arr['fld_menunm'],
				"fld_menuidp" 	=> $arr['fld_menuidp'],
				"fld_menudsc"	=> $arr['fld_menudsc'],
				"fld_menuurl"	=> $arr['fld_menuurl'],
				"fld_menusts"	=> $arr['fld_menusts']
			);
			$data = $this->db->where("fld_menuid",$arr['fld_menuid'])->update('tbl_menu',$new_set_insert);
			if($data)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	function get_descr($id)
	{
		$data = $this->db->limit(1)->where('fld_menuid',$id)->get('tbl_menu');
		if($data->num_rows () > 0)
		{
			return $data;
		}
	}
	
	function edit_akun($id)
	{
		$data = $this->db->limit(1)->where('fld_menuid',$id)->get('tbl_menu');
		if($data->num_rows () > 0)
		{
			return $data;
		}
	}

	
	
	
	
	
}
?>