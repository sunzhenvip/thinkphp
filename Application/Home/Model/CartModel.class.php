<?php
namespace Home\Model;
use Think\Model;
class CartModel extends Model 
{
	public function clear()
	{
		$id = session('member_id');
		$this->where(array(
			'member_id' => $id,
		))->delete();
	}
	/**
	 * 移动数据到数据库中
	 *
	 */
	public function moveDataToDb()
	{
		$id = session('member_id');
		if($id)
		{
			// 从COOKIE中取出商品
			$cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
			// 循环每件商品插入到数据库中
			foreach ($cart as $k => $v)
			{
				$_k = explode('-', $k);
				$this->addToCart($_k[0], $_k[1], $v); // 把商品插入到数据库中
			}
			// 清空COOKIE
			setcookie('cart', '', time()-1, '/', C('CART_COOKIE_DOMAIN'));
		}
	}
	/**
	 * 修改购物车中商品的数量
	 *
	 * @param unknown_type $goodsId
	 * @param unknown_type $goodsAttrId
	 * @param unknown_type $goodsNumber ： 修改到几，<= 0：代表删除
	 */
	public function editGoodsNumber($goodsId, $goodsAttrId, $goodsNumber)
	{
		$goodsId = (int)$goodsId;
		$goodsNumber = (int)$goodsNumber;
		if($goodsId <= 0)
		{
			$this->error = '参数错误!';
			return  FALSE;
		}
		/******** 判断库存量 ***********/
		$gnModel = D('goods_number');
		//var_dump($goodsAttrId);die;
		$gn = $gnModel->field('goods_number')->where(array(
			'goods_id' => array('eq', $goodsId),
			'attr_list' => array('eq', $goodsAttrId),
		))->find();
		if(!$gn || ($gn['goods_number'] < $goodsNumber))
		{
			$this->error = '库存不足！';
			return FALSE;
		}
		$id = session('member_id');
		if($id)
		{
			if($goodsNumber <= 0)
			{
				// 如果购物车中已经有这件商品就在原数量的基础上加上这次购物的数量
				$this->where(array(
					'member_id' => array('eq', $id),
					'goods_id' => array('eq', $goodsId),
					'goods_attr_id' => array('eq', $goodsAttrId),
				))->delete();
			}
			else 
			{
				$this->where(array(
					'member_id' => $id,
					'goods_id' => $goodsId,
					'goods_attr_id' => $goodsAttrId,
				))->setField('goods_number', $goodsNumber);
			}
		}
		else 
		{
			$cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
			$key = $goodsId.'-'.$goodsAttrId;
			if($goodsNumber <= 0)
				unset($cart[$key]);
			else 
				$cart[$key] = $goodsNumber;
			$day = C('CART_COOKIE_EXPIRE');  // 天数
			$expire = time() + $day * 24 * 3600;
			setcookie('cart', serialize($cart), $expire, '/', C('CART_COOKIE_DOMAIN'));
		}
		return TRUE;
	}
	// 加入购物车
	public function addToCart($goodsId, $goodsAttrId, $goodsNumber)
	{
		$goodsId = (int)$goodsId;
		$goodsNumber = (int)$goodsNumber;
		if($goodsId <= 0 || $goodsNumber <= 0)
		{
			$this->error = '参数错误!';
			return  FALSE;
		}
		/******** 判断库存量 ***********/
		$gnModel = D('goods_number');
		//var_dump($goodsAttrId);die;
		$gn = $gnModel->field('goods_number')->where(array(
			'goods_id' => array('eq', $goodsId),
			'attr_list' => array('eq', $goodsAttrId),
		))->find();
		if(!$gn || ($gn['goods_number'] < $goodsNumber))
		{
			$this->error = '库存不足！';
			return FALSE;
		}
		// 判断会员有没有登录 ：登录=》存数据库   未登录 =》存COOKIE
		$id = session('member_id');
		if($id)
		{
			// 先判断购物车中是否有这件商品
			$has = $this->where(array(
				'member_id' => array('eq', $id),
				'goods_id' => array('eq', $goodsId),
				'goods_attr_id' => array('eq', $goodsAttrId),
			))->find();
			if($has)
			{
				// 如果购物车中已经有这件商品就在原数量的基础上加上这次购物的数量
				$this->where(array(
					'member_id' => array('eq', $id),
					'goods_id' => array('eq', $goodsId),
					'goods_attr_id' => array('eq', $goodsAttrId),
				))->setInc('goods_number', $goodsNumber);
			}
			else 
			{
				$this->add(array(
					'member_id' => $id,
					'goods_id' => $goodsId,
					'goods_attr_id' => $goodsAttrId,
					'goods_number' => $goodsNumber,
				));
			}
		}
		else 
		{
			// 未登录保存到cookie中
			// 先从COOKIE中取出这个购物车的一维数组
			$cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
			// 判断是否数组中已经有这件商品了【已经买过】
			// 先拼出数组中的下标
			$key = $goodsId.'-'.$goodsAttrId;
			// 判断这个商品的下标是否存在
			if(isset($cart[$key]))
			{
				// 购物车是已经有这件商品就把数量加上这次购买的数量
				$cart[$key] += $goodsNumber;
			}
			else 
			{
				$cart[$key] = $goodsNumber;
			}
			// 把这个一维数组再保存回COOKIE
			$day = C('CART_COOKIE_EXPIRE');  // 天数
			$expire = time() + $day * 24 * 3600;
			// 第四个参数是指哪些目录可以网站这个COOKIE，设置为/代表整个网站都能读，否则：只有定义这个COOKIE的文件所在的目录 以及子目录可以访问：
			/**
			 * 例子：
			 * /a/b/c.php    ->   setcookie('name', 'tom');
			 * /d.php        --> echo $_COOKIE['name'];  --> 读不到
			 */
			// 第五个参数：哪些域名下这个COOKIE有效：.38s.com代码所有以38s.com为根域名的都可以读这个COOKIE
			setcookie('cart', serialize($cart), $expire, '/', C('CART_COOKIE_DOMAIN'));
		}
		return TRUE;
	}
	
