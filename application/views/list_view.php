<!DOCTYPE html>
<html lang="zh-CN">
<head>
<title><?php echo urldecode($title) ?> - 搜索结果</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="renderer" content="webkit">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="keywords" content="btmilk,种子搜索引擎,种子搜索网站,种子搜索器,迅雷种子,种子资源搜索,种子下载器,bt种子,种子全集,百度云种子,求种子,你懂的,网盘种子">
<meta name="description" content="btmilk—全球领先的种子搜索引擎，是新一代的p2p种子搜索神器，致力于给广大网友提供最先进的种子搜索服务，千万条的最全p2p种子库，没有你搜不到，只有你想不到！">
<link href="/image/favicon.ico" rel="icon">
<link href="http://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
<link href="/frontend/styles/feiliuzhixia.css" rel="stylesheet">
<script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="/frontend/scripts/jquery.twbsPagination.min.js"></script>
<script>
var _hmt = _hmt || [];
(function() {
	  var hm = document.createElement("script");
	    hm.src = "//hm.baidu.com/hm.js?8bbd5d23f57bb88e46bea517a36efa27";
	    var s = document.getElementsByTagName("script")[0];
		  s.parentNode.insertBefore(hm, s);

})();
</script>

</head>
<body>

	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#navbar">
					<span class="sr-only">导航</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/">btmilk</a>
			</div>
			<div class="navbar-collapse collapse" id="navbar">
				<ul class="nav navbar-nav">
                    <li><a href="/help.html" target="_blank">下载帮助</a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#" target="_blank">交流求片</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container" style="padding-right: 60px; padding-left: 60px;">
		<form action="/index.php/search" id="search_form">
			<div class="input-group">
				<input class="form-control" type="text" name="keyword" id="keyword"  value="<?php echo $key; ?>"  placeholder="搜索电影、剧集、动漫、番号、演员..." baiduSug="1" autofocus required>
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span>搜索</button>
				</span>
			</div>
		</form>
	</div>
	<div class="container" style="padding-right: 60px; padding-left: 60px;">
		<ol class="breadcrumb">
			<b>排序方式：</b>
			<li><a  href="#">创建时间</a></li>
			<li><a  href="#">文件大小</a></li>
			<li><a  href="#">热度指数</a></li$>
			<?php if ($datas){ ?>
			<li>&nbsp;&nbsp;已为您搜索到包含  <font color="red"><?php echo $key?></font> 的 <font color="red"><?php echo $total_found?></font> 个资源 ,耗时<font color="red"> <?php echo $time?></font> 秒。 </li>
			<?php } ?>
		</ol>
	</div>
	
	<div class="container" style="padding-right: 60px; padding-left: 60px;">
		<div class="row">
			<div class="col-md-8">
				<div class="navbar-collapse collapse" style="padding-right: 0px;padding-left: 0px;">
				</div>
				<?php if (!$datas) {echo '没有找到您搜索的关键字，换下关键字试试吧!';} else foreach( $datas as $data ) 
				{ ?>
				<div class="panel panel-default">
					<div class="panel-body">
						<h5 class="item-title"><a href="/index.php/detail/<?php echo $data['id'];?>" target="_blank"><?php echo highlight_keywords($data['title'], $words) ?></a></h5>
						<table>
							<tr>
								<td width="90px"><span class="label label-info"><b><?php echo $data['indexdate'] ?></b></span></td>
								<td width="100px"><span class="label label-info"><b><?php echo $data['size'] ?></b></span></td>
								<td width="45px"><span class="label label-success"><?php echo  guess_torrent_type($data['detail']['files'])?></span></td>
								<td width="80px"><span class="label label-danger"><b>145 &#176;C</b></span></td>
								<td width="100px"><a class="label label-primary" href="/index.php/detail/<?php echo $data['id'];?>" target="_blank" title="查看详细信息">详细信息</a></td>
							</tr>
						</table>
					</div>
				</div>
				<?php } ?>
			</div>
			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading center"><b>热门搜索</b></div>
					<div class="panel-body">
					<!--更改下面电影的时候，左侧和右侧要一致，不能只修改一侧，例如:煎饼侠.html">煎饼侠</a>-->
						<p>
						<?php foreach($hotwords as $hotword) { ?> 
						<a class="btn btn-sm btn-warning hotwords" href="/index.php/search?keyword=<?php echo $hotword ?>" target="_blank"><?php echo $hotword ?></a>
						<?php } ?>
						</p>
					</div>
				</div>
				<div class="navbar-collapse collapse"></div><!--列表页右侧上部广告位-->
				<div class="navbar-collapse collapse"></div><!--列表页右侧中部广告位-->
				<div class="navbar-collapse collapse"></div><!--列表页右侧下部广告位-->
			</div>
		</div>
	</div>
	<div class="container" style="padding-right: 60px; padding-left: 60px;">
	
		<div class="navbar-collapse collapse">
			<div class="row">
			<div style="text-align: center;" class= "pagination col-md-8">
				<ul id="pagination" class="pagination-sm"></ul>
			</div>
			<script type="text/javascript">
			$("#pagination").twbsPagination({
				totalPages: <?php echo $total_page; ?>,
						visiblePages: 10,
						startPage: <?php echo $page; ?>,
						href: "?<?php echo 'keyword='.$key?>&page={{number}}",
						first: '首页',
						prev: '上一页',
						next: '下一页',
						last: '最后一页'
				
					});
				$("#keyword").select();
			</script>
			</div>
		</div>
	</div>
	<nav class="footer navbar-inverse">
		<div class="container">
			<div class="navbar-collapse collapse navbar-text">
				<p>简单、快速、高效、稳定、影视、音乐、软件、BT、种子</p>
				<p><a href="/declare.html">btmilk声明</a> | <a href="/sitemap.xml" target="_blank">网站地图</a></p>
			</div>
			<div class="navbar-text navbar-right">
				<p>(c)<?php echo date("Y") ?> btmilk.com&nbsp;|&nbsp;</a></p>
				<p>btmilk磁力搜索引擎，第一好用的磁力搜索引擎</p>
			</div>
		</div>
	</nav>
<!--img标签横幅广告位-->
<script src="http://www.baidu.com/js/opensug.js" charset="gbk"></script>
</body>
</html>
