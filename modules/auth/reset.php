<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$title = [
    'pageTitle' => 'Register',
];

layouts('header-login', $title);

$token = filter()['token'];
if (!empty($token)) {
    $tokenQuery = getRow("SELECT id, fullname, email FROM users WHERE forgotToken = '$token'");
    if (!empty($tokenQuery)) {
        $userId = $tokenQuery['id'];
        if (isPost()) {
            $filterAll = filter();
            $errors = [];

            // Password validation
            if (empty($filterAll['password'])) {
                $errors['password']['required'] = 'Password required';
            } else {
                if (strlen($filterAll['password']) < 8) {
                    $errors['password']['min'] = 'Paswords must be at least 8 characters';
                }
            }

            //Password confirm validation
            if (empty($filterAll['password_confirm'])) {
                $errors['password_confirm']['required'] = 'Re-enter password';
            } else {
                if ($filterAll['password_confirm'] != $filterAll['password']) {
                    $errors['password_confirm']['match'] = 'The re-entered password does not match';
                }
            }

            if (empty($errors)) {
                $passwordHash = password_hash($filterAll['password'], PASSWORD_DEFAULT);
                $dataUpdate = [
                    'password' => $passwordHash,
                    'forgotToken' => null,
                    'update_at' => date('Y-m-d H:i:s'),
                ];
                $updateStatus = update('users', $dataUpdate, "id = '$userId'");
                if ($updateStatus) {
                    setFlashData('msg', 'Change password successfully!');
                    setFlashData('msg_type', 'success');
                    redirect('?module=auth&action=login');
                }
                else {
                    setFlashData('msg', 'The system is experiencing problems, please try again later!');
                    setFlashData('msg_type', 'danger');
                }
            } else {
                setFlashData('msg', 'Please check your information and try again!');
                setFlashData('msg_type', 'danger');
                setFlashData('errors', $errors);
                redirect('?module=auth&action=reset&token=' . $token);
            }
        }
        $msg = getFlashData('msg');
        $msg_type = getFlashData('msg_type');
        $errors = getFlashData('errors');
        ?>
        <div class="row">
            <div class="col-4" style="margin: 50px auto">
                <h2 class="text-center text-uppercase">PASSWORD RECOVERY</h2>
                <?php
                if (!empty($msg)) {
                    getMsg($msg, $msg_type);
                }
                ?>
                <form action="" method="post">
                    <div class="form-group mg-form">
                        <label for="">Password</label>
                        <input name="password" type="password" class="form-control" placeholder="Password">
                        <?php
                        echo form_error('password', $errors, '<span class="error">', '</span>');
                        ?>
                    </div>
                    <div class="form-group mg-form">
                        <label for="">Re-enter password</label>
                        <input name="password_confirm" type="password" class="form-control" placeholder="Re-enter password">
                        <?php
                        echo form_error('password_confirm', $errors, '<span class="error">', '</span>');
                        ?>
                    </div>
                    <input type="hidden" name="token" value="<?php echo $token ?>">
                    <button type="submit" class="mg-btn btn btn-primary bin-block">Send</button>
                    <hr>
                    <p class="text-center">
                        <a href="?module=auth&action=login">Login</a>
                    </p>
                </form>
            </div>
        </div>

        <?php

    } else {
        getMsg('Link does not exist or has expired', 'danger');
    }
} else {
    getMsg('Link does not exist or has expired', 'danger');
}

?>

<?php
layouts('footer-login');
?>