<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

layouts('header-login');

$token = filter()['token'];
if(!empty($token)) { 
    $tokenQuery = getRow("SELECT id FROM users WHERE activeToken = '$token'");
    echo $tokenQuery;
    if(!empty($tokenQuery)) { 
        $userid = $tokenQuery['id'];
        $dataUpdate = [
            'status' => 1,
            'activeToken' => null
        ];
        $updateStatus = update('users', $dataUpdate, "id=$userid");
        if (!empty($updateStatus)) { 
            setFlashData('msg', 'Activate user successfully!');
            setFlashData('msg_type', 'success');
        }
        else {
            setFlashData('msg', 'Account activation failed, please try again later.');
            setFlashData('msg_type', 'danger');
        }
        redirect('?module=auth&action=login');
    } else { 
        getMsg('Link does not exist or has expired', 'danger');
    }
}
else { 
    getMsg('Link does not exist or has expired', 'danger');
}

?>
<h1>ACTIVE</h1>

<?php

layouts('footer-login');

?>