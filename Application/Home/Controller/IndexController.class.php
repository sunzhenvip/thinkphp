<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller 
{
	/**
	 * 获取商品
	 * 
	 * 参数1：goods_id
	 * 参数2：p : 页码，不传代表1
	 *
	 */
	public function ajaxGetComment()
	{
		$goodsId = I('get.goods_id');
		$model = D('Admin/Comment');
		$data = $model->get_comment($goodsId);
		$this->success($data, '', TRUE);  // 返回json
	}
	public function ajaxComment()
	{
		if(IS_POST)
		{
			$model = D('Admin/Comment');
			if($model->create(I('post.'), 1))
			{
				if($model->add())
				{
					$member = D('Member');
					$face = $member->field('face')->find(session('member_id'));
					if($face['face'])
						$face = '/Public/Uploads/'.$face['face'];
					else 
						$face = '/Public/Home/images/taohua.jpg';
					$this->success(array(
						'username' => addStarOnEmail(session('member_email')),  // 把邮件名称加*****
						'face' => $face,
					), '', TRUE);   // 返回一个AJAX
				}
			}
			$this->error($model->getError(), '', TRUE);
		}
	}
	public function ajaxChkPinglun()
	{
		$member = D('Admin/Member');
		if($member->isBuy(I('get.goods_id')))
			echo 1;
	}
	public function ajaxMemberPrice()
	{
		$id = I('get.id');
		$goodsModel = D('Admin/Goods');
		echo $goodsModel->getMemberPrice($id);
	}
	// 首页
    public function index()
    {
    	$goodsModel = D('Admin/Goods');
    	$catModel = D('Admin/Category');
    	// 获取疯狂抢购
    	$goods1 = $goodsModel->getPromoteGoods();
    	$goods2 = $goodsModel->getRecGoods('hot');
    	$goods3 = $goodsModel->getRecGoods('rec');
    	$goods4 = $goodsModel->getRecGoods('new');
    	// 取出中间推荐楼层
    	$floorData = $catModel->getFloorCatData();
    	
    	$this->assign(array(
    		'goods1' => $goods1,
    		'goods2' => $goods2,
    		'goods3' => $goods3,
    		'goods4' => $goods4,
    		'floorData' => $floorData,
    	));
    	
    	// 设置页面信息
    	$this->assign(array(
    		'_page_title' => '首页',
    		'_page_keywords' => '首页',
    		'_page_description' => '首页',
    		'_page_hide_nav' => 0,  // 展开
    	));
		$this->display();
    }
    // 商品详情页
    public function goods()
    {
    	$id = I('get.id');
    	// 商品基本信息
    	$gModel = D('Goods');
    	$info = $gModel->find($id);
    	// 商品相册
    	$gpModel = D('goods_pics');
    	$picData = $gpModel->field('mid_pic')->where(array(
    		'goods_id' => array('eq', $id),
    	))->select();
    	// 取出商品属性
    	$gaModel = D('goods_attr');
    	$gaData = $gaModel->alias('a')
    	->field('a.*,b.attr_name,b.attr_type')
    	->join('LEFT JOIN php38_attribute b ON a.attr_id=b.id')
    	->where(array(
    		'a.goods_id' => array('eq', $id),
    	))->select();
    	// 循环所有的商品属性分为两级
    	$_uni = array(); // 唯一
    	$_mul = array();  // 可选
    	foreach ($gaData as $k => $v)
    	{
    		if($v['attr_type'] == '唯一')
    			$_uni[$v['attr_name']] = $v['attr_value'];
    		else 
    			$_mul[$v['attr_name']][$v['id']] = $v['attr_value'];
    	}
    	// 计算所在分类的导航-> 以主分类为主
    	$catModel = D('Admin/Category');
    	$catPath = $catModel->getCatPath($info['cat_id']);
    	
    	// 设置页面信息
    	$this->assign(array(
    		'catPath' => $catPath,
    		'uni' => $_uni,
    		'mul' => $_mul,
    		'info' => $info,
    		'picData' => $picData,
    		'_page_title' => '商品详情页',
    		'_page_keywords' => '商品详情页',
    		'_page_description' => '商品详情页',
    		'_page_hide_nav' => 1,  // 隐藏导航分类
    	));
		$this->display();
    }
}