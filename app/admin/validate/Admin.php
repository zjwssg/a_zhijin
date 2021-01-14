<?php
namespace app\admin\validate;
use think\Validate;
class Admin extends Validate
{
	protected $regex = [ 'phone' => '/^1(?:3\d|4[4-9]|5[0-35-9]|6[67]|7[013-8]|8\d|9\d)\d{8}$/'];
    protected $rule =   [
				'admin_phone'  => 'require|regex:phone|unique:admin',
				'admin_password'  => 'require|max:35',
				'admin_name'  => 'require|max:30',
				// 'admin_groupid'  => 'require',
    ];
    
    protected $message  =   [
				'admin_password.require' => '请输入密码',
				'admin_password.max'     => '密码最多不能超过35个字符',
				'admin_phone.require' => '请输入手机号',
				'admin_phone.regex'     => '手机号格式错误',
				'admin_phone.unique'     => '手机号不能重复',
				'admin_name.require' => '请输入姓名',
				'admin_name.max'     => '姓名最多不能超过30个字符',
				'admin_groupid.require' => '请选择用户组',
    ];
		protected $scene = [
			'add'  =>  ['admin_password','admin_phone','admin_name','admin_groupid'],
			'edit'  => ['admin_phone','admin_name','admin_groupid'],
			'gr' => ['admin_phone']
		];
}