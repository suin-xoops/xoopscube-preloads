# AwesomeXoopsMapClassLoader

クラスのオートロード機能を提供するプリロード

## 特徴

* マッピングされたクラスは ```require_once``` が不要になる

## インストール

* preload フォルダに AwesomeXoopsMapClassLoader.class.php を置く


## モジュールでの利用

* XOOPS_TRUST_PATH/modules/your_module/class_map.php を作るとこのプリロードが ```preFilter``` のタイミングで読み込みます。

```class_map.php``` の書式:

```
<?php
return array(
	'ClassName1' => XOOPS_TRUST_PATH.'/modules/your_module/class/ClassName1.class.php',
	'ClassName2' => XOOPS_TRUST_PATH.'/modules/your_module/class/ClassName2.class.php',
	'ClassName3' => XOOPS_TRUST_PATH.'/modules/your_module/class/ClassName3.class.php',
	// … more classes and paths
);

```

* MacやLinuxでは同梱の ```make_class_map``` を使うとこの ```class_map.php``` は簡単につくれます:

```
$ cd /path/to/your/module
$ ./make_class_map > class_map.php
```
