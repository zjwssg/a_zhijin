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
            //halt($data);
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
            $users = Db::table('users')->where('u_account',$data['c_with_sn'])->select();
            $coupon = Db::table('coupon')->where('c_with_sn',$data['c_with_sn'])->count();
            

            if($users[0]['u_is_members'] == 0 && $coupon > 1){

                $this->error('添加失败，非会员商家只能添加一张优惠券，开通会员可添加5张哦');

            }else if($users[0]['u_is_members'] == 0 && $coupon < 1){

                DB::table('coupon')->insert($data);
                $this->success('添加成功,开通会员可添加5张哦');

            }else if($users[0]['u_is_members'] == 1 && $coupon >= 5){

                $this->error('添加失败，至多5张哦');

            }else if($users[0]['u_is_members'] == 1 && $coupon < 5){

                DB::table('coupon')->insert($data);
                $this->success('添加成功，您是尊贵的会员，至多可添加5张优惠券');

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
