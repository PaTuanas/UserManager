<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$filterAll = filter();
if(!empty($filterAll['id'])) {
    $userId = $filterAll['id'];
    $userDetail = getRow("SELECT * FROM users WHERE id ='$userId'");
    if ($userDetail > 0) {
        $deleteToken = delete('tokenlogin', "user_id = $userId");
        if ($deleteToken) { 
            $deleteUser = delete('users', "id = $userId");
            if ($deleteUser) { 
                setFlashData('msg', 'Delete user successfully!');
                setFlashData('msg_type', 'success');
            }
            else {
                setFlashData('msg', 'The system is experiencing an error, please try again later!');
                setFlashData('msg_type', 'danger');
            }
        }
    }
    else {
        setFlashData('msg', 'User does not exist!');
        setFlashData('msg_type', 'danger');
    }
}
else {
    setFlashData('msg', 'Link does not exist!');
    setFlashData('msg_type', 'danger');
}

redirect('?module=users&action=list');