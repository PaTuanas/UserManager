<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$data = [
    'pageTitle' => 'Forgot Password',
];

layouts('header-login', $data);

if(isLogin()) {
    redirect('?module=home&action=dashboard');
}

if (isPost()) {
    $filterAll = filter();
    if(!empty($filterAll['email'])) { 
        $email = $filterAll['email'];
        
        $userQuery = getRow("SELECT id FROM users WHERE email = '$email'");
        if (!empty($userQuery)) { 
            $userId = $userQuery['id'];
            $forgotToken = sha1(uniqid().time());
            $dataUpdate = [
                'forgotToken' => $forgotToken,
            ];
            $updateStatus = update('users', $dataUpdate, "id=$userId");
            if ($updateStatus) {
                $linkReset = _WEB_HOST.'?module=auth&action=reset&token='.$forgotToken;

                $subject = 'PASSWORD RECOVERY';
                $content = 'Hi user,' . '<br>';
                $content .= 'We received a password recovery request from you.' . '<br>'; 
                $content .= 'Please click here to recover your password:' . $linkReset . '<br>';
                $content .= 'Special thanks.';

                $sendMail = sendMail($email, $subject, $content);

                if($sendMail) {
                    setFlashData('msg', 'Please check your email to recover your password');
                    setFlashData('msg_type', 'success');
                }
                else {
                    setFlashData('msg', 'The system is experiencing an error, please try again later!');
                    setFlashData('msg_type', 'danger');
                }
            }
            else {
                setFlashData('msg', 'The system is experiencing an error, please try again later!');
                setFlashData('msg_type', 'danger');
            }
        }
        else {
            setFlashData('msg', 'Email address does not exist');
            setFlashData('msg_type', 'danger');
        }
    }
    else {
        setFlashData('msg', 'Please enter your email address');
        setFlashData('msg_type', 'danger');
    }
}

$msg = getFlashData('msg');
$msg_type = getFlashData('msg_type');

?>
<body>
    <div class="row">
        <div class="col-4" style="margin: 50px auto">
            <h2 class="text-center text-uppercase">Forgot Password</h2>
            <?php
                if (!empty($msg)){
                    getMsg($msg, $msg_type);
                }     
            ?>
            <form action="" method="post">
                <div class="form-group mg-form">
                    <label for="">Email</label>
                    <input name="email" type="email" class="form-control" placeholder="Email address">
                </div>
                <button type="submit"class="mg-btn btn btn-primary bin-block">Send mail</button>
                <hr>
                <p class="text-center">
                    <a href="?module=auth&action=login">Login</a>
                </p>
                <p class="text-center">
                    <a href="?module=auth&action=register">Register</a>
                </p>
            </form>
        </div>
    </div>
</body>

<?php
    layouts('footer-login');
?>