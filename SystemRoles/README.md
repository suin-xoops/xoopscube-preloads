# SystemRoles

XOOPS Cubeでグループを扱うと処理が複雑になり、ソースコードの可読性が低下しがちです。

例えば、グループIDを使った判定は分岐を複雑にします:

```
// 可読性の低いコード
if ( $groupId = 4 or $groupId = 5 or $groupId = 10 or $groupId = 11 ) {
    // 特殊なユーザ向けの処理
}

```

このプリロードは開発者がドメイン固有言語(DSL)を利用して独自のロールを定義できるようにすることで、複雑で難読化しやすいグループ処理を簡素化します:

```
// 定義部分
if ( $user->belogsTo(4) )  $user->playsRole('SpecialUser');
if ( $user->belogsTo(5) )  $user->playsRole('SpecialUser');
if ( $user->belogsTo(10) ) $user->playsRole('SpecialUser');
if ( $user->belogsTo(11) ) $user->playsRole('SpecialUser');


// 可読性が向上した利用部分
if ( $root->mContext->mUser->isInRole('SpecialUser') ) {
    // 特殊なユーザ向けの処理
}

```

## インストール

* SystemRoles.class.php をダウンロードして、 preload フォルダに入れる
* XOOPS_ROOT_PATH/settings/roles.php または XOOPS_TRUST_PATH/settings/roles.php を作る

## 独自ロールの定義

```roles.php``` に独自ロールを定義する必要があります。定義は数個の関数とわずかな行数で書くことができます。記述はドメイン固有言語化したPHPで行います。そのため、定義は読んで意味のわかる（自然な英文として理解できる）コードになります。


### グループに属しているユーザに独自ロールを与える定義

あるグループに属しているユーザに対して、あるロールを与える場合は、 ```belongsTo()``` メソッドと ```playsRole()``` メソッドの2つを使います。典型的な書式は次のようになります:

```
if ( $user->belongsTo(グループID) ) $user->playsRole(ロール名);
```

例えば、「ユーザが営業グループ(グループIDが4)に属する場合、"Sales"ロールを担う」という定義は次のようになります:

```
$salesGroup = 4;
if ( $user->belongsTo($saleGroup) ) $user->playsRole('Sales');
```

"If user belongs to sales group, user plays role 'Sales'."と自然な英文になります。

このロールを実際のプログラムで利用する場合は次のようにします:

```
$root = XCube_Root::getSingleton();

if ( $root->mContext->mUser->isInRole('Sales') ) {
    // code...
}
```

### ロールを担うユーザに独自ロールを与える定義

あるロールを担うユーザに対して、あるロールを与える場合は、 ```hasRole()``` メソッドと ```playsRole()``` メソッドの2つを使います。典型的な書式は次のようになります:

```
if ( $user->hasRole(ロール名) ) $user->playsRole(ロール名);
```

例えば、「ユーザが"Module.bulletin.Admin"ロールを担う場合、"PublishmentManager"ロールも担う」という定義は次のようになります:

```
if ( $user->hasRole('Module.bulletin.Admin') ) $user->playsRole('PublishmentManager');
```

"If user has role 'Module.bulletin.Admin", user plays role 'PublishmentManager'."と自然な英文になります。

## Smartyテンプレートでの利用

SystemRolesプリロードは、ロール情報をSmartyテンプレートで利用可能な変数を自動で assign します。

例えば、```Site.Owner``` ロールを担うユーザの場合、Smartyに ```<{$is_site_owner}>``` がassignされます。この値は ```TRUE``` になります。

独自ロールも Smarty の変数として利用可能です。 ```PublishmentManager``` ロールを担うユーザは ```<{$is_publishment_manager}>``` の値が ```TRUE``` になります。

ビルトインロールのSmarty上での変数名は次のようになります:

* Site.Owner: ```$is_site_owner```
* Site.Administrator: ```$is_site_administrator```
* Site.RegisteredUser: ```$is_site_registered_user```
* Site.GuestUser: ```$is_site_guest_user```
* Module.{dirname}.Admin: ```$is_module_{dirname}_admin```
* Module.{dirname}.Visitor: ```$is_module_{dirname}_visitor```

独自ロールのSmarty上の変数名は、ロール名の ```"is_" + snake_case``` の形なります。ドットはアンダースコアに変換されます。

例えば、```PublishManager``` は ```$is_publish_manager``` になります。 ```Site.PublishManager``` は、```$is_site_pubish_manager``` になります。