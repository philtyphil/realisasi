<?php
class Perusahaan_model extends CI_Model{
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
	/**
	 *  @philtyphils
	 *  
	 *  @param [array] $post Data from database
	 *  @return Return_Description
	 */
	function insert($post)
	{
		$this->db->set('TGROUP', "MA");
		$this->db->set('CODE', $post['code']);
		$this->db->set('NAME', $post['name']);
		$this->db->set('ID_PARENT', "1");
		$this->db->set('DESCRIPTION', $post['description']);
		$this->db->set('SORTORDER', 2);
		$this->db->set('CDATE',"to_date('".date('Y-m-d H:i:s')."','YYYY/MM/DD HH24:MI:SS')",false);
		$this->db->set('MDATE',"to_date('".date('Y-m-d H:i:s')."','YYYY/MM/DD HH24:MI:SS')",false);
		$data = $this->db->insert('TS_MASTERS'); 
		
		if($data)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function get_group()
	{
		$data = $this->db->group_by('CODE')->select('CODE')->from('TS_MASTERS')->get();
		if(count($data) > 0)
		{
			return $data->result_array();
		}
		else
		{
			return array();
		}
	}
	
	function get_master()
	{
		$data = $this->db->get('TS_MASTERS');
		
		if($data)
		{
			return $data->result_array();
		}
	}
	/**
	 *  @philtyphils
	 *  
	 *  @param [int] $id Primary key of TS_MASTERS
	 *  @return data array() from database
	 *  
	 *  @08118779995;philtyphils@gmail.com;
	 */
	function edit($id)
	{
		$data = $this->db->get('TS_MASTERS');
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
	 *  @philtyphils
	 *  
	 *  @param [int] $id primary key T_MASTER
	 *  @return false/true
	 */
	function delete($id)
	{
		$DATA = $this->db->where('ID_MASTER',$id)->delete('TS_MASTERS');
		return $DATA;
		
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
		$columns = array('ID_MASTER','CODE','NAME','DESCRIPTION','LEVELS','F_EVALS');
		
		// Define Selected Table - @philtyphils
		$sTable  = "TS_MASTERS";
		
		

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
					$where .= $columns[$col] ." LIKE '%".$val."%' OR ";
				}
			}
			$where = substr_replace( $where, "", -3 );
			$where .= " ) ";
			
		}
		
		if(isset($id_master) && $id_master != '')
		{
			if(isset($where) && $where != " WHERE (" )
			{
				$where = " AND ( ";
			}
			else
			{
				$where = " WHERE (";
			}
			
			// Multiple Where - @philtyphils
			for($i=0;$i<count($request['columns']);$i++)
			{
				$str = $request['columns'][$i]['searchable'];
				if($str == "true")
				{
					if(($columns[$col] != "") && ($i < count($columns)))
					{
						$col	= $request['columns'][$i]['data'];
						$val	= $id_master;
						$where .= "TO_CHAR(".$columns[$col].") LIKE '%".$val."%' OR ";
					}
					
				}
				
			}
			$where = substr_replace( $where, "", -3 );
			$where .= " ) ";
			
		}
		
		unset($col);
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
		
		
	
		// Including Condition Status Pegawai - @philtyphils
		if(isset($level) && $level != "")
		{
			if($where != " WHERE (")
			{
				$where .= " AND (";
			}
		
			$exp = explode("|",$level);

			for($i=0;$i<count($exp) -1;$i++)
			{
				
				$where .= " CODE = '" .$exp[$i]. "' OR ";
			}
			$where = substr_replace( $where, "", -4 );
			$where .= " ) ";
			
		}
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
			$row[] 	= "<small>" . $value['CODE'] . "</small>";
			$row[] 	= "<small>" . $value['NAME'] . "</small>";
			$row[] 	= "<small>" . $value['LEVELS'] . "</small>";
			$row[] 	= "<small></small>";
			$row[] 	= "<small></small>";
			$row[] 	= "<small>" . $value['F_EVALS'] . "</small>";
			$row[] 	= "<small><a href='".base_url()."master/edit/".encode($value['ID_MASTER'])."'><button title='Edit Master' class='btn btn-info btn-xs btn-circle btn-line' type='button'><i class='fa fa-pencil icon-only'> </i></button></a> | <button onclick='deletemaster($value[ID_MASTER])' title='Delete Pegawai' class='btn btn-primary btn-xs btn-circle btn-line' type='button'><i class='fa fa-trash-o icon-only' ></i></button></small>";
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

	
}
?>