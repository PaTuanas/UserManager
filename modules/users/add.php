<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$title = [
    'pageTitle' => 'Add User',
];

if(isPost()) { 
    $filterAll = filter();
    $errors = [];
    // Fullname validation 
    if(empty($filterAll['fullname'])) {
        $errors['fullname']['required'] = 'Fullname required';
    } else {
        if(strlen($filterAll['fullname']) < 5) {
            $errors['fullname']['min'] = 'Fullname must have more than 5 characters.'; 
        }
    }

    // Email validation
    if(empty($filterAll['email'])) {
        $errors['email']['required'] = 'Email required';
    } else {
        $email = $filterAll['email'];
        $sql = "SELECT id FROM users WHERE email = '$email' ";
        if(getRow($sql) > 0) {
            $errors['email']['unique'] = 'Email already exists'; 
        }
    }

    // Phone number validation
    if(empty($filterAll['phone'])) {
        $errors['phone']['required'] = 'Phone required';
    } else {
        if (!isPhone($filterAll['phone'])) {
            $errors['phone']['isPhone'] = 'Invalid phone number';
        }
    }

    // Password validation
    if(empty($filterAll['password'])) {
        $errors['password']['required'] = 'Password required';
    } else {
        if(strlen($filterAll['password'] < 8)) {
            $errors['password']['min'] = 'Paswords must be at least 8 characters';
        }
    }

    //Password confirm validation
    if(empty($filterAll['password_confirm'])) {
        $errors['password_confirm']['required'] = 'Re-enter password';
    } else {
        if($filterAll['password_confirm'] != $filterAll['password']) {
            $errors['password_confirm']['match'] = 'The re-entered password does not match';
        }
    }

    if(empty($errors)) {
        $dataInsert = [
            'fullname' => $filterAll['fullname'],
            'email' => $filterAll['email'],
            'phone' => $filterAll['phone'],
            'password' => password_hash($filterAll['password'], PASSWORD_DEFAULT),
            'status' => $filterAll['status'],
            'create_at' => date('Y-m-d H:i:s'),
        ];
        $insertStatus = insert('users', $dataInsert);
        if($insertStatus) {
            setFlashData('msg', 'Add user successfully!');
            setFlashData('msg_type', 'success');
            redirect('?module=users&action=list');
        }
        else {
            setFlashData('msg', 'The system is experiencing an error, please try again later!');
            setFlashData('msg_type', 'danger');
            redirect('?module=users&action=add');
        }   
    } else {
        setFlashData('msg', 'Please check your information and try again!');
        setFlashData('msg_type', 'danger');
        setFlashData('errors', $errors);
        setFlashData('old_data',$filterAll);
        redirect('?module=users&action=add');
    }
}

layouts('header', $title);

$msg = getFlashData('msg');
$msg_type = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old_data');
?>

<div class="container">
    <div class="row" style="margin: 50px auto">
        <h2 class="text-center text-uppercase">Add new user</h2>
        <?php
        if (!empty($msg)) {
            getMsg($msg, $msg_type);
        }
        ?>
        <form action="" method="post">
            <div class="row">
                <div class="col">
                    <div class="form-group mg-form">
                        <label for="">Full name</label>
                        <input name="fullname" type="fullname" class="form-control" placeholder="Full name" value="<?php
                        echo old_data('fullname', $old);
                        ?>">
                        <?php
                        echo form_error('fullname', $errors, '<span class="error">', '</span>');
                        ?>
                    </div>
                    <div class="form-group mg-form">
                        <label for="">Phone number</label>
                        <input name="phone" type="number" class="form-control" placeholder="Phone number" value="<?php
                        echo old_data('phone', $old);
                        ?>">
                        <?php
                        echo form_error('phone', $errors, '<span class="error">', '</span>');
                        ?>
                    </div>
                    <div class="form-group mg-form">
                        <label for="">Email</label>
                        <input name="email" type="email" class="form-control" placeholder="Email address" value="<?php
                        echo old_data('email', $old);
                        ?>">
                        <?php
                        echo form_error('email', $errors, '<span class="error">', '</span>');
                        ?>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group mg-form">
                        <label for="">Password</label>
                        <input name="password" type="password" class="form-control" placeholder="Password" value="<?php
                        echo old_data('password', $old);
                        ?>">
                        <?php
                        echo form_error('password', $errors, '<span class="error">', '</span>');
                        ?>
                    </div>
                    <div class="form-group mg-form">
                        <label for="">Re-enter password</label>
                        <input name="password_confirm" type="password" class="form-control"
                            placeholder="Re-enter password" value="<?php
                            echo old_data('password_confirm', $old);
                            ?>">
                        <?php
                        echo form_error('password_confirm', $errors, '<span class="error">', '</span>');
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="">Status</label>
                        <select name="status" id="" class="form-control">
                            <option value="0" <?php echo (old_data('status', $old) == 0 ? 'selected' : false);?>>Not yet activated</option>
                            <option value="1" <?php echo (old_data('status', $old) == 1 ? 'selected' : false);?>>Activated</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="mg-btn-add btn btn-primary bin-block">Add User</button>
            <a href="?module=users&action=list" class="mg-btn-add btn btn-success bin-block"> Back </a>
            <hr>
        </form>
    </div>
</div>

<?php
    layouts('footer');
?>