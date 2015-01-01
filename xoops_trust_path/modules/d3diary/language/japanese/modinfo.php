<?php
if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3diary' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// Module Info

// The name of this module
define($constpref."_DIARY_NAME","D3ダイアリー");
define($constpref."_DIARY_DESC","D3ダイアリー");

define($constpref."_DIARYLIST","最新の日記一覧");
define($constpref."_FRIENDSDIARY","友人の日記一覧");
define($constpref."_EDIT","日記を書く");
define($constpref."_CATEGORY","カテゴリー");
define($constpref."_COMMENT","コメント一覧");
define($constpref."_CONFIG","日記の設定");
define($constpref."_CONFIG_CATEGORY","カテゴリーの設定");
define($constpref."_YES","はい");
define($constpref."_NO","いいえ");
define($constpref."_PHOTOLIST","画像一覧");

// Admin
define($constpref.'_ADMENU_MYLANGADMIN','言語定数管理');
define($constpref.'_ADMENU_MYTPLSADMIN','テンプレート管理');
define($constpref.'_ADMENU_MYBLOCKSADMIN','ブロック/権限管理');
define($constpref.'_ADMENU_IMPORT','インポート');
define($constpref.'_ADMENU_PERMISSION','パーミッション管理');

// module config
define($constpref."_MENU_LAYOUT","サイドメニューのレイアウト");
define($constpref."_MENU_LAYOUTDESC","日記カレンダーなどのサイドメニューの表示場所");
define($constpref."_MENU_LAYOUT_RIGHT","右側にサイドメニューを表示");
define($constpref."_MENU_LAYOUT_LEFT","左側にサイドメニューを表示");
define($constpref."_MENU_LAYOUT_NONE","サイドメニューを表示しない（ブロックを使用）");

define($constpref."_RIGHT_WEIDTH","サイドメニューの幅");
define($constpref."_RIGHT_WEIDTHDESC","サイドメニューの幅をpixcel単位で入力します。<br />デフォルトは140pixcel");

define($constpref."_USENAME","ユーザー名表示");
define($constpref."_USENAMEDESC","ユーザー名表示に「uname」「name」どちらを使用するかを設定します。<br />xoopsデフォルトは「uname」");
define($constpref."_USENAME_UNAME","「uname」を使用");
define($constpref."_USENAME_NAME","「name」を使用");

define($constpref."_BREADCRUMBS","パンくずを表示する");
define($constpref."_BREADCRUMBSDESC","モジュールページ上部にパンくずを表示する場合は「はい」、<br/>テーマでxoops_breadcrumbsを埋め込み表示する場合は「いいえ」を選択");

define($constpref."_PREV_CHARMAX","プレビュー文字の最大数");
define($constpref."_PREV_CHARMAXDESC","ブロックや一覧表示の際に表示される<br/>プレビュー文字の最大文字数");

define($constpref."_BLK_DNUM","リスト表示の１ページ当たり件数");
define($constpref."_BLK_DNUMDESC","リスト表示の日記の１ページ当たり最大表示件数。<br/>この数値でページ分割される。");

define($constpref."_PHOTO_MAXSIZE","写真の最大サイズ（KB）");
define($constpref."_PHOTO_MAXSIZEDESC","アップロードする写真の最大サイズを<br/>KB（キロバイト）で指定してください。");

define($constpref."_PHOTO_USERESIZE","写真の縮小保存");
define($constpref."_PHOTO_USERESIZEDESC","登録された写真を自動縮小して保存できます。<br/>自動縮小すると最大辺が640pxになります。");
define($constpref."_PHOTO_USERESIZE_Y","縮小保存する");
define($constpref."_PHOTO_USERESIZE_N","縮小保存しない");

define($constpref."_PHOTO_THUMBSIZE","写真のサムネイルサイズ");
define($constpref."_PHOTO_THUMBSIZEDESC","アップロードする写真のサムネイル（縮小表示）サイズを<br/>（ピクセル）で指定してください。");
// define($constpref."_PHOTO_RESIZEMAX","写真の縮小時の最大サイズ");
// define($constpref."_PHOTO_RESIZEMAXDESC","登録された写真を自動縮小するときに、縦／横の最大サイズpx（ピクセル）を指定します");

