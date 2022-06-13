<?php
/**
 * Rendering of error page.
 *
 * @params string $mes message
 * @params string $dest upload file path.
 * @return void
 */
function error($mes,$dest=''){
  global $upfile_name,$path;

  if(is_file($dest)){
    unlink($dest);
  }

  head($dat);
  ob_start();
?>
  <br><br><hr size="1"><br><br>
        <span class="errmsg"><?=$mes?><br><br><a href=<?=h(PHP_SELF2)?>>リロード</a></span>
        <br><br><hr size=1>
  </body></html>

<?php
  $dat.= ob_get_clean();
  echo $dat;
exit;
}
?>
