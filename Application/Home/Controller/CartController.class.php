<?php
namespace Home\Controller;
use Think\Controller;
class CartController extends Controller 
{
	/**
	 * 对JS提供的修改数量的接口
	 * 请求方式：GET
	 * 请求参数：goods_id，goods_attr_id,goods_number【0：代表修改】
	 * 返回值：成功返回json:ok:1  失败返回json:{ok:1,error:'失败原因'}
	 * JS请求地址：/index.php/Home/Cart/ajaxEditGoodsNumber
	 *
	 */
	public function ajaxEditGoodsNumber()
	{
		//var_dump($_GET);die;
		$cartModel = D('Cart');
		if($cartModel->editGoodsNumber(I('get.goods_id'), I('get.goods_attr_id'), I('goods_number')))
		{
			echo json_encode(array(
				'ok' => 1,
			));
		}
		else 
		{
			echo json_encode(array(
				'ok' => 0,
				'error' => $cartModel->getError(),
			));
		}
	}
	public function add()
	{
		$cartModel = D('Cart');
		$goodsAttrId = I('post.goods_attr_id');
		// 如果选择了商品属性，就按数字的方式把商品属性ID升序排列并拼成字符串
		if($goodsAttrId)
		{
			sort($goodsAttrId, SORT_NUMERIC);
			$goodsAttrId = implode(',', $goodsAttrId);
		}
		if($cartModel->addToCart(I('post.goods_id'), $goodsAttrId, I('post.amount')))
		{
			$this->success('添加成功！', U('lst'));		
			exit;	
		}
		else 
			$this->error($cartModel->getError());
	}
	// 购物车列表页
	public function lst()
	{
		$cartModel = D('Cart');
		$data = $cartModel->cartList();
		$this->assign('data', $data);
		
		// 设置页面信息
    	$this->assign(array(
    		'_page_title' => '购物车列表',
    		'_page_keywords' => '购物车列表',
    		'_page_description' => '购物车列表',
    	));
		$this->display();
	}
	
}