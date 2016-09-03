<?php
namespace Admin\Model;
use Think\Model;
class PrivilegeModel extends Model 
{
	protected $insertFields = array('pri_name','module_name','controller_name','action_name','parent_id');
	protected $updateFields = array('id','pri_name','module_name','controller_name','action_name','parent_id');
	protected $_validate = array(
		array('pri_name', 'require', '权限名称不能为空！', 1, 'regex', 3),
		array('pri_name', '1,150', '权限名称的值最长不能超过 150 个字符！', 1, 'length', 3),
		array('module_name', '1,30', '模块名称的值最长不能超过 30 个字符！', 2, 'length', 3),
		array('controller_name', '1,30', '控制器名称的值最长不能超过 30 个字符！', 2, 'length', 3),
		array('action_name', '1,30', '方法名称的值最长不能超过 30 个字符！', 2, 'length', 3),
		array('parent_id', 'number', '上级权限ID，0：代表顶级分类必须是一个整数！', 2, 'regex', 3),
	);
	/************************************* 递归相关方法 *************************************/
	public function getTree()
	{
		$data = $this->select();
		return $this->_reSort($data);
	}
	private function _reSort($data, $parent_id=0, $level=0, $isClear=TRUE)
	{
		static $ret = array();
		if($isClear)
			$ret = array();
		foreach ($data as $k => $v)
		{
			if($v['parent_id'] == $parent_id)
			{
				$v['level'] = $level;
				$ret[] = $v;
				$this->_reSort($data, $v['id'], $level+1, FALSE);
			}
		}
		return $ret;
	}
	public function getChildren($id)
	{
		$data = $this->select();
		return $this->_children($data, $id);
	}
	// 
	public function hasPriToEditGoods($goodsId)
	{
		$adminId = session('id');
		if($adminId == 1)
			return TRUE;
		$model = D('Goods');
		$admin_id = $model->field('admin_id')->find($goodsId);
		return ($admin_id['admin_id'] == $adminId);
	}
	private function _children($data, $parent_id=0, $isClear=TRUE)
	{
		static $ret = array();
		if($isClear)
			$ret = array();
		foreach ($data as $k => $v)
		{
			if($v['parent_id'] == $parent_id)
			{
				$ret[] = $v['id'];
				$this->_children($data, $v['id'], FALSE);
			}
		}
		return $ret;
	}
	// 判断管理员是否有权限访问这个页面
	public function hasPri()
	{
		// 获取管理员ID
		$id = session('id');
		if($id == 1)
			return TRUE;
		// 如果访问的是后台首页直接返回TRUE【所有管理员都可以进入后台首页】
		if(CONTROLLER_NAME == 'Index')
			return TRUE;
		// 如果是普通管理员，获取当前访问的模块名[MODULE_NAME]，控制器名[CONTROLLER_NAME]，方法名[ACTION_NAME]
		// 查询这个管理员有没有一个权限对应这个页面
		// SQL流程：1.根据管理员的ID取出这个管理员所在的角色ID
		//         2.再根据这些角色ID取出这些角色所拥有的权限的ID
		//         3.再根据权限ID到权限权限查询出有没有对应这个页面的权限
		$sql = 'SELECT count(*) has
				 FROM php38_admin_role a 
				  LEFT JOIN php38_role_pri b ON a.role_id=b.role_id 
				  LEFT JOIN php38_privilege c ON b.pri_id=c.id
				  WHERE a.admin_id='.$id
				  .' AND c.module_name="'.MODULE_NAME.'" '
				  .' AND c.controller_name="'.CONTROLLER_NAME.'"'
				  .' AND c.action_name="'.ACTION_NAME.'"';
		$has = $this->query($sql);
		return ($has[0]['has'] >= 1);
	}
	// 获取当前管理员前两级的权限
	public function getBtns()
	{
		$id = session('id');
		/************** 先取出这个管理员所拥有的所有的权限 *****************/
		if($id == 1)
			$allPriData = $this->select();
		else 
		{
			$sql = 'SELECT c.*
				 FROM php38_admin_role a 
				  LEFT JOIN php38_role_pri b ON a.role_id=b.role_id 
				  LEFT JOIN php38_privilege c ON b.pri_id=c.id
				  WHERE a.admin_id='.$id;
			$allPriData = $this->query($sql);
		}
		/**************** 从所有的权限中提取出前两级的权限 ********************/
		$btns = array();  // 保存最终的结果
		foreach ($allPriData as $k => $v)
		{
			if($v['parent_id'] == 0)
			{
				// 再找出这个顶级权限的子权限
				foreach ($allPriData as $k1 => $v1)
				{
					if($v1['parent_id'] == $v['id'])
					{
						// 把这个子权限放到顶级权限的children字段中
						$v['children'][] = $v1;
					}
				}
				// 把这个顶级权限放到$btns数组
				$btns[] = $v;
			}
		}
		return $btns;
	}
	/************************************ 其他方法 ********************************************/
	public function _before_delete($option)
	{
		// 先找出所有的子分类
		$children = $this->getChildren($option['where']['id']);
		// 如果有子分类都删除掉
		if($children)
		{
			$children = implode(',', $children);
			$this->execute("DELETE FROM php38_privilege WHERE id IN($children)");
		}
	}
}