<?php
class M_crawler extends CI_Model {
	public function __construct() {
		parent :: __construct(); 
		// $this -> load -> database();
		$this -> admin_config = $this -> config -> item('my_admin_config');
	}

	/**
	 * 获取全部采集方案或某个采集方案
	 * 
	 * @RETURN array 
	 */
	public function get_crawler_plan($plan_id = 0) {
		$return = $config = $this -> admin_config['crawler_plan'];
		if ($plan_id != 0) {
			foreach($config as $item) {
				if ($item['id'] == $plan_id) {
					$return = $item;
					break;
				}
			}
		}
		return $return;
	}

	/**
	 * 获取小说的采集配置
	 * 
	 * @PARAME $book_id			int	小说的ID
	 * @PARAME $crawler_plan_id	int	采集的方案ID
	 * @RETURN $config	array
	 * 		crawler_plan_id		int		采集的方案ID
	 * 		其它字段同配置方案里的字段
	 */
	public function get_book_config($book_id, $crawler_plan_id) {
		$config = array();
		$crawler_config_path = $this -> admin_config['crawler_book_config_path'] . $crawler_plan_id . '/';
		$config_file = $crawler_config_path . $book_id . '.txt';
		if (file_exists($config_file)) {
			$config = file_get_contents($config_file);
			$config = unserialize($config);
			$plan = $this -> get_crawler_plan($crawler_plan_id);
			if (isset($plan['fields']) && is_array($plan['fields'])) {
				$new_config = array('crawler_plan_id' => $crawler_plan_id);
				foreach($plan['fields'] as $item) {
					if (isset($config[$item['field']])) {
						$new_config[$item['field']] = $config[$item['field']];
					}
				}
			}
		}
		return $config;
	}

	/**
	 * 小说采集配置
	 * 
	 * @PARAME $parames	array
	 * 		book_id		int		小说ID
	 * 		crawler_plan_id		int		采集的方案ID
	 * 		其它字段同配置方案里的字段
	 * @RETURN $result	int	1成功，0失败
	 */
	public function config_book($parames = array()) {
		$result = 0;
		$crawler_plan_id = $parames['crawler_plan_id'];
		unset($parames['crawler_plan_id']);
		$crawler_config_path = $this -> admin_config['crawler_book_config_path'] . $crawler_plan_id . '/';
		if (is_array($parames) && count($parames) > 0) {
			// 获取采集方案
			$plan = $this -> get_crawler_plan($crawler_plan_id);
			$plan_fields = array();
			foreach($plan['fields'] as $item) {
				$plan_fields[] = $item['field'];
			} 
			// 设置采集配置内容
			$content = array('crawler_plan_id' => $crawler_plan_id);
			foreach($parames as $key => $item) {
				if (in_array($key, $plan_fields)) {
					$content[$key] = $item;
				}
			}

			$book_id = $parames['book_id']; 
			// 验证数据有效性
			if ($book_id > 0 && count($content) > 0) {
				// 如果目录不存在，则创建
				if (!file_exists($crawler_config_path)) {
					mkdir($crawler_config_path, 0764, true);
					chmod($crawler_config_path, 0764);
				} 
				// 把配置内容序列化后写入文件
				$config_file = $crawler_config_path . $book_id . '.txt';

				$content = serialize($content);
				$tmp_result = file_put_contents($config_file, $content);
				if ($tmp_result !== false) {
					$result = 1;
				}
			}
		}
		return $result;
	}

	/**
	 * 获取一小说的整体采集进度.
	 * 
	 * @PARAME $book_id	int	小说ID
	 * @RETURN $process	string
	 */
	public function get_book_crawler_whole_process($book_id) {
		$process = '';

		if (isset($book_id)) {
			$root_path = $this -> admin_config['crawler_book_process_path'] . $book_id;
			$filename = $this -> admin_config['crawler_book_process_filename'];
			$file = $root_path . '/' . $filename;
			if (file_exists($file)) {
				$process = file_get_contents($file);
				list($a, $b) = explode('/', $process);
				$process = percent_format($a / $b);
			}
		}
		return $process;
	}

	/**
	 * 获取一小说的失败的重新采集进度.
	 * 
	 * @PARAME $book_id	int	小说ID
	 * @RETURN $process	string
	 */
	public function get_book_crawler_fail_process($book_id) {
		$process = '';

		if (isset($book_id)) {
			$root_path = $this -> admin_config['crawler_book_process_path'] . $book_id;
			$filename = $this -> admin_config['crawler_fail_book_process_filename'];
			$file = $root_path . '/' . $filename;
			if (file_exists($file)) {
				$process = file_get_contents($file);
				list($a, $b) = explode('/', $process);
				$process = percent_format($a / $b);
			}
		}
		return $process;
	}

