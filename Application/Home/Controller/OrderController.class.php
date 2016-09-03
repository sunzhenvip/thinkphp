<?php
namespace Home\Controller;
use Think\Controller;
class OrderController extends Controller 
{
	public function add()
	{
		$id = session('member_id');
		if(!$id)
		{
			session('returnUrl', U('Order/add')); // 设置登录之后跳回的地址
			$this->error('必须先登录！', U('Login/login'));
		}
		
		if(IS_POST)
		{
			//var_dump($_POST);die;
			/*************** 下单之前的数据验证 ******************/
			// 检查一、收货人地址是否完整
			$_POST['member_id'] = $id;  // 把会员ID放到表单中
			$shr_info = I('post.shr_info'); // 收货人id
			$msModel = D('member_shr');
			if($shr_info == 0)
			{
				// 插入到数据库中
				// 接收表单并验证表单
				if($msModel->create(I('post.'), 1))
					$shr_info = $msModel->add(); // 添加收货人并获取收货人ID
				else
					$this->error($msModel->getError());
			}
			// 根据收货人ID取出详细信息
			$shrInfo = $msModel->where(array(
				'id' => array('eq', $shr_info),
				'member_id' => array('eq', $id),   // 【取出自己的收货人信息】
			))->find();
			if(!$shrInfo)
				$this->error('收货人信息不存在！');
			// 检查二、购物车中是否有商品以及库存量都够不够
			$cartModel = D('Cart');
			$cartData = $cartModel->cartList();
			if(!$cartData)
				$this->error('购买车中没有商品，请先购买商品！');
			// 先加文件锁：以下的代码不能并发执行否则会出错
			$fp = fopen('./order.lock', 'r');
			flock($fp, LOCK_EX);
			// 循环：检查每件库存量，计算总价
			$tp = 0;// 总价
			/******** 判断库存量 ***********/
			$gnModel = D('goods_number');
			foreach ($cartData as $k => $v)
			{
				// 检查库存量
				$gn = $gnModel->field('goods_number')->where(array(
					'goods_id' => array('eq', $v['goods_id']),
					'attr_list' => array('eq', $v['goods_attr_id']),
				))->find();
				if($gn['goods_number'] < $v['goods_number'])
				{
					$this->error('商品：<srong>'.$v['goods_name'].'['.$v['goods_attr_str'].']</strong>库存量不足，无法下单！');
				}
				$tp += $v['price'] * $v['goods_number'];  // 统计总价
			}
			/*************** 下定单并减少库存量 ******************/
			$cartModel->startTrans();
			//mysql_query('START TRANSACTION');
			// 生成定单
			$orderModel = D('Order');
			$orderId = $orderModel->add(array(
				'member_id' => $id,
				'addtime' => time(),
				'total_price' => $tp,
				'shr_name' => $shrInfo['shr_name'],
				'shr_province' => $shrInfo['shr_province'],
				'shr_city' => $shrInfo['shr_city'],
				'shr_area' => $shrInfo['shr_area'],
				'shr_address' => $shrInfo['shr_address'],
				'shr_tel' => $shrInfo['shr_tel'],
				'shr_name' => $shrInfo['shr_name'],
				'beizhu' => '',
				'post_method' => I('post.post_method'),
				'pay_method' => I('post.pay_method'),
			));
			if($orderId)
			{
				// 把购物车中的商品移动到定单商品表中
				$ogModel = D('order_goods');
				foreach ($cartData as $k => $v)
				{
					$rs = $ogModel->add(array(
						'member_id' => $id,
						'order_id' => $orderId,
						'goods_id' => $v['goods_id'],
						'goods_attr_id' => $v['goods_attr_id'],
						'goods_number' => $v['goods_number'],
						'price' => $v['price'],
					));
					if($rs)
					{
						// 减少库存量
						$rs1 = $gnModel->where(array(
							'goods_id' => array('eq', $v['goods_id']),
							'attr_list' => array('eq', $v['goods_attr_id']),
						))->setDec('goods_number', $v['goods_number']);
						if(FALSE === $rs1)
						{
							$orderModel->rollback(); // 回滚事务
							$this->error('下单失败!');
						}
					}
					else 
					{
						$orderModel->rollback(); // 回滚事务
						$this->error('下单失败!');
					}
				}
			}
			else 
			{
				$orderModel->rollback(); // 回滚事务
				$this->error('下单失败！');
			}
			$orderModel->commit(); // 提交整个事务
			// 释放锁
			flock($fp, LOCK_UN);
			fclose($fp);
			// 清空购物车
			$cartModel->clear();
			$this->success('下单成功！', U('pay?id='.$orderId));
			exit;
		}
		
		// 取出当前会员的收货人信息
		$msModel = D('member_shr');
		$shrData = $msModel->where(array(
			'member_id' => array('eq', $id),
		))->select();
		// 取出购物车中的商品
		$cartModel = D('cart');
		$cartData = $cartModel->cartList();
		
		// 设置页面信息
    	$this->assign(array(
    		'shrData' => $shrData,
    		'cartData' => $cartData,
    		'_page_title' => '定单确认',
    		'_page_keywords' => '定单确认',
    		'_page_description' => '定单确认',
    	));
		$this->display();
	}
	// 下单成功之后的页面
	public function pay()
	{
		// 生成支付按钮
		include('./alipay/alipayapi.php');
		
		// 设置页面信息
    	$this->assign(array(
    		'pay_btn' => $html_text,  // 把生成的按钮 放到页面中
    		'_page_title' => '下单成功',
    		'_page_keywords' => '下单成功',
    		'_page_description' => '下单成功',
    	));
		$this->display();
	}
	// 用来接收支付宝发给我们的消息
	public function receive()
	{
		// 接收并解密和验证并处理
		include('./alipay/notify_url.php');
	}
	// 支付宝支付成功之后跳回到这
	public function pay_success()
	{
		$this->success('支付成功！', '/');
	}
}















