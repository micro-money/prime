<!DOCTYPE html>
<html>
<head>
<? e('sections/head_content') ?>
</head>
<body>

<?= js_resources() ?>
	<div class="container-fluid">  
        <? e('sections/alerts') ?>		
		<? e('sections/backend/navbar') ?>

		<?= $content ?>

	</div>
<div id="alertrow" style="position: fixed;top:0;width: 100%;"></div>
</body>
</html>