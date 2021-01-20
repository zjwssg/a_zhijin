<?php
namespace app\admin\controller;
class Auth extends Base
{
	/*
	 *栏目管理
	 */
	public function lists()
	{
		$a=db('auth')->order('sort,id desc')->select();
		$fid=array_column($a,'fid');
		$a=wx($a,'fid');
		return view('lists',['a'=>$a,'fid'=>$fid]);
	}
	/*
	 *添加
	 */
	public function add()
	{
		if(request()->isPost()){
			$data=input('post.');
			// 验证规则
			$validate = validate('Auth');
			!$validate->check($data) && $this->error($validate->getError());
			
			// 表单项
			if(isset($data['form'])){
				//count($data['form']['ename']) != count(array_unique($data['form']['ename'])) && $this->error('表单项的英文名称不可重复');
				$form = [];
				foreach($data['form']['cname'] as $i=>$v){
					$form[$i]['cname'] = $data['form']['cname'][$i];
					$form[$i]['ename'] = $data['form']['ename'][$i];
					$form[$i]['type'] = $data['form']['type'][$i];
					$form[$i]['ftype'] = $data['form']['ftype'][$i];
					$form[$i]['value'] = $data['form']['value'][$i];
					$form[$i]['hint'] = $data['form']['hint'][$i];
					$form[$i]['note'] = $data['form']['note'][$i];
					$form[$i]['is_display'] = $data['form']['is_display'][$i];
					$form[$i]['is_sort'] = $data['form']['is_sort'][$i];
					$form[$i]['is_status'] = $data['form']['is_status'][$i];
					$form[$i]['is_img'] = $data['form']['is_img'][$i];
					$form[$i]['status'] = $data['form']['status'][$i];
					$form[$i]['sort'] = $data['form']['sort'][$i];
				}
				$data['form'] = json_encode($form,320);
			}
			$a = db('auth')->insert($data);
			if($a !== false){
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		// 获取列表
		$lists = db('auth')->order('sort,id desc')->select();
		$lists = wx($lists,'fid');
		return view('add',['lists'=>$lists]);
	}
	/*
	 *修改
	 */
	public function edit()
	{
		if(request()->isPost()){
			$data=input('post.');
			// 验证规则
			$validate = validate('Auth');
			!$validate->check($data) && $this->error($validate->getError());
			$data['fid'] == $data['id'] && $this->error('自身不能为上级栏目');
			// 表单项
			if(isset($data['form'])){
				//count($data['form']['ename']) != count(array_unique($data['form']['ename'])) && $this->error('表单项的英文名称不可重复');
				$form = [];
				foreach($data['form']['cname'] as $i=>$v){
					$form[$i]['cname'] = $data['form']['cname'][$i];
					$form[$i]['ename'] = $data['form']['ename'][$i];
					$form[$i]['type'] = $data['form']['type'][$i];
					$form[$i]['ftype'] = $data['form']['ftype'][$i];
					$form[$i]['value'] = $data['form']['value'][$i];
					$form[$i]['hint'] = $data['form']['hint'][$i];
					$form[$i]['note'] = $data['form']['note'][$i];
					$form[$i]['is_display'] = $data['form']['is_display'][$i];
					$form[$i]['is_sort'] = $data['form']['is_sort'][$i];
					$form[$i]['is_status'] = $data['form']['is_status'][$i];
					$form[$i]['is_img'] = $data['form']['is_img'][$i];
					$form[$i]['status'] = $data['form']['status'][$i];
					$form[$i]['sort'] = $data['form']['sort'][$i];
				}
				$data['form'] = json_encode($form,320);
			}
			$a = db('auth')->where(['id'=>$data['id']])->update($data);
			if($a !== false){
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}

		}
		// 渲染
		$a=model('auth')->where(['id'=>input('id')])->find()->toArray();
		// 获取列表
		$lists = model('auth')->order('sort,id desc')->select();
		$lists = wx($lists,'fid');
		return view('add',['lists'=>$lists,'a'=>$a]);
	}
	/*
	 *删除
	 */
	public function del(){
		!input('id') && $this->error('缺少参数ID');
		$a = db('auth')->field('id,fid')->select();
		$zid = zid(input('id'),$a,'fid');
		$zid[] = input('id');
		$a = db('auth')->where(['id'=>['in',$zid]])->delete();
		if($a){
			$this->success('已删除');
		}else{
			$this->error('删除成功');
		}
	}
	/*
	 *是否检测权限
	 */
	public function auth(){
		if(input('status')!="" && input('id')!=""){
			$a = db('auth')->where(['id'=>input('id')])->update(['auth'=>input('status')]);
			$a === false && $this->error('修改失败');
			$this->success('已修改','',input('status'));
		}else{
			$this->error('未传递变量');
		}
	}
}
