<?php
namespace app\admin\controller;
use \think\Db;
class Fields extends Base
{
	/*
	 * 配置项
	 */
	public function edit(){
		if(request()->isPost()){
			$data = input('post.');
			$mrz = cache('mrz');
			// 如果有图片就上传图片
			if($_FILES){
				foreach($_FILES as $i=>$v){
					if($v['name'] != ""){
						$img = isset($mrz[$i]) ? $mrz[$i] :'';
						$img != "" && file_exists(IMG.$img) && unlink(IMG.$img);
						$data[$i] = upload($i);
					}else{
						$data[$i] = isset($mrz[$i]) ? $mrz[$i] :'';
					}
				}
			}
			$data = json_encode($data,320);
			$a = Db::name('fields')->where(['id'=>1])->update(['mrz'=>$data]);
			if($a !== false){
				$mrz = json_decode(Db::name('fields')->where(['id'=>1])->value('mrz'),true);
				cache('mrz',$mrz);
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}
		// 渲染
		// 获取列表
		$lists = $this->formlist();
		return view('public/add',['a'=>cache('mrz'),'lists'=>$lists]);
	}
}