define($constpref."_PHOTO_MAXPICS","写真の最大掲載可能枚数");
define($constpref."_PHOTO_MAXPICSDESC","アップロード可能な写真の最大数を指定してください。");
define($constpref."_PHOTO_USEINFO","各写真に説明を付ける");
define($constpref."_PHOTO_USEINFODESC","写真に説明文を付加したい場合はYESを選択してください。");

define($constpref."_USE_AVATAR","アバターを表示する");
define($constpref."_USE_AVATARDESC","各人サイドバーとdiarylistでアバターを表示する場合は選択します。");

define($constpref."_USE_OPEN_CAT","カテゴリー毎の権限・外部ブログ設定をON/OFF");
define($constpref."_USE_OPEN_CATDESC","カテゴリー毎の権限設定・外部ブログ設定をユーザーに選択許可する場合、ONを選択。　一旦ONを選択した後にOFFに戻すには、全ユーザーのカテゴリー設定を戻してから行う必要があります。");
define($constpref."_USE_OPEN_CAT_N","カテゴリー毎の権限・外部ブログ設定：OFF");
define($constpref."_USE_OPEN_CAT_Y","カテゴリー毎の権限・外部ブログ設定：ON");
define($constpref."_USE_OPEN_CAT_G","カテゴリー毎の権限：グループ指定まで可能・外部ブログ設定：ON");
define($constpref."_USE_OPEN_CAT_P","カテゴリー毎の権限：グループ・メンバ指定まで可能・外部ブログ設定：ON");

define($constpref."_USE_OPEN_ENTRY","記事毎の権限設定をON/OFF");
define($constpref."_USE_OPEN_ENTRYDESC","記事毎の権限設定をユーザーに選択許可する場合、ONを選択　一旦ONを選択した後にOFFに戻すには、全ユーザーの記事毎の設定を戻してから行う必要があります。");
define($constpref."_USE_OPEN_ENTRY_N","記事毎の権限設定：OFF");
define($constpref."_USE_OPEN_ENTRY_Y","記事毎の権限設定：ON");
define($constpref."_USE_OPEN_ENTRY_G","記事毎の権限設定：グループ指定まで可能");
define($constpref."_USE_OPEN_ENTRY_P","記事毎の権限設定：グループ・メンバ指定まで可能");

define($constpref."_GROUP_SHOW_ALL","全てのグループを権限設定グループセレクタに表示");
define($constpref."_GROUP_SHOW_ALLDESC","権限のグループ指定をする場合に、全てのグループを権限設定グループセレクタに表示する場合は、「はい」。投稿者の所属グループのみ表示し他のグループは非表示とする場合は、「いいえ」とします。<br />どちらの場合も管理者には全てのグループが表示されます。");
define($constpref."_GROUP_EXCLD_SEL","非表示とするグループ");
define($constpref."_GROUP_EXCLD_SELDESC","全てのグループを表示に「はい」を選択した場合にも、表示を隠しておきたいグループがある場合はそのグループを選択します。<br />但し投稿者の所属グループは常に表示されます。また、サイト管理者と一般の登録ユーザーを選択しても意味を持たず、管理者には常に全グループが表示されます。");

define($constpref."_USE_FRIEND","友人機能モジュールとの連携ON/OFF");
define($constpref."_USE_FRIENDDESC","公開範囲を友人までとする含める機能を使うかどうか<br/><br/>※xsnsかmyfriendモジュールをインストールしていない<br/>場合は、ON設定にしないでください。");
define($constpref."_USE_FRIEND_N","友人機能との連携：OFF");
define($constpref."_USE_XSNS_Y","xsnsとの連携：ON");
define($constpref."_USE_MYFRIENDS_Y","myfriendsとの連携：ON");

define($constpref."_FRIEND_DIRNAME","友人機能モジュールのディレクトリ名");
define($constpref."_FRIEND_DIRNAMEDESC","友人機能との連携を行う場合、友人機能モジュールのディレクトリ名を入力してください。");

