<?php
namespace app\admin\controller;

use \think\Db;
class Users extends Base
{
	/*
	 * 商家入驻
	 */
	public function lists(){
		$a = model('users')->alias('a')->join('cater b','a.u_m_typeid=b.id','left')->field('a.id,u_password,u_account,u_m_name,u_m_logo,u_m_brief,u_m_starttime,u_m_endtime,u_m_address,b.name as u_m_typeid,status')->where(['u_is_merchants'=>1])->paginate(10);
		$lists = $this->formlist();
		return view('public/lists',['a'=>$a,'lists'=>$lists]);
	}
	/*
	 *添加
	 */
	public function add(){
		$model = model($this->controller);
		if(request()->isPost()){

			$data=input('post.');
			$controller = request()->controller();
			// 验证规则
			$validate = validate($controller);
			!$validate->check($data) && $this->error($validate->getError());
			// 如果有图片就上传图片
			if($_FILES){
				foreach($_FILES as $i=>$v){
					if($v['name'] != ""){
						$data[$i] = upload($i);
					}
				}
			}
			$data['u_is_merchants'] = 1;
			$code = 1;
			// 启动事务
			Db::startTrans();
			try{
			    $a = $model->save($data);
			    $data['u_account'] = random(7,'number').$model->id;
			    $data['u_password'] = random(6,'number');
			    $a = $model->allowField(true)->save($data,['id'=>$model->id]);
			    // 提交事务
			    Db::commit();    
			} catch (\Exception $e) {
				$code = 0;
			    // 回滚事务
			    Db::rollback();
			}
			if($code){
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
}
