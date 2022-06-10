<?php
//逆変換テーブル作成
function get_lineindex ($line){
	$lineindex = [];
	foreach($line as $i =>$value){
		if(!trim($value)){
		continue;
		}
		list($no,) = explode(",", $value);
		$lineindex[$no] = $i; // 値にkey keyに記事no
	}
	return $lineindex;
}
?>