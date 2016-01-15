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
    <script src="/frontend/scripts/jquery.min.js"></script>
    <link href="/frontend/styles/bootstrap.min.css" rel="stylesheet" />
    <link href="/frontend/styles/site.css" rel="stylesheet" />
    <link href="/frontend/styles/buttons.min.css" rel="stylesheet" />
    <link href="/frontend/styles/fontawesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="/frontend/styles/jquery-ui-1.10.0.custom.css" rel="stylesheet" />
    <script src="/frontend/scripts/site.js?1203"></script>
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
        <div>
    <script src="/frontend/scripts/jquery-ui-1.9.2.custom.min.js"></script>
    
    <script src="/frontend/scripts/ZeroClipboard.min.js" type="text/javascript"></script>

<h1 class="torrent_title"><?php echo $title ?></h1>

    <div style="margin-top: 20px; margin-bottom: 20px; text-align: center;">
	<textarea id="magnet_link_box" class="form-control" style="width: 840px; height: 80px; word-break: break-all; cursor: text; margin: 0 auto;" readonly="readonly"><?php echo $summary["magnet"];?></textarea>
    </div>

    <div style="text-align: center; margin-top: 20px; margin-bottom: 20px;">
        <span style="font-weight: bold;">一周下载统计 (0)</span>
        <div style="margin: 0 auto; width: 700px;">
            <canvas id="requestChart" height="100"></canvas>
        </div>
        <script src="/frontend/scripts/chart.min.js" type="text/javascript"></script>
    </div>

    <div style="margin-top: 5px; margin-bottom: 20px; text-align: center;">
        <span style="font-weight: bold;">种子信息</span>
        <div style="color: #333;">
		<span style="font-weight: bold; margin-left: 5px;">大小: </span><span><?php echo $summary["size"]; ?></span>
		<span style="font-weight: bold; margin-left: 20px;">文件数: </span><span><?php echo $summary["filenum"]; ?></span>
			<span style="font-weight: bold; margin-left: 20px;">索引日期: </span><span><?php echo $summary["indexdate"]; ?></span>
			<span style="font-weight: bold; margin-left: 20px;">哈希值: </span><span><?php echo $summary["hash"]; ?></span>
        </div>
    </div>

    <div style="margin-top: 5px; margin-bottom: 20px; text-align: center;">
        <span style="font-weight: bold;">相关搜索</span>
        <div style="color: #333;">
            <ul class="keywords_list">
            </ul>
        </div>
    </div>

    <div>
        <span style="font-weight: bold;">文件名</span>
        <span style="font-weight: bold; float: right;">大小</span>
    </div>
    <hr class="item_separator" />
    <div style="margin-bottom: 50px;">
<?php foreach ($files as $finfo) {

?>
            <div>
			<span style="word-break: break-all; width: 800px; display: inline-block;"><i class="fa fa-file-video-o" style="margin-right: 5px;"></i><?php echo $finfo["file"]; ?> </span>
				<span style="float: right;"><?php echo $finfo["size"]; ?> </span>
            </div>
            <hr class="item_separator" />
<?php } ?>
    </div>

    <div id="qr_dialog" style="display: none;">
        <div style="text-align: center; padding-top: 20px; padding-bottom: 20px;">
            <img style="height: 148px; width: 148px;" />
        </div>
    </div>

    <script type="text/javascript">
        var hash = 'ed1816d9e1ce32065020b5836685d6469d7ab825';
        var title = 'df5e4d243db855877b91f19d0078a52e';

        // Share
        var shareClicked = false;

		function initializeShareComponent() {
			$("#share_container a").each(function (i) {
                var platform = $(this).attr("data-platform");
                var url = 'https://api.addthis.com/oexchange/0.8/forward/' + platform + '/offer?url=http%3A%2F%2Fwww.btdepot.com%2Ft%2F' + hash + '&pubid=ra-547728fb3d5bd7ef&ct=1&title=' + title + '&pco=tbxnj-1.0';
                var src = 'https://cache.addthiscdn.com/icons/v2/thumbs/32x32/' + platform + '.png';
                $(this).attr("href", url);
                $(this).children().attr("src", src);
            
					});
        
		}

function toggle_share() {
	if (!shareClicked) {
                initializeShareComponent();
                shareClicked = true;
            
	}
            $("#share_panel").fadeToggle(400);
        
}

        // Mangnet Link Box
$("#magnet_link_box").focus(function () {
            $(this).select();
        
		});

        // Copy to Clipboard
        var client = new ZeroClipboard(document.getElementById("copy_button"));
		client.on("aftercopy", function (e) {
            $("#magnet_link_box").focus();
        
				});

$("#qr_button").click(function () {
		$("#qr_dialog").dialog({
                autoOpen: true,
                modal: true,
                resizable: false,
                width: 380,
				buttons: {
				"Close": function () {
                        $(this).dialog("close");
                    
				}
                
				},
open: function (event, ui) {
                    $(".ui-dialog-titlebar").hide();
					if (!$("#qr_dialog img").attr("src")) {
                        $("#qr_dialog img").attr("src", "/dynamic/qr/ed1816d9e1ce32065020b5836685d6469d7ab825")
                    
					}
                
}
            
			});
        
		});

        // Downloads Chart
        var rgb = "153,204,204";
		var lineChartData = {
            labels: ['10-12','10-13','10-14','10-15','10-16','10-17','10-18'],
					datasets: [{
                label: "dataset",
                fillColor: "rgba(" + rgb + ",0.2)",
                strokeColor: "rgba(" + rgb + ",1)",
                pointColor: "rgba(" + rgb + ",1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(151,187,205,1)",
                data: [0,0,0,0,0,0,0]
                
					}]
        
		};
            var ctx = document.getElementById("requestChart").getContext("2d");
			new Chart(ctx).Line(lineChartData, {
                responsive: true,
                scaleOverride: false
            
					});
    </script>
</div>
    </div>
<div class="footer_container">
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?a91a434984101618d7b364a629b31df0";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);

 })();
</script>;
</div>
</body>
</html>
