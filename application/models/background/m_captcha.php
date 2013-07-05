<?php
class M_captcha extends CI_Model {
	protected $captcha_table;
	public function __construct() {
		parent :: __construct();
		$this -> load -> database();
		$this -> captcha_table = 'captcha';
		$this->load->helper('captcha');


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
	 * 创建captcha.
	 * 
	 * @parame $ip_address	string	ip地址.options.
	 * @parame $img_path	string	options.
	 * @parame $img_url	string	options.
	 * @return $cap	array
	 * 		image	string
	 * 		time	int
	 * 		word	string
	 */
	public function create_captcha($ip_address = '', $img_path = '', $img_url = '') {
		$img_path = (!empty($img_path))?$img_path:$this -> admin_config['captcha_img_path'];
		$img_url = (!empty($img_url))?$img_url:base_url().$this->admin_config['captcha_img_url_suffix'];
		$ip_address = (!empty($ip_address))?$ip_address:$this -> input -> ip_address();
		

		$vals = array(
			'img_path' => $img_path,
			'img_url' => $img_url,
			'word' => 'adam'.rand(1,20),
			'img_width' => $this -> admin_config['captcha_img_width'],
			'img_height' => $this -> admin_config['captcha_img_height'],
			);
		$cap = create_captcha($vals);

		$data = array('captcha_time' => $cap['time'],
			'word' => $cap['word'],
			'ip_address' => $ip_address,
			);

		$this -> log_captcha_record($data);

		return $cap;
	}

	/**
	 * 删除过期的captcha记录
	 */
	private function del_expire_captcha_record() {
		$expiration = time()-$this -> admin_config['captcha_expire_timestamp']; // 2小时限制
		$this -> db -> query("DELETE FROM captcha WHERE captcha_time < " . $expiration);
	}

	/**
	 * 确认captcha是否存在
	 * 
	 * @parame $captcha	string	captcha文字.
	 * @parame $ip_address	string	options.
	 * @return $exists	int	1存在，0不存在.
	 */
	public function check_captcha_exists($captcha, $ip_address = '') {
		$exists = 0;
		$ip_address = (!empty($ip_address))?$ip_address:$this -> input -> ip_address();
		$expiration = time()-$this -> admin_config['captcha_expire_timestamp']; // 2小时限制 
		
		$this -> del_expire_captcha_record(); 
		// 看是否有验证码存在:
		$sql = "SELECT COUNT(captcha_id) AS count FROM " . $this -> captcha_table . " WHERE word = ? AND ip_address = ? AND captcha_time > ?";
		$binds = array($captcha, $ip_address, $expiration);
		$query = $this -> db -> query($sql, $binds);
		$row = $query -> row();

		if ($row -> count > 0) {
			$exists = 1;
		}
		return $exists;
	}
}

/**
 * End of file m_captcha.php
 */
/**
 * Location: ./app/model/background/m_captcha.php
 */