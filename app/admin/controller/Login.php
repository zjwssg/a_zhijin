<?php
namespace app\admin\controller;
use \think\Controller;
class Login extends Controller
{
	/*
	 * 登录
	 */
	public function login()
	{
		if(request()->isPost()){
			$data=input('post.');
			empty(input('admin_account')) && $this->error('请输入账号');
//			empty(input('admin_password')) && $this->error('请输入密码');
			if(!captcha_check($data['captcha'])){
				$this->error('验证码错误');
			};
			$a = model('admin')->where(['admin_account'=>input('admin_account'),'status'=>1])->field('id,admin_random,admin_password')->find();
			if($a){
//				password($data['admin_password'],$a['admin_random']) != $a['admin_password'] && $this->error('密码错误');
				// 修改管理员的登录时间
				model('admin')->save(['logintime'=>time()],['id'=>session('admin.id')]);
				// 获取管理员的信息
				$admin = model('admin')->where(['id'=>$a['id']])->field('admin_password,admin_random',true)->find();
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
				$this->success('登录成功');
			}else{
				$this->error('账号不存在');
			}
		}
		return view();
	}
	/*
	 *退出登录
	 */
	public function del()
	{
		session('admin',null);
		if(session('admin')){
			$this->error('退出失败');
		}else{
			$this->success('已退出');
		}
	}
}
