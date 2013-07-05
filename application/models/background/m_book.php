<?php
class M_book extends CI_Model {
	protected $book_table;
	protected $book_table_fields;
	protected $article_table_fields;
	public function __construct() {
		parent :: __construct();
		$this -> load -> database();
		$this -> book_table = 'book';
		$this -> article_table = 'article';

		$this -> admin_config = $this -> config -> item('my_admin_config');
	}

	private function set_book_table_fields() {
		$this -> book_table_fields = $this -> db -> list_fields($this -> book_table);
		return $this -> book_table_fields;
	}

	private function set_article_table_fields() {
		$this -> article_table_fields = $this -> db -> list_fields($this -> article_table);
		return $this -> article_table_fields;
	}

	public function get_book_table_fields() {
		if (!is_array($this -> book_table_fields)) {
			$this -> set_book_table_fields();
		}
		return $this -> book_table_fields;
	}

	public function get_article_table_fields() {
		if (!is_array($this -> article_table_fields)) {
			$this -> set_article_table_fields();
		}
		return $this -> article_table_fields;
	}

	/**
	 * 获取小说列表
	 * 
	 * @parame $all
	 * @return $list	array二维	表book的全部数据二维数组.
	 */
	public function get_book_list($all = true) {
		$list = array();
		if ($all) {
			$query = $this -> db -> get($this -> book_table);
			$result = $query -> result_array();
			if (count($result) > 0) {
				$list = $result;
			}
		}
		return $list;
	}

	/**
	 * 获取一本书的信息
	 * 
	 * @parame $book_id	int	book_id
	 * @return $info	array	book表的一行信息
	 */
	public function get_a_book_info($book_id) {
		$info = array();
		if (isset($book_id)) {
			$this -> db -> where(array('book_id' => $book_id));
			$query = $this -> db -> get($this -> book_table);
			$result = $query -> row_array();
			if (count($result) > 0) {
				$info = $result;
			}
		}
		return $info;
	}

	/**
	 * 添加一本书
	 * 
	 * @parame $list	array
	 * 		book_name				string	小说名称.
	 * 		web_site_uri			string	小说站点的host.
	 * 		book_author				string	小说作者.
	 * 		book_createTime			int		小说创建时间.
	 * 		book_description		string	小说描述.
	 * 		book_meta_keywords		string	小说meta keywrods.
	 * 		book_meta_description	string	小说meta description.
	 * 		book_meta_title			string	小说meta title.
	 * @return int 1成功，0失败
	 */
	public function add_a_book($list) {
		$insertResult = 0;
		if (is_array($list)) {
			foreach($list as $key => $item) {
				if (!in_array($key, $this -> get_book_table_fields())) {
					unset($list[$key]);
				}
			}
			$data = $list;
			$this -> db -> insert($this -> book_table, $data);
		}
		if ($this -> db -> insert_id() > 0) {
			$insertResult = 1;
		}
		return $insertResult;
	}

	/**
	 * 更新一本书的信息
	 * 
	 * @parame $list	array
	 * 		book_id	int	书的ID
	 * 		要更新的book表的其它字段,可选.
	 * @return $modifyResult	int	0失败，1成功
	 */
	public function modify_a_book($list) {
		$modifyResult = 0;
		if (is_array($list) && isset($list['book_id'])) {
			// 条件
			$where = array('book_id' => $list['book_id']);
			unset($list['book_id']);
			$this -> db -> where($where); 
			// 更新的数据
			foreach($list as $key => $item) {
				if (!in_array($key, $this -> get_book_table_fields())) {
					unset($list[$key]);
				}
			}
			$data = $list;
			$this -> db -> update($this -> book_table, $data);
		}
		if ($this -> db -> affected_rows() > 0) {
			$modifyResult = 1;
		}
		return $modifyResult;
	}

	/**
	 * 删除书
	 * 
	 * @parame $book_ids	array	书的ID LIST
	 * @return $delResult	int	1成功，0正确
	 */
	public function delete_books($book_ids) {
		$delResult = 0;
		if (!is_array($book_ids)) {
			$book_ids = array($book_ids);
		}
		$this -> db -> where_in('book_id', $book_ids);
		$this -> db -> delete($this -> book_table);

		if ($this -> db -> affected_rows() > 0) {
			$delResult = 1;
		}
		return $delResult;
	}

