<?php
namespace Admin\Controller;
class GoodsController extends BaseController 
{
	public function clearCache()
	{
		// 清空Html目录 
		delFile('./Application/Html/');
		// 清空临时图片【当前的目录不删除】
		delFile('./Public/Uploads/Temp/', date('Y-m-d'));
		$this->success('缓存已经删除成功！', U('Index/index'));
		exit;
	}
	public function ajaxDelGoodsAttr()
	{
		$goodsAttrId = I('get.goods_attr_id');
		// 先判断这个属性ID有没有被设置库存量
		$gnModel = D('goods_number');
		$id = (int)$goodsAttrId;  // 防SQL注入
		$has = $gnModel->where("FIND_IN_SET($id,attr_list)")->count();
		if($has > 0)
			echo 1;  // 有库存量
		else 
		{
			$gaModel = D('goods_attr');
			$gaModel->delete($id);
			echo 0;
		}
	}
	public function goods_number()
	{
		$id = I('get.id'); // 接收商品ID
		if(IS_POST)
		{
			// 循环库存量数组插入到表中
			$gnModel = D('goods_number');
			// 先清空之前的库存量
			$gnModel->where(array(
				'goods_id' => array('eq', $id),
			))->delete();
			$gai = I('post.goods_attr_id');
			$gn = I('post.gn');
			// 计算商品属性ID和库存量的比例
			$rate = count($gai) / count($gn);
			$_i = 0; // 从第几个商品属性ID中取ID
			foreach ($gn as $k => $v)
			{
				// 从商品属性ID的数组中取出 $rate 个id
				$_arr = array(); // 存入取出来的商品属性ID
				$isEmpty = FALSE; // 是否有一个属性没选
				for($i=0; $i<$rate; $i++) // 循环几次
				{
					// 如果商品有属性，那么再判断对应的这个属性是否为空
					if($gai && empty($gai[$_i]))
						$isEmpty = TRUE;  // 标识为空
					$_arr[] = $gai[$_i++];  // 一次拿一个
				}
				$_v = (int)$v;
				// 如果价格不是数字，或者有一个属性为空就跳过这条记录
				if($_v <= 0 || $isEmpty === TRUE)
					continue;
				
				sort($_arr, SORT_NUMERIC); // 把商品属性ID升降
				$_att = implode(',', $_arr);
				$gnModel->add(array(
					'goods_id' => $id,
					'goods_number' => $_v,
					'attr_list' => $_att,
				));
			}
			$this->success('修改成功！', U('lst?p='.I('get.p')));
			exit;
		}
		// 根据商品ID取出这件商品所有的可选属性的名称和值
		$gaModel = D('goods_attr');
		$gaData = $gaModel->alias('a')
		->field('a.*,b.attr_name')
		->join('LEFT JOIN php38_attribute b ON a.attr_id=b.id')
		->where(array(
			'a.goods_id' => array('eq', $id),
			'b.attr_type' => array('eq', '可选'),
		))->select();
		// 把二维转三维【把属性相同的放到一起】
		$_gaData = array();
		foreach ($gaData as $k => $v)
		{
			$_gaData[$v['attr_name']][] = $v;
		}
		// 先取出之前设置的库存量数据
		$gnModel = D('goods_number');
		$gnData = $gnModel->where(array(
			'goods_id' => array('eq', $id),
		))->select();
		//var_dump($gnData);
		
		// 设置页面信息
		$this->assign(array(
			'gaData' => $_gaData,
			'gnData' => $gnData,
			'_page_title' => '库存明细',
			'_page_btn_name' => '商品列表',
			'_page_btn_link' => U('lst?p='.I('get.p')),
		));
		$this->display();
	}
	public function ajaxGetAttr()
	{
		$typeId = I('get.type_id');
		if((int)$typeId == 0)
		{
			echo json_encode(array(
				'ok' => 0,
				'error' => '参数不正确！',
			));
			exit;
		}
		// 根据ID取出属性
		$attrModel = D('Attribute');
		$attrData = $attrModel->where(array(
			'type_id' => array('eq', $typeId)
		))->select();
		echo json_encode($attrData);
	}
	// 删除
	public function delete()
	{
		// 接收商品ID
		$id = I('get.id');
		$model = D('Goods');
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
		$model = D('Goods');
		// 获取数据以及翻页字符串
		$data = $model->search();
		$this->assign(array(
			'data' => $data['data'],
			'page' => $data['page'],
		));
		//$this->assign('data', $data['data']);
		//$this->assign('page', $data['page']);
		
		// 取出商品分类制作下拉框
		$catModel = D('Category');
		$catData = $catModel->getTree();
		
		// 设置页面信息
		$this->assign(array(
			'catData' => $catData,
			'_page_title' => '商品列表',
			'_page_btn_name' => '添加商品',
			'_page_btn_link' => U('add'),
		));
		$this->display();
	}
	// 添加
	public function add()
	{
		// IF里处理表单
		if(IS_POST)
		{
			//var_dump($_POST);die;
			// 2. 生成模型
			$model = D('Goods');
			// 3. 接收表单，根据模型中定义的规则验证表单
			// 第二个参数：1.添加 2.修改
			if($model->create(I('post.'), 1))
			{
				// 6. 表单中的数据插入到数据库中
				if($model->add())
				{
					// 7. 提示成功的信息,并且在1秒之后跳转到商品列表页？
					$this->success('添加成功！', U('lst'));
					// 8. 停止后面代码的执行
					exit;
				}
			}
			// 4. 获取失败的原因
			$error = $model->getError();
			// 5. 打印失败原因,并且在3秒之后跳回上一个页面
			$this->error($error);
		}
		
		// 取出所有的分类制作下拉框
		$catModel = D('Category');
		$catData = $catModel->getTree();
		// 取出所有的会员级别
		$mlModel = D('member_level');
		$mlData = $mlModel->select();
		
		// 设置页面信息
		$this->assign(array(
			'mlData' => $mlData,
			'catData' => $catData,
			'_page_title' => '添加商品',
			'_page_btn_name' => '商品列表',
			'_page_btn_link' => U('lst'),
		));
		// 1. 显示添加商品的表单
		$this->display();
	}
	// 修改
	public function edit()
	{
		// IF里处理表单
		if(IS_POST)
		{
			//var_dump($_POST);die;
			// 2. 生成模型
			$model = D('Goods');
			// 3. 接收表单，根据模型中定义的规则验证表单
			// 第二个参数：1.添加 2.修改
			if($model->create(I('post.'), 2))
			{
				// 6. 修改数据
				// 返回值：save返回值是mysql_affected_rows：影响的条件数，如果一件商品原名叫abc,修改之后还叫abc,返回0， 失败时返回FALSE
				if(FALSE !== $model->save())
				{
					// 7. 提示成功的信息,并且在1秒之后跳转到商品列表页？
					$this->success('修改成功！', U('lst?p='.I('get.p')));
					// 8. 停止后面代码的执行
					exit;
				}
			}
			// 4. 获取失败的原因
			$error = $model->getError();
			// 5. 打印失败原因,并且在3秒之后跳回上一个页面
			$this->error($error);
		}
		// 先取出要修改的商品的信息
		$id = I('get.id');  // 接收商品ID
		$model = M('Goods');
		$info = $model->find($id);  // 根据ID取出商品的信息
		$this->assign('info', $info);  // 分配到修改的表单
		
		// 取出所有的分类制作下拉框
		$catModel = D('Category');
		$catData = $catModel->getTree();
		// 取出这件商品已经设置的扩展分类
		$gcModel = M('goods_ext_cat');
		$gcData = $gcModel->field('cat_id')->where(array(
			'goods_id' => array('eq', $id),
		))->select();
		// 取出之前设置的相册图片
		$gpModel = D('goods_pics');
		$gpData = $gpModel->where(array(
			'goods_id' => array('eq', $id),
		))->select();
		// 取出所有的会员级别
		$mlModel = D('member_level');
		$mlData = $mlModel->select();
		// 取出之前设置的会员价格
		$mpModel = D('member_price');
		$_mpData = $mpModel->where(array(
			'goods_id' => array('eq', $id),
		))->select();
		// 二维转一维
		$mpData = array();
		foreach ($_mpData as $k => $v)
		{
			$mpData[$v['level_id']] = $v['price'];
		}
		//var_dump($gcData);
		
		// 取出当前这件商品所在类型下的属性
		// 再连表取出每个属性已经设置的值
		$attrModel = D('Attribute');
		$attrData = $attrModel->alias('a')
		->field('a.*,b.id goods_attr_id,b.attr_value')
		->join('php38_goods_attr b ON (a.id=b.attr_id AND b.goods_id='.$id.')')
		->where(array(
			'a.type_id' => $info['type_id'],
		))
		->order('a.id ASC,b.id ASC')
		->select();
		
		
		// 设置页面信息
		$this->assign(array(
			'attrData' => $attrData,
			'mpData' => $mpData,
			'mlData' => $mlData,
			'gpData' => $gpData,
			'gcData' => $gcData,
			'catData' => $catData,
			'_page_title' => '修改商品',
			'_page_btn_name' => '商品列表',
			'_page_btn_link' => U('lst?p='.I('get.p')),
		));
		// 1. 显示添加商品的表单
		$this->display();
	}
	public function ajaxDelPic()
	{
		$picId = I('get.pic_id');
		// 删除图片
		$gpModel = D('goods_pics');
		// 从硬盘上把图片删除掉
		$p = $gpModel->find($picId);
		unlink('./Public/Uploads/'.$p['pic']);
		unlink('./Public/Uploads/'.$p['sm_pic']);
		unlink('./Public/Uploads/'.$p['mid_pic']);
		// 从数据库中把记录删除
		$gpModel->delete($picId);
	}
}













