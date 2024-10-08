<?php require('repositories.php'); ?>
<?php require('models.php'); ?>
<?php require('thumbnail_gd.php'); ?>
<?php
//INPUT_POSTから変数を取得

$mode = (string)filter_input(INPUT_POST, 'mode');
$mode = $mode ? $mode : (string)filter_input(INPUT_GET, 'mode');
$resto = (string)filter_input(INPUT_POST, 'resto',FILTER_VALIDATE_INT);
$pwd = (string)(filter_input(INPUT_POST, 'pwd'));
$admin = (string)filter_input(INPUT_POST, 'admin');
$pass = (string)(filter_input(INPUT_POST, 'pass'));
$onlyimgdel = filter_input(INPUT_POST, 'onlyimgdel',FILTER_VALIDATE_BOOLEAN);

//INPUT_GETから変数を取得

$res = (string)filter_input(INPUT_GET, 'res',FILTER_VALIDATE_INT);

//INPUT_COOKIEから変数を取得
$pwdc = (string)filter_input(INPUT_COOKIE, 'pwdc');

define("LOGFILE", 'img.log');		//ログファイル名
define("TREEFILE", 'tree.log');		//ログファイル名
define("IMG_DIR", 'src/');		//画像保存ディレクトリ。futaba.phpから見て
define("THUMB_DIR",'thumb/');		//サムネイル保存ディレクトリ
define("TITLE", '画像掲示板');		//タイトル（<title>とTOP）
define("HOME",  '../');			//「ホーム」へのリンク
define("MAX_KB", '1024');			//投稿容量制限 KB（phpの設定により2Mまで
define("MAX_W",  '250');			//投稿サイズ幅（これ以上はwidthを縮小
define("MAX_H",  '250');			//投稿サイズ高さ
define("PAGE_DEF", '5');			//一ページに表示する記事
define("LOG_MAX",  '2000');		//ログ最大行数
define("ADMIN_PASS", 'admin_pass');	//管理者パス
define("RE_COL", '789922');               //＞が付いた時の色
define("PHP_SELF", 'futaba.php');	//このスクリプト名
define("PHP_SELF2", 'futaba.html');	//入り口ファイル名
define("PHP_EXT", '.html');		//1ページ以降の拡張子
define("RENZOKU", '5');			//連続投稿秒数
define("RENZOKU2", '10');		//画像連続投稿秒数
define("MAX_RES", '30');		//強制sageレス数
define("USE_THUMB", 1);		//サムネイルを作る する:1 しない:0
define("PROXY_CHECK", 0);		//proxyの書込みを制限する y:1 n:0
define("DISP_ID", 0);		//IDを表示する 強制:2 する:1 しない:0
define("BR_CHECK", 15);		//改行を抑制する行数 しない:0
define("IDSEED", 'idの種');		//idの種
define("RESIMG", 1);		//レスに画像を貼る:1 貼らない:0
define("RE_SAMPLED", 1);		//サムネイルの画質向上:1 :0 問題がなければ1
define('THUMB_Q', '92'); //サムネイルのJPEG劣化率

$path = realpath("./").'/'.IMG_DIR;
$badstring = array("dummy_string","dummy_string2"); //拒絶する文字列
$badfile = array("dummy","dummy2"); //拒絶するファイルのmd5
$badip = array("addr.dummy.com","addr2.dummy.com"); //拒絶するホスト
$addinfo='';
?>

<?php

/**
 * Rendering of message form.
 *
 * @params string $dat message log.
 * @params integer $resno res number.
 * @params string $admin administrator password.
 * @return void
 */
function form(&$dat, $resno, $admin = "")
{
	global $addinfo;
	$msg = "";
	$hidden = "";

	$maxbyte = MAX_KB * 1024;
	$no = $resno;
	ob_start();
?>

	<?php if ($resno): ?>
		[<a href="<?= h(PHP_SELF2) ?>">掲示板に戻る</a>]
		<table width='100%'>
			<tr>
				<th bgcolor=#e04000>
					<font color=#FFFFFF>レス送信モード</font>
				</th>
			</tr>
		</table>
	<?php endif; ?>

	<center>
		<form action="<?= h(PHP_SELF) ?>" method="POST" enctype="multipart/form-data">
			<input type="hidden" name="mode" value="regist">
			<input type=hidden name="MAX_FILE_SIZE" value="<?= h($maxbyte) ?>">


			<?php if ($no): ?>
				<input type=hidden name=resto value="<?= h($no) ?>">
			<?php endif; ?>

			<table cellpadding=1 cellspacing=1>
				<tr>
					<td class="ftdc"><b>おなまえ</b></td>
					<td><input type=text name="name" size="28" autocomplete="username"></td>
				</tr>
				<tr>
					<td class="ftdc"><b>E-mail</b></td>
					<td><input type=text name="email" size="28"></td>
				</tr>
				<tr>
					<td class="ftdc"><b>題　　名</b></td>
					<td><input type=text name="sub" size="35">
						<input type=submit value="送信する">
					</td>
				</tr>
				<tr>
					<td class="ftdc"><b>コメント</b></td>
					<td><textarea name=com cols="48" rows="4" wrap=soft></textarea></td>
				</tr>

				<?php if (RESIMG || !$resno): ?>
					<tr>
						<td class="ftdc"><b>添付File</b></td>
						<td><input type=file name=upfile size="35">
							[<label><input type=checkbox name=textonly value=on>画像なし</label>]</td>
					</tr>
				<?php endif; ?>

				<tr>
					<td class="ftdc"><b>削除キー</b></td>
					<td><input type=password name=pwd size=8 value=""><small>(記事の削除用)</small></td>
				</tr>
				<tr>
					<td colspan=2 class="chui">
						<li>添付可能ファイル：GIF, JPG, PNG ブラウザによっては正常に添付できないことがあります。</li>
						<li>最大投稿データ量は <?= h(MAX_KB) ?> KB までです。sage機能付き。</li>
						<li>画像は横 <?= MAX_W ?>ピクセル、縦 <?= h(MAX_H) ?>ピクセルを超えると縮小表示されます。</li>
						<?= h($addinfo) ?>
					</td>
				</tr>
			</table>
		</form>
	</center>
	<hr>
<?php
	$dat .= ob_get_clean();
}
?>
<?php

