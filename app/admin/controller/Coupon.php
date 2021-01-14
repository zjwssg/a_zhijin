<?php
namespace app\admin\controller;
use \think\Db;
class Coupon extends Base
{

    /*
     * 优惠劵
     */
    public function lists(){
        $a = model('coupon')->paginate(10);
       foreach ($a as $k => $val){
           $val->c_type= model('coupon')->getOTypeAttr($val->c_type);
       }
        $lists = $this->formlist();



        return view('public/lists',['a'=>$a,'lists'=>$lists]);
    }
    /*
	 *添加
	 */
    public function add(){
        $model = model($this->controller);
//        var_dump($this->controller);die;
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
            $code = 1;
            // 启动事务
            Db::startTrans();
            try{
                $a = $model->save($data);
                $data['c_create_user'] = session('admin.id'); //后台的id
                $data['c_available_num'] =$data['c_quota'];  //可使用数量
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
