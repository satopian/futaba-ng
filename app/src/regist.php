<?php
/**
 * Publish to futaba borad.
 *
 * @params string $name user name.
 * @params string $email user email address.
 * @params string $sub subject.
 * @params string $comment user comment.
 * @params string $url 
 * @params string $pwd user password.
 * @params string $upfile upload file path.
 * @params string $upfile_name upload filename.
 * @params string $resto thread target number.
 * @return void
 */
function regist($resto=0){
  global $path,$badstring,$badfile,$badip,$pwdc,$textonly;
  $dest="";$mes="";
  $name = (string)filter_input(INPUT_POST, 'name');
  $email = (string)filter_input(INPUT_POST, 'email');
  $sub = (string)filter_input(INPUT_POST, 'sub');
  $pwd = (string)(filter_input(INPUT_POST, 'pwd'));
  $textonly = (string)(filter_input(INPUT_POST, 'textonly',FILTER_VALIDATE_BOOLEAN));
  $url = '';
  $comment = (string)filter_input(INPUT_POST, 'com');
  $upfile_name=isset($_FILES["upfile"]["name"]) ? $_FILES["upfile"]["name"] : "";
  $upfile=isset($_FILES["upfile"]["tmp_name"]) ? $_FILES["upfile"]["tmp_name"] : "";

  // 時間
  $time = time();
  $tim = $time.substr(microtime(),2,3);


  // アップロード処理
  if(isset($_FILES["upfile"]["error"])){//エラーチェック
	if(in_array($_FILES["upfile"]["error"],[1,2])){
		error('ファイルサイズが大きすぎます。');//容量オーバー
	} 
 }
 $W="";
 $H="";
 $extension="";

  if($upfile&&is_file($upfile)){
    $dest = ImageFile::getNew()->createTempFileName($path, $tim);
    move_uploaded_file($upfile, $dest);
    //↑でエラーなら↓に変更
    //copy($upfile, $dest);
    $upfile_name = CleanStr($upfile_name);
    if(!is_file($dest)){
      error("アップロードに失敗しました<br>サーバがサポートしていない可能性があります",$dest);
    }
    $size = getimagesize($dest);
    if(!is_array($size)){
      error("アップロードに失敗しました<br>画像ファイル以外は受け付けません",$dest);
    }
    $is_uploaded = ImageFile::getNew()->isUploaded($badfile, $dest);
    if ($is_uploaded === true) {
      error("アップロードに失敗しました<br>同じ画像がありました", $dest); //拒絶画像
      return;
    }
    chmod($dest,0606);
   
    // size[0] is width, size[1] is height. 
    $desired_size = ImageFile::adjustmentImageCanvasSize(
      $size[0], $size[1]
    );
    $W = $desired_size['width'];
    $H = $desired_size['height'];
    $extension = ExtensionRepository::find($size[2]);

    $mes = "画像 $upfile_name のアップロードが成功しました<br><br>";
  }

  foreach($badstring as $value){
    $pattern = '/' . $value . '/';
    if(preg_match($pattern, $comment) === 1 || 
       preg_match($pattern, $sub) === 1 || 
       preg_match($pattern, $name) === 1 || 
       preg_match($pattern, $email) === 1 ){
      error("拒絶されました(str)",$dest);
    };
  }
  if($_SERVER["REQUEST_METHOD"] != "POST"){
    error("不正な投稿をしないで下さい(post)",$dest);
  }
  // フォーム内容をチェック
  if(!$name||preg_match("/^[ |　|]*$/",$name) === 1){
    $name="";
  }
  if(!$comment||preg_match("/^[ |　|\t]*$/",$comment) === 1){
    $comment="";
  }
  if(!$sub||preg_match("/^[ |　|]*$/",$sub) === 1){
    $sub=""; 
  }

  if(!$resto&&!$textonly&&!is_file($dest)){
    error("画像がありません",$dest);
  }
  if(!$comment&&!is_file($dest)){
    error("何か書いて下さい",$dest);
  }

  $name=preg_replace("/管理/","\"管理\"",$name);
  $name=preg_replace("/削除/","\"削除\"",$name);

  if(strlen($comment) > 1000){
    error("本文が長すぎますっ！",$dest);
  }
  if(strlen($name) > 100){
    error("本文が長すぎますっ！",$dest);
  }
  if(strlen($email) > 100){
    error("本文が長すぎますっ！",$dest);
  }
  if(strlen($sub) > 100){
    error("本文が長すぎますっ！",$dest);
  }
  if(strlen($resto) > 10){
    error("異常です",$dest);
  }
  if(strlen($url) > 10){
    error("異常です",$dest);
  }

  //ホスト取得
  $host = gethostbyaddr($_SERVER["REMOTE_ADDR"]);

  foreach($badip as $value){ //拒絶host
    if(preg_match("/$value$/i",$host)){
     error("拒絶されました(host)",$dest);
    }
  }

  if(preg_match("/^mail/i",$host)
    || preg_match("/^ns/i",$host)
    || preg_match("/^dns/i",$host)
    || preg_match("/^ftp/i",$host)
    || preg_match("/^prox/i",$host)
    || preg_match("/^pc/i",$host)
    || preg_match("/^[^\.]\.[^\.]$/i",$host)){
    $pxck = "on";
  }

  if(preg_match("/ne\\.jp$/i",$host)||
    preg_match("/ad\\.jp$/i",$host)||
    preg_match("/bbtec\\.net$/i",$host)||
    preg_match("/aol\\.com$/i",$host)||
    preg_match("/uu\\.net$/i",$host)||
    preg_match("/asahi-net\\.or\\.jp$/i",$host)||
    preg_match("/rim\\.or\\.jp$/i",$host)
    ){
    $pxck = "off";
  }
  else{
    $pxck = "on";
  }

  if($pxck=="on" && PROXY_CHECK){
    if(proxy_connect('80') == 1){
      error("ＥＲＲＯＲ！　公開ＰＲＯＸＹ規制中！！(80)",$dest);
    } elseif(proxy_connect('8080') == 1){
      error("ＥＲＲＯＲ！　公開ＰＲＯＸＹ規制中！！(8080)",$dest);
    }
  }

  // No.とパスと時間とURLフォーマット
  srand();
  if($pwd==""){
    if($pwdc==""){
      $pwd=rand();$pwd=substr($pwd,0,8);
    }else{
      $pwd=$pwdc;
    }
  }

  $c_pass = $pwd;
  $pass = ($pwd) ? substr(md5($pwd),2,8) : "*";
  $youbi = array('日','月','火','水','木','金','土');
  $yd = $youbi[gmdate("w", $time+9*60*60)] ;
  $now = (
    gmdate("y/m/d",$time+9*60*60) . 
    "(" .(string)$yd . ")" . 
    gmdate("H:i",$time+9*60*60)
  );

  if(DISP_ID){
    if($email&&DISP_ID==1){
      $now .= " ID:???";
    }else{
      $now.=" ID:".substr(crypt(md5($_SERVER["REMOTE_ADDR"].IDSEED.gmdate("Ymd", $time+9*60*60)),'id'),-8);
    }
  }

  $email = PrettifyText::replaceStringOfMail($email);
  $sub   = PrettifyText::replaceStringOfSubject($sub);
  $url   = PrettifyText::replaceStringOfUrl($url);
  $resto = PrettifyText::replaceStringOfResNumber($resto);
  $comment = PrettifyText::replaceStringOfComment($comment);
  $name  = PrettifyText::replaceStringOfName($name);
  $names = $name;

  if(!$name){
    $name="名無し";
  }
  if(!$comment){
    $comment="本文なし";
  }
  if(!$sub){
    $sub="無題"; 
  }

  //ログ読み込み
  $fp=fopen(LOGFILE,"r+");
  flock($fp, 2);
  rewind($fp);
  $buf=fread($fp,1000000);
  if($buf==''){ 
    error("error load log",$dest);
  }
  $line = explode("\n",$buf);
  $countline=count($line);
  $lineindex=get_lineindex($line);//逆変換テーブル作成

  // 二重投稿チェック
  $imax=count($line)>20 ? 20 : count($line)-1;
  for($i=0;$i<$imax;$i++){
    list($lastno,,$lname,,,$lcom,,$lhost,$lpwd,,,,$ltime,) = explode(",", $line[$i]);
    if(strlen($ltime)>10){
      $ltime=substr($ltime,0,-3);
    }
    if($host==$lhost||substr(md5($pwd),2,8)==$lpwd||substr(md5($pwdc),2,8)==$lpwd){
      $p=1;
    }
    else{
      $p=0;
    }

    if(RENZOKU && $p && $time - $ltime < RENZOKU){
      error("連続投稿はもうしばらく時間を置いてからお願い致します",$dest);
    }

    if(RENZOKU && $p && $time - $ltime < RENZOKU2 && $upfile_name){
      error("画像連続投稿はもうしばらく時間を置いてからお願い致します",$dest);
    }
    if(RENZOKU && $p && $comment == $lcom && !$upfile_name){
      error("連続投稿はもうしばらく時間を置いてからお願い致します",$dest);
    }
  }

  // ログ行数オーバー
  if(count($line) >= LOG_MAX){
    for($d = count($line)-1; $d >= LOG_MAX-1; $d--){
      list($dno,,,,,,,,,$dext,,,$dtime,) = explode(",", $line[$d]);
      if(is_file($path.$dtime.$dext)){
        unlink($path.$dtime.$dext);
      }
      if(is_file(THUMB_DIR.$dtime.'s.jpg')){
        unlink(THUMB_DIR.$dtime.'s.jpg');
      }
      unset($line[$d]);
      treedel($dno);
    }
  }
  // アップロード処理
  $chk=''; 
  if($dest&&is_file($dest)){
    $imax=count($line)>200 ? 200 : count($line)-1;
    $chk = md5_file($dest);

    for($i=0;$i<$imax;$i++){ //画像重複チェック
		if(!trim($line[$i])){
			continue;
		}

      list(,,,,,,,,,$extensionp,,,$timep,$p,) = explode(",", $line[$i]);

	  if($chk===$p&&is_file($path.$timep.$extensionp)){
        error("アップロードに失敗しました<br>同じ画像があります",$dest);
      }
    }
  }
  list($lastno,) = explode(",", $line[0]);
  $no = $lastno + 1;
  $newline = "$no,$now,$name,$email,$sub,$comment,$url,$host,$pass,$extension,$W,$H,$tim,$chk,\n";
  $newline.= implode("\n", $line);
  ftruncate($fp,0);
  set_file_buffer($fp, 0);
  rewind($fp);
  fputs($fp, $newline);

    //ツリー更新
  $find = false;
  $newline = '';
  $tp=fopen(TREEFILE,"r+");
  set_file_buffer($tp, 0);
  rewind($tp);
  $buf=fread($tp,1000000);
  if($buf==''){error("error tree update",$dest);}
  $line = explode("\n",$buf);
  $countline=count($line);
  for($i = 0; $i < $countline; $i++){
    if($line[$i]!=""){
      $line[$i].="\n";
      $j=explode(",", rtrim($line[$i]));
      if(!isset($lineindex[$j[0]])){
        unset($line[$i]);
      } 
    } 
}
	if($resto){
    for($i = 0; $i < $countline; $i++){
      $rtno = explode(",", rtrim($line[$i]));
      if($rtno[0]==$resto){
        $find = TRUE;
        $line[$i]=rtrim($line[$i]).','.$no."\n";
        $j=explode(",", rtrim($line[$i]));
        if(count($j)>MAX_RES){
          $email='sage';
        }
        if(!stristr($email,'sage')){
          $newline=$line[$i];
          $line[$i]='';
        }
        break;
      } 
    } 
  }

  if(!$find){
    if(!$resto){
      $newline="$no\n";
    }
    else{
      error("スレッドがありません",$dest);
    }
  }
  $newline.=implode('', $line);
  ftruncate($tp,0);
  set_file_buffer($tp, 0);
  rewind($tp);
  fputs($tp, $newline);
  fclose($tp);
  fclose($fp);

  //クッキー保存
 setcookie ("pwdc", $c_pass,time()+7*24* 3600);  /* 1週間で期限切れ */
 $c_name=$names;
 setcookie ("namec", $c_name,time()+7*24*3600);  /* 1週間で期限切れ */

  if($dest&&is_file($dest)){
    rename($dest,$path.$tim.$extension);
    if(USE_THUMB){thumb($path,$tim,$extension,MAX_W,MAX_H);}
  }
  updatelog();

  echo "<html><head><meta charset=\"UTF-8\"><meta http-equiv=\"refresh\" content=\"1;URL=".PHP_SELF2."\"></head>";
  echo "<body>$mes 画面を切り替えます</body></html>";
}
?>