	/**
	 * 获取章节列表信息.
	 * 
	 * @parame $parames	array.
	 * 		status		int	状态,options.
	 * 		book_belong	int	所属的书ID,options.
	 * 		offset		int	offset,options.
	 * 		limit		int	limit,options.
	 * 		article_createTime_orderby		string	desc or asc.
	 * @return $list	arrray二维	$this -> article_table表的二维列表.
	 */
	public function get_article_list($parames = array()) {
		$list = array(); 
		// 条件：状态
		$status = isset($parames['status'])?$parames['status']:null;
		if (isset($status) && $status >= 0) {
			$where['status'] = (string)$status;
		} 
		// 条件：所属的书
		$book_belong = isset($parames['book_belong'])?$parames['book_belong']:null;
		if (isset($book_belong)) {
			$where['book_belong'] = (int)$book_belong;
		} 
		// limit
		$offset = isset($parames['offset'])?$parames['offset']:null;
		$limit = isset($parames['limit'])?$parames['limit']:null;
		if (isset($offset) && isset($limit)) {
			$this -> db -> limit((int)$limit, (int)$offset);
		} 
		// 查询
		$this -> db -> where($where);
		$parames['article_createTime_orderby'] = isset($parames['article_createTime_orderby'])?$parames['article_createTime_orderby']:'desc';
		$this -> db -> order_by("article_createTime", $parames['article_createTime_orderby']);
		$query = $this -> db -> get($this -> article_table);
		$result = $query -> result_array();

		if (is_array($result) && count($result) > 0) {
			$list = $result;
		}

		return $list;
	}

	/**
	 * 获取一本书的章节总数.
	 * 
	 * @parame $parames	array.
	 * 		status		int	状态.
	 * 		book_belong	int	所属书ID.
	 * @return $total	int	章节总数.
	 */
	public function get_one_book_article_total($parames = array()) {
		$total = 0; 
		// 条件：状态
		$status = isset($parames['status'])?$parames['status']:null;
		if (isset($status) && $status >= 0) {
			$where['status'] = (int)$status;
		} 
		// 条件：所属的书
		$book_belong = isset($parames['book_belong'])?$parames['book_belong']:null;
		if (isset($book_belong)) {
			$where['book_belong'] = (int)$book_belong;
		} 
		// 查询
		$this -> db -> where($where);
		$result = $this -> db -> count_all_results($this -> article_table);

		if ($result > 0) {
			$total = $result;
		}

		return $total;
	}

	/**
	 * 获取一章节的信息.
	 * 
	 * @parame $article_id	int	章节ID.
	 * @return $list	array	一章节的信息（$this -> article_table中的一行数据）.
	 */
	public function get_an_article_info($article_id) {
		$list = array(); 
		// 条件：章节ID
		if (isset($article_id)) {
			$where['article_id'] = (int)$article_id; 
			// 查询
			$this -> db -> where($where);
			$query = $this -> db -> get($this -> article_table);
			$result = $query -> row_array();
			if (is_array($result) && count($result) > 0) {
				$list = $result;
			}
		}
		return $list;
	}

	/**
	 * 修改一章节的信息.
	 * 
	 * @parame $parames	array.
	 * 		article_id			int		文章ID.
	 * 		article_title		string	文章标题,options.
	 * 		article_content		string	文章内容,options.
	 * 		article_createTime	int		文章创建时间,options.
	 * 		book_belong			int		文章所属的小说ID,options.
	 * 		is_divide			int		是否为分隔作用,options.
	 * 		status				int		是否有效,options.
	 * @return $return	int	1成功，0失败.
	 */
	public function modify_an_article($parames = array()) {
		$return = 0; 
		// 条件:章节ID
		$article_id = isset($parames['article_id'])?$parames['article_id']:null;
		unset($parames['article_id']);
		if (isset($article_id)) {
			$article_id = (int)$article_id;
			$this -> db -> where(array('article_id' => $article_id)); 
			// 准备更新的数据
			foreach($parames as $key => $item) {
				if (!in_array($key, $this -> get_article_table_fields())) {
					unset($parames[$key]);
				}
			}
			$data = $parames;
			if (isset($data['article_content'])) {
				$data['article_content'] = htmlspecialchars($data['article_content']);
			} 
			// 更新
			$this -> db -> update($this -> article_table, $data);
			$result = $this -> db -> affected_rows();
			if ($result > 0) {
				$return = 1;
			}
		}
		return $return;
	}

	/**
	 * 添加一章节
	 * 
	 * @parame $parames	array
	 * 		article_title		string	文章标题,options.
	 * 		article_content		string	文章内容,options.
	 * 		article_createTime	int		文章创建时间,options.
	 * 		book_belong			int		文章所属的小说ID,options.
	 * 		is_divide			int		是否为分隔作用,options.
	 * 		status				int		是否有效,options.
	 * @return $insert_id	INT	插入的ID
	 */
	public function add_an_article($parames = array()) {
		$insert_id = 0; 
		// 准备插入的数据
		foreach($parames as $key => $item) {
			if (!in_array($key, $this -> get_article_table_fields())) {
				unset($parames[$key]);
			}
		}
		$data = $parames;
		if (isset($data['article_content'])) {
			$data['article_content'] = htmlspecialchars($data['article_content']);
		} 
		// 添加
		$this -> db -> insert($this -> article_table, $data);
		$insert_id = $this -> db -> insert_id();

		return $insert_id;
	}

