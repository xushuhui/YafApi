<?php
/**
 * xushuhui
 * 2017/7/3
 * 9:46
 */

namespace lib\exception;


class SuccessMessage extends BaseException
{
	public $code      = 201;
	public $msg       = '操作成功';
	public $errorCode = 0;
}