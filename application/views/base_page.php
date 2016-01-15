<!DOCTYPE html>
<html lang="en-us">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="renderer" content="webkit" />
	<meta name="description" content="This is a powerful Magnet Link search engine that helps you discovery the DHT network." />
	<meta name="keywords" content="btmilk, magnet link, torrent, magnet search, torrent search engine" />
	<title>欢迎使用奶茶磁力链接搜索引擎</title>
	<script src="/frontend/scripts/jquery.min.js"></script>
	<link href="/frontend/styles/bootstrap.min.css" rel="stylesheet" />
	<link href="/frontend/styles/site.css" rel="stylesheet" />
	<link href="/frontend/styles/buttons.min.css" rel="stylesheet" />
	<script src="/frontend/scripts/site.js?1203"></script>
</head>
<body style="background: url(/frontend/xxxxImages/pattern.png) repeat fixed;">
	<div>
		<div "header">
			<a href="/"></a>
		</div>
	</div>

	<div class="content">
		<div>
			<div style="text-align: center; margin-top: 100px;">
				<DIV style=" margin-left: -30px;">
					<img src="/frontend/images/titlePg.png"> 
				</DIV>
				<DIV>
					<H3><SPAN style="margin-left: -17px;color: rgb(0, 0, 0); font-size: 16px;">全国最全最好的资源搜索引擎网站</SPAN></H3>
				</DIV>
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