/**
 * Update message.
 * 
 * @params integer $resno target message number.
 * @return void
 */
function updatelog($resno = 0)
{
	global $path;
	$p = 0;

	$tree = file(TREEFILE);
	$find = false;
	if ($resno) {
		$counttree = count($tree);
		for ($i = 0; $i < $counttree; $i++) {
			list($artno,) = explode(",", rtrim($tree[$i]));
			if ($artno == $resno) { //レス先検索
				$st = $i;
				$find = true;
				break;
			}
		}
		if (!$find) {
			error("該当記事がみつかりません");
		}
	}
	$line = file(LOGFILE);
	$countline = count($line);
	$lineindex = get_lineindex($line); // 逆変換テーブル作成

	$counttree = count($tree);
	for ($page = 0; $page < $counttree; $page += PAGE_DEF) {
		$dat = '';
		head($dat);
		form($dat, $resno);
		if (!$resno) {
			$st = $page;
		}
		$dat .= '<form action="' . PHP_SELF . '" method=POST>';

		for ($i = $st; $i < $st + PAGE_DEF; $i++) {
			ob_start();

			if (!isset($tree[$i])) {
				continue;
			}
			$treeline = explode(",", rtrim($tree[$i]));
			$disptree = $treeline[0];
			if (!isset($lineindex[$disptree])) {
				continue;
			}
			$j = $lineindex[$disptree]; //該当記事を探して$jにセット

			if (!trim($line[$j])) {
				continue;
			} //$jが範囲外なら次の行

			list(
				$no,
				$now,
				$name,
				$email,
				$sub,
				$com,
				$url,
				$host,
				$pwd,
				$ext,
				$w,
				$h,
				$time,
				$chk
			) = explode(",", $line[$j]);
			$com = preg_replace("#<br( *)/?>#i", "\n", $com); // <br>または<br />を改行へ戻す
			// URLとメールにリンク
			$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';

			// 画像ファイル名
			$img = $path . $time . $ext;
			$src = IMG_DIR . $time . $ext;
?>
			<?php if ($ext && is_file($img)): ?>
				<?php $size = filesize($img); //altにサイズ表示
				?>
				画像タイトル：<a href="<?= h($src) ?>" target=_blank><?= h($time . $ext) ?></a>-(<? h($size) ?> B)<br>
				<?php if ($w && $h): ?><!-- サイズがある時 -->

					<?php if (@is_file(THUMB_DIR . $time . 's.jpg')): ?>
						<small>サムネイルを表示しています.クリックすると元のサイズを表示します.</small><br><a href="<?= h($src) ?>" target=_blank><img src="<?= h(THUMB_DIR . $time . 's.jpg') ?>" border=0 align=left width="<?= h($w) ?>" height="<?= h($h) ?>" hspace=20 alt="<?= h($size) ?> B"></a>
					<?php else: ?>
						<a href="<?= h($src) ?>" target=_blank><img src="<?= h($src) ?>
      " border="0" align="left" width="<?= h($w) ?>" height="<?= h($h) ?>" hspace="20" alt="<?= h($size) ?> B"></a>";
					<?php endif; ?>
				<?php endif; ?>

			<?php endif; ?>

			<!-- // メイン作成 -->
			<input type=checkbox name="del[]" value="<?= h($no) ?>"><span class="csb"><?= h($sub) ?></span>
			Name <span class="cnm">
				<?php if ($email): ?><a href="mailto:<?= h($email) ?>"><?= h($name) ?></a><?php else: ?><?= h($name) ?><?php endif; ?>
			</span>
			<span class="cnw"><?= h($now) ?></span><span class="cno"> No.<?= h($no) ?></span>
			<?php if (!$resno): ?> [<a href="<?= PHP_SELF ?>?res=<?= h($no) ?>">返信</a>]<?php endif; ?>
				<?php $com = auto_link(h($com)); ?>
				<?php $com = preg_replace("/(^|>)(&gt;[^<]*)/i", "\\1<span style=\"color:" . RE_COL . ";\">\\2</span>", $com) ?>

				<blockquote><?= nl2br($com, false) ?></blockquote>

				<!-- // そろそろ消える。 -->
				<?php if ($lineindex[$no] >= LOG_MAX * 0.95): ?>
					<font color="#f00000"><b>このスレは古いので、もうすぐ消えます。</b></font><br>
				<?php endif; ?>
				<?php
				//レス作成
				if (!$resno) {
					$s = count($treeline) - 10;
					if ($s < 1) {
						$s = 1;
					} elseif ($s > 1) {
				?>
						<font color="#707070">レス
							(<?= h($s - 1) ?>)?>件省略。全て読むには返信ボタンを押してください。</font><br>\n";
					<?php
					}
				} else {
					$s = 1;
				}

				for ($k = $s; $k < count($treeline); $k++) {
					$disptree = $treeline[$k];
					$j = $lineindex[$disptree];
					if (!trim($line[$j])) {
						continue;
					}
					list(
						$no,
						$now,
						$name,
						$email,
						$sub,
						$com,
						$url,
						$host,
						$pwd,
						$ext,
						$w,
						$h,
						$time,
						$chk
					) = explode(",", $line[$j]);
					// URLとメールにリンク
					$com = preg_replace("#<br( *)/?>#i", "\n", $com); // <br>または<br />を改行へ戻す
					$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
					// 画像ファイル名
					$img = $path . $time . $ext;
					$src = IMG_DIR . $time . $ext;
					?>

					<!-- メイン作成 -->
					<table border="0">
						<tr>
							<td class="rts">…</td>
							<td <td class="rtd">
								<input type=checkbox name="<?= h($no) ?>" value="delete"><span class="csb"><?= h($sub) ?></span>
								Name <span class="cnm">
									<?php if ($email): ?><a href="mailto:<?= h($email) ?>"><?= h($name) ?></a><?php else: ?><?= h($name) ?><?php endif; ?>
								</span>
								<span class="cnw"><?= h($now) ?></span><span class="cno"> No.<?= h($no) ?></span>
								<?php if ($ext && is_file($img)): ?>
									<?php $size = filesize($img); //altにサイズ表示
									?>
									<?php if ($w && $h): ?> <!-- サイズがある時 -->
										<br> &nbsp; &nbsp; <a href="<?= h($src) ?>" target=_blank><?= h($time . $ext) ?></a>-(<?= h($size) ?> B)
										<?php if (is_file(THUMB_DIR . $time . 's.jpg')): ?>
											<small>サムネイル表示</small><br><a href=<?= h($src) ?>" target=_blank><img src="<?= h(THUMB_DIR . $time . 's.jpg') ?>" border="0" align="left" width="<?= h($w) ?>" height="<?= h($h) ?>" hspace="20" alt="<?= h($size) ?> B"></a>
										<?php else: ?>
											<a href="<?= h($src) ?>" target=_blank><img src="<?= h($src) ?>
		  " border="0" align="left" width="<?= h($w) ?>" height=<?= h($h) ?> hspace="20" alt="<?= h($size) ?> B"></a>
										<?php endif; ?>
									<?php endif; ?>
								<?php endif; ?>
								<?php $com = auto_link(h($com)); ?>
								<?php $com = preg_replace("/(^|>)(&gt;[^<]*)/i", "\\1<font color=" . RE_COL . ">\\2</font>", $com) ?>

								<blockquote><?= nl2br($com, false) ?></blockquote>
							</td>
						</tr>
					</table>
				<?php
				}
				?>
				<!-- //ここまで -->
				<br clear=left>
				<hr>
				<?php
				clearstatcache(); //ファイルのstatをクリア
				$p++;
				?>

	<?php
			$dat .= ob_get_clean();
			if ($resno) {
				break;
			} //res時はtree1行だけ

		}

		$dat .= '<table align=right><tr><td nowrap align=center>
<input type=hidden name=mode value=usrdel>【記事削除】[<input type=checkbox name=onlyimgdel value=on>画像だけ消す]<br>
削除キー<input type=password name=pwd size="8" value="">
<input type=submit value="削除"></form></td></tr></table>';

		if (!$resno) { //res時は表示しない
			$prev = $st - PAGE_DEF;
			$next = $st + PAGE_DEF;
			// 改ページ処理
			$dat .= "<table align=left border=1><tr>";
			if ($prev >= 0) {
				if ($prev == 0) {
					$dat .= "<form action=\"" . PHP_SELF2 . "\" method=get><td>";
				} else {
					$dat .= "<form action=\"" . $prev / PAGE_DEF . PHP_EXT . "\" method=get><td>";
				}
				$dat .= "<input type=submit value=\"前のページ\">";
				$dat .= "</td></form>";
			} else {
				$dat .= "<td>最初のページ</td>";
			}

			$dat .= "<td>";
			for ($i = 0; $i < count($tree); $i += PAGE_DEF) {
				if ($st == $i) {
					$dat .= "[<b>" . ($i / PAGE_DEF) . "</b>] ";
				} else {
					if ($i == 0) {
						$dat .= "[<a href=\"" . PHP_SELF2 . "\">0</a>] ";
					} else {
						$dat .= "[<a href=\"" . ($i / PAGE_DEF) . PHP_EXT . "\">" . ($i / PAGE_DEF) . "</a>] ";
					}
				}
			}

			$dat .= "</td>";

			if ($p >= PAGE_DEF && count($tree) > $next) {
				$dat .= "<form action=\"" . $next / PAGE_DEF . PHP_EXT . "\" method=get><td>";
				$dat .= "<input type=submit value=\"次のページ\">";
				$dat .= "</td></form>";
			} else {
				$dat .= "<td>最後のページ</td>";
			}
			$dat .= "</tr></table><br clear=all>\n";
		}

		foot($dat);
		if ($resno) {
			echo $dat;
			break;
		}
		if ($page == 0) {
			$logfilename = PHP_SELF2;
		} else {
			$logfilename = $page / PAGE_DEF . PHP_EXT;
		}

		$fp = fopen($logfilename, "w");
		set_file_buffer($fp, 0);
		rewind($fp);
		fputs($fp, $dat);
		fclose($fp);
		chmod($logfilename, 0606);
	}

	if (!$resno && is_file(($page / PAGE_DEF + 1) . PHP_EXT)) {
		unlink(($page / PAGE_DEF + 1) . PHP_EXT);
	}
}
?>
<?php
/* フッタ */

