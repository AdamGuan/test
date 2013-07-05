<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class C_Crawler extends CI_Controller {
	protected $background;
	protected $admin_config;

	public function __construct() {
		parent :: __construct();

		$this -> background = 'background';

		$this -> load -> config('my_admin_config', true, true);
		$this -> admin_config = $this -> config -> item('my_admin_config');

		$this -> load -> model('background/m_book', 'mbook');
		$this -> load -> model('background/m_crawler', 'mcrawler');

		//检查登录情况
		$this -> load -> model('background/m_login', 'mlogin');
		$this->mlogin->other_page_check_login();
	}

	/**
	 * 把实际的action重新路由到对应的方法,严格控制用户可访问的action
	 */
	public function _remap($method, $params = array()) {
		$method = 'process_' . str_replace('-', '_', $method);
		if (method_exists($this, $method)) {
			return call_user_func_array(array($this, $method), $params);
		}
		show_404();
	}

	/**
	 * 输出数据到视图（组合了头部，脚部公共部分的视图）.
	 * 
	 * @parame $template 模板名称 string
	 * @parame $data		数据 array
	 */
	private function _output_view($template, $data = array()) {
		$this -> load -> view($this -> background . '/templates/v_header', $data);
		$this -> load -> view($this -> background . '/' . $template);
		$this -> load -> view($this -> background . '/templates/v_footer');
	}

	/**
	 * ajax输出数据
	 * 
	 * @parame $datas	mix
	 */
	private function ajax_echo($datas) {
		echo json_encode($datas);
		exit;
	}

	/**
	 * 小说采集配置
	 */
	public function process_book_crawler_config() {
		$crawler_config_path = $this -> admin_config['crawler_book_config_path']; 
		// ajax处理
		$sign = $this -> input -> post('sign');
		switch ($sign) {
			// 配置对应小说的采集配置
			case 'doconfig':
				$posts = $this -> input -> post();
				$result = $this -> mcrawler -> config_book($posts);
				$this -> ajax_echo($result);
				break; 
			// 获取一本小说的配置信息
			case 'get_an_book_info':
				$book_id = $this->input->post('book_id');
				$book_name = $this->input->post('book_name');
				$plan_id = $this->input->post('plan_id');

				//获取当前的采集方案 字段
				$all_crawler_plan = $this -> admin_config['crawler_plan']; 
				foreach($all_crawler_plan as $crawler_plan)
				{
					if($crawler_plan['id'] == $plan_id)
					{
						$current_plan_fields = $crawler_plan['fields'];
						$current_plan_name = $crawler_plan['text'];
						break;
					}
				}
				
				//获取当前小说的采集配置
				$result = $this->mcrawler->get_book_config($book_id,$plan_id);

				//field生成table
				if(isset($current_plan_fields) && is_array($current_plan_fields))
				{
					$table = '<table class="tab_data" id="tab_data">';
					$table .= '<tr><td>采集方案：</td><td>'.$current_plan_name.'</td></tr>';
					$table .= '<tr><td>书名：</td><td>'.$book_name.'</td></tr>';
					foreach($current_plan_fields as $field)
					{
						$table .= '<tr><td>'.$field['title'].'：</td><td><input name="'.$field['field'].'" type="text" value="'.(isset($result[$field['field']])?$result[$field['field']]:'').'" /></td></tr>';
					}
					$table .= '</table>';
				}
				
				$echo = array('table'=>$table);
				$this -> ajax_echo($echo);
				break;
			//获取小说配置列表
			case 'get_book_config_list':
				// 获取所有的小说列表
				$book_list = $this -> mbook -> get_book_list();
				//获取采集方案ID
				$plan_id = $this->input->post('plan_id');
				//获取当前的采集方案 字段
				$all_crawler_plan = $this -> admin_config['crawler_plan']; 
				foreach($all_crawler_plan as $crawler_plan)
				{
					if($crawler_plan['id'] == $plan_id)
					{
						$current_plan_fields = $crawler_plan['fields'];
						break;
					}
				}
				if(isset($current_plan_fields))
				{
					array_unshift($current_plan_fields,array('field'=>'book_name','title'=>'小说名称'));
					$current_plan_fields[] = array('field'=>'actions','title'=>'操作');
					$current_plan_fields = array($current_plan_fields);
				}
				//小说列表结合对应的配置信息
				if (is_array($book_list) && count($book_list) > 0 && isset($crawler_config_path)) {
					foreach($book_list as $key => $book) {
						// 获取小说采集的配置信息
						$config = $this -> mcrawler -> get_book_config($book['book_id'],$plan_id);
						if (is_array($config) && count($config) > 0) {
							$book_list[$key] = array_merge($book_list[$key],$config);
						}
						$book_list[$key]['actions'] = '<a class="actions" href="javascript:void(0);window.j_module_page.open_modify_win(\''.$book['book_id'].'\',\''.$book['book_name'].'\')">修改</a>';
					}
				}

				$echo = array('book_list'=>$book_list,'fields'=>$current_plan_fields);
				$this -> ajax_echo($echo);
			default:
				break;
		} 

		//获取采集方案
		$crawler_plan_list = $this->mcrawler->get_crawler_plan();
		foreach($crawler_plan_list as $key=>$item)
		{
			unset($crawler_plan_list[$key]['fields']);
			unset($crawler_plan_list[$key]['crawler_method']);
			unset($crawler_plan_list[$key]['crawler_fail_method']);
		}
		$crawler_plan_list = str_replace('"','\'',json_encode($crawler_plan_list));
		// output
		$datas = array();
		$datas['crawler_plan_json'] = $crawler_plan_list;
		$this -> _output_view('book_crawler_config', $datas);
	}

	public function process_book_crawler() {

		$sign = $this->input->post('sign');
		switch($sign)
		{
			//采集整本书
			case 'crawler_whole_book':
				$book_id = $this->input->post('book_id');
				$crawler_plan_id = $this->input->post('crawler_plan_id');

				$all_plan_list = $this->mcrawler->get_crawler_plan(0);
				foreach($all_plan_list as $plan)
				{
					if($crawler_plan_id == $plan['id'])
					{
						$crawler_method = $plan['crawler_method'];
						break;
					}
				}
				if(isset($crawler_method))
				{
					$this->mcrawler->$crawler_method($book_id, $crawler_plan_id);
				}
				$this -> ajax_echo(1);
				break;
			//重新采集失败的整本书
			case 'crawler_failed':
				$book_id = $this->input->post('book_id');
				$crawler_plan_id = $this->input->post('crawler_plan_id');

				$all_plan_list = $this->mcrawler->get_crawler_plan(0);
				foreach($all_plan_list as $plan)
				{
					if($crawler_plan_id == $plan['id'])
					{
						$crawler_method = $plan['crawler_fail_method'];
						break;
					}
				}
				$this->mcrawler->$crawler_method($book_id, $crawler_plan_id);
				$this -> ajax_echo(1);
				break;
			//获取一本书的采集进度
			case 'get_book_crawler_process':
				$book_id = $this->input->post('book_id');
				$process = $this->mcrawler->get_book_crawler_whole_process($book_id);
				$this -> ajax_echo($process);
				break;
			//获取失败重新采集的采集进度
			case 'get_fail_book_crawler_process':
				$book_id = $this->input->post('book_id');
				$process = $this->mcrawler->get_book_crawler_fail_process($book_id);
				$this -> ajax_echo($process);
				break;
			//采集成功总数
			case 'get_crawler_success_total':
				$book_id = $this->input->post('book_id');
				$total = $this->mcrawler->get_book_crawler_success_total($book_id);
				$this -> ajax_echo($process);
				break;
			//采集失败总数
			case 'get_crawler_fail_total':
				$book_id = $this->input->post('book_id');
				$total = $this->mcrawler->get_book_crawler_fail_total($book_id);
				$this -> ajax_echo($process);
				break;
			//获取采集方案
			case 'get_crawler_plan':
				$paln_list = $this->mcrawler->get_crawler_plan(0);
				foreach($paln_list as $key=>$plan)
				{
					unset($paln_list[$key]['fields']);
					unset($paln_list[$key]['crawler_method']);
					unset($paln_list[$key]['crawler_fail_method']);
				}
				$this -> ajax_echo($paln_list);
				break;
			//获取list
			case 'get_list':
				//获取book list
				$book_list = $this -> mbook -> get_book_list();
				//获取采集进度
				if(is_array($book_list) && count($book_list) > 0)
				{
					foreach($book_list as $key=>$item)
					{
						$book_list[$key]['whole_process'] = $this->mcrawler->get_book_crawler_whole_process($item['book_id']);
						$book_list[$key]['fail_process'] = $this->mcrawler->get_book_crawler_fail_process($item['book_id']);
						$book_list[$key]['success_total'] = $this->mcrawler->get_book_crawler_success_total($item['book_id']);
						$book_list[$key]['fail_total'] = $this->mcrawler->get_book_crawler_fail_total($item['book_id']);
					}
				}
				$echo = array('rows'=>$book_list);
				$this -> ajax_echo($echo);
				break;
			//导入到数据库
			case 'import':
				$base_time = $this->input->post('base_time');
				$book_id = $this->input->post('book_id');
				//获取小说的host
				$book_info = $this->mbook->get_a_book_info($book_id);
				$book_host = $book_info['web_site_uri'];
				$this->mcrawler->import_crawler_book_to_db($book_id, $book_host, $base_time);
				$this -> ajax_echo(1);
				break;
			default:
				break;
		}
		//获取采集方案
		$paln_list = $this->mcrawler->get_crawler_plan(0);
		foreach($paln_list as $key=>$plan)
		{
			unset($paln_list[$key]['fields']);
			unset($paln_list[$key]['crawler_method']);
			unset($paln_list[$key]['crawler_fail_method']);
		}
		// output
		$datas = array('paln_list'=>json_encode($paln_list));
		$this -> _output_view('book_crawler', $datas);
	}

}

/**
 * End of file c_crawler.php
 */
/**
 * Location: ./application/controllers/background/c_crawler.php
 */