define($constpref."_EXCERPTOK","タイトル・要約部分は閲覧可能");
define($constpref."_EXCERPTOKDESC","記事単位で権限のない閲覧者に、「下書き」以外の<br/>記事タイトル・要約部分をオープンにする範囲を選択します。");
define($constpref."_EXCERPTOK_NOUSE","閲覧権限の無い記事はタイトル・要約も表示しない");
//define($constpref."_EXCERPTOK_BYPERSON","各人の日記全体の設定に委譲する");
define($constpref."_EXCERPTOK_FORMEMBER","ログインメンバーまでオープンにする");
define($constpref."_EXCERPTOK_FORGUEST","ゲストまでオープンにする");

define($constpref."_DISP_EXCERPTCOM","タイトル・要約部分のみ閲覧可能の場合のコメント表示");
define($constpref."_DISP_EXCERPTCOMDESC","コメントを表示する場合は「はい」、<br/>非表示にしておく場合は「いいえ」を選択。");

define($constpref."_USE_TAG","タグ機能のON/OFF");
define($constpref."_USE_TAGDESC","タグ機能を使用する場合、タグクラウドを表示するページを選択してください。");
define($constpref."_USE_TAG_N","タグ機能：OFF");
define($constpref."_USE_TAG_INDEXONLY","タグクラウドを各人のINDEXページに表示");
define($constpref."_USE_TAG_ALSODIARYLIST","タグクラウドを各人のINDEXページと全ユーザー混合LISTページに表示");
define($constpref."_USE_TAG_BLOCK","タグクラウドをメインページに表示しない。ブロック表示を使用。");

define($constpref."_BODY_EDITOR","本文編集エリアの種類");
define($constpref."_BODY_EDITORDSC","simpleはBBcode入力補助を表示しません。　BBcode入力する場合は、「xoopsdhtml」が便利です。");
define($constpref."_BODY_HTMLEDITOR","本文HTMLエディタボタンの表示");
define($constpref."_BODY_HTMLEDITORDSC","HTMLの許可を「パーミッション管理」タブ画面で行い、ここを「common/FCKeditor」を選択すると、FCKeditorボタンが表示されます。");
define($constpref."_HTMLPR_EXCEPT","HTMLPurifierによる強制書き換えをしないグループ");
define($constpref."_HTMLPR_EXCEPTDSC","ここに指定されて「いない」グループによるHTML投稿は、Protector3.14以上に付属しているHTMLPurifierによって強制的に正しく無毒なHTMLに書き換えられます。ただし、HTMLPurifier自体、PHPバージョンが5以上でないと機能しません。");
define($constpref."_GTICKET_SET_TIME","記事送信フォームのチケットタイムアウト時間(秒)");
define($constpref."_GTICKET_SET_TIMEDSC","フォームを表示してからタイムアウトになるまでの時間設定。<br />タイムアウトになっても再送信すれば投稿できます。");

define($constpref."_USE_UPDATEPING","更新ping送信許可" );
define($constpref."_USE_UPDATEPING_DSC","更新ping送信の許可を指定します。" );
define($constpref."_UPDATEPING","更新pingサーバー" );
define($constpref."_UPDATEPING_DSC","更新pingサーバーを指定します。改行で区切ります。" );
define($constpref."_UPDATEPING_SERVERS","http://ping.rss.drecom.jp/\nhttp://blog.goo.ne.jp/XMLRPC" );
define($constpref."_ENABLE_SHOWOPTION","記事リストページオプションを表示する" );
define($constpref."_ENABLE_SHOWOPTION_DSC","各人Indexページ・diaryリストページでの全文表示オプションをカテゴリー毎に設定可能としますか。" );
define($constpref."_ENC_FROM" , "RSSフィードへの変換用の内部エンコード");
define($constpref."_ENC_FROMDSC" , "通常は'default'でOKですが、RSSフィードが文字化けする場合は、'xoops_chrset'や'auto'をお試しください。");
define($constpref.'_PERM_CLASS' , '閲覧権限処理クラス名');
define($constpref.'_PERM_CLASSDSC' , '閲覧権限処理をオーバーライドしたい時に指定。デフォルトはd3diaryPermission');