/**
 * Rendering of footer.
 *
 * @params string $dat string of log.
 * @return void
 */
function foot(&$dat)
{
	ob_start();
?>
	<center>
		<small><!-- GazouBBS v3.0 --><!-- ふたば改0.8 -->
			- <a href="http://php.s3.to" target=_top>GazouBBS</a> + <a href="http://www.2chan.net/" target=_top>futaba</a>-
		</small>
	</center>
	<script>
		l(); //LoadCookie
	</script>
	</body>

	</html>
<?php
	$dat .= ob_get_clean();
}
?>
<?php

/**
 * Create http link.
 *
 * @params string $message message.
 * @return string Replaced to the link.
 */
function auto_link($message)
{
	return preg_replace(
		"/(https?|ftp|news)(:\/\/[[:alnum:]\+\$\;\?\.%,!#~*\/:@&=_-]+)/",
		"<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>",
		$message
	);
}
?>

<?php

/**
 * Rendering of error page.
 *
 * @params string $mes message
 * @params string $dest upload file path.
 * @return void
 */
function error($mes, $dest = '')
{
	global $upfile_name, $path;

	if (is_file($dest)) {
		unlink($dest);
	}

	head($dat);
	ob_start();
?>
	<br><br>
	<hr size="1"><br><br>
	<span class="errmsg"><?= $mes ?><br><br><a href=<?= h(PHP_SELF2) ?>>リロード</a></span>
	<br><br>
	<hr size=1>
	</body>

	</html>

<?php
	$dat .= ob_get_clean();
	echo $dat;
	exit;
}
?>
<?php

