# futaba.php

## forked from futoase

PHP4のふたばの画像掲示板をPHP5対応に改造しリファクタリングしたfutoaseさんのスクリプトをベースに、同じくふたばの画像掲示板から派生したPOTI-boardのコードを使って改造してみました。

### 改善点
- PHP8.1で動作するようになりました。
- POTI-boardのGD処理を移植してサムネイルの画質を向上させました。
### 課題
- CSRFトークンをセットできない構造のため、CSRFの攻撃に対して脆弱です。
- 出力時の特殊文字のエスケープは実装されていません。
- XSSの検証は不十分です。
- そのほかセキュリティリスクに関する調査は不十分かもしれません。(改造して数日なので不明な点がまだあります)  
セキュリティチェックのテスト項目が存在していましたが、私の知識ではよくわかりませんでした。  
また、srcと関数単体のテスト、ファイルの結合も私にはわからないので、destのファイルを直接編集させていただきました。  
誰でも投稿できるウェブに設置したときに問題が発生しても、何もできませんのでよろしくお願い致します。  

futaba.php is Message board scripts.  

I branched script to this repository.
Is script used in http://www.2chan.net/.

This script aims running with PHP 5.4.15 or later...

# How to running
- PHP 5.4.15 or later

![みさわ](http://jigokuno.img.jugem.jp/20090928_1487687.gif)

# Lisence

This script for License is Public-domain.
I accordance with the distribution of the [original license](http://www.2chan.net/script/).

thumbnail_gd.php - Copyright (C)SakaQ 2005 >> http://www.punyu.net/php/
