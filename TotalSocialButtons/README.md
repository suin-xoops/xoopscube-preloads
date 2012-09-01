# TotalSocialButton

![](https://dl.dropbox.com/u/949822/github.com/xoopscube-preload-total-social-button.png)

ソーシャルボタンをテーマに簡単に埋め込めるプリロードです。

## 要件

* PHP 5.2以上 (5.3以上推奨)
* XOOPS Cube Legacy 2.2 以上

## インストール

1 [`TotalSocialButton.class.php`](https://raw.github.com/suin/xoopscube-preloads/master/TotalSocialButtons/TotalSocialButtons.class.php) を `preload` フォルダに置く。

2 `theme.html` を編集してボタンを表示したい場所に下記のタグを書く。

```
<{xoops_explaceholder control="TotalSocialButton"}>
```

3 `theme.html` の `</body>` の直前に下記のタグを書く。

```
<{xoops_explaceholder control="TotalSocialButton.JavaScript"}>
</body>
```

## カスタマイズ

特定のボタンだけ出したい場合は、 `use` で表示するボタンの名前を指定できます。

Facebookいいね！ボタンだけ出す例:

```
<{xoops_explaceholder control="TotalSocialButton" use="facebook-like"}>
```

複数指定する場合はボタン名をカンマや半角スペースなどで区切ってください。

Google PlusとFacebookいいね！を出す例:

```
<{xoops_explaceholder control="TotalSocialButton" use="google-plus facebook-like"}>
```

利用可能なボタン:

```
mixi-voice        : mixiボイス
mixi-check        : mixiチェック
mixi-diary        : mixi日記
hatena-bookmark   : はてなブックマーク
facebook-like     : Facebookいいね！
twitter           : Twitter
google-plus       : Google+1
google-plus-share : Google+ 共有
```

## Facebookいいね!

Facebookいいね！を使うには `appId` が必要です。[Facebook Developers](https://developers.facebook.com/apps)から取得してください。

```
<{xoops_explaceholder control="TotalSocialButton" use="facebook-like" fb_app_id="1234567890"}>
```

## mixiチェック

mixiチェックは「識別キー」が必要です。[Mixi Developer Center](http://developer.mixi.co.jp)から取得してください。取得方法は、[こちら](http://developer.mixi.co.jp/connect/mixi_plugin/mixi_check/mixicheck/)を御覧ください。

取得したキーは `mixi_key` にセットしてください:

```
<{xoops_explaceholder control="TotalSocialButton" mixi_key="1234567890abcdef1234567890abcdef"}>
```