/**
 * Connect to port with reverse proxy. 
 * 
 * @params integer $port target port.
 * @return integer 1 then success.
 *         integer 0 then error.
 */
function proxy_connect($port)
{
	$a = "";
	$b = "";
	$fp = @fsockopen($_SERVER["REMOTE_ADDR"], $port, $a, $b, 2);
	if (!$fp) {
		return 0;
	} else {
		return 1;
	}
}
?>

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
function regist($resto = 0)
{
	global $path, $badstring, $badfile, $badip, $pwdc, $textonly;
	$dest = "";
	$mes = "";
	$name = (string)filter_input(INPUT_POST, 'name');
	$email = (string)filter_input(INPUT_POST, 'email');
	$sub = (string)filter_input(INPUT_POST, 'sub');
	$pwd = (string)(filter_input(INPUT_POST, 'pwd'));
	$textonly = (string)(filter_input(INPUT_POST, 'textonly', FILTER_VALIDATE_BOOLEAN));
	$url = '';
	$comment = (string)filter_input(INPUT_POST, 'com');
	$upfile_name = isset($_FILES["upfile"]["name"]) ? $_FILES["upfile"]["name"] : "";
	$upfile = isset($_FILES["upfile"]["tmp_name"]) ? $_FILES["upfile"]["tmp_name"] : "";

	// 時間
	$time = time();
	$tim = $time . substr(microtime(), 2, 3);


	// アップロード処理
	if (isset($_FILES["upfile"]["error"])) { //エラーチェック
		if (in_array($_FILES["upfile"]["error"], [1, 2])) {
			error('ファイルサイズが大きすぎます。'); //容量オーバー
		}
	}
	$W = "";
	$H = "";
	$extension = "";

	if ($upfile && is_file($upfile)) {
		$dest = ImageFile::getNew()->createTempFileName($path, $tim);
		move_uploaded_file($upfile, $dest);
		//↑でエラーなら↓に変更
		//copy($upfile, $dest);
		$upfile_name = CleanStr($upfile_name);
		if (!is_file($dest)) {
			error("アップロードに失敗しました<br>サーバがサポートしていない可能性があります", $dest);
		}
		$size = getimagesize($dest);
		if (!is_array($size)) {
			error("アップロードに失敗しました<br>画像ファイル以外は受け付けません", $dest);
		}
		$is_uploaded = ImageFile::getNew()->isUploaded($badfile, $dest);
		if ($is_uploaded === true) {
			error("アップロードに失敗しました<br>同じ画像がありました", $dest); //拒絶画像
			return;
		}
		chmod($dest, 0606);

		// size[0] is width, size[1] is height. 
		$desired_size = ImageFile::adjustmentImageCanvasSize(
			$size[0],
			$size[1]
		);
		$W = $desired_size['width'];
		$H = $desired_size['height'];
		$extension = ExtensionRepository::find($size[2]);

		$mes = "画像 $upfile_name のアップロードが成功しました";
	}

	foreach ($badstring as $value) {
		$pattern = '/' . $value . '/';
		if (
			preg_match($pattern, $comment) === 1 ||
			preg_match($pattern, $sub) === 1 ||
			preg_match($pattern, $name) === 1 ||
			preg_match($pattern, $email) === 1
		) {
			error("拒絶されました(str)", $dest);
		};
	}
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		error("不正な投稿をしないで下さい(post)", $dest);
	}
	// フォーム内容をチェック
	if (!$name || preg_match("/^[ |　|]*$/", $name) === 1) {
		$name = "";
	}
	if (!$comment || preg_match("/^[ |　|\t]*$/", $comment) === 1) {
		$comment = "";
	}
	if (!$sub || preg_match("/^[ |　|]*$/", $sub) === 1) {
		$sub = "";
	}

	if (!$resto && !$textonly && !is_file($dest)) {
		error("画像がありません", $dest);
	}
	if (!$comment && !is_file($dest)) {
		error("何か書いて下さい", $dest);
	}
	if ($pwd !== ADMIN_PASS) {
		$name = preg_replace("/管理/", "\"管理\"", $name);
		$name = preg_replace("/削除/", "\"削除\"", $name);
	}
	if (strlen($comment) > 1000) {
		error("本文が長すぎますっ！", $dest);
	}
	if (strlen($name) > 100) {
		error("本文が長すぎますっ！", $dest);
	}
	if (strlen($email) > 100) {
		error("本文が長すぎますっ！", $dest);
	}
	if (strlen($sub) > 100) {
		error("本文が長すぎますっ！", $dest);
	}
	if (strlen($resto) > 10) {
		error("異常です", $dest);
	}
	if (strlen($url) > 10) {
		error("異常です", $dest);
	}

	//ホスト取得
	$host = gethostbyaddr($_SERVER["REMOTE_ADDR"]);

	foreach ($badip as $value) { //拒絶host
		if (preg_match("/$value$/i", $host)) {
			error("拒絶されました(host)", $dest);
		}
	}

	if (
		preg_match("/^mail/i", $host)
		|| preg_match("/^ns/i", $host)
		|| preg_match("/^dns/i", $host)
		|| preg_match("/^ftp/i", $host)
		|| preg_match("/^prox/i", $host)
		|| preg_match("/^pc/i", $host)
		|| preg_match("/^[^\.]\.[^\.]$/i", $host)
	) {
		$pxck = "on";
	}

	if (
		preg_match("/ne\\.jp$/i", $host) ||
		preg_match("/ad\\.jp$/i", $host) ||
		preg_match("/bbtec\\.net$/i", $host) ||
		preg_match("/aol\\.com$/i", $host) ||
		preg_match("/uu\\.net$/i", $host) ||
		preg_match("/asahi-net\\.or\\.jp$/i", $host) ||
		preg_match("/rim\\.or\\.jp$/i", $host)
	) {
		$pxck = "off";
	} else {
		$pxck = "on";
	}

	if ($pxck == "on" && PROXY_CHECK) {
		if (proxy_connect('80') == 1) {
			error("ＥＲＲＯＲ！　公開ＰＲＯＸＹ規制中！！(80)", $dest);
		} elseif (proxy_connect('8080') == 1) {
			error("ＥＲＲＯＲ！　公開ＰＲＯＸＹ規制中！！(8080)", $dest);
		}
	}

	// No.とパスと時間とURLフォーマット
	srand();
	if ($pwd == "") {
		if ($pwdc == "") {
			$pwd = rand();
			$pwd = substr($pwd, 0, 8);
		} else {
			$pwd = $pwdc;
		}
	}

	$c_pass = $pwd;
	$pass = ($pwd) ? substr(md5($pwd), 2, 8) : "*";
	$youbi = array('日', '月', '火', '水', '木', '金', '土');
	$yd = $youbi[gmdate("w", $time + 9 * 60 * 60)];
	$now = (
		gmdate("y/m/d", $time + 9 * 60 * 60) .
		"(" . (string)$yd . ")" .
		gmdate("H:i", $time + 9 * 60 * 60)
	);

	if (DISP_ID) {
		if ($email && DISP_ID == 1) {
			$now .= " ID:???";
		} else {
			$now .= " ID:" . substr(crypt(md5($_SERVER["REMOTE_ADDR"] . IDSEED . gmdate("Ymd", $time + 9 * 60 * 60)), 'id'), -8);
		}
	}

	$email = PrettifyText::replaceStringOfMail($email);
	$sub   = PrettifyText::replaceStringOfSubject($sub);
	$url   = PrettifyText::replaceStringOfUrl($url);
	$resto = PrettifyText::replaceStringOfResNumber($resto);
	$comment = PrettifyText::replaceStringOfComment($comment);
	$name  = PrettifyText::replaceStringOfName($name);
	$names = $name;

	if (!$name) {
		$name = "名無し";
	}
	if (!$comment) {
		$comment = "本文なし";
	}
	if (!$sub) {
		$sub = "無題";
	}

	//ログ読み込み
	$fp = fopen(LOGFILE, "r+");
	flock($fp, 2);
	rewind($fp);
	$buf = fread($fp, 1000000);
	if ($buf == '') {
		error("error load log", $dest);
	}
	$line = explode("\n", $buf);
	$countline = count($line);
	$lineindex = get_lineindex($line); //逆変換テーブル作成

	// 二重投稿チェック
	$imax = count($line) > 20 ? 20 : count($line) - 1;
	for ($i = 0; $i < $imax; $i++) {
		list($lastno,, $lname,,, $lcom,, $lhost, $lpwd,,,, $ltime,) = explode(",", $line[$i]);
		if (strlen($ltime) > 10) {
			$ltime = substr($ltime, 0, -3);
		}
		if ($host == $lhost || substr(md5($pwd), 2, 8) == $lpwd || substr(md5($pwdc), 2, 8) == $lpwd) {
			$p = 1;
		} else {
			$p = 0;
		}

		if (RENZOKU && $p && $time - $ltime < RENZOKU) {
			error("連続投稿はもうしばらく時間を置いてからお願い致します", $dest);
		}

		if (RENZOKU && $p && $time - $ltime < RENZOKU2 && $upfile_name) {
			error("画像連続投稿はもうしばらく時間を置いてからお願い致します", $dest);
		}
		if (RENZOKU && $p && $comment == $lcom && !$upfile_name) {
			error("連続投稿はもうしばらく時間を置いてからお願い致します", $dest);
		}
	}

	// ログ行数オーバー
	if (count($line) >= LOG_MAX) {
		for ($d = count($line) - 1; $d >= LOG_MAX - 1; $d--) {
			list($dno,,,,,,,,, $dext,,, $dtime,) = explode(",", $line[$d]);
			if (is_file($path . $dtime . $dext)) {
				unlink($path . $dtime . $dext);
			}
			if (is_file(THUMB_DIR . $dtime . 's.jpg')) {
				unlink(THUMB_DIR . $dtime . 's.jpg');
			}
			unset($line[$d]);
			treedel($dno);
		}
	}
	// アップロード処理
	$chk = '';
	if ($dest && is_file($dest)) {
		$imax = count($line) > 200 ? 200 : count($line) - 1;
		$chk = md5_file($dest);

		for ($i = 0; $i < $imax; $i++) { //画像重複チェック
			if (!trim($line[$i])) {
				continue;
			}

			list(,,,,,,,,, $extensionp,,, $timep, $p,) = explode(",", $line[$i]);

			if ($chk === $p && is_file($path . $timep . $extensionp)) {
				error("アップロードに失敗しました<br>同じ画像があります", $dest);
			}
		}
	}
	list($lastno,) = explode(",", $line[0]);
	$no = $lastno + 1;
	$newline = "$no,$now,$name,$email,$sub,$comment,$url,$host,$pass,$extension,$W,$H,$tim,$chk,\n";
	$newline .= implode("\n", $line);
	ftruncate($fp, 0);
	set_file_buffer($fp, 0);
	rewind($fp);
	fputs($fp, $newline);

	//ツリー更新
	$find = false;
	$newline = '';
	$tp = fopen(TREEFILE, "r+");
	set_file_buffer($tp, 0);
	rewind($tp);
	$buf = fread($tp, 1000000);
	if ($buf == '') {
		error("error tree update", $dest);
	}
	$line = explode("\n", $buf);
	$countline = count($line);
	for ($i = 0; $i < $countline; $i++) {
		if ($line[$i] != "") {
			$line[$i] .= "\n";
			$j = explode(",", rtrim($line[$i]));
			if (!isset($lineindex[$j[0]])) {
				unset($line[$i]);
			}
		}
	}
	if ($resto) {
		for ($i = 0; $i < $countline; $i++) {
			$rtno = explode(",", rtrim($line[$i]));
			if ($rtno[0] == $resto) {
				$find = TRUE;
				$line[$i] = rtrim($line[$i]) . ',' . $no . "\n";
				$j = explode(",", rtrim($line[$i]));
				if (count($j) > MAX_RES) {
					$email = 'sage';
				}
				if (!stristr($email, 'sage')) {
					$newline = $line[$i];
					$line[$i] = '';
				}
				break;
			}
		}
	}

	if (!$find) {
		if (!$resto) {
			$newline = "$no\n";
		} else {
			error("スレッドがありません", $dest);
		}
	}
	$newline .= implode('', $line);
	ftruncate($tp, 0);
	set_file_buffer($tp, 0);
	rewind($tp);
	fputs($tp, $newline);
	fclose($tp);
	fclose($fp);

	//クッキー保存
	setcookie("pwdc", $c_pass, time() + 7 * 24 * 3600);  /* 1週間で期限切れ */
	$c_name = $names;
	setcookie("namec", (string)filter_input(INPUT_POST, 'name'), time() + 7 * 24 * 3600);  /* 1週間で期限切れ */




	if ($dest && is_file($dest)) {
		rename($dest, $path . $tim . $extension);
		if (USE_THUMB) {
			thumb($path, $tim, $extension, MAX_W, MAX_H);
		}
	}
	updatelog();
	header("Content-type: text/html; charset=UTF-8");
