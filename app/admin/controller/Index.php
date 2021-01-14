<?php
namespace app\admin\controller;
class Index extends Base
{
		/*
		 * 首页
		 */
    public function index()
    {
		// 获取管理员的信息
		$admin = model('admin')->where(['id'=>session('admin.id')])->field('admin_password,admin_random',true)->find();
		$admin['group_name'] = $admin->group->group_name;
		$admin['group_roleid'] = $admin->group->group_roleid;
		$admin = $admin->toArray();
		// 获取管理员有权限的列表
		$where['status'] = 1;
		if($admin['admin_gid']!=1){
			$where['id'] = ['in',$admin['group_roleid']];
		}
		$index = model('auth')->where($where)->field('id,name,address,fid,icon')->order('sort,id desc')->select()->toArray();
		$admin['auth'] = wx1($index,'fid');
		session('admin',$admin);
      	return view('index',['index'=>session('admin.auth')]);
	}

	public function console()
	{
	  return view();
	}
	
	public function err()
	{
	  return view('error');
	}
}
