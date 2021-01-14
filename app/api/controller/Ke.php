<?php
namespace app\api\controller;
use \think\Controller;
use \think\Request;
use \think\Route;
use \think\Db;
class Ke extends Controller
{
	protected $user = 0;
	protected $token = 0;
	protected function _initialize(){
		if(!cache('mrz')){
			$mrz = db('pz')->column('ename,mrz');
			cache('mrz',$mrz);
		}
		$this->token = request()->header('token');
		$this->token_yz();// 验证token
	}
	/*
	 * token验证
	 */
	protected function token_yz(){
		!cache('yh_token') && die(json_encode(['code'=>305,'msg'=>'数据异常，请重新登录'],320));
		$token = array_column(cache('yh_token'),'token','id');
		$i = array_search($this->token,$token);
		if(!$i){
			die(json_encode(['code'=>303,'msg'=>'请登录'],320));
		}else if(cache('yh_token')[$i]['time']<=time()){
			die(json_encode(['code'=>304,'msg'=>'有效期已过，请重新登录'],320));
		}
		$this->user = model('users')->get($i)->toArray();
	}
}