?>
	<html>

	<head>
		<meta charset="UTF-8">
		<meta http-equiv="refresh" content="1;URL=" <?= h(PHP_SELF2) ?>">
	</head>

	<body><?php if ($mes): ?><?= h($mes) ?><br><br><?php endif; ?>画面を切り替えます</body>

	</html>
<?php
}
?>
<?php

/**
 * Get GD Version.
 *
 * @return string GD Information.
 */
function get_gd_ver()
{
	if (function_exists("gd_info")) {
		$gdver = gd_info();
		$phpinfo = $gdver["GD Version"];
	} else { //php4.3.0未満用
		ob_start();
		phpinfo(8);
		$phpinfo = ob_get_contents();
		ob_end_clean();
		$phpinfo = strip_tags($phpinfo);
		$phpinfo = stristr($phpinfo, "gd version");
		$phpinfo = stristr($phpinfo, "version");
	}

	$end = strpos($phpinfo, ".");
	$phpinfo = substr($phpinfo, 0, $end);
	$length = strlen($phpinfo) - 1;
	$phpinfo = substr($phpinfo, $length);
	return $phpinfo;
}
?>

<?php
//GD版が使えるかチェック
function gd_check()
{
	$check = array("ImageCreate", "ImageCopyResized", "ImageCreateFromJPEG", "ImageJPEG", "ImageDestroy");

	//最低限のGD関数が使えるかチェック
	if (!(get_gd_ver() && (ImageTypes() & IMG_JPG))) {
		return false;
	}
	foreach ($check as $cmd) {
		if (!function_exists($cmd)) {
			return false;
		}
	}
	return true;
}
?>

