<?php
namespace app\api\controller;
use think\Controller;
//创建一个类TestImage，继承基类Controller
class Upload extends Common
{
    //定义一个方法名upload_img，和view/TestImage文件夹下面的upload_img同名，提交信息时匹配文件
    public function upload_img(){
        //判断是否是post 方法提交的
        if(request()->isPost()){
            $data=input('post.');

            //讲传入的图片写入到test_images表中，使用Thinkphp5自定义的函数insert()
            $add=db('client_user')->where(['user_id'=>$data['user_id']])->update(['user_icon'=>$data['user_icon']]);
            return $add;
            if($add){
                //如果添加成功，提示添加成功。success也可以定义跳转链接，success('添加图片成功！','这里写人跳转的url')
                $this->return_msg(200, '修改图片成功！',$data['user_icon']);
            }else{
                $this->return_msg(400, '修改图片失败！');
            }
            return;
        }
//        return view();
    }

    function upload()
    {
        if(!empty($_FILES['file'])){
            //获取扩展名
            $exename  = $this->getExeName($_FILES['file']['name']);
            if($exename != 'png' && $exename != 'jpg' && $exename != 'gif'){
                exit('不允许的扩展名');
            }
            $path = 'static/img/'
            $imageSavePath = uniqid().'.'.$exename;
            if(move_uploaded_file($_FILES['file']['tmp_name'], $path.$imageSavePath)){
                return $imageSavePath;
            }
        }
        /*$img='user_icon';
        if($_FILES[$img]['type']=="image/png" || $_FILES[$img]['type']=="image/jpeg" || $_FILES[$img]['type']=="image/gif" || $_FILES[$img]['type']=="image/jpg" || $_FILES[$img]['type']=="image/bmp"){
            // 获取表单上传文件 例如上传了001.jpg
            $file = request()->file($img);
            if ($file) {
                $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'img');
                if ($info) {
                    return str_replace('\\', '/', $info->getSaveName());
                } else {
                    // 上传失败获取错误信息
                    $this->error($file->getError());
                }
            }
        }else{
            $this->error('文件类型不是图片');
        }*/
    }
    public function getExeName($fileName){
        $pathinfo      = pathinfo($fileName);
        return strtolower($pathinfo['extension']);
    }
}