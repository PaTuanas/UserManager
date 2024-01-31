<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

if(isLogin()) {
    $token = getSession('loginToken');
    delete('tokenlogin', "token='$token'");
    removeSession('loginToken');
    redirect('?module=auth&action=login');
}