<?php

/**
 * Delete of message.
 *
 * @params integer $delno delete message number.
 * @return void
 */
function treedel($delno)
{
	$fp = fopen(TREEFILE, "r+");
	set_file_buffer($fp, 0);
	flock($fp, 2);
	rewind($fp);
	$buf = fread($fp, 1000000);
	if ($buf == '') {
		error("error tree del");
	}
	$line = explode("\n", $buf);
	$countline = count($line);
	if ($countline > 2) {
		for ($i = 0; $i < $countline; $i++) {
			if ($line[$i] != "") {
				$line[$i] .= "\n";
			}
		}
		for ($i = 0; $i < $countline; $i++) {
			$treeline = explode(",", rtrim($line[$i]));
			$counttreeline = count($treeline);
			for ($j = 0; $j < $counttreeline; $j++) {
				if ($treeline[$j] == $delno) {
					$treeline[$j] = '';
					if ($j == 0) {
						$line[$i] = '';
					} else {
						$line[$i] = implode(',', $treeline);
						$line[$i] = preg_replace("/,,/", ",", $line[$i]);
						$line[$i] = preg_replace("/,$/", "", $line[$i]);
						$line[$i] .= "\n";
					}
					break 2;
				}
			}
		}
		ftruncate($fp, 0);
		set_file_buffer($fp, 0);
		rewind($fp);
		fputs($fp, implode('', $line));
	}
	fclose($fp);
}
?>

<?php
/** 
 * Delete of user post message.
 *
 * @params integer $no post message number.
 * @params string $pwd post message password.
 * @return void
 */
