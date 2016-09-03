<?php
namespace Home\Model;
use Think\Model;
class MemberShrModel extends Model 
{
	protected $_validate = array(
		array('shr_name', 'require', '收货人姓名不能为空！', 1),
		array('shr_province', 'require', '收货人省不能为空！', 1),
		array('shr_city', 'require', '收货人城市不能为空！', 1),
		array('shr_area', 'require', '收货人地区不能为空！', 1),
		array('shr_address', 'require', '收货人详细地址不能为空！', 1),
		array('shr_tel', 'require', '收货人电话不能为空！', 1),
	);
}













