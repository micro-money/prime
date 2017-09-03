<div class="navbar_line">
    <nav class="navbar navbar-default wrapper navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button aria-expanded="false" class="navbar-toggle collapsed" data-target="#navbar-collapse" data-toggle="collapse" type="button">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="nav__home false">
                        <a href="/"><i class="fa fa-home"></i></a>
                    </li>

                <?  
					$countrym=$GLOBALS['countrym']; 
					$pages = db_array("SELECT * FROM `web_pages` WHERE `show` = 1 order by `sort`");
                    foreach ($pages as $p) { 
						$ln=$app['current_language']; if ($p['title_' . $app['current_language']]=='') $ln='en';
				?>
                    <li class="false">
                        <a href="/<?= $p['slug'] ?>"><?= $p['title_' . $ln] ?></a>
                    </li>
                <?  } ?>
                    <li class="false">
                        <a href="https://play.google.com/store/apps/details?id=mm.com.money"><?= l('Android Application') ?></a>
                    </li>
					
					<li class="navbar__locales">Language:
						<? foreach ($countrym[$app['current_country']]['l'] as $k=>$v) { ?>
							<span <?= ($v == $app['current_language']) ? ' class="active"' : '' ?>><a href="?language=<?= $v ?>"><?= $app['langn'][$v] ?></a></span>
							
						<? } ?>
					</li>
	
	
                </ul>
            </div>
        </div>
    </nav>
</div>