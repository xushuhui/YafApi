<?php

namespace lib\validate;
use lib\exception\ParameterException;
use think\Exception;
use think\Request;
use think\Validate;
class BaseValidate extends Validate
{
	/***验证参数是否符合规范
	 * @return bool
	 * @throws ParameterException
	 */
	public function goCheck()
	{
		//必须设置contetn-type:application/json
		$params  = Request::instance()->param();
		if (!$this->batch()->check($params)) {
			$exception   = new ParameterException(
				[
					'msg' => is_array($this->error) ? implode(
							';', $this->error): $this->error,
				]);
			throw $exception;
		}
		return true;
	}
	/**验证参数是否为正整数
	 * @param $value 传入参数
	 * @param string $rule 规则
	 * @param string $data
	 * @param string $field
	 * @return bool|string
	 */
	 protected function isPositiveInteger($value='',$rule='',$data='',$field='')
    {

        if(is_numeric($value) && is_int($value+0) && ($value+0)>0){
            return  true;
        }else{
            return false;
        }
    }

	/**
	 * @param array $arrays 通常传入request.post变量数组
	 * @return array 按照规则key过滤后的变量数组
	 * @throws ParameterException
	 */
	public function getDataByRule($arrays)
	{
		if (array_key_exists('user_id', $arrays) | array_key_exists('uid', $arrays)) {
			// 不允许包含user_id或者uid，防止恶意覆盖user_id外键
			throw new ParameterException([
					'msg' => '参数中包含有非法的参数名user_id或者uid'
			]);
		}
		$newArray = [];
		foreach ($this->rule as $key => $value) {
			$newArray[$key] = $arrays[$key];
		}
		return $newArray;
	}
	
	/**验证参数是否为空
	 * @param $value 传入参数
	 * @param string $rule 规则
	 * @param string $data
	 * @param string $field
	 * @return bool|string
	 */
	protected function isNotEmpty($value, $rule='', $data='', $field='')
	{
		if (empty($value)) {
			return false;
		} else {
			return true;
		}
	}

	//检验手机号
	protected function isMobile($value)
	{
		$rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
		$result = preg_match($rule, $value);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	//检验身份证
	protected function isIDCard($value)
	{
		$rule = '/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/';
		$result = preg_match($rule, $value);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	//检验银行卡
	protected function isBankCard($value)
	{
		$rule = '/^\d{16}|\d{19}$/';
		$result = preg_match($rule, $value);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

}