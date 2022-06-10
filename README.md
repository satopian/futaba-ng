# futaba.php

## forked from futoase

PHP4のふたばの画像掲示板をPHP5対応に改造しリファクタリングしたfutoaseさんのスクリプトをベースに、同じくふたばの画像掲示板から派生したPOTI-boardのコードを使って改造してみました。

### 改善点
- PHP8.1で動作するようになりました。
- POTI-boardのGD処理を移植してサムネイルの画質を向上させました。
### 課題
- CSRFトークンをセットできない構造のため、CSRF攻撃に対して脆弱です。
- 出力時の特殊文字のエスケープは実装されていません。
- XSSの検証は不十分です。
- `$_POST`の取得に`extract()`が使用されているので、任意に変数がセットされてしまう脆弱性があります。  
- そのほかセキュリティリスクに関する調査も不十分です。

誰でも投稿できるウェブに設置したときに問題が発生しても、何もできませんのでよろしくお願い致します。  

futaba.php is Message board scripts.  

I branched script to this repository.
Is script used in http://www.2chan.net/.

This script aims running with PHP 5.4.15 or later...

# How to running
- PHP 5.4.15 or later
```
> cd app
> grunt concat:model
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
