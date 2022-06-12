<?php
/**
 * Rendering of message form.
 *
 * @params string $dat message log.
 * @params integer $resno res number.
 * @params string $admin administrator password.
 * @return void
 */
function form(&$dat,$resno,$admin=""){
  global $addinfo; $msg=""; $hidden="";

  $maxbyte = MAX_KB * 1024;
  $no=$resno;
	ob_start();
?>

  <?php if($resno):?>
    [<a href="<?=h(PHP_SELF2)?>">掲示板に戻る</a>];
    <table width='100%'><tr><th bgcolor=#e04000>
    <font color=#FFFFFF>レス送信モード</font>
    </th></tr></table>
  <?php endif;?>

  <center>
<form action="<?=h(PHP_SELF)?>" method="POST" enctype="multipart/form-data">
<input type="hidden" name="mode" value="regist">
<input type=hidden name="MAX_FILE_SIZE" value="<?=h($maxbyte)?>">


<?php if($no):?>
    <input type=hidden name=resto value="<?=h($no)?>">
<?php endif;?>

  <table cellpadding=1 cellspacing=1>
  <tr><td bgcolor=#eeaa88><b>おなまえ</b></td><td><input type=text name="name" size="28" autocomplete="username"></td></tr>
  <tr><td bgcolor=#eeaa88><b>E-mail</b></td><td><input type=text name="email" size="28"></td></tr>
  <tr><td bgcolor=#eeaa88><b>題　　名</b></td><td><input type=text name="sub" size="35">
  <input type=submit value="送信する"></td></tr>
  <tr><td bgcolor=#eeaa88><b>コメント</b></td><td><textarea name=com cols="48" rows="4" wrap=soft></textarea></td></tr>

  <?php if(RESIMG || !$resno):?>
    <tr><td bgcolor=#eeaa88><b>添付File</b></td>
    <td><input type=file name=upfile size="35">
    [<label><input type=checkbox name=textonly value=on>画像なし</label>]</td></tr>
  <?php endif;?>

  <tr><td bgcolor=#eeaa88><b>削除キー</b></td><td><input type=password name=pwd size=8 value=""><small>(記事の削除用)</small></td></tr>
  <tr><td colspan=2>
  <small>
  <LI>添付可能ファイル：GIF, JPG, PNG ブラウザによっては正常に添付できないことがあります。
  <LI>最大投稿データ量は '.MAX_KB.' KB までです。sage機能付き。
  <LI>画像は横 '.MAX_W.'ピクセル、縦 '.MAX_H.'ピクセルを超えると縮小表示されます。
  <?=h($addinfo)?></small></td></tr></table></form></center><hr>
<?php
	$dat.= ob_get_clean();

}
?>
