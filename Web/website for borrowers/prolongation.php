<?php require_once 'config.php';
/* ----------------------- ПАРАМЕТРЫ СТРАНИЦЫ ----------------------- */

$page['js_raw'] = <<<JS
    window.gon={};
    gon.locale="en";
    gon.translations={"js.slider.needAmount":"MMK","resendCode":"translation missing: en.resend_code","js.slider.terms":"days"};
JS;
$page['template'] = 'sections/frontend/layout_content';

/* ---------------------- КОНТРОЛЛЕР СТРАНИЦЫ ----------------------- */

$content = db_row("SELECT * FROM `web_pages` WHERE `slug` = 'prolongation'");
$page['title'] = $content['title_' . $app['current_language']];

$pcon=$content['content_' . $app['current_language']];
if ($page['title']=='') { $page['title'] = $content['title_en'];  $pcon=$content['content_en']; }

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start();

echo html_entity_decode($pcon);

require PHIX_CORE . '/render_view.php';