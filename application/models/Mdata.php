<?php
require ( "sphinxapi.php" );
require ("XDecode.php");

class Mdata extends CI_Model
{
	private $cl;//sphinx client
	private $keyword;
	private $coder;

	function Mdata()
	{
		parent::__construct();
		$this->load->database();
		$this->init_sphinx();
		$this->key = 345678.1;
		$this->coder = new XDecode(16, $this->key);
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
				"id" => $this->coder->encode($row->id),
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
		
		$real_id = $this->coder->decode($id);
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


function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {   
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙   
    $ckey_length = 4;   
       
    // 密匙   
    $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);   
       
    // 密匙a会参与加解密   
    $keya = md5(substr($key, 0, 16));   
    // 密匙b会用来做数据完整性验证   
    $keyb = md5(substr($key, 16, 16));   
    // 密匙c用于变化生成的密文   
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): 
substr(md5(microtime()), -$ckey_length)) : '';   
    // 参与运算的密匙   
    $cryptkey = $keya.md5($keya.$keyc);   
    $key_length = strlen($cryptkey);   
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)， 
//解密时会通过这个密匙验证数据完整性   
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确   
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :  
sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;   
    $string_length = strlen($string);   
    $result = '';   
    $box = range(0, 255);   
    $rndkey = array();   
    // 产生密匙簿   
    for($i = 0; $i <= 255; $i++) {   
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);   
    }   
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度   
    for($j = $i = 0; $i < 256; $i++) {   
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;   
        $tmp = $box[$i];   
        $box[$i] = $box[$j];   
        $box[$j] = $tmp;   
    }   
    // 核心加解密部分   
    for($a = $j = $i = 0; $i < $string_length; $i++) {   
        $a = ($a + 1) % 256;   
        $j = ($j + $box[$a]) % 256;   
        $tmp = $box[$a];   
        $box[$a] = $box[$j];   
        $box[$j] = $tmp;   
        // 从密匙簿得出密匙进行异或，再转成字符   
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));   
    }   
    if($operation == 'DECODE') {  
        // 验证数据有效性，请看未加密明文的格式   
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && 
substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {   
            return substr($result, 26);   
        } else {   
            return '';   
        }   
    } else {   
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因   
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码   
        return $keyc.str_replace('=', '', base64_encode($result));   
    }   
} 

}
?>
