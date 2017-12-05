<?php
namespace app\lib\validate;


class IDMustBePostiveInt extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger'
    ];
    protected $message = [
        'id' => 'id必须是正整数'
    ]; 
   
}