	/**
	 * 获取一小说的采集成功的章节总数.
	 * 
	 * @PARAME $book_id	int	小说ID
	 * @RETURN $total	int
	 */
	public function get_book_crawler_success_total($book_id) {
		$total = 0;

		if (isset($book_id)) {
			$root_path = $this -> admin_config['crawler_book_process_path'] . $book_id;
			$filename = $this -> admin_config['crawler_book_success_filename'];
			$file = $root_path . '/' . $filename;
			if (file_exists($file)) {
				$total = file_get_contents($file);
			}
		}
		return $total;
	}

	/**
	 * 获取一小说的采集失败的章节总数.
	 * 
	 * @PARAME $book_id	int	小说ID
	 * @RETURN $total	int
	 */
	public function get_book_crawler_fail_total($book_id) {
		$total = 0;

		if (isset($book_id)) {
			$root_path = $this -> admin_config['crawler_book_process_path'] . $book_id;
			$filename = $this -> admin_config['crawler_book_fail_filename'];
			$file = $root_path . '/' . $filename;
			if (file_exists($file)) {
				$total = file_get_contents($file);
			}
		}
		return $total;
	}

	/**
	 * 采集（A方案）书的章节url list。
	 * 
	 * @PARAME $book_id			INT	小说ID
	 * @PARAME $crawler_plan_id	INT	采集方案的ID
	 * @RETURN $matcheds	array	章节的url list
	 */
	private function crawler_A_article_url($book_id, $crawler_plan_id) {
		ignore_user_abort(true);
		set_time_limit(0); 
		// 抓取目录页，获取其每个章节url地址
		$book_crawler_config = $this -> get_book_config($book_id, $crawler_plan_id);
		$url = $book_crawler_config['crawler_book_url'];
		$urlCotentFile = $this -> admin_config['crawler_book_process_path'] . $book_id . '/' . $this -> admin_config['crawler_article_url_filename'];
		$urlContentSp = ',';
		$pattern = '/<a href="(\d+)\.html">/i'; 
		// 初始化
		$hand = curl_init();
		$curParame = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => true
			);
		curl_setopt_array($hand, $curParame); 
		// 执行获取
		$return = curl_exec($hand);

		$matches = array();
		preg_match_all($pattern, $return, $matches);

		if (isset($matches[1])) {
			$matcheds = $matches[1];
		} 
		
