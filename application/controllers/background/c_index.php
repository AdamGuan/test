<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class C_Index extends CI_Controller {
	protected $background;
	protected $admin_config;

	public function __construct() {
		parent :: __construct();
		/**
		 * $this->load->model('foreground/member/m_login','mlogin');
		 * $this->load->helper(array('form', 'url'));
		 * $this->load->library('form_validation');
		 * $this->load->config('my_foreground_login',true,true);
		 * $this->login_config = $this->config->item('my_foreground_login');
		 */

		$this -> background = 'background';

		$this -> load -> config('my_admin_config', true, true);
		$this -> admin_config = $this -> config -> item('my_admin_config');

		$this -> load -> model('background/m_book', 'mbook');

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
	 * 后台首页
	 */
	public function process_index() {
		$config = $this -> admin_config['admin_tab']; 
		// 左侧数据的数组
		$left_menu = array();
		foreach($config as $item) {
			$tab_title = $item['title'];
			$tab_list = $item['list'];
			$tmp = array();
			foreach($tab_list as $it) {
				$tmp[] = array('id' => $it['id'],
					'text' => $it['text'],
					'iconCls' => 'icon-blank',
					'checked' => true,
					'attributes' => array("url" => base_url($it['url']))
				);
			}
			$left_menu[] = array('title' => $tab_title, 'list' => json_encode($tmp));
		}

		$datas = array('left_menu' => $left_menu, 'default_page' => base_url('background/c_index/welcome'));
		$this -> _output_view('index', $datas);
	}

	/**
	 * 欢迎页面
	 */
	public function process_welcome() {
		echo 'welcome!';
		exit;
	}

	/**
	 * 小说管理页
	 */
	public function process_book_manage() {
		$datas = array();
		$assign = $this -> input -> post('assign');
		//ajax 
		switch ($assign) {
			//修改
			case 'do_modify':
				$posts = $this->input->post();
				$posts['book_createTime'] = strtotime($posts['book_createTime']);
				$result = $this -> mbook->modify_a_book($posts);
				$this -> ajax_echo($result);
				break;
			//打开修改窗口
			case 'open_modify':
				//获取一本书的信息
				$book_id = $this->input->post('book_id');
				$info = $this -> mbook -> get_a_book_info($book_id);
				//print_r($info);exit;
				$this -> ajax_echo($info);
				break;
			// 获取小说列表
			case 'get_book_list':
				$book_list = $this -> mbook -> get_book_list();
				if (count($book_list) > 0) {
					foreach($book_list as $key => $item) {
						$book_list[$key]['book_createTime'] = date('Y-m-d', $item['book_createTime']);
					}
				}
				$rows['rows'] = $book_list;
				$rows['total'] = count($book_list);
				$this -> ajax_echo($rows);
				break;
			//添加一本小说
			case 'do_add':
				$posts = $this->input->post();
				$posts['book_createTime'] = time();
				$result = $this -> mbook -> add_a_book($posts);
				$this -> ajax_echo($result);
				break;
			//删除小说
			case 'do_delete':
				$result = 0;
				$book_ids = $this->input->post('book_ids');						
				if($book_ids !== FALSE)
				{
					$book_ids = explode(',',$book_ids);	
					$result = $this -> mbook -> delete_books($book_ids);
				}
				$this -> ajax_echo($result);
				break;
			default:
				break;
		} 
		
		$this -> _output_view('book_manage', $datas);
	}

	/**
	 * 章节管理页
	 */
	public function process_article_manage() {
		$datas = array();
		$assign = $this -> input -> post('assign');
		$book_id = $this->input->post('book_id');
		$book_id = ($book_id !== false)?$book_id:0;
		$article_status = $this->input->post('article_status');
		$article_status = ($article_status !== false)?$article_status:-1;		

		//ajax 
		switch ($assign) {
			//获取文章列表
			case 'get_article_list':
				$posts = $this->input->post();
				//设置limit,offset
				$page = ($posts['page'] !== false)?(int)$posts['page']:1;
				$rows = ($posts['rows'] !== false)?(int)$posts['rows']:20;
				unset($posts['page']);
				unset($posts['rows']);
				$posts['limit'] = $rows;
				$posts['offset'] = ($page -1 )*$rows;
				//获取列表数据
				$list_result = $this -> mbook->get_article_list($posts);
				if(is_array($list_result) && count($list_result) > 0)
				{
					foreach($list_result as $key=>$item)
					{
						$list_result[$key]['article_createTime'] = date('Y-m-d H:i:s',$item['article_createTime']);
						//判断章节的缓存
						$cache_exists = $this->mbook->check_article_cache_exists($item['article_id']);
						$list_result[$key]['cache_exists'] = $cache_exists;
					}
				}
				//获取总数
				$parames = array('book_belong'=>$posts['book_belong']);
				if(isset($posts['status']))
				{
					$parames['status'] = $posts['status'];
				}
				$total_result = $this -> mbook->get_one_book_article_total($parames);

				$result = array('rows'=>$list_result,'total'=>$total_result);
				$this -> ajax_echo($result);
				break;
			//获取一章节信息
			case 'get_an_article_info':
				//获取一章节信息
				$article_id = $this->input->post('article_id');
				$article_result = $this -> mbook->get_an_article_info($article_id);
				//获取全部小说列表
				$all_book_list = $this -> mbook->get_book_list();
				//设置章节所属小说的下拉数据
				$book_select_data = array();

				foreach($all_book_list as $key=>$item)
				{
					$tmp = array('text'=>$item['book_name'],'id'=>$item['book_id']);
					if($article_result['book_belong'] == $item['book_id'])
					{
						$tmp['selected'] = true;
					}
					$book_select_data[] = $tmp;
				}

				//格式化输出信息
				$article_result['article_createTime'] = date('Y-m-d H:i:s',$article_result['article_createTime']);
				$article_result['book_select_data'] = $book_select_data;

				$this -> ajax_echo($article_result);
				break;
			//修改一文章信息
			case 'modify_an_article':
				$posts = $this->input->post();
				$posts['article_content'] = unescape($posts['article_content']);
				$posts['article_createTime'] = strtotime($posts['article_createTime']);
				$result = $this -> mbook->modify_an_article($posts);
				$this -> ajax_echo($result);
				break;
			//修改一文章的状态
			case 'modify_an_article_status':
				$posts = array('status'=>$this->input->post('status'),'article_id'=>$this->input->post('article_id'));
				$result = $this -> mbook->modify_an_article($posts);
				$this -> ajax_echo($result);
				break;
			//添加一文章
			case 'add_an_article':
				$posts = $this->input->post();
				$posts['article_createTime'] = strtotime($posts['article_createTime']);
				$result = $this->mbook->add_an_article($posts);
				$this -> ajax_echo($result);
				break;
			//删除文章
			case 'del_article':
				$article_ids = explode(',',$this->input->post('article_ids'));
				$result = $this->mbook->del_article($article_ids);
				$this -> ajax_echo($result);
				break;
			default:
				break;
		} 

		//设置小说选择下拉列表所用数据 $book_select_data
		$book_select_data = array();
		$all_book_list = $this -> mbook -> get_book_list();
		$config_book_default = $this -> admin_config['book_default']; 
		$all_book_list[] = $config_book_default;
		if(is_array($all_book_list) && count($all_book_list) > 0)
		{
			foreach($all_book_list as $book_item)
			{
				$tmp = array('id'=>$book_item['book_id'],'text'=>$book_item['book_name']);
				if($book_item['book_id'] == $book_id)
				{
					$tmp['selected'] = true;
				}
				$book_select_data[] = $tmp;
			}
		}
		$book_select_data = json_encode($book_select_data);
		//设置章节状态下拉列表所用数据 $article_status_select_data
		$article_status_select_data = array();
		$config_article_status = $this -> admin_config['article_status']; 
		if(is_array($config_article_status) && count($config_article_status) > 0)
		{
			foreach($config_article_status as $key=>$item)
			{
				$tmp = array('id'=>$key,'text'=>$item);
				if($article_status == $key)
				{
					$tmp['selected'] = true;
				}
				$article_status_select_data[] = $tmp;
			}
		}
		$article_status_select_data = json_encode($article_status_select_data);

		//ouput data准备
		$datas['book_select_data'] = str_replace('"','\'',$book_select_data);
		$datas['article_status_select_data'] = str_replace('"','\'',$article_status_select_data);

		$this -> _output_view('article_manage', $datas);
	}

}

/**
 * End of file c_index.php
 */
/**
 * Location: ./application/controllers/background/c_index.php
 */