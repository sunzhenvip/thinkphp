<?php
namespace Admin\Model;
use Think\Model;
class CategoryModel extends Model 
{
	// 设置添加时表单中允许接收的字段【安全】
	protected $insertFields = 'cat_name,parent_id,is_floor';
	// 设置修改的表单中允许出现的字段
	protected $updateFields = 'id,cat_name,parent_id,is_floor';
	// 定义表单验证的规则
	protected $_validate = array(
		array('cat_name', 'require', '分类名称不能为空！', 1),
	);
	
	/****************** 打印树形结构 ***************************/
	public function getTree()
	{
		$id = session('id');
		if($id == 1)
			$data = $this->select();
		else 
			// 取出当前管理员有权限访问的分类
			$data = $this->where("id IN(SELECT cat_id FROM php38_admin_goods_cat WHERE admin_id=$id)")->select();
		// 递归重新排序数据
		return $this->_getTree($data);
	}
	/**
	 * 排序
	 *
	 * @param unknown_type $data ： 要排序的数据
	 * @param unknown_type $parent_id ： 从第几级开始排序 默认从顶级
	 * @param unknown_type $level ： 标记每个分类是第几级的 0：顶级
	 */
	protected function _getTree($data, $parent_id=0, $level=0)
	{
		static $_ret = array(); // 保存排序好的结果的数组
		foreach ($data as $k => $v)
		{
			if($v['parent_id'] == $parent_id)
			{
				// 为这个分类添加一个level字段，标记这个分类是第几级的
				$v['level'] = $level;
				$_ret[] = $v;
				// 找这个$v的子分类
				$this->_getTree($data, $v['id'], $level+1);
			}
		}
		return $_ret;
	}
	
	/**
	 * 获取一个分类到顶级的路径
	 *
	 * @param unknown_type $catId
	 */
	public function getCatPath($catId)
	{
		static $ret = array();
		$cat = $this->field('id,cat_name,parent_id')->find($catId);
		$ret[] = $cat;
		// 判断是否有上级
		if($cat['parent_id'] > 0)
			$this->getCatPath($cat['parent_id']);
		return $ret;
	}
	
	/****************** 找子分类的方法 ***************************/
	/**
	 * 找所有的子分类的ID
	 *
	 * @param unknown_type $catId ： 父分类的ID
	 */
	public function getChildren($catId)
	{
		// 取出所有的分类
		$data = $this->select();
		// 先清空再递归找子分类
		return $this->_getChildren($data, $catId, TRUE);
	}
	protected function _getChildren($data, $parent_id, $isClear=FALSE)
	{
		static $_ret = array();  // 保存找到的子分类的ID
		// 如果是新的查询就清空,递归过程中不清空
		if($isClear)
			$_ret = array();  // 清空这个数组
		foreach ($data as $k => $v)
		{
			if($v['parent_id'] == $parent_id)
			{
				// 把这个子分类的ID放到数组中
				$_ret[] = $v['id'];
				// 再找这个分类的子分类
				$this->_getChildren($data, $v['id']);
			}
		}
		return $_ret;
	}
	protected function _before_update(&$data, $option)
	{
		if(empty($data['is_floor']))
			$data['is_floor'] = '否';
	}
	// 在调用delete之前先自动执行
	protected function _before_delete($option)
	{
		/************ 如果一个分类有子分类就不允许删除 ***********/
		$children = $this->getChildren(I('get.id'));
		if($children)
		{
			$this->error = '有子分类无法删除!';
			return FALSE; // 阻止删除
		}
		/************** 删除一个分类同时删除子分类 
		$children = $this->getChildren(I('get.id'));
		if($children)
		{
			$children = implode(',', $children);  // 转化成字符串
			// $this->delete($children); ---> 注意：这里如果delete就死循环了！！所以不能delete只能执行SQL语句删除
			$this->execute("DELETE FROM php38_category WHERE id IN($children)");
		}
		**/
	}
	/**
	 * 取出前台导航条上的分类数据
	 *
	 */
	public function getNavData()
	{
		// 取出所有的分类
		$all = $this->select();
		$ret = array(); 
		// 挑出顶级分类
		foreach ($all as $k => $v)
		{
			if($v['parent_id'] == 0)
			{
				// 再挑出这个顶级的子级
				foreach ($all as $k1 => $v1)
				{
					if($v1['parent_id'] == $v['id'])
					{
						// 再挑出这个二级的子级
						foreach ($all as $k2 => $v2)
						{
							if($v2['parent_id'] == $v1['id'])
							{
								// 存到上级的children字段
								$v1['children'][] = $v2;
							}
						}
						// 存到上级的children字段
						$v['children'][] = $v1;
					}
				}
				$ret[] = $v;
			}
		}
		return $ret;
	}
	/**
	 * 获取首页推荐的楼层数据
	 *
	 * @return unknown
	 */
	public function getFloorCatData()
	{
		$goodsModel = D('Admin/Goods');
		// 取出所有的分类
		$all = $this->select();
		$ret = array();
		foreach ($all as $k => $v)
		{
			// 挑出被推荐的顶级分类
			if($v['parent_id'] == 0 && $v['is_floor'] == '是')
			{
				foreach ($all as $k1 => $v1)
				{
					// 挑出没有被推荐的二级分类
					if($v1['parent_id'] == $v['id'] && $v1['is_floor'] == '否')
					{
						$v['normalSubCat'][] = $v1;
					}
					// 挑出5个被推荐的二级分类
					$_i = 0; // 取几个了
					if($_i < 5 && $v1['parent_id'] == $v['id'] && $v1['is_floor'] == '是')
					{
						// 再取出这个二级分类下的8个被推荐的商品
						$v1['recGoods'] = $goodsModel->getGoodsByCatId($v1['id'], 8, array('is_floor' => array('eq', '是')));
						$v['recSubCat'][] = $v1;
						$_i++;
					}
				}
				
				$ret[] = $v;
			}
		}
		return $ret;
	}
}













