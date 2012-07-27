# ProfileDefaultValueInjector

現時点でprofileモジュールにてデフォルト値を設定することができないのを、プリロードで頑張って解決しようというアプローチです。このプリロードは、フォーム入力画面描画時に `profile_data` テーブルにプロフィールデータがないか確認し、なければフォームの入力値を上書きすることで、デフォルト値を設定できるようにしています。

## インストール

プリロードなので、```ProfileDefaultValueInjector.class.php``` を ```preload``` フォルダに置くだけです。

## 設定

`ProfileDefaultValueInjector.class.php` の `_getDefaultValues()` メソッドを書き換えます。連想配列のキーが項目名、値がデフォルト値になるように、項目を追加していきます。

```php
	/**
	 * Return default values
	 * @return array
	 */
	protected function _getDefaultValues()
	{
		// Write default values here!
		return array(
			'birthday' => '',
			'gender' => '女性',
		);
	}
```
