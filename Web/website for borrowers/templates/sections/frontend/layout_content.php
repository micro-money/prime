<!DOCTYPE html>
<html lang="<?= $app['current_language'] ?>">
    <head>
        <? e('sections/head_content') ?>
    </head>

    <body class="front no-trans">

        <? if (isset($jcrop_modal_need)) e('jcrop/modal') ?>

        <div class="container-fluid container-main" role="container">

            <? e('sections/frontend/header') ?>
            <? e('sections/frontend/navbar') ?>

            <? e('sections/alerts') ?>

            <div class="wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <div class="content">
                            <div class="static_pages">
                                <?= $content ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <? e('sections/footer') ?>

        <?= js_resources() ?>
    </body>
</html>