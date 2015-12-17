<?php
require ( "sphinxapi.php" );

class Mdata extends CI_Model
{
	private $cl;//sphinx client
	private $keyword;

	function Mdata()
	{
		parent::__construct();
		$this->load->library('encryption');
		$this->load->database();
		$this->init_sphinx();
		$this->encryption->initialize(
			array(
				'cipher' => 'rc4',
				'mode' => 'stream',
				'hmac' => False,
				'key' => 'V1243509786',
			)
		);
	}
	function search($key, $page)
	{
		$key = urldecode($key);
		$this->keyword = $key;
		$res = $this->sphinx_query($key, "*", $page);
		if($res == NULL)
			return NULL;
		$where = array();

		foreach($res["matches"] as $match)
		{
			$where[] = $match['id'];
		}

		//$sql = "select * from hash_info where " . $where;
		//echo $sql;
		$this->db->where_in('id', $where);
		$q = $this->db->get('hash_info');
		$arr = array();
		$this->load->helper("date");
		foreach($q->result() as $row)
		{
			$infos = explode("\n", $row->info);
			$i = 0;
			$size = $this->make_size($row->size);
			foreach($infos as $info)
			{
				$i++;
				if($i == 1)	
					continue;
				$s = explode(" ", $info);
				
			}
			$t = $row->time;
			$arr[] = array(
				"title" => $infos[0],
				"infohash" => $row->hash,
				"id" => urlencode($this->encryption->encrypt($row->id)),
				"info" =>  $row->info,
				"size" => $size,
				"indexdate" => datetime("Y-m-d", $t),

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
		
		$real_id = $this->encryption->decrypt(urldecode($id));
		if( !$real_id )
			return NULL;
		//$sql = "select * from hash_info where id=" . $real_id) ;
		//echo $sql;
		//$q = $this->db->query($sql);

		$q = $this->db->get_where('hash_info', array('id'=>$real_id), 1);
		$arr = $q->result();
		if(count($arr) <= 0)
		{
			return NULL;
		}
		$infoall = $arr[0]->info;
		$hash = $arr[0]->hash;
		$infos = explode("\n", $infoall);
		$i = 0;
		$total_size = $arr[0]->size;
		$t = $arr[0]->time;
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
			$size = $this->make_size(end($s));
			$content = join(" ", array_slice($s, 0, -1));
			$ret[] = array(
				"file" => $content,
				"size" => $size,
			);
		}
		$this->load->helper("date");

		$data["summary"] =  array(
			"title" => $title,
			"size" => $total_size,
			"filenum" => $i-1,
			"indexdate" => datetime("Y-m-d", $t),
			"hash" => $hash,
			"magnet" => "magnet:?xt=urn:btih:$hash&xl=$total_size&dn=$title",
		);
		$data["files"] = $ret;
		return $data;
	}
	function make_size($size)
	{
		if(is_numeric($size))
		{
			$this->load->helper("misc_helper");
			$unit = get_auto_unit($size);
			$size = get_num_by_unit($size, $unit).$unit;

		}
		return $size;
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
	function do_statistics()
	{
		$tbl = $this->get_statistic_table();
		$sql = "CREATE TABLE IF NOT EXIST  `$tbl`
			(
				`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`time` int UNSIGNED NOT NULL,
				`ip`  char(20) NOT NULL ,
				`keyword` char(40) NOT NULL ,
				`session`  char(80) NOT NULL,
                PRIMARY KEY (`id`) 
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
		$data = array(
			'time' => time(),
			'ip' =>  $this->input->ip_adress(),
			'keyword' => $this->keyword,
			'session' => '0000',
		);
		$this->db->insert($tbl, $data);

	}

	function get_statistic_table()
	{
		$date  = date('Ymd');
		echo $date;
		return "statistic_$date";
	}

}
?>
