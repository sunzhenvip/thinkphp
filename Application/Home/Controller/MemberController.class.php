<?php
namespace Home\Controller;
use Think\Controller;
class MemberController extends Controller 
{
	private $_memberId;
	// 最早自动执行的方法，在这里进行登录的验证【只能登录才能访问这个控制器中的方法】
	public function __construct()
	{
		parent::__construct();	
		$this->_memberId = session('member_id');
		if(!$this->_memberId)
		{
			// 把要访问的地址保存到session中，这样登录成功之后会跳回来
			session('returnUrl', U(CONTROLLER_NAME.'/'.ACTION_NAME));
			$this->error('请先登录！', U('Login/login'));
		}
	}
	public function set_face()
	{
		// 把上传的头像上传到Temp临时目录中并生成一个缩略图
		$ret = uploadOne('face', 'Temp', array(array(
			500, 500
		)));
		// 把原图删除掉只留一个缩略图
		unlink("./Public/Uploads/{$ret['images'][0]}");
		if($ret['ok'] == 1)
		{
			// 拼出要显示出图片的img标签
			$img = "<img id='face_img' src='/Public/Uploads/{$ret['images'][1]}' />";
			// 在iframe窗口中输出一段JS代码，把这个图片显示在父窗口中并使用jcrop插件裁切图片
			echo "<script>parent.$('input[name=img]').val('{$ret['images'][1]}');parent.document.getElementById('div_image').innerHTML=\"$img\";parent.$('#face_img').Jcrop({onSelect:parent.setPoint});</script>";
		}
	}
	// 设置头像
	public function face()
	{
		if(IS_POST)
		{
			// 裁切图片
			$image = new \Think\Image(); 
			$img = I('post.img');
			$image->open('./Public/Uploads/'.$img);
			// 把路径中的Temp替换成Member目录
			$img = str_replace('Temp', 'Member', $img);
			// 在Member目录下生成当天的日期的目录 
			$date = date('Y-m-d');
			if(!is_dir('./Public/Uploads/Member/'.$date))
				mkdir('./Public/Uploads/Member/'.$date, 0777);
			//裁切的图片保存到Member目录下
			$image->crop(I('post.w'), I('post.h'),I('post.x'),I('post.y'))->save('./Public/Uploads/'.$img);
			// 设置会员头像
			$member = D('Member');
			$member->where(array(
				'id' => array('eq', $this->_memberId)
			))->setField('face', $img);
			$this->success('成功！');
			exit;
		}
		$member = D('Member');
		$face = $member->field('face')->find($this->_memberId);
		// 设置页面信息
    	$this->assign(array(
    		'face' => $face['face'],
    		'_page_title' => '设置头像',
    		'_page_keywords' => '设置头像',
    		'_page_description' => '设置头像',
    		'_page_hide_nav' => 1,
    	));
		$this->display();
	}
	// 我的定单
	public function order()
	{
		// 取出我的定单
		$order = D('Admin/Order');
		$data = $order->getMyOrder($this->_memberId);
		// 设置页面信息
    	$this->assign(array(
    		'data' => $data['data'],
    		'page' => $data['page'],
    		'_page_title' => '我的定单',
    		'_page_keywords' => '我的定单',
    		'_page_description' => '我的定单',
    		'_page_hide_nav' => 1,
    	));
		$this->display();
	}
}