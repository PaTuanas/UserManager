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

    $attachments = [];
    $dataUpdate = [
        'title' => $filterAll['title'],
        'description' => $filterAll['description'],
        'userid' => $filterAll['userid'],
        'status' => $filterAll['status'],
        'updated_at' => date('Y-m-d H:i:s'),
    ];

    // Upload new image
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/images/";
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $targetFile = $targetDir . uniqid() . '.' . $imageFileType;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // Xóa ảnh cũ
            if (!empty($taskDetail['image']) && file_exists($taskDetail['image'])) {
                unlink($taskDetail['image']);
            }
            $dataUpdate['image'] = $targetFile;
            $attachments[] = $targetFile; // Add to attachments
        } else {
            $errors['image']['upload'] = 'Image upload failed. Error: ' . $_FILES['image']['error'];
        }
    }

    // Upload new file
    if (!empty($_FILES['file']['name'])) {
        $targetDir = "uploads/files/";
        $fileFileType = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
        $targetFile = $targetDir . uniqid() . '.' . $fileFileType;
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            // Xóa tệp cũ
            if (!empty($taskDetail['file']) && file_exists($taskDetail['file'])) {
                unlink($taskDetail['file']);
            }
            $dataUpdate['file'] = $targetFile;
            $attachments[] = $targetFile; // Add to attachments
        } else {
            $errors['file']['upload'] = 'File upload failed. Error: ' . $_FILES['file']['error'];
        }
    }

    if (empty($errors)) {
        $condition = "id = '$taskId'";
        $updateStatus = update('tasks', $dataUpdate, $condition);
        if ($updateStatus) {
            // Gửi email thông báo cập nhật task cho người dùng
            $userDetail = getRow("SELECT fullname, email FROM users WHERE id = '{$filterAll['userid']}'");
            if ($userDetail) {
                $subject = 'Task Updated';
                $content = 'Your task has been updated:<br>'
                         . 'Title: ' . $filterAll['title'] . '<br>'
                         . 'Description: ' . $filterAll['description'] . '<br>'
                         . 'Status: ' . $filterAll['status'] . '<br>'
                         . 'Assigned to: ' . $userDetail['fullname'] . ' (' . $userDetail['email'] . ')';
                
                // Gọi hàm sendMail với tệp đính kèm
                sendMail($userDetail['email'], $subject, $content, $attachments);
            }

            // Gửi email thông báo cập nhật task cho admin
            $adminDetail = getRow("SELECT fullname, email FROM users WHERE admin = 1");
            if ($adminDetail) {
                $adminEmail = $adminDetail['email'];
                $adminSubject = 'Task Updated Notification';
                $adminContent = 'A task has been updated:<br>'
                              . 'Title: ' . $filterAll['title'] . '<br>'
                              . 'Description: ' . $filterAll['description'] . '<br>'
                              . 'Status: ' . $filterAll['status'] . '<br>'
                              . 'Assigned to: ' . $userDetail['fullname'] . ' (' . $userDetail['email'] . ')';
                
                // Gọi hàm sendMail với tệp đính kèm
                sendMail($adminEmail, $adminSubject, $adminContent, $attachments);
            }

            setFlashData('msg', 'Edit task successfully and email sent to user and admin!');
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
        <form action="" method="post" enctype="multipart/form-data">
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
                    <div class="form-group mg-form">
                        <label for="">Current Image</label>
                        <?php if (!empty($old['image'])) { ?>
                            <div>
                                <img src="<?php echo $old['image']; ?>" alt="Current Image" style="max-width: 100px;">
                            </div>
                        <?php } ?>
                        <label for="">Replace Image</label>
                        <input name="image" type="file" class="form-control">
                        <?php
                        echo form_error('image', $errors, '<span class="error">', '</span>');
                        ?>
                    </div>
                    <div class="form-group mg-form">
                        <label for="">Current File</label>
                        <?php if (!empty($old['file'])) { ?>
                            <div>
                                <a href="<?php echo $old['file']; ?>" target="_blank">View Current File</a>
                            </div>
                        <?php } ?>
                        <label for="">Replace File</label>
                        <input name="file" type="file" class="form-control">
                        <?php
                        echo form_error('file', $errors, '<span class="error">', '</span>');
                        ?>
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
