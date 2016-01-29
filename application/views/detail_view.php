<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="renderer" content="webkit">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo $title ?>-btmilk</title>
<meta name="keywords" content="btmilk,奶茶,<?php echo $title ?>-下载,<?php echo $title ?>-资源">
<meta name="description" content="btmilk—全球领先的种子搜索引擎，是新一代的p2p种子搜索神器，致力于给广大网友提供最先进的种子搜索服务，千万条的最全p2p种子库，没有你搜不到，只有你想不到！">
<link href="/frontend/images/favor.jpg" rel="icon">
<link href="http://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
<link href="/frontend/styles/feiliuzhixia.css" rel="stylesheet">
</head>
<body>
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#navbar">
					<span class="sr-only">磁力链接</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/">btmilk</a>
			</div>
			<div class="navbar-collapse collapse" id="navbar">
			<ul class="nav navbar-nav">
                    <li><a href="" target="_blank">下载帮助</a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="" target="_blank">交流求片</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container" >
		<form action="/index.php/search">
			<div class="input-group">
			<input class="form-control" type="text" name="keyword" value="" placeholder="搜索电影、剧集、动漫、番号、演员..." baiduSug="1" autofocus required>
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span>&nbsp;搜索</button>
				</span>
			</div>
		</form>
	</div>
	<div class="container"  >
	<h3><?php echo $title ?></h3>
	</div>
	<div class="container"  >
		<div class="row">
			<div class="col-md-8">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="info row">
							<div class="col-xs-4"><b>创建时间</b>&nbsp;&nbsp;&nbsp;<span class=""><?php echo $summary["indexdate"]; ?></span></div>
							<div class="col-xs-4"><b>文件大小</b>&nbsp;&nbsp;&nbsp;<span class=""><?php echo $summary["size"]; ?></span></div>
							<div class="col-xs-4"><b>连接速度</b>&nbsp;&nbsp;&nbsp;<span class="">很快</span></div>
						</div>
						<div class="info row">
						<div class="col-xs-4"><b>活跃时间</b>&nbsp;&nbsp;&nbsp;<span class=""><?php echo date("Y-m-d") ?></span></div>
						<div class="col-xs-4"><b>文件数量</b>&nbsp;&nbsp;&nbsp;<span class=""><?php echo $summary["filenum"]; ?></span></div>
						<!-- <div class="col-xs-4"><b>热度指数</b>&nbsp;&nbsp;&nbsp;<span class="">2 &#176;C</span></div> -->
						</div>
						<div class="info row">
							<div class="col-md-12"><b>种子哈希</b>&nbsp;&nbsp;&nbsp;<span class="badge green"><?php echo $summary["hash"]; ?></span></div>
						</div>
					</div>
				</div>
				<textarea class="well magnet center" id="MagnetLink" onclick="$(this).select();" readonly><?php echo $summary["magnet"];?></textarea>
			</div>
			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading center"><b>热门搜索</b></div>
					<div class="panel-body otherwords">
						<p>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=狼图腾" target="_blank">狼图腾</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=以和为贵" target="_blank">以和为贵</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=速度与激情" target="_blank">速度与激情</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=超能陆战队" target="_blank">超能陆战队</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=生死阻击" target="_blank">生死阻击</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=死神来了" target="_blank">死神来了</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=道士出山" target="_blank">道士出山</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=天将雄狮" target="_blank">天将雄狮</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=左耳" target="_blank">左耳</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=战狼" target="_blank">战狼</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=澳门风云" target="_blank">澳门风云</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=万物生长" target="_blank">万物生长</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=何以笙箫默" target="_blank">何以笙箫默</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=星际穿越" target="_blank">星际穿越</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=功夫" target="_blank">功夫</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=咱们结婚吧" target="_blank">咱们结婚吧</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=复仇者联盟" target="_blank">复仇者联盟</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=四大名捕" target="_blank">四大名捕</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=木星上行" target="_blank">木星上行</a>
							<a class="btn btn-xs btn-danger hotwords" href="/index.php/search?keyword=一步之遥" target="_blank">一步之遥</a>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container"  >
		<div class="row">
			<div class="col-md-8">
				<div class="downbutton center">
					<a class="btn btn-sm btn-success" href="<?php echo $summary['magnet'];?>">立即下载</a>
					<a class="btn btn-sm btn-primary" href="http://vod.xunlei.com/mini.html?url=<?php echo $summary['magnet'];?>" target="_blank"><b>迅雷云播</b></a>
					<a class="btn btn-sm btn-danger" href="http://pan.baidu.com/" target="_blank"><b>百度网盘</b></a>			
					</div>
				<table class="table table-striped">
					<tr>
						<th colspan="1">
							<span>文件列表</span>
						</th>
						<th colspan="1">
							<span>文件大小</span>
						</th>
					</tr>
					
					<?php foreach ($files as $finfo) { ?>
					<tr>
						<td><?php echo $finfo["file"]; ?></td>
						<td><?php echo $finfo["size"]; ?></td>
					</tr>
					<?php } ?>
					<tr>
						<td>btmilk影视搜索（www.btmilk.com）</td>
						<td>btmilk.com</td>
					</tr>
				</table>
			</div>
			<div class="col-md-4">
				<div class="navbar-collapse collapse"></div><p>btmilk磁力搜索引擎，第一好用的磁力搜索引擎</p>
				<!--内容页左侧上部广告位-->
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-md-4">
				<div class="gg-item-left"></div>
			</div><!--内容页左侧下部广告位-->
		</div>
		<div class="col-md-8">
			<div class="gg-item-center"></div>
		</div>
	</div>
<nav class="footer navbar-inverse">
		<div class="container">
			<div class="navbar-collapse collapse navbar-text">
				<p>简单、快速、高效、稳定、影视、音乐、软件、BT、种子</p>
				<p><a href="/declare.html">btmilk声明</a> | <a href="/sitemap.xml" target="_blank">网站地图</a></p>
			</div>
			<div class="navbar-text navbar-right">
			<p>(c)<?php echo date("Y") ?> btmilk.com&nbsp;&nbsp;|&nbsp;<a><span>
			<p>btmilk磁力搜索引擎，第一好用的磁力搜索引擎</p>
			</div>
		</div>
</nav>
<!--img标签横幅广告位-->
<script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="http://www.baidu.com/js/opensug.js" charset="gbk"></script>
</body>
</html>
