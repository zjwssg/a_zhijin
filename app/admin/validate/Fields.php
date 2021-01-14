<?php
namespace app\admin\validate;
use think\Validate;
class Fields extends Validate
{
    protected $rule =   [
        'cname'  => 'require|max:25',
				'ename'  => 'require|max:25|alphaDash',
				'form_type'  => 'require'
    ];
    
    protected $message  =   [
        'cname.require' => '请输入中文名称',
        'cname.max'     => '中文名称最多不能超过25个字符',
				'ename.require' => '请输入英文名称',
				'ename.max'     => '英文名称最多不能超过25个字符',
				'ename.alphaDash'     => '英文名称格式不正确',
        'form_type.require'  => '请选择表单类型'
    ];
    
}