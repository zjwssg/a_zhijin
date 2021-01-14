<?php
namespace app\admin\controller;
class Group extends Base
{
	
	/*
	 *添加
	 */
	public function add()
	{
		if(request()->isPost()){
			$data=input('post.');
			empty($data['group_name']) && $this->error('请输入用户组名称');
			!isset($data['group_roleid']) && $this->error('请选择权限');
			$data['group_roleid'] = implode(',',$data['group_roleid']);
			$a = model('group')->insert($data);
			if($a !== false){
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		
		// 渲染
		$qx = model('auth')->order('sort,id desc')->select();
		$qx = $this->wx($qx,'fid');
		return view('add',['qx'=>$qx]);
	}
	/*
	 *修改
	 */
	public function edit()
	{
		if(request()->isPost()){
			$data=input('post.');
			empty($data['group_name']) && $this->error('请输入用户组名称');
			!isset($data['group_roleid']) && $this->error('请选择权限');
			$data['group_roleid'] = implode(',',$data['group_roleid']);
			$a = model('group')->where(['id'=>$data['id']])->update($data);
			if($a !== false){
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}
		
		// 渲染
		$a=db('group')->where(['id'=>input('id')])->find();
		$qx = db('auth')->order('sort,id desc')->select();
		$qx = $this->wx($qx,'fid');
		return view('add',['a'=>$a,'qx'=>$qx]);
	}
	/*
	 *无限级权限
	 */
	public function wx($a,$n,$fid=0,$k=0,$arr=[]){
		foreach($a as $i=>$b){
			if($b[$n]==$fid){
				$b['k']=$k;
				$b['dataid'] = implode('-',$this->fid($b['id'],$a));
				$arr[] = $b;
				$arr = $this->wx($a,$n,$b['id'],$k+1,$arr);
			}
		}
		return $arr;
	}
	/*
	 *获取上级的所有id
	 */
	public function fid($id,$a,$arr=[]){
		foreach($a as $i=>$b){
			if($b['id']==$id){
				unset($a[$i]);
				$arr[] = $b['id'];
				$arr = $this->fid($b['fid'],$a,$arr);
			}
		}
		return $arr;
	}
}
