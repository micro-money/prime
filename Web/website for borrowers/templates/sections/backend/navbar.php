<nav class="navbar navbar-inverse">
    <!--<div class="container">-->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Menu</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">
                <i class="fa fa-money"></i> Micromoney
            </a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-left">
			<?  
				$self=$GLOBALS['self']; $selfc=$GLOBALS['selfc'];  $dr=$GLOBALS['dr']; 
				 
				if (isset($user['id'])) {
					if (in_array($user['role'],$GLOBALS['rolem'])) {
				?>
					<li class="<?= ($self == '/a/userlist/index.php') ? 'active' : '' ?>">
						<a href="/a/userlist/">Users</a></li>
					<li class="<?= ($self == '/a/leadlist/index.php') ? 'active' : '' ?>">
						<a href="/a/leadlist/">Leads</a></li>
					<li class="<?= ($self == '/a/loanlist/index.php') ? 'active' : '' ?>">
						<a href="/a/loanlist/">Loans</a></li>	
					<li class="<?= ($self == '/a/cashlist/index.php') ? 'active' : '' ?>">
						<a href="/a/cashlist/">Cash</a></li>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">WEB <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li class="<?= ($self == '/a/localelist/index.php') ? 'active' : '' ?>">
								<a href="/a/localelist/">Locales</a></li>
							<li class="<?= ($self == '/a/staticlist/index.php') ? 'active' : '' ?>">
								<a href="/a/staticlist/">Static Pages</a></li>
						</ul>
					</li>
						
					<li class="dropdown" >
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">Works <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li class="<?= ($self == '/a/scanch/index.php') ? 'active' : '' ?>"><a href="/a/scanch">Scans checking</a></li>
							<li class="<?= ($self == '/a/workleads/index.php') ? 'active' : '' ?>"><a href="/a/workleads">Full completed leads</a></li>
						</ul>
					</li>
					
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">Reports <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<!--<li><a href="/admin/report1">Sales funnel 24hours</a></li>
							<li><a href="/admin/report2">Sales funnel by days</a></li>
							<li><a href="/admin/report6">leads step3,4,5 at 48hours nodubl</a></li>
							<li><a href="/admin/report5">leads step3,4,5 at 48hours all</a></li>
							<li><a href="/admin/report31">CRM errors</a></li>
							<li><a href="/admin/sec2">Security check ver.2</a></li>-->
							<li class="<?= ($self == '/a/appdata/index.php') ? 'active' : '' ?>"><a href="/a/appdata">App user data</a></li>
							<li class="<?= ($self == '/a/calllist/index.php') ? 'active' : '' ?>"><a href="/a/calllist">Call list </a></li>
							<li class="<?= ($self == '/a/sec/index.php') ? 'active' : '' ?>"><a href="/a/sec">Security</a></li>
						</ul>
					</li>
				
				<? } ?>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				
				<? require_once($dr.'/a/f/fil.php'); ?>

				<li class="<?= ($self == '/a/profile/index.php') ? 'active' : '' ?>">
					<a href="/a/profile/"><?= $user['login'] ?></a>
				</li>
				<li><a href="?act=quit" title="Выход"><i class="fa fa-sign-out"></i></a></li>
			</ul>

			<?  } else { ?>
				<li class="<?= ($self == 'login.php') ? 'active' : '' ?>">
					<a href="/login">
						<i class="fa fa-power-off"></i> Login
					</a>
				</li>
				</ul>
			<?  } ?>

            

        </div>
    <!--</div>-->
</nav>