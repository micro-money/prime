<?php $backend_environment = TRUE;  #$ShowErr=TRUE; #$PlainText=true;
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php'); 
if ((isset($_GET['act']) && $_GET['act']=='quit') || empty($user['id'])) {header("Location: /login?act=quit"); exit;}  
/* ----------------------- ПАРАМЕТРЫ СТРАНИЦЫ ----------------------- */

$page['title'] = 'Profile Edit';
resource([
    'datatables/datatables/media/css/jquery.dataTables.min.css',
    'datatables/datatables/media/js/jquery.dataTables.min.js',
]);

$fid=$user['id'];

if ($user['role'] == 'admin' && isset($_GET['id'])) $fid=$_GET['id'];

$_GET['id']=$fid;

// Настройки CRUD редактора на этой странице
$page['crud_editor'] = [
    /*Склонение названия элемента редактирования*/
    'header'=>'echo "Profile Edit";',
	'bottom'=>'echo "";',
	'messages' => [
        'elements_list' => 		'User list',
        'delete_success' => 	'User deleted',
        'delete_error' => 		'Delete error',
        'create_success' => 	'User created',
        'save_success' => 		'User edited',
        'create_error' => 		'Error throw creating',
        'save_error' => 		'Error throw saving',
        'edit_element' => 		'User profile edit',
        'create_element' => 	'Create User',
        'new_element' => 		'New User'
    ],
    /*Редактируемая таблица*/
    'table' => 'users',
    /*Столбец с первичным ключом таблицы (по умолчанию - 'id')*/
    'primary_key' => 'id',
	// $passchange = $_POST['passchange']; $user_pass = $_POST['user_pass'];
    /*Поля для редактирования и их настройки*/
    'fields' => [
        'Name' => [
            'desc' => 'Name',
            'required' => true,
        ],
        'email' => [
            'desc' => 'E-mail',
        ],
        'Phone' => ['desc' => 'Phone'],
		'user_pass' => [
            'tag' => 'eva','noupdate'=>1,
			'eva' => '$type="password"; $data_in[$f_name]="";',
			'desc' => 'New Password',
		],
		'passchange' => [
            'tag' => 'eva','noupdate'=>1,
			'eva' => '$type="hidden";  $data_in[$f_name]=1; $eval_tdc=""; ',
		],
    ],
// 
    /*Столбцы для вывода в таблицу и их настройки*/
    'table_list_fields' => [
        'id' => ['desc' => '#id'],
        'Name' => ['desc' => 'Full Name'],
        'email' => ['desc' => 'E-mail'],
        'create_at' => ['desc' => 'Create At'],
    ],
	
    /*Запрос списка элементов*/
    'list_request' => 'SELECT * FROM `users` WHERE `id` = '.$fid,

    /*Скрыть кнопки редактирования*/
    'hide_edit' => false,

    /*Сортировка по колонке №*/
    'sort_column' => 1,
    'sort_order' => 'asc',

    /*Количество на странице*/
    'display_length' => 5,

];
$setname='AdminAcces';
$setvol=[
            'tag' => 'evafull','noupdate'=>1,
			'evafull' => '$estr.= "<a class=\"btn btn-info\" target=\"_blank\" href=\"?id='.$fid.'&toadmin=1\">Give this url to admin for get access</a>";  ',
			'desc' => 'Set admin access',
		];

if ( $user['role'] == 'admin' ) {
	$setname='role';
	$setvol=[
            'desc' => 'Role',
            'tag' => 'select',
            'type' => 'text',
            'default' => 'manager',
            'variants' => [
                'admin'	=>	'Admin',
                ''	=>	'',
            ],
        ];
}
$page['crud_editor']['fields'][$setname]=$setvol;

# Админ сам себя лишить прав не может
if ($user['role'] == 'admin' && $fid==$user['id']) unset($page['crud_editor']['fields'][$setname]);

// <input type="button|checkbox|file|hidden|image|password|radio|reset|submit|text">
/* ---------------------- КОНТРОЛЛЕР СТРАНИЦЫ ----------------------- */
//die($log);
require (PHIX_CORE . '/crud_editor/core.php');
if (isset($_GET['toadmin']) && $user['role'] != 'admin') aPgE('You must give this link to admin for get access');
/* -------------------------- ОТОБРАЖЕНИЕ ------------ */ ob_start(); ?>

<h2><?= $page['title'] ?></h2>
<hr />

<div class="row">
    <div class="col-xs-12 mtop-20">
        <? require MC_ROOT . "/templates/{$page['view']}.php"; ?>
    </div><!-- /.col -->
</div><!-- /.row -->

<?php require PHIX_CORE . '/render_view.php';