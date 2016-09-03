<?php
namespace Admin\Model;
use Think\Model;
class CommentModel extends Model 
{
	protected $insertFields = array('member_id','goods_id','content','star');
	protected $updateFields = array('id','member_id','goods_id','content','star');
	protected $_validate = array(
		array('goods_id', 'require', '商品id不能为空！', 1, 'regex', 3),
		array('goods_id', 'number', '商品id必须是一个整数！', 1, 'regex', 3),
		array('content', 'require', '评论内容不能为空！', 1, 'regex', 3),
		array('content', '1,200', '评论内容的值最长不能超过 200 个字符！', 1, 'length', 3),
		array('star', 'number', '评分分值必须是一个整数！', 2, 'regex', 3),
	);
	public function search($pageSize = 20)
	{
		/**************************************** 搜索 ****************************************/
		$where = array();
		if($member_id = I('get.member_id'))
			$where['member_id'] = array('eq', $member_id);
		if($goods_id = I('get.goods_id'))
			$where['goods_id'] = array('eq', $goods_id);
		if($addtime = I('get.addtime'))
			$where['addtime'] = array('eq', $addtime);
		if($content = I('get.content'))
			$where['content'] = array('like', "%$content%");
		if($star = I('get.star'))
			$where['star'] = array('eq', $star);
		/************************************* 翻页 ****************************************/
		$count = $this->alias('a')->where($where)->count();
		$page = new \Think\Page($count, $pageSize);
		// 配置翻页的样式
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$data['page'] = $page->show();
		/************************************** 取数据 ******************************************/
		$data['data'] = $this->alias('a')->where($where)->group('a.id')->limit($page->firstRow.','.$page->listRows)->select();
		return $data;
	}
	/**
	 * 获取某件商品所有的评论
	 *
	 * @param unknown_type $goodsId
	 */
	public function get_comment($goodsId)
	{
		$where = array(
			'a.goods_id' => array('eq', $goodsId),
		);
		/*********** 统计好评率和印象数据 ****************/
		// 获取当前页码
		$p = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
		if($p == 1)
		{
			/************** 统计 好评率 *************/
			$allComment = $this->alias('a')->field('star')->where($where)->select();
			$hao = 0;
			$zhong = 0;
			$cha = 0;
			foreach ($allComment as $k => $v)
			{
				if($v['star'] >= 4)
					$hao++;
				elseif ($v['star'] == 3)
					$zhong++;
				else 
					$cha++;
			}
			$total = $hao + $zhong + $cha;
			$hao = round($hao / $total * 100, 2);  // 四舍五入保留2位小数
			$zhong = round($zhong / $total * 100, 2);  // 四舍五入保留2位小数
			$cha = round($cha / $total * 100, 2);  // 四舍五入保留2位小数
			/*********** 取印象数据 **************/
			$yxModel = D('Yinxiang');
			$yxData = $yxModel->alias('a')->where($where)->select();
			/********** 是否显示表单 **********/
			$member = D('Admin/Member');
			$isBuy = (int)$member->isBuy($goodsId);
		}
		
		/************* 取数据 **************/
		/********** 翻页 ***********/
		$perpage = 5; // 每页条数
		$count = $this->alias('a')->where($where)->count();  // 取总记录数
		$page = new \Think\Page($count, $perpage);
		$data = $this->alias('a')
		->field('a.*,b.face,b.email')
		->join('LEFT JOIN php38_member b ON a.member_id=b.id')
		->where($where)
		->limit($page->firstRow.','.$page->listRows)
		->order('a.id DESC')
		->select();
		// 循环评论的数据处理email和头像
		foreach ($data as $k => $v)
		{
			$data[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
			$data[$k]['email'] = addStarOnEmail($v['email']);
			if($v['face'])
				$data[$k]['face'] = '/Public/Uploads/'.$v['face'];
			else 
				$data[$k]['face'] = '/Public/Home/images/taohua.jpg';
		}
		
		/************ 返回数据 *************/
		return array(
			'hao' => $hao,
			'zhong' => $zhong,
			'cha' => $cha,
			'yx' => $yxData,
			'data' => $data,
			'hao' => $hao,
			'pageNumber' => ceil($count/$perpage), // 总页数
			'can_comment' => $isBuy,
		);
	}
	// 添加前
	protected function _before_insert(&$data, $option)
	{
		// 会员是否可以评论
		$member = D('Admin/Member');
		if(!$member->isBuy($data['goods_id']))
		{
			$this->error = '无权评论！';
			return  FALSE;
		}
		$data['member_id'] = session('member_id');
		$data['addtime'] = time();
	}
	// 修改前
	protected function _before_update(&$data, $option)
	{
	}
	// 删除前
	protected function _before_delete($option)
	{
		if(is_array($option['where']['id']))
		{
			$this->error = '不支持批量删除';
			return FALSE;
		}
	}
	/************************************ 其他方法 ********************************************/
}