<?php
namespace app\admin\controller;
class Uorder extends Base
{
	/*
	 * 订单管理
	 */
	public function lists(){
		$a = model('uorder')->alias('a')->join('users b','a.o_uid=b.id','left')->field('a.*,b.u_account')->paginate(10);
		$lists = $this->formlist();
		return view('public/lists',['a'=>$a,'b'=>$a->render(),'lists'=>$lists]);
	}
}