define($constpref.'_USE_MAILPOST' , 'メールによる投稿を可能にする');
define($constpref.'_USE_MAILPOSTDSC' , 'メールによる投稿を可能にする場合は「yes」を選択し、パーミッションでグループに許可を与えます。');
define($constpref."_POP3_SERVER","受信メールサーバ");
define($constpref."_POP3_SERVER_DESC","受信メールのPOP3サーバ名");
define($constpref."_POP3_PORT","受信ポート番号");
define($constpref."_POP3_PORT_DESC","pop3サーバはたいてい110ですが、サーバに合わせてください。");
define($constpref."_POP3_APOP","APOP暗号化認証を使用する");
define($constpref."_POP3_APOP_DESC","APOP暗号化認証を使用するかどうか、サーバの設定に合わせてください。");
define($constpref."_POST_EMAIL_ADDRESS","取込み用メールアカウントID");
define($constpref."_POST_EMAIL_ADDRESS_DESC","取込み用のメールアカウントIDを設定して下さい。");
define($constpref."_POST_EMAIL_PASSWORD","取込み用メールアドレスのパスワード");
define($constpref."_POST_EMAIL_PASSWORD_DESC","取込み用メールアドレスのパスワードを設定して下さい。");
define($constpref."_POST_EMAIL_FULLADD","メール送信先アドレス");
define($constpref."_POST_EMAIL_FULLADDDSC","取込み用のメール送信先アドレス。説明ページへの表示用で、制御には使われません。");
define($constpref."_POST_DETECT_ORDER","メール文字列エンコード検出順指定");
define($constpref."_POST_DETECT_ORDERDSC","メール文字列エンコードの検出順を指定します。<br />空欄は'auto'を意味し、これで文字化けする場合、'ISO-2022-JP, UTF-8, UTF-7, ASCII, EUC-JP, JIS, SJIS, eucJP-win, SJIS-win'の中から列挙してみます。<br />例：'ISO-2022-JP, UTF-8, JIS, EUC-JP, eucJP-win, SJIS'");

define($constpref."_TB_APPROVAL","トラックバック受付は承認を必要とする");
define($constpref."_TB_TICKET","チケット式トラックバックURLを使う");
define($constpref."_TB_TICKETDSC","Javascriptが使えないユーザーには無効ですので注意してください。生存時間はデフォルトで1日。");
define($constpref."_TB_NOT_ADMIN","トラックバックがあったら通知する");
define($constpref."_TB_NOT_ADMINDSC","モジュール管理ユーザー宛に通知します。");

define($constpref."_USE_SIMPLECOMMENT","コメントの表示モード");
define($constpref."_USE_SIMPLECOMMENTDESC","従来のXOOPS標準のコメントを使う代わりに、<br/>簡易的なコメントフォームを使うことができます。<br/><br/>※簡易フォームの場合、匿名投稿はできません。");
define($constpref."_USE_SIMPLECOMMENT_Y","簡易的なコメントモードを使う");
define($constpref."_USE_SIMPLECOMMENT_N","XOOPSの標準コメント機能を使う");

//d3comment integration
define($constpref."_COM_DIRNAME","コメント統合するd3forumのdirname");
define($constpref."_COM_DIRNAMEDSC","d3forumのコメント統合機能を使用する場合は<br/>フォーラムのhtml側ディレクトリ名を指定します。<br/>xoopsコメントを使用する場合やコメント機能を無効にする場合は空欄です。");
define($constpref."_COM_FORUMID","コメント統合するフォーラムの番号");
define($constpref."_COM_FORUMIDDSC","コメント統合を選択した場合、forum_idを必ず指定してください。");
define($constpref."_COM_ORDER","コメント統合の表示順序");
define($constpref."_COM_ORDERDSC","コメント統合を選択した場合の、コメントの新しい順／古い順を指定できます。");
define($constpref."_COM_VIEW","コメント統合の表示方法");
define($constpref."_COM_VIEWDSC","フラット表示かスレッド表示かを選択します。");
define($constpref."_COM_POSTSNUM","コメント統合のフラット表示における最大表示件数");
define($constpref."_COM_ANCHOR","コメント統合の記事アンカー");
define($constpref."_COM_ANCHORDSC","記事アンカーのデフォルトは「post_path」です。<br />コメントスレッドを分割しても関連性を保てる「post_id」を使う場合、<br />d3forum側のテンプレートを編集し、ここで変更します。（相互連動はしません。）");
define($constpref."_USE_COM_ANCHOR_UNIQUEPATH","d3forumデフォルトの「post_path」を使う");
define($constpref."_USE_COM_ANCHOR_POSTNUM","「post_id」を使う");

