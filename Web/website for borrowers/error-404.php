<?php require_once 'config.php';
/* -----------------------   ----------------------- */
$page['title'] = 'Error 404';
$page['desc'] = 'Error 404 - page is not found';

/* ----------------------   ----------------------- */

/* --------------------------  ------------ */ ob_start(); ?>

    <h2>Error 404</h2>
    <p>page is not found</p>

<?php require PHIX_CORE . '/render_view.php';