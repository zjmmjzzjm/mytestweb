
<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="renderer" content="webkit" />
    <meta name="keywords" content="btmilk, magnet link, torrent, magnet search, torrent search engine" />
    <meta name="description" content="This is a powerful Magnet Link search engine that helps you discovery the DHT network." />
	<title><?php echo $title; ?> - The Magnet Link Search Engine</title>
    <script src="/static/scripts/jquery.min.js"></script>
    <link href="/static/styles/bootstrap.min.css" rel="stylesheet" />
    <link href="/static/styles/site.css" rel="stylesheet" />
    <link href="/static/styles/buttons.min.css" rel="stylesheet" />
    <link href="/static/styles/fontawesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="/static/styles/jquery-ui-1.10.0.custom.css" rel="stylesheet" />
    <script src="/static/scripts/site.js?1203"></script>
</head>
<body>
    <div class="header_container">
        <div class="header">
            <form action="index.php/search" method="get" id="search_form" style="height: 80px;">
                <a href="/">Home Page</a>
                    <div style="float: right;">
                        <input type="text" name="keyword" id="keyword" class="search_box" />
                        <input type="submit" value="搜索" class="search_button" />
                    </div>
            </form>
                <script type="text/javascript">
                    $("#keyword").focus();
					$("#search_form").submit(function (e) {
                        e.preventDefault();
                        var q = $("#keyword").val();
						if (!q || q.trim() == '') {
                            $("#keyword").focus();
                            return false;
						}
                        window.location.pathname = "/index.php/search/" + encodeURIComponent(q.trim());
                        return false;
                    
							});
                </script> 
        </div>
    </div>
    <div class="content">
		<div style="margin-top: 20px; margin-bottom: 20px; text-align: center;">
			<span style="font-weight: bold;">没有找到您搜索的关键字，换下关键字试试吧!</span>
		</div>
    </div>

</body>
</html>