//notifications
define($constpref."_GLOBAL_NOTIFY","全体の日記");
define($constpref."_GLOBAL_NOTIFYDSC","全体の日記");
define($constpref."_BLOGGER_NOTIFY","特定メンバーの日記");
define($constpref."_BLOGGER_NOTIFYDSC","特定メンバーの日記");

define($constpref."_GLOBAL_NEWENTRY_NOTIFY","日記の新規投稿");
define($constpref."_GLOBAL_NEWENTRY_NOTIFYCAP","日記全体で新規投稿があったら通知します");
define($constpref."_GLOBAL_NEWENTRY_NOTIFYDSC","日記全体で新規投稿があったら通知する");
define($constpref."_GLOBAL_NEWENTRY_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE}:新規記事投稿");

define($constpref."_BLOGGER_NEWENTRY_NOTIFY","特定メンバーの日記の投稿");
define($constpref."_BLOGGER_NEWENTRY_NOTIFYCAP","この人の日記で新規投稿があったら通知します");
define($constpref."_BLOGGER_NEWENTRY_NOTIFYDSC","この人の日記で新規投稿があったら通知する");
define($constpref."_BLOGGER_NEWENTRY_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE}:新規記事投稿");

define($constpref."_BLOGGER_COMMENT_NOTIFY","特定メンバーの日記へのコメント[d3コメント統合限定]");
define($constpref."_BLOGGER_COMMENT_NOTIFYCAP","この人の日記へのコメントがあったら通知します");
define($constpref."_BLOGGER_COMMENT_NOTIFYDSC","この人の日記へのコメントがあったら通知する");
define($constpref."_BLOGGER_COMMENT_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE}:コメント通知");

define($constpref."_ENTRY_NOTIFY","この日記（記事）へのコメント");
define($constpref."_ENTRY_NOTIFYDSC","この日記（記事）へのコメントがあったら通知");

// Block
define($constpref."_BLOCK_NEWENTRY","新着日記／ブログ");
define($constpref."_BLOCK_NEWENTRYDSC","新着コンテンツ - 日記／ブログ");
define($constpref."_BLOCK_BLOGGER","日記投稿者リスト");
define($constpref."_BLOCK_BLOGGERDSC","日記投稿者リスト");
define($constpref."_BLOCK_D3COMPOSTS","日記コメント投稿リスト");
define($constpref."_BLOCK_D3COMPOSTSDSC","d3コメント統合時のみ有効です");
define($constpref."_BLOCK_D3COMTOPICS","日記コメントトピック");
define($constpref."_BLOCK_D3COMTOPICSDSC","d3コメント統合時のみ有効です");
define($constpref."_BLOCK_PERSON","投稿者");
define($constpref."_BLOCK_PERSONDSC","日記投稿者ブロック");
define($constpref."_BLOCK_CALENDAR","カレンダー");
define($constpref."_BLOCK_CALENDARDSC","日記のカレンダーブロック");
define($constpref."_BLOCK_CATEGORY","カテゴリー");
define($constpref."_BLOCK_CATEGORYDSC","日記のカテゴリブロック");
define($constpref."_BLOCK_ENTRY","新着日記");
define($constpref."_BLOCK_ENTRYDSC","日記の新着ブロック");
define($constpref."_BLOCK_COMMENT","新着コメント");
define($constpref."_BLOCK_COMMENTDSC","日記コメントの新着ブロック");
define($constpref."_BLOCK_MLIST","月表示");
define($constpref."_BLOCK_MLISTDSC","日記月表示ブロック");
define($constpref."_BLOCK_FRIENDS","友人リスト");
define($constpref."_BLOCK_FRIENDSDSC","日記友人リストブロック");
define($constpref."_BLOCK_TAGCROUD","タグクラウド");
define($constpref."_BLOCK_TAGCROUDDSC","タグクラウドブロック");
define($constpref."_BLOCK_PHOTOS","画像表示");
define($constpref."_BLOCK_PHOTOSDSC","画像表示ブロック");

//others
define($constpref."_BLOGGER","さんの日記");

}
?>
