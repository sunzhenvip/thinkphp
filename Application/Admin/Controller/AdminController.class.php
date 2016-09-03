<?php
namespace Admin\Controller;
class AdminController extends BaseController 
{
    public function add()
    {
    	if(IS_POST)
    	{
    		$model = D('Admin');
    		if($model->create(I('post.'), 1))
    		{
    			if($id = $model->add())
    			{
    				$this->success('添加成功！', U('lst?p='.I('get.p')));
    				exit;
    			}
    		}
    		$this->error($model->getError());
    	}
    	
    	// 取出所有的角色
    	$roleModel = D('Role');
    	$roleData = $roleModel->select();
    	// 取出所有的商品分类
    	$catModel = D('Category');
    	$catData = $catModel->getTree();

		// 设置页面中的信息
		$this->assign(array(
			'roleData' => $roleData,
			'catData' => $catData,
			'_page_title' => '添加管理员',
			'_page_btn_name' => '管理员列表',
			'_page_btn_link' => U('lst'),
		));
		$this->display();
    }
    public function edit()
    {
    	$id = I('get.id');
    	if(IS_POST)
    	{
    		$model = D('Admin');
    		if($model->create(I('post.'), 2))
    		{
    			if($model->save() !== FALSE)
    			{
    				$this->success('修改成功！', U('lst', array('p' => I('get.p', 1))));
    				exit;
    			}
    		}
    		$this->error($model->getError());
    	}
    	$model = M('Admin');
    	$data = $model->find($id);
    	$this->assign('data', $data);
    	
    	// 取出所有的角色
    	$roleModel = D('Role');
    	$roleData = $roleModel->select();
    	// 取出所有的商品分类
    	$catModel = D('Category');
    	$catData = $catModel->getTree();
    	// 取出当前管理员所在的角色ID
    	$arModel = M('admin_role');
    	$rids = $arModel->field('GROUP_CONCAT(role_id) rids')->where(array(
    		'admin_id' => array('eq', $id),
    	))->select();
    	// 取出当前管理员可以管理的商品分类ID
    	$agcModel = M('admin_goods_cat');
    	$cids = $agcModel->field('GROUP_CONCAT(cat_id) cids')->where(array(
    		'admin_id' => array('eq', $id),
    	))->select();
    	
		// 设置页面中的信息
		$this->assign(array(
			'rids' => $rids[0]['rids'],
			'cids' => $cids[0]['cids'],
			'roleData' => $roleData,
			'catData' => $catData,
			'_page_title' => '修改管理员',
			'_page_btn_name' => '管理员列表',
			'_page_btn_link' => U('lst'),
		));
		$this->display();
    }
    public function delete()
    {
    	$model = D('Admin');
    	if($model->delete(I('get.id', 0)) !== FALSE)
    	{
    		$this->success('删除成功！', U('lst', array('p' => I('get.p', 1))));
    		exit;
    	}
    	else 
    	{
    		$this->error($model->getError());
    	}
    }
    public function lst()
    {
    	$model = D('Admin');
    	$data = $model->search();
    	$this->assign(array(
    		'data' => $data['data'],
    		'page' => $data['page'],
    	));

		// 设置页面中的信息
		$this->assign(array(
			'_page_title' => '管理员列表',
			'_page_btn_name' => '添加管理员',
			'_page_btn_link' => U('add'),
		));
    	$this->display();
    }
}