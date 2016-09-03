<?php
namespace Admin\Controller;
class CategoryController extends BaseController 
{
	// 删除
	public function delete()
	{
		// 接收商品ID
		$id = I('get.id');
		$model = D('Category');
		if(FALSE !== $model->delete($id))
		{
			$this->success('删除成功！');
			exit;
		}
		$this->error($model->getError());
	}
	// 列表页
	public function lst()
	{
		$model = D('Category');
		$data = $model->getTree();
		$this->assign('data', $data);
		
		// 设置页面信息
		$this->assign(array(
			'_page_title' => '分类列表',
			'_page_btn_name' => '添加分类',
			'_page_btn_link' => U('add'),
		));
		$this->display();
	}
	// 添加
	public function add()
	{
		if(IS_POST)
		{
			$model = D('Category');
			if($model->create(I('post.'), 1))
			{
				if($model->add())
				{
					$this->success('添加成功！', U('lst'));
					exit;
				}
			}
			$error = $model->getError();
			$this->error($error);
		}
		
		// 取出所有的分类制作下拉框
		$catModel = D('Category');
		$catData = $catModel->getTree();
		
		// 设置页面信息
		$this->assign(array(
			'catData' => $catData,
			'_page_title' => '添加分类',
			'_page_btn_name' => '分类列表',
			'_page_btn_link' => U('lst'),
		));
		// 1. 显示添加商品的表单
		$this->display();
	}
	// 修改
	public function edit()
	{
		if(IS_POST)
		{
			$model = D('Category');
			if($model->create(I('post.'), 2))
			{
				if(FALSE !== $model->save())
				{
					$this->success('修改成功！', U('lst?p='.I('get.p')));
					exit;
				}
			}
			$error = $model->getError();
			$this->error($error);
		}
		$id = I('get.id');  // 接收分类id
		$model = M('Category');
		$info = $model->find($id);  // 根据ID取出分类的信息
		$this->assign('info', $info);  // 分配到修改的表单
		
		// 取出所有的分类制作下拉框
		$catModel = D('Category');
		$catData = $catModel->getTree();
		
		// 取出当前分类的子分类
		$children = $catModel->getChildren($id);
		
		// 设置页面信息
		$this->assign(array(
			'children' => $children,
			'catData' => $catData,
			'_page_title' => '修改分类',
			'_page_btn_name' => '分类列表',
			'_page_btn_link' => U('lst?p='.I('get.p')),
		));
		// 1. 显示添加商品的表单
		$this->display();
	}
}