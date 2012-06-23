# OpenGraphProtocolPlugin

OpenGraphProtocolPlugin は OGP(Open Graph Protocol)をXOOPS Cube Legacyで簡単に対応するためのプリロードです。marineさんの [XOOPSに適切なメタ情報とOGPを付加するカスタマイズ](http://xoops123.com/modules/d3downloads/index.php?page=singlefile&cid=9&lid=61) の盛大なパクリです。

OGPというのはThe Open Graph protocolの略でフェイスブックを始めmixiやGoogle＋等のSNSとウェブページを連携…まあ要するにSNSとフレンドリーになる仕組みってことです！

## インストール

プリロードなので、```OpenGraphProtocolPlugin.class.php``` を ```preload``` フォルダに置くだけです。

## プログラマ向けAPI

```OGP.SetUp``` というデリゲートを用意してあるので、モジュール開発者は自分のモジュールでOGPに任意の情報をセットすることができます。

第一引数の ```$ogpData``` が配列なので、そこに好きな情報をいれてください。（詳細は後述）

### コールバック関数のインターフェイス

コールバック関数のインターフェイスは下記のようにしてください。

```
/**
 * @param array $ogpData
 * @param Legacy_XoopsTpl $xoopsTpl
 */
public function (array &$ogpData, Legacy_XoopsTpl $xoopsTpl)
```

### $ogpData のプロパティ

$ogpData　でサポートするプロパティは下記のとおりです。

```
return array(
	'title'       => , // ページタイトル
	'url'         => , // ページURL
	'image'       => , // 画像のURL
	'site_name'   => , // サイトタイトル
	'description' => , // 概要
);
```

全部任意です。セットしないと、デリゲート呼び出し元が適当にデフォルト値をセットします。

ちなみに、```buleltin``` モジュールでは ```xoops_trust_path/modules/bulletin/main/article.php``` の 180行目あたりに、

```php
$root = XCube_Root::getSingleton();
$root->mDelegateManager->add('OGP.SetUp', function (array &$ogpData) use ($story, $description) {
	return array(
		'title'       => $story['title'],
		'description' => $description,
	);
});
```

って書いたところ、OGPタグの記事タイトル、本文に置き換わりました。