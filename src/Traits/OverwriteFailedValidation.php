<?php

namespace OuZhou\LaravelToolGenerator\Traits;

use Illuminate\Contracts\Validation\Validator;

trait OverwriteFailedValidation
{
	/**
	 * Function: failedValidation
	 * Notes: 重写
	 * Author: joker_oz
	 * Date: 19-4-17
	 * Time: 下午4:19
	 *
	 * @param Validator $validator
	 *
	 * @throws \Exception
	 */
	protected function failedValidation(Validator $validator)
	{
		throw new \Exception($validator->errors()->first());
	}


	/**
	 * Function: getRequiredError
	 * Notes: 数据为空错误提示
	 * Author: joker_oz
	 * Date: 19-4-17
	 * Time: 下午4:29
	 *
	 * @param string $msg
	 *
	 * @return string
	 */
	protected function getRequiredError(string $msg)
	{
		return '"' . $msg . '"不能为空';
	}


	/**
	 * Function: getFormatError
	 * Notes: 格式不正确错误提示
	 * Author: joker_oz
	 * Date: 19-4-17
	 * Time: 下午4:29
	 *
	 * @param string $msg
	 *
	 * @param string $note
	 * @return string
	 */
	public function getFormatError(string $msg, string $note = 'null'): string
	{
		$return = '"' . $msg . '" 字段格式错误，请使用正确的格式!';
		if ($note !== 'null') {
			$return .= "比如：$note";
		}
		return $return;
	}

	/**
	 * Function: getMaxLengthError
	 * Notes:
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-15  9:11
	 * @param string $msg
	 * @param int $maxLength
	 * @return string
	 */
	public function getMaxLengthError(string $msg, int $maxLength = 10): string
	{
		return '"' . $msg . '"字段长度不能超过' . $maxLength;
	}

	/**
	 * Function: getRegexError
	 * Notes:
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-15  9:10
	 * @param string $column 字段名称
	 * @param string $rule 规则
	 * @return string
	 */
	public function getRegexError(string $column, string $rule): string
	{
		return '"' . $column . '"字段不满足规则：' . $rule;
	}
}
