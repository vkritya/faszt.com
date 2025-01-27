<!DOCTYPE html>
<html class="min-h-svh">

<head>
	<link rel="shortcut icon" href="favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no" />
	<meta charset="UTF-8" />
	<script type="text/javascript" src="speedtest.js"></script>
	<script type="text/javascript">
		<?php
		// Inject test server list (or url) and title into javascript
		$mode = getenv("MODE");
		if ($mode == "standalone" || $mode == "dual") { ?>
			var SPEEDTEST_SERVERS = []
		<?php } else { ?>
			var SPEEDTEST_SERVERS = <?= file_get_contents('/servers.json') ?: '[]' ?>
		<?php } ?>
		var TITLE = '<?= getenv('TITLE') ?: 'LibreSpeed' ?>'
	</script>

	<script type="module" src="resources/js/app.js"></script>
	<title><?= getenv('TITLE') ?: 'LibreSpeed' ?></title>
</head>

<body class="min-h-full">
	<div id="app" class="min-h-full grid grid-rows-[min-content_1fr] py-4"></div>
</body>

</html>