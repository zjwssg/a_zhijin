<?php
namespace app\admin\controller;
class Advices extends Base
{
		/*
		 * 商家入驻
		 */
	public function myadvices(){
		$a = model('advices')->paginate(10);
		
		$lists = $this->formlist();



		return view('public/lists',['a'=>$a,'lists'=>$lists]);
    }
}