	/**
	 * 删除章节.
	 * 
	 * @parame $article_ids	array or int	章节ID.
	 * @return $return	int	1成功，0失败.
	 */
	public function del_article($article_ids) {
		$return = 0; 
		// 条件:章节ID
		if (isset($article_ids)) {
			if (!is_array($article_ids)) {
				$article_ids = array($article_ids);
			}
		} 
		// 删除
		if (is_array($article_ids)) {
			$this -> db -> where_in('article_id', $article_ids);
			$this -> db -> delete($this -> article_table);

			if ($this -> db -> affected_rows() > 0) {
				$return = 1;
			}
		}
		return $return;
	}

	/**
	 * check章节缓存是否存在.
	 * 
	 * @parame $article_id	int
	 * @return $cache_exists	int	1存在,0不存在.
	 */
	public function check_article_cache_exists($article_id) {
		$cache_exists = 0;

		$article_file = $this -> get_an_article_cache_file($article_id);
		if (file_exists($article_file)) {
			$cache_exists = 1;
		}

		return $cache_exists;
	}

	/**
	 * 获取章节缓存文件
	 * 
	 * @parame $article_id	int
	 * @return $article_file	string
	 */
	private function get_an_article_cache_file($article_id) {
		$cache_path = $this -> admin_config['cache_path']; 
		// 获取章节信息
		$article_info = $this -> mbook -> get_an_article_info($article_id); 
		// 获取所属的小说的信息
		$belong_book_info = $this -> mbook -> get_a_book_info($article_info['book_belong']);
		$host = $belong_book_info['web_site_uri']; 
		// 获取章节缓存的路径
		$path = $cache_path . $host . '/'; 
		if(!file_exists($path))
		{
			mkdir($path,0764,true);
			chmod($path,0764);
		}
		// 获取章节缓存的文件
		$article_title = trans_url($article_info['article_title']);
		$article_file = $path . $article_title . '.html';
		$article_file = mb_convert_encoding($article_file, 'GB2312', 'UTF-8');
		return $article_file;
	}

	/**
	 * 创建一个章节的缓存
	 * 
	 * @parame $article_id	int
	 */
	public function create_an_article_cache($article_id) {
		// 获取章节缓存文件
		$article_file = $this -> get_an_article_cache_file($article_id);
		if (1) {
			// 获取章节的信息
			$article_info = $this -> mbook -> get_an_article_info($article_id);
			// 获取小说的信息
			$belong_book_info = $this -> mbook -> get_a_book_info($article_info['book_belong']);

			$book_meta_title = $belong_book_info['book_meta_title'];
			$book_meta_description = $belong_book_info['book_meta_description'];
			$book_meta_keywords = $belong_book_info['book_meta_keywords'];
			$article_content = html_entity_decode($article_info['article_content']);
			$article_create_time = $article_info['article_createTime']; 
			// 设置上下章节
			$this -> db -> select('article_title');
			$where = array('status' => '1',
				'book_belong' => $article_info['book_belong'],
				'article_createTime <' => $article_create_time
				);
			$this -> db -> where($where);
			$this -> db -> order_by("article_createTime", "desc");
			$this -> db -> limit(1);
			$query = $this -> db -> get($this -> article_table);
			$pre_article = $query -> row_array();

			$this -> db -> select('article_title');
			$where = array('status' => '1',
				'book_belong' => $article_info['book_belong'],
				'article_createTime >' => $article_create_time
				);
			$this -> db -> where($where);
			$this -> db -> order_by("article_createTime", "asc");
			$this -> db -> limit(1);
			$query = $this -> db -> get($this -> article_table);
			$next_article = $query -> row_array();

			$pre_next = '<div>';
			$base_url = 'http://'.$belong_book_info['web_site_uri'];
			if (is_array($pre_article) && count($pre_article) > 0) {
				$pre_article_title = $pre_article['article_title'];
				$pre_article_title_href = trans_url($pre_article_title);
				$pre_next .= '<div>上一章: <a id="pre_article" href="' . get_article_url($pre_article_title_href,$base_url) . '">' . $pre_article_title . '</a></div>';
			}
			if (is_array($next_article) && count($next_article) > 0) {
				$next_article_title = $next_article['article_title'];
				$next_article_title_href = trans_url($next_article_title);
				$pre_next .= '<div>下一章: <a id="next_article" href="' . get_article_url($next_article_title_href,$base_url) . '">' . $next_article_title . '</a></div>';
			}
			$pre_next .= '<div><a id="category" href="'.get_book_category_url($base_url).'">目录</a></div>';
			$pre_next .= '</div>'; 
			// 写缓存
			$cache_article_pattern = $this -> admin_config['cache_article_pattern'];
			$sample_content = file_get_contents($cache_article_pattern);
			$book_name = $belong_book_info['book_name'];
			$base_url = 'http://'.$belong_book_info['web_site_uri'].'/';
			$book_author = $belong_book_info['book_author'];
			$article_title = $article_info['article_title'];
			$article_create_time = date('Y-m-d H:i',$article_create_time);
			$content = str_replace(
				array(
					'$title',
					'$description', 
					'$keywords',
					'$content',
					'$book_name',
					'$base_url',
					'$book_author',
					'$article_title',
					'$logo_name',
					'$logo_title',
					'$article_create_time'),
				array(
					$book_meta_title, 
					$book_meta_description, 
					$book_meta_keywords, 
					$article_content.$pre_next,
					$book_name,
					$base_url,
					$book_author,
					$article_title,
					$book_name.'.png',
					$book_name,
					$article_create_time),
				$sample_content
			);

			file_put_contents($article_file, $content);
		}
	}

