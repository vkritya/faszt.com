<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" href="favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no" />
	<meta charset="UTF-8" />
	<script type="text/javascript" src="speedtest.js"></script>
	<script type="text/javascript">
		//LIST OF TEST SERVERS. See documentation for details if needed
		<?php
		$mode = getenv("MODE");
		if ($mode == "standalone" || $mode == "dual") { ?>
			var SPEEDTEST_SERVERS = [];
		<?php } else { ?>
			var SPEEDTEST_SERVERS = <?= file_get_contents('/servers.json') ?: '[]' ?>;
		<?php } ?>

		//INITIALIZE SPEED TEST
		var s = new Speedtest(); //create speed test object
		s.setParameter("getIp_ispInfo", false);
	
		//SERVER AUTO SELECTION
		function initServers() {
			if (!SPEEDTEST_SERVERS.length == 0) {
				var noServersAvailable = function () {
					I("message").innerHTML = "No servers available";
				}
				var runServerSelect = function () {
					s.selectServer(function (server) {
						if (server != null) { //at least 1 server is available
							I("loading").className = "hidden"; //hide loading message
							//populate server list for manual selection
							for (var i = 0; i < SPEEDTEST_SERVERS.length; i++) {
								if (SPEEDTEST_SERVERS[i].pingT == -1) continue;
								var option = document.createElement("option");
								option.value = i;
								option.textContent = SPEEDTEST_SERVERS[i].name;
								if (SPEEDTEST_SERVERS[i] === server) option.selected = true;
								I("server").appendChild(option);
							}
							//show test UI
							I("testWrapper").className = "visible";
							initUI();
						} else { //no servers are available, the test cannot proceed
							noServersAvailable();
						}
					});
				}
				if (typeof SPEEDTEST_SERVERS === "string") {
					//need to fetch list of servers from specified URL
					s.loadServerList(SPEEDTEST_SERVERS, function (servers) {
						if (servers == null) { //failed to load server list
							noServersAvailable();
						} else { //server list loaded
							SPEEDTEST_SERVERS = servers;
							runServerSelect();
						}
					});
				} else {
					//hardcoded server list
					s.addTestPoints(SPEEDTEST_SERVERS);
					runServerSelect();
				}
			}
		}

		function mbpsToAmount(s) {
			return 1 - (1 / (Math.pow(1.3, Math.sqrt(s))));
		}
		function format(d) {
			d = Number(d);
			if (d < 10) return d.toFixed(2);
			if (d < 100) return d.toFixed(1);
			return d.toFixed(0);
		}

		//UI CODE
		var uiData = null;
		function startStop() {
			if (s.getState() == 3) {
				//speed test is running, abort
				s.abort();
				data = null;
				I("startStopBtn").className = "";
				I("server").disabled = false;
				initUI();
			} else {
				//test is not running, begin
				I("startStopBtn").className = "running";
				I("shareArea").style.display = "none";
				I("server").disabled = true;
				s.onupdate = function (data) {
					uiData = data;
				};
				s.onend = function (aborted) {
					I("startStopBtn").className = "";
					I("server").disabled = false;
					updateUI(true);
					if (!aborted) {
						//if testId is present, show sharing panel, otherwise do nothing
						try {
							var testId = uiData.testId;
							if (testId != null) {
								var shareURL = window.location.href.substring(0, window.location.href.lastIndexOf("/")) + "/results/?id=" + testId;
								I("resultsImg").src = shareURL;
								I("resultsURL").value = shareURL;
								I("testId").innerHTML = testId;
								I("shareArea").style.display = "";
							}
						} catch (e) { }
					}
				};
				s.start();
			}
		}
		//this function reads the data sent back by the test and updates the UI
		function updateUI(forced) {
			if (!forced && s.getState() != 3) return;
			if (uiData == null) return;
			var status = uiData.testState;
			I("ip").textContent = uiData.clientIp;
			I("dlText").textContent = (status == 1 && uiData.dlStatus == 0) ? "..." : format(uiData.dlStatus);
			drawMeter(I("dlMeter"), mbpsToAmount(Number(uiData.dlStatus * (status == 1 ? oscillate() : 1))), meterBk, dlColor, Number(uiData.dlProgress), progColor);
			I("ulText").textContent = (status == 3 && uiData.ulStatus == 0) ? "..." : format(uiData.ulStatus);
			drawMeter(I("ulMeter"), mbpsToAmount(Number(uiData.ulStatus * (status == 3 ? oscillate() : 1))), meterBk, ulColor, Number(uiData.ulProgress), progColor);
			I("pingText").textContent = format(uiData.pingStatus);
			I("jitText").textContent = format(uiData.jitterStatus);
		}
		function oscillate() {
			return 1 + 0.02 * Math.sin(Date.now() / 100);
		}
		//update the UI every frame
		window.requestAnimationFrame = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.msRequestAnimationFrame || (function (callback, element) { setTimeout(callback, 1000 / 60); });
		function frame() {
			requestAnimationFrame(frame);
			updateUI();
		}
		frame(); //start frame loop
		//function to (re)initialize UI
		function initUI() {
			drawMeter(I("dlMeter"), 0, meterBk, dlColor, 0);
			drawMeter(I("ulMeter"), 0, meterBk, ulColor, 0);
			I("dlText").textContent = "";
			I("ulText").textContent = "";
			I("pingText").textContent = "";
			I("jitText").textContent = "";
			I("ip").textContent = "";
		}
	</script>
	
    <script type="module" src="resources/js/app.js"></script>
	<title><?= getenv('TITLE') ?: 'LibreSpeed' ?></title>
