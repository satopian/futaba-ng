<?php
/**
 * Rendering of header. 
 * 
 * @params string $dat message log.
 * @return void
 */
function head(&$dat){
	ob_start();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8"/>
<!-- meta HTTP-EQUIV="pragma" CONTENT="no-cache" -->
<style>
body,tr,td,th { font-size:12pt }
body{
background-color:#FFFFEE;
color: #800000;	
}
a{
	color: #0000EE;
}
.title{
	color: #800000;
	font-size:160%;
	text-align: center;
}
a:hover { color: #DD0000; }
small { font-size: 10pt }
.csb{color:#cc1105;font-weight:bold;margin: 0 5px;}/*sub*/
.cnm{color:#117743;font-weight:bold;margin: 0 5px;}/*name*/
.cnw{margin: 0 5px;}/*now*/
.rtd{max-width: 1800px;padding-right: 8px;background-color:#F0E0D6;}
.rts{width: 18px;vertical-align:top;}
.ftdc{background-color:#ea8;width:4.5em;white-space:nowrap;}
.chui{font-size:small;}
.errmsg{
	font-size:150%;color:red;
	font-weight: 600;
	text-align: center;
	display: inherit;
}
</style>
<title><?=h(TITLE)?></title>
<script>
function l(){var b=loadCookie("pwdc"),d=loadCookie("namec"),c=loadCookie("emailc"),h=loadCookie("urlc"),a;for(a=0;a<document.forms.length;a++)document.forms[a].pwd&&(document.forms[a].pwd.value=b),document.forms[a].name&&(document.forms[a].name.value=d),document.forms[a].email&&(document.forms[a].email.value=c),document.forms[a].url&&(document.forms[a].url.value=h)}
function loadCookie(b){var d=document.cookie;if(""==d)return"";var c=d.indexOf(b+"=");if(-1==c)return"";c+=b.length+1;b=d.indexOf(";",c);-1==b&&(b=d.length);return decodeURIComponent(d.substring(c,b))};
</script>
</head>
<body>
<p align="right">
[<a href="<?=h(HOME)?>" target="_top">ホーム</a>]
[<a href="<?=h(PHP_SELF)?>?mode=admin">管理用</a>]
<p align="center">
<h1 class="title"><?=h(TITLE)?></h1>
<hr width="90%" size="1">
<?php
	$dat.= ob_get_clean();
}
?>