	/**
	 * 获取小说目录缓存文件
	 * 
	 * @parame $book_id	int
	 * @return $book_category_file	string
	 */
	private function get_book_category_cache_file($book_id) {
		$cache_path = $this -> admin_config['cache_path']; 
		// 获取小说的信息
		$book_info = $this -> mbook -> get_a_book_info($book_id);
		$host = $book_info['web_site_uri']; 
		// 获取小说目录缓存的路径
		$path = $cache_path . $host . '/'; 
		if(!file_exists($path))
		{
			mkdir($path,0764,true);
			chmod($path,0764);
		}
		// 获取小说目录缓存的文件
		$book_category_file = $path . '目录.html';
		$book_category_file = mb_convert_encoding($book_category_file, 'GB2312', 'UTF-8');
		return $book_category_file;
	}

	/**
	 * 创建小说的目录缓存文件
	 * 
	 * @parame $book_id	int
	 */
	public function create_book_directory_cache($book_id) {
		$book_info = $this -> get_a_book_info($book_id);
		$article_list = $this -> get_article_list(array('book_belong' => $book_id, 'status' => 1,'article_createTime_orderby'=>'asc'));

		$content = '';
		$i = 1;
		$total = count($article_list);
		$base_url = 'http://'.$book_info['web_site_uri'];
		foreach($article_list as $article) {			
			if($i == 1 || $i%3 == 1)
			{
				$content .= '<tr>';
			}
			if($article['is_divide'] == 1)
			{
				if(!is_int($i/3))
				{
					
					$need_repair = 3 - ($i-1)%3;
					if($need_repair > 0)
					{
						for($j=0;$j<$need_repair;++$j)
						{
							$content .= '<td></td>';
						}
					}
					$content .= '</tr>';
				}
				$content .= '<tr><td colspan="3"><center>'.$article['article_title'].'</center></td></tr>';
				$i = 0;
			}
			else
			{
				$href_title = trans_url($article['article_title']);
				$content .= '<td><a href="' . get_article_url($href_title,$base_url) . '">' . $article['article_title'] . '</a></td>';
				if(is_int($i/3) || $i == $total)
				{
					if(!is_int($i/3) && $i == $total)
					{
						$need_repair = ($i-1)%3;
						for($j=0;$j<$need_repair;++$j)
						{
							$content .= '<td></td>';
						}
					}				
					$content .= '</tr>';
				}
			}
			++$i;
		}

		$cache_category_pattern = $this -> admin_config['cache_category_pattern'];
		$sample_content = file_get_contents($cache_category_pattern);
		$book_meta_title = $book_info['book_meta_title'];
		$book_meta_description = $book_info['book_meta_description'];
		$book_meta_keywords = $book_info['book_meta_keywords'];
		$book_name = $book_info['book_name'];
		$base_url = 'http://'.$book_info['web_site_uri'].'/';
		$book_author = $book_info['book_author'];
		$content = str_replace(
			array('$title', '$description', '$keywords','$content','$book_name','$base_url','$book_author','$logo_name','$logo_title'),
			array($book_meta_title, $book_meta_description, $book_meta_keywords, $content . $pre_next,$book_name,$base_url,$book_author,$book_name.'.png',$book_name),
			$sample_content
		);

		$book_category_file = $this -> get_book_category_cache_file($book_id);
		file_put_contents($book_category_file, $content);
	}


}

/**
 * End of file m_book.php
 */
/**
 * Location: ./app/model/background/m_book.php
 */