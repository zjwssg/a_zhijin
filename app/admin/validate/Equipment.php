<?php
namespace app\admin\validate;
use think\Validate;
class Equipment extends Validate
{
    protected $rule =   [
  			'e_account'  => 'require|unique:equipment',
  			'e_nums'  => 'require|number',
  			'e_amount'  => 'require|number',
  			'e_uaccount'  => 'require',
        
    ];
    
    protected $message  =   [
  			'e_account.require' => '请输入设备号',
  			'e_account.unique'     => '设备号不能重复',
  			'e_nums.require' => '请输入纸巾总数',
  			'e_nums.number'     => '纸巾总数必须为数字',
  			'e_amount.require' => '请输入收益金额',
  			'e_amount.number'     => '收益金额必须为数字',
				'e_uaccount.require' => '请输入商家账号',
    ];
}