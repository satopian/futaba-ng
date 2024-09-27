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