	// 获取当前购物车中的商品
	public function cartList()
	{
		/************************ 从购物车中取出商品的基本信息 **********************/
		$id = session('member_id');
		if($id)
		{
			$cart = $this->where(array(
				'member_id' => array('eq', $id),
			))->select();
		}
		else 
		{
			$_cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
			// 把这个一维转成二维，和上面的结构一样
			$cart = array();
			foreach ($_cart as $k => $v)
			{
				// 从下标中取出商品ID和商品属性ID
				$_k = explode('-', $k);
				$cart[] = array(
					'goods_id' => $_k[0],
					'goods_attr_id' => $_k[1],
					'goods_number' => $v,
				);
			}
		}
		/********************* 再把商品ID和商品属性ID转化成相应的详情信息 *****************/
		$goodsModel = D('Admin/Goods');
		// 循环购物车中每件商品取出详细信息
		foreach ($cart as $k => $v)
		{
			$info = $goodsModel->field('sm_logo,goods_name')->find($v['goods_id']);
			// 把名称和LOGO再放到购物车
			$cart[$k]['sm_logo'] = $info['sm_logo'];
			$cart[$k]['goods_name'] = $info['goods_name'];
			// 取出商品属性字符串并放到购物车数组中
			if($v['goods_attr_id'])
			{
				$sql = 'SELECT GROUP_CONCAT(CONCAT(b.attr_name,":",a.attr_value) SEPARATOR "<br />") goods_attr_str
				         FROM php38_goods_attr a
				          LEFT JOIN php38_attribute b ON a.attr_id=b.id
				          WHERE a.id IN('.$v['goods_attr_id'].')';
				$gastr = $goodsModel->query($sql);
				$cart[$k]['goods_attr_str'] =$gastr[0]['goods_attr_str'];
			}
			else 
				$cart[$k]['goods_attr_str'] = '';
			// 计算出商品的会员价格并放到购物车数组中
			$cart[$k]['price'] = $goodsModel->getMemberPrice($v['goods_id']);
		}
		return $cart;
	}
}













