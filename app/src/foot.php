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