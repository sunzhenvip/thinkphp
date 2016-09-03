<?php
namespace Home\Controller;
use Think\Controller;
class SearchController extends Controller 
{
	// 根据分类搜索
	public function search()
	{
		$goodsModel = D('Admin/Goods');
		$data = $goodsModel->front_search(); // 根据分类搜索
		$this->assign($data);
		// 设置页面信息
    	$this->assign(array(
    		'_page_title' => '商品搜索',
    		'_page_keywords' => '商品搜索',
    		'_page_description' => '商品搜索',
    		'_page_hide_nav' => 1,  // 折叠
    	));
		$this->display();
	}
	// 根据关键字搜索
	public function key_search()
	{
		$goodsModel = D('Admin/Goods');
		$data = $goodsModel->front_key_search(); // 根据关键字搜索
		$this->assign($data);
		// 设置页面信息
    	$this->assign(array(
    		'_page_title' => '商品搜索',
    		'_page_keywords' => '商品搜索',
    		'_page_description' => '商品搜索',
    		'_page_hide_nav' => 1,  // 折叠
    	));
		$this->display();
	}
}