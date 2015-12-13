<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="renderer" content="webkit" />
    <meta name="keywords" content="btmilk, magnet link, torrent, magnet search, torrent search engine" />
    <meta name="description" content="This is a powerful Magnet Link search engine that helps you discovery the DHT network." />
    <title>f - The Magnet Link Search Engine</title>
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
            <form action="/search" method="get" id="search_form" style="height: 80px;">
                <a href="/">Discover the DHT</a>
                    <div style="float: right;">
					<input type="text" name="keyword" id="keyword" value="<?php echo $key; ?>" class="search_box" />
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
                        window.location = "/index.php/search/" + encodeURIComponent(q.trim());
                        return false;
                    
							});
                </script> 
        </div>
    </div>
    <div class="content">
        <div>
    <div>
        <div style="margin-bottom: 10px;">
		<span style="color: #888888;">在<?php echo $time; ?>内找到约 <?php echo $total_found; ?> 条记录匹配关键词 &quot;<?php echo $key; ?>&quot;.</span>
        </div>
        <div class="item_container">
            <span class="title_span"><strong>标题</strong></span>
            <span class="size_span"><strong>大小</strong></span>
            <span class="included_date_span"><strong>索引日期</strong></span>
            <hr class="item_separator" />
        </div>
			<?php foreach( $datas as $data ) 
						{
?>
            <div class="item_container">
			<span class="title_span"><a href="/index.php/detail/<?php echo $data['id'];?>" target="_blank"><?php echo $data['title'];?></a></span>
			<span class="size_span"><?php echo $data['size']; ?></span>
			<span class="included_date_span"><?php echo $data['indexdate'];?></span>
                <a href="/index.php/detail/<?php echo $data['id'];?>" ><input type="button" value="详细" class="button button-tiny" style="margin-left: 8px; width: 60px; height: 25px; padding-left: 0px; padding-right: 0px;" /></a>
            </div>
            <hr class="item_separator" />
<?php } ?>
    </div>

    <div style="text-align: center;">
        <ul id="pagination" class="pagination-sm"></ul>
    </div>
    <script src="/static/scripts/jquery.twbsPagination.min.js"></script>
    <script type="text/javascript">
	$("#pagination").twbsPagination({
		totalPages: <?php echo $total_page; ?>,
                visiblePages: 10,
				startPage: <?php echo $page; ?>,
				href: "?page={{number}}",
                first: '首页',
                prev: '上一页',
                next: '下一页',
                last: '最后一页'
        
			});
        $("#keyword").select();
    </script>
</div>
    </div>
    <div class="footer_container">
        <div class="footer">
    <div style="width: 340px; float: left; text-align: center; padding-top: 5px;">
        <img src="/static/images/symantec.png" alt="Symantec" />
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
</script>;
    </div>
</body>
</html>
