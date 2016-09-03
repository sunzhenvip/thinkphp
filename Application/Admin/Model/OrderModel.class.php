<?php
namespace Admin\Model;
use Think\Model;
class OrderModel extends Model 
{
	public function getMyOrder($memberId)
	{
		$where['member_id'] = array('eq', $memberId);
		/********************* 翻页 ********************/
		$count = $this->where($where)->count();
		$page = new \Think\Page($count, 20);
		// 配置翻页的样式
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$pageString = $page->show();
		
		$data = $this
		->limit($page->firstRow.','.$page->listRows)
		->where($where)
		->order('id DESC')
		->select();
		
		return array(
			'data' => $data,
			'page' => $pageString,
		);
	}
	// 设置为已支付状态 
	public function setPaid($orderId)
	{
		// 取出定单的基本信息
		$info = $this->field('member_id,total_price,pay_status')->find($orderId);
		// 判断如果定单还没有支付那么就执行以下代码否则跳过【同一个包可能接收到时多次】
		if($info['pay_status'] == 0)
		{
			// 更新定单状态
			$this->where(array(
				'id' => array('eq', $orderId),
			))->save(array(
				'pay_status' => 1,
				'pay_time' => time(), // 支付时间
			));
			// 增加会员的积分和经验值
			$memberModel = D('Admin/Member');
			$memberModel->updateJifenAndJyz($info['member_id'], $info['total_price'], $info['total_price']);
		}
	}
	// 设置定单为不能再申请退款的状态 
	public function setNoRefund($orderId)
	{
		
	}
}