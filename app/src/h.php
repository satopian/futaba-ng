<?php
function h($str){//出力のエスケープ
	return htmlspecialchars((string)$str,ENT_QUOTES,'utf-8',false);
}
?>