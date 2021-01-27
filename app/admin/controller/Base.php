<?php
namespace app\admin\controller;

use \think\Controller;
use \think\Model;
use \think\Cache;
use \think\Db;

class Base extends Controller
{
		public $module = '';
		public $controller = '';
		public $action = '';
    protected function _initialize() {
      /* 判断是否登录 */
			if (!session('admin')) {
				$this->redirect(url('login/login'));
			}
			if(!cache('mrz')){
				$mrz = json_decode(model('fields')->where(['id'=>1])->value('mrz'),true);
				cache('mrz',$mrz);
			}
			$this->module = strtolower(request()->module());
			$this->controller = strtolower(request()->controller());
			$this->action = strtolower(request()->action());
    }
		public function lists()
		{
			$a = model($this->controller)->order('sort,id desc')->paginate(10);
			$lists = $this->formlist();
			return view('public/lists',['a'=>$a,'b'=>$a->render(),'lists'=>$lists]);
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
    /*
     * 删除
     * 参数(id,img为字段名)
     */
    public function del()
    {
			$id = input('id/a');
			$lists = $this->formlist();
			// 删除图片
			if($lists['form']!=''){
				foreach($lists['form'] as $v){
					if($v['is_img']==1){
						$img = model($this->controller)->where(['id'=>['in',$id]])->column($v['ename']);
						foreach($img as $v){
							$v!="" && file_exists(IMG.$v) && unlink(IMG.$v);
						}
					}
				}
			}
			$a = model($this->controller)->destroy($id);
			if ($a !== false) {
					$this->success('已删除');
			} else {
					$this->error('删除成功');
			}
    }
    /*
     * 排序
		 * 参数(sort=['id'=>'值'])
     */
    public function sort()
    {
			$sort = input('sort/a');
			$data = [];
			foreach ($sort as $i=>$v) {
				$data[] = ['id'=>$i,'sort'=>$v];
			}
		  $a=model($this->controller)->saveAll($data);
			$this->success('排序成功');
    }
    /*
     * 修改状态
		 * 参数(状态status,当前id)
     */
    public function status()
    {	
			$id =  implode(',',input('id/a'));
			if (input('status') != "" && $id != "") {
				$data = ['status'=>input('status')];
				$a = model($this->controller)->save($data,['id'=>['in',$id]]);
				$a === false && $this->error('修改失败');
				$this->success('修改成功');
			} else {
				$this->error('缺少参数');
			}
    }
		/*
		 * 日志
		 * 参数(备注，日志类型)
		 */
		public function log($note,$type){
			$cz = $this->module.'/'.$this->controller.'/'.$this->action;
			model('log')->save([
				'uid'=>session('admin.id'),
				'operation'=>$cz,
				'note'=>$note,
				'type'=>$type
			]);
		}
		/*
		 * 获取表单列表
		 */
		public function formlist(){

			$lists = model('auth')->where(['address'=>$this->controller.'/'.$this->action])->field('is_batch,is_add,is_edit,is_del,type,form,formid,width,height')->find()->toArray();
			//halt($lists);
			if($lists['formid']!=0){
				$form = model('auth')->where(['id'=>$lists['formid']])->field('type,form')->find()->toArray();
				$lists['form'] = $form['form'];
				$lists['type'] = $form['type'];
			}
			// 升序排序
			if($lists['form']!=''){
				array_multisort(array_column($lists['form'],'sort'),SORT_ASC,SORT_NUMERIC,$lists['form']);
			}
			return $lists;
		}
}
