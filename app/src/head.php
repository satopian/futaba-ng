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
<html><head>
<meta charset="UTF-8"/>
<!-- meta HTTP-EQUIV="pragma" CONTENT="no-cache" -->
<STYLE TYPE="text/css">
<!--
body,tr,td,th { font-size:12pt }
a:hover { color:#DD0000; }
span { font-size:20pt }
small { font-size:10pt }
-->
</STYLE>
<title><?=h(TITLE)?></title>
<script>
function l(){var b=loadCookie("pwdc"),d=loadCookie("namec"),c=loadCookie("emailc"),h=loadCookie("urlc"),a;for(a=0;a<document.forms.length;a++)document.forms[a].pwd&&(document.forms[a].pwd.value=b),document.forms[a].name&&(document.forms[a].name.value=d),document.forms[a].email&&(document.forms[a].email.value=c),document.forms[a].url&&(document.forms[a].url.value=h)}
function loadCookie(b){var d=document.cookie;if(""==d)return"";var c=d.indexOf(b+"=");if(-1==c)return"";c+=b.length+1;b=d.indexOf(";",c);-1==b&&(b=d.length);return decodeURIComponent(d.substring(c,b))};
</script>
</head>
<body bgcolor="#FFFFEE" text="#800000" link="#0000EE" vlink="#0000EE">
<p align="right">
[<a href="<?=h(HOME)?>" target="_top">ホーム</a>]
[<a href="<?=h(PHP_SELF)?>?mode=admin">管理用</a>]
<p align="center">
<font color="#800000" size=5>
<b><SPAN><?=h(TITLE)?></SPAN></b></font>
<hr width="90%" size="1">
<?php
	$dat.= ob_get_clean();
}
?>
