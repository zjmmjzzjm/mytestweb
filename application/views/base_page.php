<!DOCTYPE html>
<html lang="en-us">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="renderer" content="webkit" />
	<meta name="description" content="This is a powerful Magnet Link search engine that helps you discovery the DHT network." />
	<meta name="keywords" content="btmilk, magnet link, torrent, magnet search, torrent search engine" />
	<title>Magnet Link Search Engine</title>
	<script src="/static/scripts/jquery.min.js"></script>
	<link href="/static/styles/bootstrap.min.css" rel="stylesheet" />
	<link href="/static/styles/site.css" rel="stylesheet" />
	<link href="/static/styles/buttons.min.css" rel="stylesheet" />
	<script src="/static/scripts/site.js?1203"></script>
</head>
<body style="background: url(/static/xxxxImages/pattern.png) repeat fixed;">
	<div>
		<div class="header">
			<a href="/">Discover the DHT</a>
		</div>
	</div>

	<div class="content">
		<div>
			<div style="text-align: center; margin-top: 100px;">
				<div>
					<h3><span style="color: #FFF; font-size: 16px;">Discover the DHT Network.</span></h3>
				</div>
				<form action="/index.php/" method="get" id="search_form_h" style="height: 80px;">
					<div>
						<input type="text" name="keyword" id="keyword" class="search_box" style="height: 35px; width: 400px;" />
						<input type="submit" value="搜索" class="search_button" style="height: 35px; width: 100px;" />
					</div>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$("#keyword").focus();
		$("#search_form_h").submit(function (e) {
			e.preventDefault();
			var q = $("#keyword").val();
			if (!q || q.trim() == '') {
				$("#keyword").focus();
				return false;
			
			}
			window.location = "index.php/search/" + encodeURIComponent(q.trim());
			return false;
		
				});
	</script>
</body>
</html>
