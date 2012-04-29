<?php

// D3DIARY IMPORT
define('_MD_D3DIARY_H2_IMPORTFROM','インポート');
define('_MD_D3DIARY_H3_IMPORTDATABASE','d3diaryへのデータベースコピー（複製）');
define('_MD_D3DIARY_BTN_DOIMPORT','インポート実行');
define('_MD_D3DIARY_LABEL_SELECTMODULE','モジュール選択');
define('_MD_D3DIARY_CONFIRM_DOIMPORT','インポートを実行してよろしいですか?');

define('_MD_D3DIARY_HELP_IMPORTFROM','現在インポートに対応しているのは d3diary 、minidiary 、weblogD3 、d3blogです。weblogD3はtrackbackテーブルのインポートは行いません。d3blogはtrackbackテーブルのインポートも行いますがtrackback機能はありません。　また、いずれもカテゴリは予め２階層までにまとめておく必要があります。<br /><font color="#FF0000">インポート先のd3diaryは全て上書きされますのでご注意ください。</font>');
define('_MD_D3DIARY_IMPORTDONE','インポートが完了しました');
define('_MD_D3DIARY_ERR_INVALIDMID','指定されたモジュールからはインポートできません');
define('_MD_D3DIARY_SQLONIMPORT','インポートに失敗しました。インポート元とインポート先で、テーブル構造が違う可能性があります。<br />　両方とも最新版にアップデートしているか確認してください');

define('_MD_D3DIARY_H3_IMPORTCOM','コメントデータベース移動');
define('_MD_D3DIARY_HELP_COMIMPORT','コメントインポートで、コメントは移動されます。<br />　一旦移動すると、元には戻せません。');
define('_MD_D3DIARY_CONFIRM_DOCOMIMPORT','コメントを移動してよろしいですか?');

define('_MD_D3DIARY_H3_IMPORTNOTIF','イベント通知設定データベース移動');
define('_MD_D3DIARY_HELP_NOTIFIMPORT','イベント通知設定インポートで、イベント通知設定は移動されます。<br />　一旦移動すると、元には戻せません。');
define('_MD_D3DIARY_CONFIRM_DONOTIFIMPORT','イベント通知設定を移動してよろしいですか?');

// D3DIARY PERMISSION
define('_MD_D3DIARY_LANG_PERMISSION_MANAGER','パーミッション');
define('_MD_D3DIARY_LANG_CATEGORY_NAME','カテゴリー');
define('_MD_D3DIARY_LANG_CATEGORY_GLOBAL','全般');
define('_MD_D3DIARY_LANG_GROUP_NAME','グループ名');
define('_MD_D3DIARY_PERMDESC_ALLOW_EDIT','日記投稿許可');
define('_MD_D3DIARY_PERMDESC_ALLOW_HTML','HTML投稿許可');
define('_MD_D3DIARY_PERMDESC_ALLOW_REGDATE','任意日付での投稿を許可');
define('_MD_D3DIARY_PERMDESC_ALLOW_GPERM','記事毎のグループ閲覧権限設定を許可');
define('_MD_D3DIARY_PERMDESC_ALLOW_PPERM','記事毎のユーザー閲覧権限設定を許可');
define('_MD_D3DIARY_PERMDESC_ALLOW_MAILPOST','メールによる投稿を許可');
define('_MD_D3DIARY_MESSAGE_DBUPDATE_FAILED','パーミッション設定の更新が失敗しました');
define('_MD_D3DIARY_MESSAGE_DBUPDATE_SUCCESS','パーミッション設定を更新しました');

// For D3pipes Options
define('_MD_D3DIARY_MAXTEXT','本文最大文字数:'); 
define('_MD_D3DIARY_TOPICSPOSTS','表示ブロック:'); 
define('_MD_D3DIARY_USEAGGRE','他のフォーラムとの集約表示:'); 
define('_MD_D3DIARY_CATLIMIT','カテゴリーID');
define('_MD_D3DIARY_FORUMLIMIT','フォーラムID');
define('_MD_D3DIARY_DISPLAY_PERSONAL','一人当たり最大表示件数:');

?>
