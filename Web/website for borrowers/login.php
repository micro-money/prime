<?php $backend_environment = TRUE; $ShowErr=1; 
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
/* ----------------------- ПАРАМЕТРЫ СТРАНИЦЫ ----------------------- */
$page['title'] = 'Login';  $page['desc'] = 'Acoount login';

$ttt1='';

if ( isset($user['role'])) {
	$ttt1='/login?act=quit'; if ($user['role']!='') $ttt1='/a/staticlist';
}

if ($ttt1!='') { header("Location: ".$ttt1); exit; }

/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>
<div class="form-block center-block">
    <h2 class="title">Log In</h2>
    <hr>
        <form action="login.php" method="post" class="form-horizontal">
            <div class="form-group has-feedback">
                <label for="inputUserName" class="col-sm-3 control-label">Login or E-mail</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="login" placeholder="mail@mail.com" value="<?= $login; ?>" required>
                    <i class="fa fa-user form-control-feedback"></i>
                </div>
            </div>
            <div class="form-group has-feedback">
                <label for="inputPassword" class="col-sm-3 control-label">Password</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" name="pass" placeholder="Password" required>
                    <i class="fa fa-lock form-control-feedback"></i>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-8">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                    </div><br />
                    <button type="submit" class="btn btn-group btn-default btn-sm">Enter</button>
                </div>
            </div>
        </form>
</div>

<?php require PHIX_CORE . '/render_view.php';
