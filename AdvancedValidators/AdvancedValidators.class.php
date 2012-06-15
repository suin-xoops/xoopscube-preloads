<?php
/**
 * XCube_ActionForm により高度なバリデーションを提供するプリロード
 */

/**
 * name: 選択肢にあるかチェックする
 */
class XCube_OptionValidator extends XCube_Validator
{
	/**
	 * @param XCube_PropertyInterface $property
	 * @param array $vars
	 * @return bool
	 */
	public function isValid(XCube_PropertyInterface $property, $vars)
	{
		if ( $property->isNull() ) {
			return true;
		}

		return array_key_exists($property->get(), $vars['option']);
	}
}

/**
 * date: YYYY-MM-DD 形式かチェックする
 */
class XCube_DateValidator extends XCube_Validator
{
	/**
	 * @param XCube_PropertyInterface $property
	 * @param array $vars
	 * @return bool
	 */
	public function isValid(XCube_PropertyInterface $property, $vars)
	{
		if ( $property->isNull() ) {
			return true;
		}

		$value = $property->get();

		if ( ! preg_match('/^(?P<year>[0-9]+)-(?P<month>[0-9]{1,2})-(?P<day>[0-9]{1,2})$/', $value, $matches) ) {
			return false;
		}

		if ( AdvancedValidator_DateTime::isDate($matches['year'], $matches['month'], $matches['day']) === false ) {
			return false;
		}

		return true;
	}
}

/**
 * datetime: YYYY-MM-DD HH:MM:SS 形式かチェックする
 */
class XCube_DatetimeValidator extends XCube_Validator
{
	/**
	 * @param XCube_PropertyInterface $property
	 * @param array $vars
	 * @return bool
	 */
	public function isValid(XCube_PropertyInterface $property, $vars)
	{
		if ( $property->isNull() ) {
			return true;
		}

		$value = $property->get();

		if ( ! preg_match('/^(?P<year>[0-9]+)-(?P<month>[0-9]{1,2})-(?P<day>[0-9]{1,2}) (?P<hour>[0-9]{1,2}):(?P<minute>[0-9]{1,2}):(?P<second>[0-9]{1,2})$/', $value, $matches) ) {
			return false;
		}

		if ( AdvancedValidator_DateTime::isDate($matches['year'], $matches['month'], $matches['day']) === false ) {
			return false;
		}

		if ( AdvancedValidator_DateTime::isTime($matches['hour'], $matches['minute'], $matches['second']) === false ) {
			return false;
		}

		return true;
	}
}

/**
 * date_hour_minute: YYYY-MM-DD HH:MM 形式かチェックする
 */
class XCube_Date_hour_minuteValidator extends XCube_Validator
{
	/**
	 * @param XCube_PropertyInterface $property
	 * @param array $vars
	 * @return bool
	 */
	public function isValid(XCube_PropertyInterface $property, $vars)
	{
		if ( $property->isNull() ) {
			return true;
		}

		$value = $property->get();

		if ( ! preg_match('/^(?P<year>[0-9]+)-(?P<month>[0-9]{1,2})-(?P<day>[0-9]{1,2}) (?P<hour>[0-9]{1,2}):(?P<minute>[0-9]{1,2})$/', $value, $matches) ) {
			return false;
		}

		if ( AdvancedValidator_DateTime::isDate($matches['year'], $matches['month'], $matches['day']) === false ) {
			return false;
		}

		if ( AdvancedValidator_DateTime::isTime($matches['hour'], $matches['minute'], 0) === false ) {
			return false;
		}

		return true;
	}
}

/**
 * hiragana: ひらがなかチェックする
 */
class XCube_HiraganaValidator extends XCube_Validator
{
	/**
	 * @param XCube_PropertyInterface $property
	 * @param array $vars
	 * @return bool
	 */
	public function isValid(XCube_PropertyInterface $property, $vars)
	{
		if ( $property->isNull() ) {
			return true;
		}

		$value = $property->get();
		return $this->_isHiragana($value);
	}

