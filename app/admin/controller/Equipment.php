<?php
namespace app\admin\controller;
use app\api\controller\Equipment as e;
use think\Db;
class Equipment extends Base
{
		/*
		 * 设备管理
		 */
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
			$id = model('users')->where(['u_account'=>$data['e_uaccount'],'u_is_merchants'=>1])->value('id');
			!$id && $this->error('商家账号不存在');
			// 如果有图片就上传图片
			if($_FILES){
				foreach($_FILES as $i=>$v){
					if($v['name'] != ""){
						$data[$i] = upload($i);
					}
				}
			}
			$a = $model->save($data);
			if($a !== false){
				$this->log('添加数据',2);
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
		$model = model($this->controller);
		if(request()->isPost()){
			$data=input('post.');
			$controller = request()->controller();
			// 验证规则
			$validate = validate($controller);
			!$validate->check($data) && $this->error($validate->getError());
			$id = model('users')->where(['u_account'=>$data['e_uaccount'],'u_is_merchants'=>1])->value('id');
			!$id && $this->error('商家账号不存在');
			// 如果有图片就上传图片
			if($_FILES){
				foreach($_FILES as $i=>$v){
					if($v['name'] != ""){
						$img = $model->where(['id'=>$data['id']])->value($i);
						$img != "" && file_exists(IMG.$img) && unlink(IMG.$img);
						$data[$i] = upload($i);
					}
				}
			}
			$a = $model->save($data,['id'=>$data['id']]);
			if($a !== false){
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}
		// 渲染
		$a = $model->get(input('id'))->toArray();
		// 获取列表
		$lists = $this->formlist();
		return view('public/add',['a'=>$a,'lists'=>$lists]);
	}
	public function lists(){
		// $Equipment = new e();
		// $data = '{"sn": "ookkma", "nonceStr": "123456"}';
		// $res = $Equipment->facility_message($data);
		$Equipment = new e();
		$date = time();
		$sn = [];
		$state = Db::table('equipment')->field('e_sn')->select();
		
		foreach ($state as $value) {
			foreach ($value as $v) {
				$sn[] = $v;
			}
		}
		$data = [
			"snList" => $sn,
			"nonceStr" => $date
		];
		$data = json_encode($data);
		//halt($data);
		$res = $Equipment->facility_message_ports($data);
		$res = json_decode($res,true);
		//halt(count($res['data']));
		if(count($res['data']) == 1){
			Db::table('equipment')->where('e_sn',$res['data'][0]['sn'])->update(['e_statet'=>$res['data'][0]['online'] == 1? "在线":"离线"]);
		}else{
			foreach ($res['data'] as $value) {
				//halt($value);
				Db::table('equipment')->where('e_sn',$value['sn'])->update(['e_statet'=>$value['online'] == 1? "在线":"离线"]);
				
			}
		}
		//halt($res['data'][0]['online']);
		$a = model('equipment')->paginate(10);
		//halt($a);
		$lists = $this->formlist();
		//halt($lists);
        return view('public/lists',['a'=>$a,'lists'=>$lists]);
	}
}