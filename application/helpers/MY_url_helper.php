<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_article_url($title,$base_url = '')
{
	if(!empty($base_url))
	{
		return $base_url.'/目录/'. $title;
	}
	else
	{
		return base_url('目录/' . $title);
	}
}

function get_book_category_url($base_url = '')
{
	if(!empty($base_url))
	{
		return $base_url.'/目录';
	}
	else
	{
		return base_url('目录/');
	}
}

/* End of file MY_url_helper.php */
/* Location: ./system/helpers/MY_url_helper.php */