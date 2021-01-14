<?php
namespace app\api\controller;
use \think\Controller;
use \think\Request;
use \think\Route;
use \think\Db;
class Shang extends Controller
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
		!cache('stoken') && die(json_encode(['code'=>305,'msg'=>'数据异常，请重新登录'],320));
		$token = array_column(cache('stoken'),'token','id');
		$i = array_search($this->token,$token);
		if(!$i){
			die(json_encode(['code'=>303,'msg'=>'请登录'],320));
		}else if(cache('stoken')[$i]['time']<=time()){
			die(json_encode(['code'=>304,'msg'=>'有效期已过，请重新登录'],320));
		}
		$this->user = model('users')->get($i)->toArray();
	}
	/*
	 * 配置
	 */
	public function config(){
		$data['phone'] = cache('mrz')['phone'];
		$data['amount'] = cache('mrz')['amount'];
		$data['day'] = cache('mrz')['day'];
		$data['starttime'] = date('Y-m-d');
		$data['endtime'] = date('Y-m-d',strtotime("+{$data['day']} days"));
		$data['teq'] = cache('mrz')['teq'];
		die(json_encode(['code'=>1,'msg'=>'获取成功','data'=>$data],320));
	}
	/*
	 * 商家信息
	 */
	public function user(){
		$data = model('users')->alias('a')->join('cater b','a.u_m_typeid=b.id','left')->where(['a.id'=>$this->user['id']])->field('u_name,u_img,u_balance,u_cash,u_is_members,u_members_starttime,u_members_endtime,u_m_name,u_m_logo,u_m_brief,u_m_starttime,u_m_endtime,u_m_address,u_m_focus,name')->find();
		if($data!==false){
			die(json_encode(['code'=>1,'msg'=>'获取成功','data'=>$data],320));
		}else{
			die(json_encode(['code'=>0,'msg'=>'获取失败','data'=>$data],320));
		}
	}
	/*
	 * 商家设备
	 */
	public function equipment(){
		$data = model('equipment')->where(['e_uaccount'=>$this->user['u_account']])->field('e_uaccount,e_amount,e_ing,e_lat,sort',true)->select();
		if($data!==false){
			die(json_encode(['code'=>1,'msg'=>'获取成功','data'=>$data],320));
		}else{
			die(json_encode(['code'=>0,'msg'=>'获取失败','data'=>$data],320));
		}
	}
	/*
	 * 发布优惠券
	 */
	public function fdiscount(){
		$data = input('post.');
		input('d_jamount')=="" && die(json_encode(['code'=>0,'msg'=>'请输入优惠券金额'],320));
		input('d_mamount')=="" && die(json_encode(['code'=>0,'msg'=>'请输入优惠券使用条件'],320));
		input('d_endtime')=="" && die(json_encode(['code'=>0,'msg'=>'请输入有效期'],320));
		input('d_nums')=="" && die(json_encode(['code'=>0,'msg'=>'请输入优惠券数量'],320));
		input('d_img')=="" && die(json_encode(['code'=>0,'msg'=>'请选择优惠卷封面'],320));
		(!preg_match('/^[1-9][0-9]+(\.[0-9]{0,2})?$/',input('d_jamount')) || !preg_match('/^[1-9][0-9]+(\.[0-9]{0,2})?$/',input('d_mamount'))) && die(json_encode(['code'=>0,'msg'=>'优惠券金额格式不正确'],320));
		!preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/',input('d_endtime')) && die(json_encode(['code'=>0,'msg'=>'时间格式不正确'],320));
		!preg_match('/^[1-9][0-9]*$/',input('d_nums')) && die(json_encode(['code'=>0,'msg'=>'数量必须是正整数'],320));
		$data['d_name'] = $this->user['u_m_name'];
		$data['d_starttime'] = date('Y-m-d');
		$data['d_uid'] = $this->user['id'];
		$num = model('discount')->where(['d_uid'=>$this->user['id']])->count(); //已发布的优惠卷数量
		$numm = $this->user['u_is_members'] == 1?cache('mrz')['mcoupons']:cache('mrz')['coupons']; //可发布的优惠卷数量
		if($num<$numm){
			$a = model('discount')->insert($data);
			if($a){
				die(json_encode(['code'=>1,'msg'=>'发布成功'],320));
			}else{
				die(json_encode(['code'=>0,'msg'=>'发布失败'],320));
			}
		}else{
			die(json_encode(['code'=>307,'msg'=>cache('mrz')['tixing']],320));
		}
		
	}
	/*
	 * 优惠券
	 */
	public function discount(){
		$data = model('discount')->where(['d_uid'=>$this->user['id']])->field('d_uid',true)->select();
		if($data!==false){
			die(json_encode(['code'=>1,'msg'=>'获取成功','data'=>$data],320));
		}else{
			die(json_encode(['code'=>0,'msg'=>'获取失败','data'=>$data],320));
		}
	}
	/*
	 * 删除优惠券
	 */
	public function del_discount(){
		input('id')=="" && die(json_encode(['code'=>0,'msg'=>'请传递优惠卷id'],320));
		$a = model('discount')->where(['id'=>input('id')])->delete();
		if($a){
			die(json_encode(['code'=>1,'msg'=>'已删除'],320));
		}else{
			die(json_encode(['code'=>0,'msg'=>'删除失败'],320));
		}
	}
	/*
	 * 设备收益
	 */
	public function uequipment(){
		$data = Db::name('equipment')->where(['e_uaccount'=>$this->user['u_account']])->field('e_account,e_znum,e_amounts,addtime')->select();
		foreach($data as $i=>$v){
			$data[$i]['days'] = floor((time()-$v['addtime']) / 86400);
		}
		if($data!==false){
			die(json_encode(['code'=>1,'msg'=>'获取成功','data'=>$data],320));
		}else{
			die(json_encode(['code'=>0,'msg'=>'获取失败','data'=>$data],320));
		}
	}
	/*
	 * 纸巾领取记录
	 */
	public function record(){
		input('starttime')=="" && die(json_encode(['code'=>0,'msg'=>'请传递开始时间'],320));
		input('endtime')=="" && die(json_encode(['code'=>0,'msg'=>'请传递结束时间'],320));
		(!preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/',input('starttime')) || !preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/',input('endtime'))) && die(json_encode(['code'=>0,'msg'=>'时间格式不正确'],320));
		$data = model('equipment')->alias('a')->join('uequipment b','a.e_account=b.e_eid')->where(['e_uaccount'=>$this->user['u_account'],'addtime'=>['between',[strtotime(input('starttime')),strtotime(input('endtime'))]]])->field('e_eid,b.e_num,addtime')->select();
		if($data!==false){
			die(json_encode(['code'=>1,'msg'=>'获取成功','data'=>$data],320));
		}else{
			die(json_encode(['code'=>0,'msg'=>'获取失败','data'=>$data],320));
		}
	}
	/*
	 * 广告位
	 */
	public function position(){
		$data = model('position')->field('sort',true)->select();
		if($data!==false){
			die(json_encode(['code'=>1,'msg'=>'获取成功','data'=>$data],320));
		}else{
			die(json_encode(['code'=>0,'msg'=>'获取失败','data'=>$data],320));
		}
	}
	/*
	 * 广告
	 */
	public function adver(){
		input('status')=="" && die(json_encode(['code'=>0,'msg'=>'请传递状态'],320));
		$data = model('adver')->where(['a_uid'=>$this->user['id'],'status'=>input('status')])->alias('a')->join('position b','a.a_position=b.id')->field('a.id,a_name,a_img,a_address,a_type,a_position,a_plays,a_clicks,a_note,audittime,addtime,endtime,p_title')->select();
		if($data!==false){
			die(json_encode(['code'=>1,'msg'=>'获取成功','data'=>$data],320));
		}else{
			die(json_encode(['code'=>0,'msg'=>'获取失败','data'=>$data],320));
		}
	}
	/*
	 * 扫码核销
	 */
	public function write(){
		input('id')=="" && die(json_encode(['code'=>0,'msg'=>'请传递优惠卷id'],320));
		$a = Db::name('udiscount')->where(['id'=>input('id')])->update(['c_type'=>1]);
		if($a){
			die(json_encode(['code'=>1,'msg'=>'核销成功'],320));
		}else{
			die(json_encode(['code'=>0,'msg'=>'核销失败'],320));
		}
	}
	/*
	 * 我的消息
	 */
	public function message(){
		$data = model('log')->where(['uid'=>$this->user['id']])->field('note,addtime')->select();
		if($data!==false){
			die(json_encode(['code'=>1,'msg'=>'获取成功','data'=>$data],320));
		}else{
			die(json_encode(['code'=>0,'msg'=>'获取失败','data'=>$data],320));
		}
	}
	/*
	 * 发送消息
	 */
	public function fmessage(){
		input('content')=="" && die(json_encode(['code'=>0,'msg'=>'内容不能为空'],320));
		$a = model('message')->save(['m_content'=>input('content'),'m_uid'=>$this->user['id']]);
		if($a){
			die(json_encode(['code'=>1,'msg'=>'发送成功'],320));
		}else{
			die(json_encode(['code'=>0,'msg'=>'发送失败'],320));
		}
	}
}
