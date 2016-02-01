<?php
	class Pages extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();
			 date_default_timezone_set('Etc/GMT-8');
		}
		public function view($page = "")
		{

			 $data['title'] = ucfirst("wwwwwwwww");

			$this->load->model('mdata');
			$ret = $this->mdata->get_torrent_cnt();
			
			if($ret)
			{
				$data['total'] = $ret["total"];
				$data['yesterday'] = $ret['yesterday'];
			}
			else
			{
				$data['total'] = 17000000;
				$data['yesterday'] = 10278;
			}
			$data['hotwords'] = $this->mdata->get_hot_words(1);
			 $this->load->view('base_page', $data);
			 $this->load->view('footer', $data);

		}
		public function search($key = "")
		{
			/*
			$data["title"] = "";
			$data['datas'] = array(
				array(
					'title' => "this is the title",
					'infohash' => "12341241241241241241"
					'size' => 0,
				),
				array(
					'title' => "this is the title",
					'infohash' => "asdfasdfadfadfadfa"
					'size' => 0,
				),
				array(
					'title' => "this is the title",
					'infohash' => "womenshi zong guo reng"
					'size' => 0,
				),
				array(
					'title' => "this is the title",
					'infohash' => "hahahahahahhhahhah29370238"
					'size' => 0,
				),
			);
			 */

			$page = $this->input->get("page");
			if($page == NULL)
			{
				$page = 1;
			}
			if(!$key)
			{
				$key = $this->input->get("keyword");
			}
			$this->load->model('mdata');
			$data = $this->mdata->search($key, $page);
/*			echo "<pre>";
			var_dump($data["datas"]);
			echo "</pre>";
 */
			if($data != NULL)
			{
				$data['hotwords'] = $this->mdata->get_hot_words(2);
				$data['title'] = $key;
				$data['page'] = $page;
				$this->load->helper('view_helper');
				$this->load->view('list_view', $data);
				$this->load->view('footer', $data);
			}
			else
			{
				$data = array(
					'key' => $key,
					'title'=>$key,
					'datas'=>array(),
				);
				$data['hotwords'] = $this->mdata->get_hot_words(2);
				$this->load->view('list_view', $data);
			}
		}
	
		public function detail($id= "")
		{
			/*
			$data["title"] = "This is the title";
			$data["summary"] =  array(
				"size" => "12GB",
				"filenum" => 4,
				"indexdate" => 20041021,
				"hash" => "12123801283102893012983",
				"magnet" => "magnet:?xt=urn:btih:adfadfadfadfadfadfadfad&xl=1231l23123123123123123&dn=adfasdfadfadfadfadfadfadfadf",
			);
			$data["files"] = array(
				array(
					"file" => "file1",
					"size" => "100M"
				),	
				array(
					"file" => "file2",
					"size" => "200M"
				),	
				array(
					"file" => "file3",
					"size" => "300M"
				),	
			);
			 */

			$this->load->model('mdata');
			$data = $this->mdata->detail_data($id);
			if($data != NULL)
			{
				$data["title"] = $data['summary']['title'];
				$data['hotwords'] = $this->mdata->get_hot_words(3);
				$this->load->helper('view_helper');
				$this->load->view("detail_view", $data);
				$this->load->view('footer', $data);
			}
			else
			{
				$this->load->view('error_view', $data);
			}
		}
		public function test()
		{
			$this->load->model('mdata');
			$this->mdata->test();
		}
	}