function usrdel($no,$pwd){
	global $path,$pwdc,$onlyimgdel;
	$del = filter_input(INPUT_POST,'del',FILTER_VALIDATE_INT,FILTER_REQUIRE_ARRAY);//$del は配列
	$host = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
	$delno = array("dummy");
	$delflag = false;
  
	if(!is_array($del)){
	  return;
	}
	sort($del);
	reset($del);
  
	if($pwd==""&&$pwdc!=""){
	  $pwd=$pwdc;
	}
  
	$fp=fopen(LOGFILE,"r+");
	set_file_buffer($fp, 0);
	flock($fp, 2);
	rewind($fp);
	$buf=fread($fp,1000000);
	fclose($fp);
  
	if($buf==''){
	  error("error user del");
	}
  
	$line = explode("\n",$buf);
	$countline=count($line);
  
	for($i = 0; $i < $countline; $i++){
	  if($line[$i]!=""){
		$line[$i].="\n";
	  };
	}
  
	$flag = false;
	$countline=count($line)-1;
	for($i = 0; $i<$countline; $i++){
	  if(!trim($line[$i])){
		  continue;
	  }
	  list($dno,,,,,,,$dhost,$pass,$dext,,,$dtim,) = explode(",", $line[$i]);
	  if(in_array($dno,$del) && (substr(md5($pwd),2,8) == $pass || $dhost == $host||ADMIN_PASS==$pwd)){
		$flag = true;
		$line[$i] = "";			//パスワードがマッチした行は空に
		$delfile = $path.$dtim.$dext;	//削除ファイル
		if(!$onlyimgdel){
		  treedel($dno);
		}
		if(is_file($delfile)){
		  unlink($delfile);//削除
		}
		if(is_file(THUMB_DIR.$dtim.'s.jpg')){
		  unlink(THUMB_DIR.$dtim.'s.jpg');//削除
		}
	  }
	}
	if(!$flag){
	  error("該当記事が見つからないかパスワードが間違っています");
	}
  
  }
  ?>

<?php

/**
 * Validatio of password. 
 * ...And rendering form.
 *
 * @params string $pass password.
 * @return void
 */
function valid($pass)
{
	if ($pass && $pass != ADMIN_PASS) {
		error("パスワードが違います");
	}

	head($dat);
	echo $dat;
	echo "[<a href=\"" . PHP_SELF2 . "\">掲示板に戻る</a>]\n";
	echo "[<a href=\"" . PHP_SELF . "\">ログを更新する</a>]\n";
	echo "<table width='100%'><tr><th bgcolor=#E08000>\n";
	echo "<span style=\"color: #fff;\">管理モード</span>\n";
	echo "</th></tr></table>\n";
	echo "<p><form action=\"" . PHP_SELF . "\" method=POST>\n";

	// ログインフォーム
	if (!$pass) {
		echo "<center><input type=radio name=admin value=del checked>記事削除 ";
		echo "<input type=radio name=admin value=post>管理人投稿<p>";
		echo "<input type=hidden name=mode value=admin>\n";
		echo "<input type=password name=pass size=8>";
		echo "<input type=submit value=\" 認証 \"></form></center>\n";
		die("</body></html>");
	}
}
?>

<?php

/**
 * Administration of message log.
 *
 * @params string $pass administration password.
 * @return void
 */
function admindel($pass)
{
	global $path, $onlyimgdel;
	$del = filter_input(INPUT_POST, 'del', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY); //$del は配列

	$all = 0;
	$msg = "";
	$delno = array("dummy");
	$delflag = false;
	reset($_POST);


	if (is_array($del)) {
		sort($del);
		reset($del);
		$fp = fopen(LOGFILE, "r+");
		set_file_buffer($fp, 0);
		flock($fp, 2);
		rewind($fp);
		$buf = fread($fp, 1000000);

		if ($buf == '') {
			error("error admin del");
		}

		$line = explode("\n", $buf);
		$countline = count($line) - 1;

		$find = false;

		for ($i = 0; $i < $countline; $i++) {

			if (!trim($line[$i])) {
				continue;
			}
			list($no, $now, $name, $email, $sub, $com, $url, $host, $pw, $ext, $w, $h, $tim, $chk) = explode(",", $line[$i]);
			if ($onlyimgdel == "on") {
				if (in_array($no, $del)) { //画像だけ削除
					$delfile = $path . $tim . $ext;  //削除ファイル
					if (is_file($delfile)) unlink($delfile); //削除
					if (is_file(THUMB_DIR . $tim . 's.jpg')) unlink(THUMB_DIR . $tim . 's.jpg'); //削除
				}
			} else {
				if (in_array($no, $del)) { //削除
					$find = true;
					unset($line[$i]);
					$delfile = $path . $tim . $ext;  //削除ファイル
					if (is_file($delfile)) {
						unlink($delfile); //削除
					}
					if (is_file(THUMB_DIR . $tim . 's.jpg')) {
						unlink(THUMB_DIR . $tim . 's.jpg'); //削除
					}
					treedel($no);
				}
			}
		}

		if ($find) { //ログ更新
			ftruncate($fp, 0);
			set_file_buffer($fp, 0);
			rewind($fp);
			fputs($fp, implode("\n", $line));
		}
		fclose($fp);
	}
?>
	<!-- 削除画面を表示 -->
	<input type=hidden name=mode value=admin>
	<input type=hidden name=admin value=del>
	<input type=hidden name=pass value="<?= h($pass) ?>">
	<center>
		<P>削除したい記事のチェックボックスにチェックを入れ、削除ボタンを押して下さい。
		<p><input type=submit value="削除する">
			<input type=reset value="リセット">
			[<input type=checkbox name=onlyimgdel value="on">画像だけ消す]
		<P>
		<table border=1 cellspacing="0">
			<tr bgcolor="6080f6">
				<th>削除</th>
				<th>記事No</th>
				<th>投稿日</th>
				<th>題名</th>
				<th>投稿者</th>
				<th>コメント</th>
				<th>ホスト名</th>
				<th>添付<br>(Bytes)</th>
				<th>md5</th>
			</tr>
			<?php
			$line = file(LOGFILE);
			for ($j = 0; $j < count($line); $j++) {
				if (!trim($line[$j])) {
					continue;
				}
				$img_flag = false;
				list(
					$no,
					$now,
					$name,
					$email,
					$sub,
					$com,
					$url,
					$host,
					$pw,
					$ext,
					$w,
					$h,
					$time,
					$chk
				) = explode(",", $line[$j]);
				// フォーマット
				$now = preg_replace('/.{2}\/(.*)$/', '\1', $now);
				$now = preg_replace('/\(.*\)/', ' ', $now);

				if (strlen($name) > 10) {
					$name = substr($name, 0, 9) . ".";
				}
				if (strlen($sub) > 10) {
					$sub = substr($sub, 0, 9) . ".";
				}
				$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';

				$com = str_replace("<br />", " ", $com);
				$com = htmlspecialchars($com);

				if (strlen($com) > 20) {
					$com = substr($com, 0, 18) . ".";
				}

				// 画像があるときはリンク
				if ($ext && is_file($path . $time . $ext)) {
					$img_flag = true;
					$clip = true;
					$size = filesize($path . $time . $ext);
					$all += $size;      //合計計算
					$chk = substr($chk, 0, 10);
				} else {
					$clip = false;
					$size = 0;
					$chk = "";
				}
				$bg = ($j % 2) ? "d6d6f6" : "f6f6f6"; //背景色
			?>
				<tr bgcolor="<?= h($bg) ?>">
					<th><input type=checkbox name="del[]" value="<?= h($no) ?>"></th>
					<th><?= h($no) ?></th>
					<td><small><?= h($now) ?></small></td>
					<td><?= h($sub) ?></td>
					<td><b><?php if ($email): ?><a href="mailto:<?= h($email) ?>"><?= h($name) ?></a><?php else: ?><?= h($name) ?><?php endif; ?></b>
					</td>
					<td><small><?= h($com) ?></small></td>

					<td><?= h($host) ?></td>
					<td align=center>
						<?php if ($clip): ?>
							<a href="<?= h(IMG_DIR . $time . $ext) ?>" target=_blank><?= h($time . $ext) ?></a><br>
						<?php endif; ?>
						(<?= h($size) ?>)
					</td>
					<td><?= h($chk) ?></td>
				</tr>
			<?php
			}
			?>
		</table>
		<p><input type=submit value="削除する<?= h($msg) ?>">
			<input type=reset value="リセット"></form>

			<?php $all = (int)($all / 1024) ?>
			【 画像データ合計 : <b><?= h($all) ?></b> KB 】


	</center>
	</body>

	</html>
<?php

	exit;
}
?>
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

