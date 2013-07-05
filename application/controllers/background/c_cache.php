<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class C_Cache extends CI_Controller {
	protected $background;
	protected $admin_config;

	public function __construct() {
		parent :: __construct();

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
	 * 整本小说的缓存
	 */
	public function process_book_cache() {
		$sign = $this -> input -> post('sign'); 
		// ajax 处理
		switch ($sign) {
			// 获取每本书生成缓存的进度
			case 'query_cache_process':
				$book_id = $this -> input -> post('book_id');
				$book_cache_process_path = $this -> admin_config['book_cache_process_path'];
				$file = $book_cache_process_path . $book_id . '.txt';
				if (is_file($file) && file_exists($file)) {
					$process = file_get_contents($book_cache_process_path . $book_id . '.txt');
				}
				if (isset($process) && $process !== false) {
					list($a, $b) = explode('/', $process);
					$process = (int)($a * 100 / $b) . '%';
				}else {
					$process = '无';
				}
				$this -> ajax_echo($process);
				break; 
			// 生成整本小说的缓存
			case 'create_book_cache':
				$book_id = $this -> input -> post('book_id');
				$cache_path = $this -> admin_config['cache_path']; 
				// 获取小说的信息
				$result = $this -> mbook -> get_a_book_info($book_id);
				if (is_array($result) && count($result) > 0) {
					$host = $result['web_site_uri'];
					$path = $cache_path . $host . '/';
					if (!file_exists($path)) {
						mkdir($path, 0764);
						chmod($path, 0764);
					}
					// 获取小说的所有有效章节
					$article_list = $this -> mbook -> get_article_list(array('book_belong' => $book_id, 'status' => 1));
					if (is_array($article_list)) {
						$article_total = count($article_list);
					}
					if (isset($article_total) && $article_total > 0) {
						// 设置 记录小说生成缓存进度的文件$log_process_file
						$book_cache_process_path = $this -> admin_config['book_cache_process_path'];
						$log_process_file = $book_cache_process_path . $book_id . '.txt'; 
						// 循环生成小说的章节缓存，并记录进度
						$i = 1;
						foreach($article_list as $item) {
							$this->mbook->create_an_article_cache($item['article_id']);
							// log cache process
							$log_content = $i . '/' . $article_total;
							file_put_contents($log_process_file, $log_content);
							++$i;
						}
					}
					//创建小说的目录缓存文件
					$this->mbook->create_book_directory_cache($book_id);
				}
				$this -> ajax_echo(1);
				break;
			//生成小说的目录缓存
			case 'create_book_category_cache':
				$book_id = $this->input->post('book_id');
				$this->mbook->create_book_directory_cache($book_id);
				$this -> ajax_echo(1);
				break;
			default:
				break;
		} 
		// 获取book list
		$book_list = $this -> mbook -> get_book_list();
		if (is_array($book_list) && count($book_list) > 0) {
			foreach($book_list as $key => $item) {
				$book_list[$key]['book_createTime'] = date('Y-m-d H:i:s', $item['book_createTime']);
			}
		}
		$book_list = array_values($book_list);

		$datas['book_total'] = count($book_list);
		$datas['book_list'] = str_replace('"', '\'', json_encode($book_list));
		$this -> _output_view('book_cache', $datas);
	}


	/**
	 * 小说章节的缓存
	 */
	public function process_article_cache() {
		$sign = $this -> input -> post('sign'); 
		// ajax 处理
		switch ($sign) {
			// 生成章节的缓存
			case 'create_article_cache':
				$article_id = $this -> input -> post('article_id');				
				$this->mbook->create_an_article_cache($article_id);
				//判断缓存是否存在
				$result = $this->mbook->check_article_cache_exists($article_id);
				$this -> ajax_echo($result);
				break;
			//判断某个章节的缓存是否存在
			case 'check_article_cache':
				$article_id = $this -> input -> post('article_id');
				$result = $this->mbook->check_article_cache_exists($article_id);
				$this -> ajax_echo($result);
				break;
			default:
				break;
		} 
	}
}

/**
 * End of file c_cache.php
 */
/**
 * Location: ./application/controllers/background/c_cache.php
 */