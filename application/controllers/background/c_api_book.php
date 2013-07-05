<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class C_Api_book extends CI_Controller {
	protected $background;

	public function __construct() {
		parent :: __construct();
		$this -> load -> model('background/m_book', 'mbook');

		//检查登录情况
		$this -> load -> model('background/m_login', 'mlogin');
		$this->mlogin->other_page_check_login();
	}

	/**
	 * 把实际的action重新路由到对应的方法,严格控制用户可访问的action
	 */
	public function _remap($method, $params = array()) {
		$method = 'process_'.str_replace('-', '_', $method);
		if (method_exists($this, $method)) {
			return call_user_func_array(array($this, $method), $params);
		}
		show_404();
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
	 * 导入文章
	 */
	public function process_import_article() {
		//header('Content-Type: multipart/form-data');
		$posts = $this->input->post();
		foreach($posts as $item)
		{
			$result = $this->mbook->add_an_article($item);
		}
		$this -> ajax_echo($result);
	}

	public function process_get_article()
	{
		print_r($this->mbook->get_article_table_fields());
		exit;
	}

}

/**
 * End of file c_api_book.php
 */
/**
 * Location: ./application/controllers/background/c_api_book.php
 */