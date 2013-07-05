<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//left tab
$config['admin_tab']	= array(
	array(
		'title'=>'小说',
		'list' => array(
			array('url'=>'background/c_index/book_manage','text'=>'小说管理','id'=>1),
			array('url'=>'background/c_index/article_manage','text'=>'章节管理','id'=>2),
		)
	),
	/*
	array(
		'title'=>'缓存',
		'list' => array(
			array('url'=>'background/c_cache/book_cache','text'=>'整本小说缓存','id'=>1),
		)
	),
	*/
	array(
		'title'=>'采集',
		'list' => array(
			array('url'=>'background/c_crawler/book_crawler_config','text'=>'采集配置','id'=>1),
			array('url'=>'background/c_crawler/book_crawler','text'=>'采集','id'=>2),
		)
	),
);

$config['admin_default'] = 'welcome';

$config['article_status'] = array('-1'=>'无选择','0'=>'无效','1'=>'有效');

$config['book_default'] = array('book_id'=>'0','book_name'=>'无选择');

//cache
$config['book_cache_process_path'] = APPPATH.'mycache/process/books/';
$config['cache_path'] = FCPATH.'content/';
$config['cache_article_pattern'] = APPPATH.'mycache/pattern/article.html';
$config['cache_category_pattern'] = APPPATH.'mycache/pattern/category.html';

//采集
$config['crawler_plan'] = array(
	array(
		'text'=>'采集方案A',
		'id'=>1,
		'selected'=>true,
		'fields'=>array(
			array('field'=>'crawler_book_url','title'=>'小说目录url'),
//			array('field'=>'crawler_article_url','title'=>'章节url'),
			array('field'=>'article_base_url','title'=>'章节base url'),
			array('field'=>'article_url_suffix','title'=>'章节url的后缀'),
		),
		'crawler_method'=>'crawler_A_book',
		'crawler_fail_method'=>'crawler_A_fail_book',
	),
	array(
		'text'=>'采集方案B',
		'id'=>2,
		'fields'=>array(
			array('field'=>'crawler_book_url','title'=>'小说目录url'),
			array('field'=>'crawler_article_url','title'=>'章节url'),
		),
		'crawler_method'=>'',
		'crawler_fail_method'=>'',
	)
);
//采集配置方案存放文件的路径 $config['crawler_book_config_path'].$planid/
$config['crawler_book_config_path'] = APPPATH.'mycrawler/config/book/';

//采集进度
$config['crawler_book_process_path'] = APPPATH.'mycrawler/process/book/';
$config['crawler_book_process_filename'] = 'whole_process.txt';
$config['crawler_book_success_filename'] = 'success_total.txt';
$config['crawler_fail_book_process_filename'] = 'fail_process.txt';
$config['crawler_book_fail_filename'] = 'fail_total.txt';
$config['crawler_book_fail_url_filename'] = 'fail_url.txt';
$config['crawler_article_url_filename'] = 'url.txt';

//
$config['crawler_book_path'] = FCPATH.'content/crawler/';
$config['crawler_article_title_filename'] = 'title.txt';
$config['crawler_article_content_filename'] = 'content.txt';

//captcha
$config['captcha_img_path'] = './image/captcha/';
$config['captcha_img_url_suffix'] = 'image/captcha/';
$config['captcha_expire_timestamp'] = 7200;
$config['captcha_img_width'] = 150;
$config['captcha_img_height'] = 30;

//admin user,pwd
$config['user'] = 'adam';
$config['pwd'] = 'ghqhch11';


/* End of file my_admin_config.php */
/* Location: ./application/config/my_admin_config.php */