		//debug
//		$matcheds = array_slice($matcheds,0,50);
		// 写入文件
		$content = implode($urlContentSp, $matcheds);
		file_put_contents($urlCotentFile, $content); 
		// return
		return $matcheds;
	}

	/**
	 * 获取采集失败的章节url list。
	 * 
	 * @PARAME $book_id			INT	小说ID
	 * @PARAME $crawler_plan_id	INT	采集方案的ID
	 * @RETURN $matcheds	array	章节的url list
	 */
	private function crawler_A_article_fail_url($book_id, $crawler_plan_id) {
		$url_list = array(); 
		// 
		$book_crawler_config = $this -> get_book_config($book_id, $crawler_plan_id);
		$urlCotentFile = $this -> admin_config['crawler_book_process_path'] . $book_id . '/' . $this -> admin_config['crawler_book_fail_url_filename'];

		if (file_exists($urlCotentFile)) {
			$url_list = file_get_contents($urlCotentFile);
			$url_list = explode(',',$url_list);
		} 
		// return
		return $url_list;
	}

	/**
	 * 采集整本小说（A方案）.
	 * 
	 * @PARAME $book_id			int	小说ID.
	 * @PARAME $crawler_plan_id	int	采集方案ID.
	 */
	public function crawler_A_book($book_id, $crawler_plan_id) {
		ignore_user_abort(true);
		set_time_limit(0);
		$book_crawler_config = $this -> get_book_config($book_id, $crawler_plan_id); 
		// 设置内容页基本地址
		$baseUrl = $book_crawler_config['article_base_url']; 
		// 内容页url地址的后缀
		$contentUrlSuffix = $book_crawler_config['article_url_suffix']; 
		// 标题正则
		$titlePattern = '/<div id="title">(.+?)<\/div>/i'; 
		// 内容正则
		$contentPattern = '/<div id="content".*>(.+?)<\/div>/i'; 
		// 保存各类文件的名称
		$config = $this -> admin_config;
		$savePathBase = $config['crawler_book_path'];
		$saveTitleFileName = $config['crawler_article_title_filename'];
		$saveContentFileName = $config['crawler_article_content_filename'];

		$logPath = $config['crawler_book_process_path'] . $book_id . '/';
		if (!file_exists($logPath)) {
			mkdir($logPath, 0764, true);
			chmod($logPath, 0764);
		}
		$failUrlFile = $logPath . $config['crawler_book_fail_url_filename'];

		$processFile = $logPath . $config['crawler_book_process_filename'];

		$successTotalFile = $logPath . $config['crawler_book_success_filename'];

		$failTotalFile = $logPath . $config['crawler_book_fail_filename']; 
		// 抓取目录页，获取其每个章节url地址
		$urlList = $this -> crawler_A_article_url($book_id, $crawler_plan_id);

		$failNumList = array();

		$countUrl = count($urlList);

		if ($countUrl > 0) {
			$i = 1;
			foreach($urlList as $urlNum) {
				$url = $baseUrl . $urlNum . $contentUrlSuffix; 
				// 初始化
				$hand = curl_init();
				$curlParame = array(
					CURLOPT_URL => $url,
					CURLOPT_HEADER => 0,
					CURLOPT_RETURNTRANSFER => true
					);
				curl_setopt_array($hand, $curlParame); 
				// 执行获取
				$return = curl_exec($hand);

				if (strlen($return) > 0) {
					preg_match_all($titlePattern, $return, $title);
					preg_match_all($contentPattern, $return, $content);
					$title = str_replace('正文 ', '', $title[1][0]);
					$content = str_replace(array('<div id="content">', '<div id="adright">', '<div>', '</div>'), '', $content[0][0]);

					$savePath = $savePathBase . $book_id . '/' . $urlNum . '/';

					if (!file_exists($savePath)) {
						mkdir($savePath, 0764, true);
						chmod($savePath, 0764);
					} 
					// 检查当前章节内容获取是否失败
					if (is_string($title) && is_string($content) && strlen($title) > 0 && strlen($content) > 0) {
						file_put_contents($savePath . $saveTitleFileName, $title);
						file_put_contents($savePath . $saveContentFileName, $content); 
						// 记录成功的数量
						if (file_exists($successTotalFile)) {
							$successTotal = file_get_contents($successTotalFile);
						}
						$successTotal = isset($successTotal)?($successTotal + 1):1;
						file_put_contents($successTotalFile, $successTotal);
					}else {
						$failNumList[] = $urlNum; 
						// 记录失败的数量 以及 url
						if (file_exists($failTotalFile)) {
							$failTotal = file_get_contents($failTotalFile);
						}
						$failTotal = isset($failTotal)?($failTotal + 1):1;
						file_put_contents($failTotalFile, $failTotal);
						file_put_contents($failUrlFile, implode(',', $failNumList));
					} 
					// 记录进度
					file_put_contents($processFile, $i . '/' . $countUrl);
					++$i;
				}
			}
		}
	}

	/**
	 * 采集失败的章节（A方案）.
	 * 
	 * @PARAME $book_id			int	小说ID.
	 * @PARAME $crawler_plan_id	int	采集方案ID.
	 */
	public function crawler_A_fail_book($book_id, $crawler_plan_id) {
		ignore_user_abort(true);
		set_time_limit(0);
		$book_crawler_config = $this -> get_book_config($book_id, $crawler_plan_id); 
		// 设置内容页基本地址
		$baseUrl = $book_crawler_config['article_base_url']; 
		// 内容页url地址的后缀
		$contentUrlSuffix = $book_crawler_config['article_url_suffix']; 
		// 标题正则
		$titlePattern = '/<div id="title">(.+?)<\/div>/i'; 
		// 内容正则
		$contentPattern = '/<div id="content".*>(.+?)<\/div>/i'; 
		// 保存各类文件的名称
		$config = $this -> admin_config;
		$savePathBase = $config['crawler_book_path']; 
		$saveTitleFileName = $config['crawler_article_title_filename'];
		$saveContentFileName = $config['crawler_article_content_filename'];

		$logPath = $config['crawler_book_process_path'] . $book_id . '/';
		if (!file_exists($logPath)) {
			mkdir($logPath, 0764, true);
			chmod($logPath, 0764);
		}

		$failUrlFile = $logPath . $config['crawler_book_fail_url_filename'];
		$processFile = $logPath . $config['crawler_fail_book_process_filename'];
		$successTotalFile = $logPath . $config['crawler_book_success_filename'];
		$failTotalFile = $logPath . $config['crawler_book_fail_filename']; 
		// 抓取目录页，获取其每个章节url地址
		$urlList = $this -> crawler_A_article_fail_url($book_id, $crawler_plan_id);

		$failNumList = array();

		$countUrl = count($urlList);

		if ($countUrl > 0) {
			$i = 1;
			//清空失败的记录
			if(file_exists($failTotalFile))
			{
				unlink($failTotalFile);
			}
			if(file_exists($failUrlFile))
			{
				unlink($failUrlFile);
			}

			foreach($urlList as $urlNum) {
				$url = $baseUrl . $urlNum . $contentUrlSuffix; 
				// 初始化
				$hand = curl_init();
				$curlParame = array(
					CURLOPT_URL => $url,
					CURLOPT_HEADER => 0,
					CURLOPT_RETURNTRANSFER => true
					);
				curl_setopt_array($hand, $curlParame); 
				// 执行获取
				$return = curl_exec($hand);

				if (strlen($return) > 0) {
					preg_match_all($titlePattern, $return, $title);
					preg_match_all($contentPattern, $return, $content);
					$title = str_replace('正文 ', '', $title[1][0]);
					$content = str_replace(array('<div id="content">', '<div id="adright">', '<div>', '</div>'), '', $content[1][0]);

					$savePath = $savePathBase . $book_id . '/' . $urlNum . '/';
					if (!file_exists($savePath)) {
						mkdir($savePath, 0764,true);
						chmod($savePath, 0764);
					} 
					// 检查当前章节内容获取是否失败
					if (is_string($title) && is_string($content) && strlen($title) > 0 && strlen($content) > 0) {
						file_put_contents($savePath . $saveTitleFileName, $title);
						file_put_contents($savePath . $saveContentFileName, $content); 
						// 记录成功的数量
						if (file_exists($successTotalFile)) {
							$successTotal = file_get_contents($successTotalFile);
						}
						$successTotal = isset($successTotal)?($successTotal + 1):1;
						file_put_contents($successTotalFile, $successTotal);
					}else {
						$failNumList[] = $urlNum; 
						// 记录失败的数量 以及 连接
						if (file_exists($failTotalFile)) {
							$failTotal = file_get_contents($failTotalFile);
						}
						$failTotal = isset($failTotal)?($failTotal + 1):1;
						file_put_contents($failTotalFile, $failTotal);
						file_put_contents($failUrlFile, implode(',', $failNumList));
					} 
					// 记录进度
					file_put_contents($processFile, $i . '/' . $countUrl);
					++$i;
				}
			}
		}
	}

	/**
	 * 把采集的小说导入到数据库中
	 * 
	 * @parame $book_id		int			小说ID
	 * @parame $book_host	int			小说HOST
	 * @parame $base_time	datetime	小说导入时间基点
	 */
	public function import_crawler_book_to_db($book_id, $book_host, $base_time) {
		$config = $this -> admin_config;

		$baseTime = strtotime($base_time);
		$titleFileName = $config['crawler_article_title_filename'];
		$contentFileName = $config['crawler_article_content_filename'];
		$bookDir = $config['crawler_book_path'] . $book_id . '/';
		$d = dir($bookDir);
		$i = 1;

		$importDatas = array();

		while (false !== ($entry = $d -> read())) {
			if ($entry != '.' && $entry != '..') {
				$dir = $bookDir . $entry . '/';
				$titleFile = $dir . $titleFileName;
				$contentFile = $dir . $contentFileName;
				$title = file_get_contents($titleFile);
				$content = file_get_contents($contentFile);
				$title = mb_convert_encoding($title, 'UTF-8', 'GB2312');
				$content = mb_convert_encoding($content, 'UTF-8', 'GB2312');
				$importDatas[] = array('article_title' => $title,
					'article_content' => $content,
					'article_createTime' => $baseTime + $i*60,
					'book_belong' => $book_id,
					'is_divide' => 0,
					'status' => 1
					);
				if (is_int($i / 10)) {
					$url = 'http://' . $book_host . '/background/c_api_book/import_article';

					$hand = curl_init();

					$curlParame = array(
						CURLOPT_URL => $url,
						CURLOPT_HEADER => false,
						CURLOPT_RETURNTRANSFER => false,
						CURLOPT_POST => 1,
						CURLOPT_POSTFIELDS => http_build_query($importDatas),
						);

					curl_setopt_array($hand, $curlParame);

					curl_exec($hand);

					curl_close($hand);

					$importDatas = array();
				}
				++$i;
			}
		}
		$d -> close();
	}
}

/**
 * End of file m_crawler.php
 */
/**
 * Location: ./app/model/background/m_crawler.php
 */