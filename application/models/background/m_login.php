<?php
class M_login extends CI_Model {
	public function __construct() {
		parent :: __construct();

		$this -> load -> library('session'); 
		// $this -> load -> database();
		$this -> admin_config = $this -> config -> item('my_admin_config');
	}

	/**
	 * 记录生成的captcha记录.
	 * 
	 * @parame $data	array
	 * 		captcha_time	int		生成时间戳.
	 * 		word			string	
	 * 		ip_address		string
	 */
	private function log_captcha_record($data) {
		$query = $this -> db -> insert_string($this -> captcha_table, $data);
		$this -> db -> query($query);
	}

	/**
	 * 登录页面检查是否登录
	 * 
	 * @return $result	int	1登录，0未登录.
	 */
	public function login_page_check_login() {
		$result = 0;

		$user_name = $this -> session -> userdata('user_name');

		if ($user_name != false && $user_name == $this -> admin_config['user']) {
			$result = 1;
			redirect('/background/c_index/index/', 'refresh');
			exit;
		}

		return $result;
	}

	/**
	 * 后台其它页面检查是否登录
	 * 
	 * @return $result	int	1登录，0未登录.
	 */
	public function other_page_check_login() {
		$result = 0;

		$user_name = $this -> session -> userdata('user_name');

		if ($user_name != false && $user_name == $this -> admin_config['user']) {
			$result = 1;
		}else {
			redirect('/background/c_login/index/', 'refresh');
			exit;
		}

		return $result;
	}

	/**
	 * 登出
	 */
	public function login_out() {
		$this -> session -> unset_userdata('user_name');
		redirect('/background/c_login/index/', 'refresh');
		exit;
	}
}

/**
 * End of file m_login.php
 */
/**
 * Location: ./app/model/background/m_login.php
 */