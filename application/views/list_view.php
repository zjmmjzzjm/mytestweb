<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="renderer" content="webkit" />
    <meta name="keywords" content="btmilk, magnet link, torrent, magnet search, torrent search engine" />
    <meta name="description" content="This is a powerful Magnet Link search engine that helps you discovery the DHT network." />
	<title><?php echo urldecode($title) ?> - 搜索结果</title>
    <script src="/frontend/scripts/jquery.min.js"></script>
    <link href="/frontend/styles/bootstrap.min.css" rel="stylesheet" />
     <LINK href="/frontend/styles/default.css" rel="stylesheet" type="text/css">    
    <link href="/frontend/styles/site.css" rel="stylesheet" />
    <link href="/frontend/styles/buttons.min.css" rel="stylesheet" />
    <link href="/frontend/styles/fontawesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="/frontend/styles/jquery-ui-1.10.0.custom.css" rel="stylesheet" />
    <script src="/frontend/scripts/site.js?1203"></script>
    <script src="/frontend/scripts/jquery.twbsPagination.min.js"></script>
</head>
<body>
    <div class="header_container">
        <div class="header">
            <form action="/search" method="get" id="search_form" style="height: 80px;">
                <a href="/">探索DHT网络</a>
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
			<?php foreach( $datas as $data ) 
						{
?>

	<DIV class="search-item">
	<DIV class="item-title">
		<H3><A href="/index.php/detail/<?php echo $data['id'];?>" target="_blank"><?php echo $data['title'] ?></A>                 
		</H3>
	</DIV>
	<DIV class="item-list">
		<UL>
		  <LI><?php echo $data['title'] ?><SPAN class="lightColor"><?php echo $data['size'] ?></SPAN>
		  </LI>
		</UL>
	</DIV>
		  
	  
	<DIV class="item-bar"><SPAN class="cpill fileType1">视频</SPAN><SPAN>创建时间：<B>1年前</B></SPAN><SPAN>文件大小:<B><?php echo $data['size'] ?></B> </SPAN><SPAN>下载热度：<B>12</B></SPAN>              
	   <SPAN>最近下载：<B>1年前</B></SPAN>                 <A class="download" href="<?php echo $data['magnet'] ?>">磁力链接</A> 
	<A class="download" href="thunder://QUFtYWduZXQ6P3h0PXVybjpidGloOjQ2NTQzMDA4MjJBRDRCNDlDQzhCN0I3NEYyMTBEMkNBNjg2QzQ3QjBaWg==/">迅雷链接</A> 
	 </DIV>
	</DIV>

	<?php } ?>
    </div>

    <div style="text-align: center;">
        <ul id="pagination" class="pagination-sm"></ul>
    </div>
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
</body>
</html>
