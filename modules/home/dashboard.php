<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$data = [
    'pageTitle' => 'Dashboard',
];

layouts('header', $data);

if(!isLogin()) {
    redirect('?module=auth&action=login');
}

?>

<h1>Dash Board</h1>

<?php

layouts('footer');
?>
