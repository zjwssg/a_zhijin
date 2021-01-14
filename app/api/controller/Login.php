<?php
namespace app\api\controller;
use \think\Controller;
use \think\Request;
use \think\Route;
use \think\Db;
class Login extends Controller
{
	protected function _initialize(){
		if(!cache('mrz')){
			$mrz = db('pz')->column('ename,mrz');
			cache('mrz',$mrz);
		}
	}
	// 商家端登录
	public function slogin(){
		!input('account') && die(json_encode(['code'=>0,'msg'=>'请输入账号'],320));
		!input('password') && die(json_encode(['code'=>0,'msg'=>'请输入密码'],320));
		$a = model('users')->where(['u_account'=>input('account'),'u_is_merchants'=>1,'status'=>1])->field('u_password,id')->find();
		if($a){
			input('password') != $a['u_password'] && die(json_encode(['code'=>0,'msg'=>'密码错误'],320));
			$token = md5(md5($a['id'].time().random()));
			$token = strtoupper($token);
			$t = cache('stoken');
			$t[$a['id']] = ['token'=>$token,'time'=>time()+cache('mrz')['tokenTime']*86400,'id'=>$a['id']];
			cache('stoken',$t);
			die(json_encode(['code'=>1,'msg'=>'登录成功','data'=>$token],320));
		}else{
			die(json_encode(['code'=>0,'msg'=>'账号不存在'],320));
		}
	}
	/*
	 * 手机号登录
	 */
	public function jlogin(){
		!input('phone') && die(json_encode(['code'=>0,'msg'=>'请输入手机号'],320));
		!input('code') && die(json_encode(['code'=>0,'msg'=>'请输入验证码'],320));
		!cache('yzm_'.input('phone')) && die(json_encode(['code'=>0,'msg'=>'请发送验证码'],320));
		!input('code') && die(json_encode(['code'=>0,'msg'=>'请输入验证码'],320));
		cache('yzm_'.input('phone'))['yzm'] != input('code') && die(json_encode(['code'=>0,'msg'=>'验证码错误'],320));
		$a = model('users')->get(['u_phone'=>input('phone')]);
		if(!$a){
			$a = model('users')->save(['u_phone'=>input('phone')]);
		}
		$a = model('users')->where(['u_phone'=>input('phone')])->value('id');
		die(json_encode(['code'=>1,'msg'=>'登录成功','data'=>token($id)],320));
	}
	/*
	 * Facebook登录
	 */
	public function flogin(){
		
		die(json_encode(['code'=>1,'msg'=>'登录成功','data'=>token($id)],320));
	}
	/*
	 * 客户端微信登录
	 */
	public function wlogin(){
		!input('openid') && die(json_encode(['code'=>0,'msg'=>'请传递微信openid'],320));
		!input('img') && die(json_encode(['code'=>0,'msg'=>'请传递微信头像'],320));
		!input('name') && die(json_encode(['code'=>0,'msg'=>'请传递微信昵称'],320));
		$a = model('users')->get(['u_wx'=>input('openid')]);
		if(!$a && input('phone')){
			// 未绑定手机号就绑定手机号
			!cache('yzm_'.input('phone')) && die(json_encode(['code'=>0,'msg'=>'请发送验证码'],320));
			!input('code') && die(json_encode(['code'=>0,'msg'=>'请输入验证码'],320));
			cache('yzm_'.input('phone'))['yzm'] != input('code') && die(json_encode(['code'=>0,'msg'=>'验证码错误'],320));
			$data['u_phone'] = input('phone');
			$data['u_wx'] = input('openid');
			$b = model('users')->get(['u_phone'=>input('phone')]);
			$data['u_img'] = $b['u_img'] == ""?input('img'):$b['u_img'];
			$data['u_name'] = $b['u_name'] == ""?input('name'):$b['u_name'];
			if($b){
				$a = model('users')->save($data,['u_phone'=>input('phone')]);
			}else{
				$a = model('users')->save($data);
			}
		}else if(!$a){
			die(json_encode(['code'=>306,'msg'=>'请绑定手机号'],320));
		}
		$id = model('users')->where(['u_wx'=>input('openid')])->value('id');
		die(json_encode(['code'=>1,'msg'=>'登录成功','data'=>token($id)],320));
	}
	/*
	 * 判断是否需要填写手机号
	 */
	public function is_phone(){
		!input('openid') && die(json_encode(['code'=>0,'msg'=>'请传递微信openid'],320));
		$a = model('users')->get(['u_wx'=>input('openid')]);
		if($a){
			die(json_encode(['code'=>1,'msg'=>'不需要手机号绑定'],320));
		}else{
			die(json_encode(['code'=>306,'msg'=>'需要手机号绑定'],320));
		}
	}
	/*
	 * 微信code
	 */
	public function wx_xin(){
		!input('access_token') && die(json_encode(['code'=>0,'msg'=>'请传递微信access_token'],320));
		!input('openid') && die(json_encode(['code'=>0,'msg'=>'请传递微信openid'],320));
		$access_token = input('access_token');
		$openid = input('openid');
		$url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}";
		$a = json_decode(http_request($url),true);
		die(json_encode(['code'=>1,'msg'=>'获取成功','data'=>$a],320));
	}
	/*
	 * 验证码
	 */
	public function code(){

        $AppID = '';//开发平台有
        $AppSecret = '';//开发平台也有
        $Redirect_uri = '';//回调地址
        $scope = 'snsapi_login';//这里不用动如果是微信登录




        !input('phone') && die(json_encode(['code'=>0,'msg'=>'请输入手机号'],320));
		return json_encode(yzm(input('phone')),320);
	}
}
