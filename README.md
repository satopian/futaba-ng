# futaba.php

## forked from futoase

PHP4のふたばの画像掲示板をファクタリングしたfutoaseさんのスクリプトをベースに、同じくふたばの画像掲示板から派生したPOTI-boardのコードを使って改造しました。

### 改善点
- PHP8.1でも動くようになりました。
- POTI-boardのGD処理を移植してサムネイルの画質を向上させました。
- ログファイルに空行が入っている時でもエラーがでなくなりました。
- Chromeのパスワード保存機能を使った時に題名がユーザーネームとして認識されていたため名前がユーザーネームになるように修正しました。
- extract()による変数の取得を廃止して、filter_input()に置き換えました。

### 課題
- CSRFトークンをセットできない構造のため、CSRF攻撃に対して脆弱です。
- 出力時の特殊文字のエスケープは実装されていません。
- XSSの検証は不十分です。
- ~~`$_POST`、`$_GET`に`extract()`が使用しているためユーザーが入力した`name`をもとに変数名がセットされる脆弱性があります。 ~~


誰でも投稿できるウェブに設置したときに問題が発生しても、何もできませんのでよろしくお願い致します。  

futaba.php is Message board scripts.  

I branched script to this repository.
Is script used in http://www.2chan.net/.

This script aims running with PHP 5.4.15 or later...

# How to running
- PHP 5.4.15 or later
```
> cd app
> npm i
> grunt concat:models
> grunt concat:futaba
> grunt shell:phpRunning
```

# ToDo

Script charactor code is UTF-8, But previous code is Shift-JIS. 
There is a problem of compatibility of the log charactor code.

## Countermeasure

Use by nkf (Nihongo Kanji Filter).

```
> nkf -Lu old.log > new.log
```
![みさわ](http://jigokuno.img.jugem.jp/20090928_1487687.gif)

# Lisence

This script for License is Public-domain.
I accordance with the distribution of the [original license](http://www.2chan.net/script/).

thumbnail_gd.php - Copyright (C)SakaQ 2005 >> http://www.punyu.net/php/
