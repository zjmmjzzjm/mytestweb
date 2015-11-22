<?php
	class Pages extends CI_Controller
	{
		public function view($page = "")
		{

			 $data['title'] = ucfirst("wwwwwwwww");

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
			$this->load->model('mdata');
			$data = $this->mdata->search($key, $page);
			$data['title'] = $key;
			$data['page'] = $page;
/*			echo "<pre>";
			var_dump($data["datas"]);
			echo "</pre>";
 */
			 $this->load->view('list_view', $data);
			 $this->load->view('footer', $data);
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
			$data["title"] = "btmilk";
			$this->load->view("detail_view", $data);
			$this->load->view('footer', $data);
		}
		public function test()
		{
			$this->load->model('mdata');
			$this->mdata->test();
		}
	}
