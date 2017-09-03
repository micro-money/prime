<!DOCTYPE html>
<html lang="<?= $app['current_language'] ?>">
    <head>
        <meta name="google-site-verification" content="dG2tNW46scdxOSbqKTl6zYzDQahPq6MGT4LC9Jfbteo"/>
		<? e('sections/head_content') ?>
    </head>

    <body class="front no-trans">

        <div class="container-fluid container-main" role="container">

            <? e('sections/frontend/header') ?>
            <? e('sections/frontend/navbar') ?>

            <? e('sections/alerts') ?>

            <div class="main">
                <?= $content ?>
            </div>

        </div>

        <? e('sections/footer') ?>

        <?= js_resources() ?>
    </body>
</html>