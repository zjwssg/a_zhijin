<?php
namespace app\admin\controller;
class Admin extends Base
{
	/*
	 *管理员管理
	 */
	/*
	 *添加
	 */
	public function add(){
		if(request()->isPost()){
			$data=input('post.');
			// 验证规则
			$validate = validate('Admin');
			!$validate->scene('add')->check($data) && $this->error($validate->getError());
			strlen($data['admin_password'])<6 && $this->error('密码长度不能低于6个字符');
			$data['admin_random'] = random();
			$data['admin_password'] = password($data['admin_password'],$data['admin_random']);
			// 如果有图片就上传图片
			if($_FILES){
				foreach($_FILES as $i=>$v){
					if($v['name']!=""){
						$data[$i] = upload($i);
					}
				}
			}
			$a = model('admin')->save($data);
			if($a !== false){
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		// 渲染
		// 获取列表
		$lists = $this->formlist();
		return view('public/add',['lists'=>$lists]);
	}
	/*
	 *修改
	 */
	public function edit(){
		if(request()->isPost()){
			$data=input('post.');
			// 验证规则
			$validate = validate('Admin');
			!$validate->scene('edit')->check($data) && $this->error($validate->getError());
			if(empty($data['admin_password'])){
				unset($data['admin_password']);
			}else{
				strlen($data['admin_password'])<6 && $this->error('密码长度不能低于6个字符');
				$data['admin_random'] = random();
				$data['admin_password'] = password($data['admin_password'], $data['admin_random']);
			}
			// 如果有图片就上传图片
			if($_FILES){
				foreach($_FILES as $i=>$v){
					if($v['name']!=""){
						$img = db('admin')->where(['id'=>$data['id']])->value($i);
						$img!="" && file_exists(IMG.$img) && unlink(IMG.$img);
						$data[$i] = upload($i);
					}
				}
			}
			$a = model('admin')->save($data,['id'=>$data['id']]);
			if($a !== false){
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}
		// 渲染
		$a=model('admin')->get(input('id'));
		// 获取列表
		$lists = $this->formlist();
		return view('public/add',['a'=>$a,'lists'=>$lists]);
	}
	/*
	 *个人资料
	 */
	public function user(){
		// 渲染
		$a = model('admin')->where(['id'=>session('admin.id')])->find();
		return view('user',['a'=>$a]);
	}
	/*
	 *修改密码
	 */
	public function password(){
		if(request()->isPost()){
			$data=input('post.');
			(empty($data['admin_password']) || empty($data['admin_password1']) || empty($data['admin_password2'])) && $this->error('请输入密码');
			$pass = db('admin')->where(['id'=>session('admin.id')])->field('admin_password,admin_random')->find();
			$pass['admin_password'] != password($data['admin_password'],$pass['admin_random']) && $this->error('密码不正确');
			$data['admin_password1'] != $data['admin_password2'] && $this->error('两次密码不一致');
			strlen($data['admin_password1'])<6 && $this->error('密码长度不能低于6个字符');
			$random = random();
			$a = db('admin')->where(['id'=>session('admin.id')])->update(['admin_password'=>password($data['admin_password1'],$random),'admin_random'=>$random]);
			if($a!==false){
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}
		// 渲染
		return view();
	}
}
