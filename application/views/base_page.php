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
    <script src="/Static/Scripts/site.js?1203"></script>
</head>
<body style="background: url(/Static/xxxxImages/pattern.png) repeat fixed;">
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
                        <input type="submit" value="commit" class="search_button" style="height: 35px; width: 100px;" />
                    </div>
                </form>
            </div>

            <script type="text/javascript">
			function initializeShareComponent() {
				$("#share_container a").each(function (i) {
                        var platform = $(this).attr("data-platform");
                        var url = 'https://api.addthis.com/oexchange/0.8/forward/' + platform + '/offer?url=http%3A%2F%2Fwww.btdepot.com&pubid=ra-547728fb3d5bd7ef&ct=1&title=BTDepot - The Magnet link Search engine&pco=tbxnj-1.0';
                        var src = 'https://cache.addthiscdn.com/icons/v2/thumbs/32x32/' + platform + '.png';
                        $(this).attr("href", url);
                        $(this).children().attr("src", src);
                    
						});
                
			}
                initializeShareComponent();

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
        </div>
    </div>
    <div class="footer_container" style="background: none;">
        <div class="footer">
    <div style="width: 220px; float: left; color: #FFF;">
        <ul>
            <li><a href="mailto:2780564719@qq.com">DMCA</a></li>
            <li><a href="mailto:2780564719@qq.com">Contact US</a></li>
        </ul>
    </div>
</div>
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?a91a434984101618d7b364a629b31df0";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);

 })();
</script>
    </div>
</body>
</html>