	/**
	 * 文字列がひらがなかを返す
	 *
	 * 次の文字以外にマッチした場合、FALSEを返します:
	 * ぁ あ ぃ い ぅ う ぇ え ぉ お か が き ぎ く ぐ け げ こ ご さ ざ し じ す ず せ ぜ そ ぞ
	 * た だ ち ぢ っ つ づ て で と ど な に ぬ ね の は ば ぱ ひ び ぴ ふ ぶ ぷ へ べ ぺ ほ ぼ
	 * ぽ ま み む め も ゃ や ゅ ゆ ょ よ ら り る れ ろ ゎ わ ゐ ゑ を ん ゔ ゕ ゖ ー
	 *
	 * @param string $string
	 * @return bool
	 */
	protected function _isHiragana($string)
	{
		$hiragana = '\xe3(\x81[\x81-\xbf]|\x82[\x80-\x96])'; // [ぁ-み]|[む-ゖ]
		$chonpu   = '\xe3\x83\xbc'; // ー
		$pattern  = "/^($hiragana|$chonpu)+$/";

		if ( preg_match($pattern, $string) ) {
			return true;
		}

		return false;
	}
}

/**
 * zipcode: 日本の郵便番号(000-0000)の形式かチェックする
 */
class XCube_ZipcodeValidator extends XCube_Validator
{
	/**
	 * @param XCube_PropertyInterface $property
	 * @param array $vars
	 * @return bool
	 */
	public function isValid(XCube_PropertyInterface $property, $vars)
	{
		if ( $property->isNull() ) {
			return true;
		}

		$value = $property->get();

		if ( preg_match('/^[0-9]{3}-[0-9]{4}$/', $value) ) {
			return true;
		}

		return false;
	}
}

/**
 * phone_number: 電話番号かをチェックする
 */
class XCube_Phone_numberValidator extends XCube_Validator
{
	/**
	 * @param XCube_PropertyInterface $property
	 * @param array $vars
	 * @return bool
	 */
	public function isValid(XCube_PropertyInterface $property, $vars)
	{
		if ( $property->isNull() ) {
			return true;
		}

		$value = $property->get();

		if ( preg_match('/^[0-9]+$/', $value) ) {
			return true;
		}

		return false;
	}
}

/**
 * mb_maxlength: 文字数以内かをチェックする(マルチバイト対応版)
 *
 * UTF-8で「あ」は3文字ですが、このバリデータでは1文字としてカウントします。
 */
class XCube_Mb_maxlengthValidator extends XCube_Validator
{
	/**
	 * @param XCube_PropertyInterface $property
	 * @param array $vars
	 * @return bool
	 */
	public function isValid(XCube_PropertyInterface $property, $vars)
	{
		if ($property->isNull()) {
			return true;
		} else {
			return mb_strlen($property->toString(), 'UTF-8') <= $vars['mb_maxlength'];
		}
	}
}

/**
 * 日時チェックユーティリティ
 * @internal このクラスを使っていいのはこのプリロードだけ！
 */
class AdvancedValidator_DateTime
{
	/**
	 * 正しい日付かを返す
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 * @return bool
	 */
	public static function isDate($year, $month, $day)
	{
		return checkdate($month, $day, $year);
	}

	/**
	 * 正しい時分秒かを返す
	 * @param int $hour
	 * @param int $minute
	 * @param int $second
	 * @return bool
	 */
	public static function isTime($hour, $minute, $second)
	{
		return ( self::isHour($hour) and self::isMinute($minute) and self::isSecond($second) );
	}

	/**
	 * 正しい時(じ)かを返す
	 * @param int $hour
	 * @return bool
	 */
	public static function isHour($hour)
	{
		return ( 0 <= $hour and $hour <= 23 );
	}

	/**
	 * 正しい分かを返す
	 * @param int $minute
	 * @return bool
	 */
	public static function isMinute($minute)
	{
		return ( 0 <= $minute and $minute <= 59 );
	}

	/**
	 * 正しい秒かを返す
	 * @param int $second
	 * @return bool
	 */
	public static function isSecond($second)
	{
		return ( 0 <= $second and $second <= 59 );
	}
}
