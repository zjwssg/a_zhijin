<?php
namespace app\api\controller;
use think\Cache;
use think\Controller;
use think\Session;

class User extends Common
{
    /**
         * 获取店铺列表信息
     * @return json API返回的json数据
     */
    public function get_shop_list()
    {
        /***********  获取参数  ***********/
        $data = input();
        $db_res = db('users')
            ->where(['status'=>1])
            ->select();


        if($db_res){
            //如果添加成功，提示添加成功。success也可以定义跳转链接，success('添加图片成功！','这里写人跳转的url')
            $this->return_msg(200, 'info',$db_res);
        }else{
            $this->return_msg(400, '暂无数据');
        }
    }

    /**
     * 获取店铺详情
     * @return json API返回的json数据info
     */
    public function get_shop_info()
    {
        /***********  获取参数  ***********/
        $data = input();
        $db_res = db('users')
            ->where(['id'=>$data['id'],'status'=>1])
            ->find();


        if($db_res){
            //如果添加成功，提示添加成功。success也可以定义跳转链接，success('添加图片成功！','这里写人跳转的url')
            $this->return_msg(200, 'info',$db_res);
        }else{
            $this->return_msg(400, '暂无数据');
        }
    }

    /**
     * 关注处理
     * @return json API返回的json数据info
     */
    public function get_shop_attention()
    {
        /***********  获取参数  ***********/
        $data = input();
        $datas = [
            'shop_id' => $data['shop_id'],
            'user_id' => $data['user_id'],
        ];
        $id = db('attention')->where(['shop_id'=>$data['shop_id'],'user_id'=>$data['user_id']])->select();
        //halt($id);
        if(!empty($id)){
            
            if($id[0]['states'] == 0){
                $states_update = db('attention')->where('id',$id[0]['id'])->update(['states' => 1]);
            }else if($id[0]['states'] == 1){
                $states_update = db('attention')->where('id',$id[0]['id'])->update(['states' => 0]);
            }
            $d_id = $id[0]['id'];
        }else{
            $attention_id = db('attention')->insertGetId($datas);
            $d_id = $attention_id;
            $attention_states = db('attention')->where('id',$attention_id)->select();

            if($attention_states[0]['states'] == 0){
                $states_update = db('attention')->where('id',$attention_id)->update(['states' => 1]);
            }else if($attention_states[0]['states'] == 1){
                $states_update = db('attention')->where('id',$attention_id)->update(['states' => 0]);
            }
        }
        
        //$states = 
        if($states_update){
            $gz = db('attention')->where('id',$d_id)->select();
            //halt($gz);
            if($gz[0]['states'] == 1){
                $this->return_msg(200, '关注成功',$states_update);
            }else if($gz[0]['states'] == 0){
                //如果添加成功，提示添加成功。success也可以定义跳转链接，success('添加图片成功！','这里写人跳转的url')
                $this->return_msg(202, '取消关注',$states_update);
            }
            
        }else{
            $this->return_msg(400, '关注失败');
        }
    }

}
