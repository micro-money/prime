<? $db=$GLOBALS['countryid'];  $cf=$GLOBALS['curr_fil']; ?>
<li class="dropdown">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?= ($cf==-1) ? 'All countries' :  $db[$cf]['t'] ?> <span class="caret"></span></a>
	<ul class="dropdown-menu">
		<li class="<?= ($cf == -1) ? 'active' : '' ?>"><a href="/a/f/chdb?fil=-1&h=<?= $_SERVER['REQUEST_URI'] ?>">All countries</a></li>
		<? foreach ($db as $cb=>$cbm) { ?>
			<li class="<?= ($cf == $cb) ? 'active' : '' ?>"><a href="/a/f/chdb?fil=<?= $cb ?>&h=<?= $_SERVER['REQUEST_URI'] ?>"><?= $cbm['t'] ?></a></li>
		<? } ?>
	</ul>
</li>