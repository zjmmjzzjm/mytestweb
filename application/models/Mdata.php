<?php
require ( "sphinxapi.php" );

class Mdata extends CI_Model
{
	private $cl;//sphinx client

	function Mdata()
	{
		parent::__construct();
		$this->load->database();
		$this->init_sphinx();
	}
	function search($key, $page)
	{
		$key = urldecode($key);
		$res = $this->sphinx_query($key, "*", $page);
		if($res == NULL)
			return NULL;
		$where = "";

		foreach($res["matches"] as $match)
		{
			if($where != "")
			{
				$where .= " or id=".$match['id'];
			}
			else
			{
				$where = "id=".$match['id'];
			}
		}
		$sql = "select * from hash_info where " . $where;
		//echo $sql;
		$q = $this->db->query($sql);
		$arr = array();
		foreach($q->result() as $row)
		{
			$infos = explode("\n", $row->info);
			$i = 0;
			$size = 0;
			foreach($infos as $info)
			{
				$i++;
				if($i == 1)	
					continue;
				$s = explode(" ", $info);
				$size += end($s);

				
			}
			$arr[] = array(
				"title" => $infos[0],
				"infohash" => $row->hash,
				"id" => $row->id,
				"info" =>  $row->info,
				"size" => $size,

			);
		}
		$totalPages =  intval($res["total_found"] / 20);
		if($totalPages > 50)
		{
			$totalPages = 50;
		}
		$data = array(
			'total_page' =>  $totalPages,
			'time' => $res["time"],
			'datas' => $arr,
			'total_found' => $res['total_found'],
			'key' => $key,
		);
		/*
		echo "<pre>";
		var_dump($arr);
		echo "</pre>";
		 */
		return $data;
		


	}
	function sphinx_query($query_str, $index, $page = 1)
	{
		$index = "*";
		$limit = 20 * $page;
		$this->cl->SetLimits ( $limit - 20, 20, ( $limit>1000 ) ? $limit : 1000 );
		$res = $this->cl->Query ( $query_str, $index );
		if($res['total'] == 0)
			return NULL;
		if($res['error'])
			return NULL;
		if($res['status'] != 0)
			return NULL;
		return $res;
	}
	function detail_data($id)
	{
		
		$sql = "select * from hash_info where id=" . $id ;
		//echo $sql;
		$q = $this->db->query($sql);
		$arr = $q->result();
		if(count($arr) <= 0)
		{
			return NULL;
		}
		$infoall = $arr[0]->info;
		$hash = $arr[0]->hash;
		$infos = explode("\n", $infoall);
		$i = 0;
		$total_size = 0;
		$ret = array();
		$title = "";
		foreach($infos as $info)
		{
			if(strlen($info) == 0)
				break;
			$i++;
			if($i == 1)	
			{
				$title = $info;
				continue;
			}
			$s = explode(" ", $info);
			$size = end($s);
			$total_size += $size;
			$content = join(" ", array_slice($s, 0, -1));
			$ret[] = array(
				"file" => $content,
				"size" => $size,
			);
		}
		$data["summary"] =  array(
			"size" => $total_size,
			"filenum" => $i-1,
			"indexdate" => 20141021,
			"hash" => $hash,
			"magnet" => "magnet:?xt=urn:btih:$hash&xl=$total_size&dn=$title",
		);
		$data["files"] = $ret;
		return $data;
	}
	function init_sphinx()
	{
		$this->cl = new SphinxClient ();
		$sql = "";
		$mode = SPH_MATCH_ALL;
		$host = "localhost";
		$port = 9312;
		$index = "*";
		$groupby = "";
		$groupsort = "@group desc";
		$filter = "group_id";
		$filtervals = array();
		$distinct = "";
		$sortby = "";
		$sortexpr = "";
		$limit = 20;
		$ranker = SPH_RANK_PROXIMITY_BM25;
		$select = "";


		$this->cl->SetServer ( $host, $port );
		$this->cl->SetConnectTimeout ( 1 );
		$this->cl->SetArrayResult ( true );
		$this->cl->SetWeights ( array ( 100, 1 ) );
		$this->cl->SetMatchMode ( $mode );
		if ( count($filtervals) )	$this->cl->SetFilter ( $filter, $filtervals );
		if ( $groupby )				$this->cl->SetGroupBy ( $groupby, SPH_GROUPBY_ATTR, $groupsort );
		if ( $sortby )				$this->cl->SetSortMode ( SPH_SORT_EXTENDED, $sortby );
		if ( $sortexpr )			$this->cl->SetSortMode ( SPH_SORT_EXPR, $sortexpr );
		if ( $distinct )			$this->cl->SetGroupDistinct ( $distinct );
		if ( $select )				$this->cl->SetSelect ( $select );
		if ( $limit )				$this->cl->SetLimits ( 0, $limit, ( $limit>1000 ) ? $limit : 1000 );
		$this->cl->SetRankingMode ( $ranker );



	}

}
?>
