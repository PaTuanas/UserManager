<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$data = [
    'pageTitle' => 'Dashboard',
];

layouts('header', $data);

$loginStatus = isLogin();
if (!$loginStatus['isLoggedIn']) {
    redirect('?module=auth&action=login');
}

$msg = getFlashData('msg');
$msg_type = getFlashData('msg_type');
?>
<?php
if (!empty($msg)) {
    getMsg($msg, $msg_type);
}
?>
<h1>Dash Board</h1>

<?php

layouts('footer');
?>