<?php
function h($str){//出力のエスケープ
	return htmlspecialchars((string)$str,ENT_QUOTES,'utf-8',false);
}
?>
<?php
//逆変換テーブル作成
function get_lineindex($line)
{
	$lineindex = [];
	foreach ($line as $i => $value) {
		if (!trim($value)) {
			continue;
		}
		list($no,) = explode(",", $value);
		$lineindex[$no] = $i; // 値にkey keyに記事no
	}
	return $lineindex;
}
?>

<?php

/**
 * Bootstrap setting.
 *
 * @return void
 */
function init()
{
	$err = "";
	$chkfile = array(LOGFILE, TREEFILE);
	if (!is_writable(realpath("./"))) {
		error("カレントディレクトリに書けません<br>");
	}

	foreach ($chkfile as $value) {
		if (!file_exists(realpath($value))) {
			$fp = fopen($value, "w");
			set_file_buffer($fp, 0);
			if ($value == LOGFILE) {
				fputs($fp, "1,2002/01/01(月) 00:00,名無し,,無題,本文なし,,,,,,,,\n");
			}
			if ($value == TREEFILE) {
				fputs($fp, "1\n");
			}
			fclose($fp);
			if (file_exists(realpath($value))) {
				@chmod($value, 0606);
			}
		}
		if (!is_writable(realpath($value))) {
			$err .= $value . "を書けません<br>";
		}
		if (!is_readable(realpath($value))) {
			$err .= $value . "を読めません<br>";
		}
	}
	@mkdir(IMG_DIR, 0707);
	@chmod(IMG_DIR, 0707);
	if (!is_dir(realpath(IMG_DIR))) {
		$err .= IMG_DIR . "がありません<br>";
	}
	if (!is_writable(realpath(IMG_DIR))) {
		$err .= IMG_DIR . "を書けません<br>";
	}
	if (!is_readable(realpath(IMG_DIR))) {
		$err .= IMG_DIR . "を読めません<br>";
	}
	if (USE_THUMB) {
		@mkdir(THUMB_DIR, 0707);
		@chmod(THUMB_DIR, 0707);
		if (!is_dir(realpath(IMG_DIR))) {
			$err .= THUMB_DIR . "がありません<br>";
		}
		if (!is_writable(realpath(THUMB_DIR))) {
			$err .= THUMB_DIR . "を書けません<br>";
		}
		if (!is_readable(realpath(THUMB_DIR))) {
			$err .= THUMB_DIR . "を読めません<br>";
		}
	}
	if ($err) {
		error($err);
	}
}
?>

<?php
init();		//←■■初期設定後は不要なので削除可■■
$iniv = array('mode', 'name', 'email', 'sub', 'com', 'pwd', 'upfile', 'upfile_name', 'resto', 'pass', 'res', 'post', 'no');
foreach ($iniv as $iniva) {
	if (!isset($$iniva)) {
		$$iniva = "";
	}
}

switch ($mode) {
	case 'regist':
		regist($resto);
		break;
	case 'admin':
		valid($pass);
		if ($admin == "del") {
			admindel($pass);
		}
		if ($admin == "post") {
			echo "</form>";
			form($post, $res, 1);
			echo $post;
			die("</body></html>");
		}
		break;
	case 'usrdel':
		usrdel($no, $pwd);
	default:
		if ($res) {
			updatelog($res);
		} else {
			updatelog();
			echo "<META HTTP-EQUIV=\"refresh\" content=\"0;URL=" . PHP_SELF2 . "\">";
		}
}
?>
