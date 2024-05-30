<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$data = [
    'pageTitle' => 'Login',
];

layouts('header-login', $data);

$loginStatus = isLogin();
if ($loginStatus['isLoggedIn']) {
    redirect('?module=auth&action=login');
}

if (isPost()) {
    $filterAll = filter();
    if (!empty(trim($filterAll['email'])) && !empty(trim($filterAll['password']))) {
        $email = $filterAll['email'];
        $password = $filterAll['password'];

        $userQuery = getRow("SELECT password, id FROM users WHERE email = '$email'");
        if (!empty($userQuery)) {
            $passwordHash = $userQuery['password'];
            $userId = $userQuery['id'];
            if (password_verify($password, $passwordHash)) {

                $userLogin = getRow("SELECT * FROM tokenlogin WHERE user_id = '$userId'");
                if ($userLogin > 0) {
                    setFlashData('msg', 'Account is logged in somewhere else!');
                    setFlashData('msg_type', 'danger');
                    redirect('?module=auth&action=login');
                } else {
                    $tokenLogin = sha1(uniqid() . time());
                    $dataInsert = [
                        'user_Id' => $userId,
                        'token' => $tokenLogin,
                        'create_at' => date('Y-m-d H:i:s')
                    ];
                    $insertStatus = insert('tokenlogin', $dataInsert);
                    if ($insertStatus) {
                        setSession('loginToken', $tokenLogin);
                        redirect('?module=home&action=dashboard');
                    } else {
                        setFlashData('msg', 'Unable to log in, please try again later!');
                        setFlashData('msg_type', 'danger');
                    }
                }
            } else {
                setFlashData('msg', 'Incorrect password');
                setFlashData('msg_type', 'danger');
            }
        } else {
            setFlashData('msg', 'Email not exists');
            setFlashData('msg_type', 'danger');
        }
    } else {
        setFlashData('msg', 'Please enter your email address and password!');
        setFlashData('msg_type', 'danger');
    }
    redirect('?module=auth&action=login');
}

$msg = getFlashData('msg');
$msg_type = getFlashData('msg_type');

?>

<body>
    <div class="row">
        <div class="col-4" style="margin: 50px auto">
            <h2 class="text-center text-uppercase">User Login</h2>
            <?php
            if (!empty($msg)) {
                getMsg($msg, $msg_type);
            }
            ?>
            <form action="" method="post">
                <div class="form-group mg-form">
                    <label for="">Email</label>
                    <input name="email" type="email" class="form-control" placeholder="Email address">
                </div>
                <div class="form-group mg-form">
                    <label for="">Password</label>
                    <input name="password" type="password" class="form-control" placeholder="Password">
                </div>
                <button type="submit" class="mg-btn btn btn-primary bin-block">Login</button>
                <hr>
                <p class="text-center">
                    <a href="?module=auth&action=forgot">Forgot password?</a>
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