<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class C_Login extends CI_Controller {
	protected $background;
	protected $admin_config;

	public function __construct() {
		parent :: __construct();

		$this -> load -> library('session');

		$this -> background = 'background';

		$this -> load -> config('my_admin_config', true, true);
		$this -> admin_config = $this -> config -> item('my_admin_config');

		$this -> load -> model('background/m_login', 'mlogin');
		$this -> load -> model('background/m_captcha', 'mcaptcha'); 		
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
//		$this -> load -> view($this -> background . '/templates/v_header', $data);
		$this -> load -> view($this -> background . '/' . $template, $data);
//		$this -> load -> view($this -> background . '/templates/v_footer');
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
	 * 登录首页
	 */
	public function process_index() {
		// 检查登录情况
		$this -> mlogin -> login_page_check_login();

		$config = $this -> admin_config;

		$sign = $this -> input -> post('sign');
		switch ($sign) {
			// 创建captcha
			case 'create_captcha':
				$cap = $this -> mcaptcha -> create_captcha($this -> input -> ip_address());
				echo $cap['image'];
				exit;
				break; 
			// 登录验证
			case 'check_login': 
				// $result 为1则登录成功，2则captcha错误，3则用户名或密码错误
				$result = 0; 
				// 检查captcha是否存在
				$captcha_exists = $this -> mcaptcha -> check_captcha_exists($this -> input -> post('captcha'), $this -> input -> ip_address());
				if (!$captcha_exists) {
					$result = 2;
				}else {
					// 验证用户名密码是否正确
					$user = $this -> input -> post('user');
					$pwd = $this -> input -> post('pwd');
					if ($user != $config['user'] || $pwd != $config['pwd']) {
						$result = 3;
					}else {
						$result = 1; 
						// 设置session
						$this -> session -> set_userdata('user_name', $config['user']);
					}
				}
				$echo = array('result' => $result, 'success_url' => base_url('background/c_index/index/'));
				$this -> ajax_echo($echo);
				break;
			default:
				break;
		} 
		// 创建captcha
		$cap = $this -> mcaptcha -> create_captcha($this -> input -> ip_address());

		$datas = array('captcha_image' => $cap['image']);
		$this -> _output_view('login', $datas);
	}

	/**
	 * 登出
	 */
	public function process_login_out() {
		$this -> mlogin -> login_out();
	}

}

/**
 * End of file c_login.php
 */
/**
 * Location: ./application/controllers/background/c_login.php
 */