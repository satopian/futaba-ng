<?php
/**
 * Update message.
 * 
 * @params integer $resno target message number.
 * @return void
 */
function updatelog($resno=0){
  global $path;$p=0;

  $tree = file(TREEFILE);
  $find = false;
  if($resno){
    $counttree=count($tree);
    for($i = 0;$i<$counttree;$i++){
      list($artno,)=explode(",",rtrim($tree[$i]));
      if($artno==$resno){ //レス先検索
        $st=$i;$find=true;break;
      }
    }
    if(!$find){
      error("該当記事がみつかりません");
    }
  }
  $line = file(LOGFILE);
  $countline=count($line);
  $lineindex = get_lineindex($line); // 逆変換テーブル作成

  $counttree = count($tree);
  for($page=0;$page<$counttree;$page+=PAGE_DEF){
    $dat='';
    head($dat);
    form($dat,$resno);
    if(!$resno){
      $st = $page;
    }
    $dat.='<form action="'.PHP_SELF.'" method=POST>';

  for($i = $st; $i < $st+PAGE_DEF; $i++){
	ob_start();

    if(!isset($tree[$i])){
      continue;
    }
    $treeline = explode(",", rtrim($tree[$i]));
    $disptree = $treeline[0];
	if(!isset($lineindex[$disptree])){
		continue;
	}
    $j=$lineindex[$disptree] ; //該当記事を探して$jにセット

    if(!trim($line[$j])){
		continue;
    } //$jが範囲外なら次の行
    
    list($no,$now,$name,$email,$sub,$com,$url,
         $host,$pwd,$ext,$w,$h,$time,$chk) = explode(",", $line[$j]);
	$com = preg_replace("#<br( *)/?>#i","\n",$com); // <br>または<br />を改行へ戻す
	// URLとメールにリンク
	$email=filter_var($email,FILTER_VALIDATE_EMAIL)?$email:'';
   
    // 画像ファイル名
    $img = $path.$time.$ext;
    $src = IMG_DIR.$time.$ext;
	?>
    <?php if($ext && is_file($img)):?>
      <?php $size = filesize($img);//altにサイズ表示?>
      画像タイトル：<a href="<?=h($src)?>" target=_blank><?=h($time.$ext)?></a>-(<?h($size)?> B)<br>
	  <?php if($w && $h):?><!-- サイズがある時 -->
		
        <?php if(@is_file(THUMB_DIR.$time.'s.jpg')):?>
          <small>サムネイルを表示しています.クリックすると元のサイズを表示します.</small><br><a href="<?=h($src)?>" target=_blank><img src="<?=h(THUMB_DIR.$time.'s.jpg')?>" border=0 align=left width="<?=h($w)?>" height="<?=h($h)?>" hspace=20 alt="<?=h($size)?> B"></a>
        <?php else:?>
          <a href="<?=h($src)?>" target=_blank><img src="<?=h($src)?>
      " border="0" align="left" width="<?=h($w)?>" height="<?=h($h)?>" hspace="20" alt="<?=h($size)?> B"></a>";
      <?php endif;?>
      <?php endif;?>

    <?php endif;?>

    <!-- // メイン作成 -->
    <input type=checkbox name="del[]" value="<?=h($no)?>"><font color=#cc1105 size=+1><b><?=h($sub)?></b></font>
    <font color=#117743><b>
	<?php if($email):?><a href="mailto:<?=h($email)?>"><?=h($name)?></a><?php else:?><?=h($name)?><?php endif;?></b>
	</font> <?=h($now)?> No.<?=h($no)?> &nbsp;
    <?php if(!$resno):?> [<a href="<?=PHP_SELF?>?res=<?=h($no)?>">返信</a>]<?php endif;?>
	<?php $com = auto_link(h($com));?>
	<?php $com = preg_replace("/(^|>)(&gt;[^<]*)/i", "\\1<font color=".RE_COL.">\\2</font>", $com)?>;
	
	<blockquote><?=nl2br($com,false)?></blockquote>

     <!-- // そろそろ消える。 -->
     <?php if($lineindex[$no] >= LOG_MAX*0.95):?>
      <font color="#f00000"><b>このスレは古いので、もうすぐ消えます。</b></font><br>
     <?php endif;?>
<?php
    //レス作成
    if(!$resno){
      $s=count($treeline) - 10;
      if($s<1){
        $s=1;
      }
      elseif($s>1){
		?>
       <font color="#707070">レス
              (<?=h($s - 1)?>)?>件省略。全て読むには返信ボタンを押してください。</font><br>\n";
      <?php
		}
    }
    else{
      $s=1;
    }

    for($k = $s; $k < count($treeline); $k++){
		$disptree = $treeline[$k];
		$j=$lineindex[$disptree] ;
		if(!trim($line[$j])){
		  continue;
		}
		list($no,$now,$name,$email,$sub,$com,$url,
			 $host,$pwd,$ext,$w,$h,$time,$chk) = explode(",", $line[$j]);
		// URLとメールにリンク
		$com = preg_replace("#<br( *)/?>#i","\n",$com); // <br>または<br />を改行へ戻す
		$email=filter_var($email,FILTER_VALIDATE_EMAIL)?$email:'';
		// 画像ファイル名
		$img = $path.$time.$ext;
		$src = IMG_DIR.$time.$ext;
		?>
  
		  <!-- メイン作成 -->
		  <table border="0"><tr><td nowrap align="right" valign=top>…</td><td bgcolor=#F0E0D6 nowrap>
		  <input type=checkbox name="<?=h($no)?>" value="delete"><font color=#cc1105 "size=+1"><b><?=h($sub)?></b></font>
		  Name <font color="#117743"><b>
		  <?php if($email):?><a href="mailto:<?=h($email)?>"><?=h($name)?></a><?php else:?><?=h($name)?><?php endif;?></b>
		  </font> <?=h($now)?> No.<?=h($no)?> &nbsp;
		  <?php if($ext && is_file($img)):?>
		  <?php $size = filesize($img);//altにサイズ表示?>
		  <?php if($w && $h):?>	<!-- サイズがある時 -->
		  <br> &nbsp; &nbsp; <a href="<?=h($src)?>" target=_blank><?=h($time.$ext)?></a>-(<?=h($size)?> B)
		  <?php if(is_file(THUMB_DIR.$time.'s.jpg')):?>
			  <small>サムネイル表示</small><br><a href=<?=h($src)?>" target=_blank><img src="<?=h(THUMB_DIR.$time.'s.jpg')?>
		  " border="0" align=left width="<?=h($w)?>" height="<?=h($h)?>" hspace="20" alt="<?=h($size)?> B"></a>
			<?php else:?>
			  <a href="<?=h($src)?>" target=_blank><img src="<?=h($src)?>
		  " border="0" align="left" width="<?=h($w)?>" height=<?=h($h)?> hspace="20" alt="<?=h($size)?> B"></a>
		<?php endif;?>
		<?php endif;?>
		<?php endif;?>
		<?php $com = auto_link(h($com));?>
		<?php $com = preg_replace("/(^|>)(&gt;[^<]*)/i", "\\1<font color=".RE_COL.">\\2</font>", $com)?>;
		 
		 <blockquote><?=nl2br($com,false)?></blockquote>
		  </td></tr></table>
		<?php  
		}
		?>
		<!-- //ここまで -->
		<br clear=left><hr>
		<?php
		clearstatcache();//ファイルのstatをクリア
		$p++;
		?>
	
	<?php
		$dat.= ob_get_clean();
		if($resno){
		  break;
		} //res時はtree1行だけ
		
	}

$dat.='<table align=right><tr><td nowrap align=center>
<input type=hidden name=mode value=usrdel>【記事削除】[<input type=checkbox name=onlyimgdel value=on>画像だけ消す]<br>
削除キー<input type=password name=pwd size="8" value="">
<input type=submit value="削除"></form></td></tr></table>';

    if(!$resno){ //res時は表示しない
      $prev = $st - PAGE_DEF;
      $next = $st + PAGE_DEF;
      // 改ページ処理
      $dat.="<table align=left border=1><tr>";
      if($prev >= 0){
        if($prev==0){
          $dat.="<form action=\"".PHP_SELF2."\" method=get><td>";
        }
        else{
          $dat.="<form action=\"".$prev/PAGE_DEF.PHP_EXT."\" method=get><td>";
        }
        $dat.="<input type=submit value=\"前のページ\">";
        $dat.="</td></form>";
      }
      else{
        $dat.="<td>最初のページ</td>";
      }

      $dat.="<td>";
      for($i = 0; $i < count($tree) ; $i+=PAGE_DEF){
        if($st==$i){
          $dat.="[<b>".($i/PAGE_DEF)."</b>] ";
        }
        else{
          if($i==0){
            $dat.="[<a href=\"".PHP_SELF2."\">0</a>] ";
          }
          else{
            $dat.="[<a href=\"".($i/PAGE_DEF).PHP_EXT."\">".($i/PAGE_DEF)."</a>] ";
          }
        }
      }

      $dat.="</td>";

      if($p >= PAGE_DEF && count($tree) > $next){
        $dat.="<form action=\"".$next/PAGE_DEF.PHP_EXT."\" method=get><td>";
        $dat.="<input type=submit value=\"次のページ\">";
        $dat.="</td></form>";
      }
      else{
        $dat.="<td>最後のページ</td>";
      }
        $dat.="</tr></table><br clear=all>\n";
    }
    
    foot($dat);
    if($resno){
      echo $dat;break;
    }
    if($page==0){
      $logfilename=PHP_SELF2;
    }
    else{
      $logfilename=$page/PAGE_DEF.PHP_EXT;
    }

    $fp = fopen($logfilename, "w");
    set_file_buffer($fp, 0);
    rewind($fp);
    fputs($fp, $dat);
    fclose($fp);
    chmod($logfilename,0606);
  }

  if(!$resno&&is_file(($page/PAGE_DEF+1).PHP_EXT)){
    unlink(($page/PAGE_DEF+1).PHP_EXT);
  }
}
?>
