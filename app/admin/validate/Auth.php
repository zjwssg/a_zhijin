<?php
namespace app\admin\validate;
use think\Validate;
class Auth extends Validate
{
    protected $rule =   [
        'name'  => 'require|max:25',
				'fid'  => 'require',
				'address'  => 'max:50'
    ];
    
    protected $message  =   [
        'name.require' => '请输入栏目名称',
        'name.max'     => '栏目名称最多不能超过25个字符',
        'fid.require'  => '请选择所属栏目',
				'address.max'     => '控/方最多不能超过50个字符'
    ];
    
}