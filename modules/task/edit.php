<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$title = [
    'pageTitle' => 'Edit Task',
];

$filterAll = filter();

if (!empty($filterAll['id'])) {
    $taskId = $filterAll['id'];

    $taskDetail = getRow("SELECT * FROM tasks WHERE id = '$taskId'");
    if (!empty($taskDetail)) {
        setFlashData('task_detail', $taskDetail);
    } else {
        // Task không tồn tại
        setFlashData('msg', 'Task not found!');
        setFlashData('msg_type', 'danger');
        redirect('?module=task&action=list');
    }
}

if (isPost()) {
    $filterAll = filter();
    $errors = [];
    // Title validation 
    if (empty($filterAll['title'])) {
        $errors['title']['required'] = 'Title required';
    } else {
        if (strlen($filterAll['title']) < 5) {
            $errors['title']['min'] = 'Title must have more than 5 characters.';
        }
    }

    // Description validation
    if (empty($filterAll['description'])) {
        $errors['description']['required'] = 'Description required';
    }

    // Assigned to validation
    if (empty($filterAll['userid'])) {
        $errors['userid']['required'] = 'Assigned to required';
    }

    if (empty($errors)) {
        $dataUpdate = [
            'title' => $filterAll['title'],
            'description' => $filterAll['description'],
            'userid' => $filterAll['userid'],
            'status' => $filterAll['status'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $condition = "id = '$taskId'";
        $updateStatus = update('tasks', $dataUpdate, $condition);
        if ($updateStatus) {
            setFlashData('msg', 'Edit task successfully!');
            setFlashData('msg_type', 'success');
        } else {
            setFlashData('msg', 'The system is experiencing an error, please try again later!');
            setFlashData('msg_type', 'danger');
        }
    } else {
        setFlashData('msg', 'Please check your information and try again!');
        setFlashData('msg_type', 'danger');
        setFlashData('errors', $errors);
        setFlashData('old_data', $filterAll);
    }
    redirect('?module=task&action=list');
}

layouts('header', $title);

$msg = getFlashData('msg');
$msg_type = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old_data');
$taskDetail = getFlashData('task_detail');
if (!empty($taskDetail)) {
    $old = $taskDetail;
}

$users = getAll("SELECT * FROM users");

?>

<div class="container">
    <div class="row" style="margin: 50px auto">
        <h2 class="text-center text-uppercase">Edit Task</h2>
        <?php
        if (!empty($msg)) {
            getMsg($msg, $msg_type);
        }
        ?>
        <form action="" method="post">
            <div class="row">
                <div class="col">
                    <div class="form-group mg-form">
                        <label for="">Title</label>
                        <input name="title" type="text" class="form-control" placeholder="Title" value="<?php
                        echo old_data('title', $old);
                        ?>">
                        <?php
                        echo form_error('title', $errors, '<span class="error">', '</span>');
                        ?>
                    </div>
                    <div class="form-group mg-form">
                        <label for="">Description</label>
                        <textarea name="description" class="form-control" placeholder="Description"><?php
                        echo old_data('description', $old);
                        ?></textarea>
                        <?php
                        echo form_error('description', $errors, '<span class="error">', '</span>');
                        ?>
                    </div>
                    <div class="form-group mg-form">
                        <label for="">Assigned To</label>
                        <select name="userid" class="form-control">
                            <?php foreach ($users as $user) { ?>
                                <option value="<?php echo $user['id']; ?>" <?php echo (old_data('userid', $old) == $user['id'] ? 'selected' : ''); ?>><?php echo $user['fullname']; ?></option>
                            <?php } ?>
                        </select>
                        <?php
                        echo form_error('userid', $errors, '<span class="error">', '</span>');
                        ?>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="">Status</label>
                        <select name="status" id="" class="form-control">
                            <option value="Pending" <?php echo (old_data('status', $old) == 'Pending' ? 'selected' : false);?>>Pending</option>
                            <option value="In Progress" <?php echo (old_data('status', $old) == 'In Progress' ? 'selected' : false);?>>In Progress</option>
                            <option value="Done" <?php echo (old_data('status', $old) == 'Done' ? 'selected' : false);?>>Done</option>
                        </select>
                    </div>
                </div>
            </div>

            <input type="hidden" name="id" value="<?php echo $taskId; ?>">

            <button type="submit" class="mg-btn-add btn btn-primary bin-block">Update Task</button>
            <a href="?module=task&action=list" class="mg-btn-add btn btn-success bin-block"> Back </a>
            <hr>
        </form>
    </div>
</div>

<?php
layouts('footer');
?>
