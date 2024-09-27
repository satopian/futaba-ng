<?php

/**
 * prettify of message.
 * 
 * @params string $message message.
 * @return string Finished special charactor the replacement.
 */
function CleanStr($message)
{
	$message = trim($message); //先頭と末尾の空白除去

	$message = htmlspecialchars((string)$message, ENT_QUOTES, 'utf-8');
	$message = str_replace(",", "&#44;", $message); //カンマを変換
	return $message;
}
?>