</head>

<body onload="initServers()">
	<h1><?= getenv('TITLE') ?: 'LibreSpeed' ?></h1>
	<div id="loading" class="visible">
		<p id="message"><span class="loadCircle"></span>Selecting a server...</p>
	</div>
	<div id="testWrapper" class="hidden">
		<div id="startStopBtn" onclick="startStop()"></div><br />
		<?php if (getenv("TELEMETRY") == "true") { ?>
			<a class="privacy" href="#" onclick="I('privacyPolicy').style.display=''">Privacy</a>
		<?php } ?>
		<div id="serverArea">
			Server: <select id="server" onchange="s.setSelectedServer(SPEEDTEST_SERVERS[this.value])"></select>
		</div>
		<div id="test">
			<div class="testGroup">
				<div class="testArea2">
					<div class="testName">Ping</div>
					<div id="pingText" class="meterText" style="color:#AA6060"></div>
					<div class="unit">ms</div>
				</div>
				<div class="testArea2">
					<div class="testName">Jitter</div>
					<div id="jitText" class="meterText" style="color:#AA6060"></div>
					<div class="unit">ms</div>
				</div>
			</div>
			<div class="testGroup">
				<div class="testArea">
					<div class="testName">Download</div>
					<canvas id="dlMeter" class="meter"></canvas>
					<div id="dlText" class="meterText"></div>
					<div class="unit">Mbit/s</div>
				</div>
				<div class="testArea">
					<div class="testName">Upload</div>
					<canvas id="ulMeter" class="meter"></canvas>
					<div id="ulText" class="meterText"></div>
					<div class="unit">Mbit/s</div>
				</div>
			</div>
			<div id="ipArea">
				<span id="ip"></span>
			</div>
			<div id="shareArea" style="display:none">
				<h3>Share results</h3>
				<p>Test ID: <span id="testId"></span></p>
				<input type="text" value="" id="resultsURL" readonly="readonly"
					onclick="this.select();this.focus();this.select();document.execCommand('copy');alert('Link copied')" />
				<img src="" id="resultsImg" />
			</div>
		</div>
	</div>
</body>

</html>