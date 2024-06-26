<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$title = [
    'pageTitle' => 'Add Task',
];

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

    //Upload image
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/images/";
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $targetFile = $targetDir . uniqid() . '.' . $imageFileType;
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $errors['image']['upload'] = 'Image upload failed. Error: ' . $_FILES['image']['error'];
        } else {
            $filterAll['image'] = $targetFile;
            $attachments[] = $targetFile; // Add to attachments
        }
    }

    // Upload file
    if (!empty($_FILES['file']['name'])) {
        $targetDir = "uploads/files/";
        $fileFileType = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
        $targetFile = $targetDir . uniqid() . '.' . $fileFileType;
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            $filterAll['file'] = $targetFile;
            $attachments[] = $targetFile; // Add to attachments
        } else {
            $errors['file']['upload'] = 'File upload failed. Error: ' . $_FILES['file']['error'];
        }
    }

    if (empty($errors)) {
        $dataInsert = [
            'title' => $filterAll['title'],
            'description' => $filterAll['description'],
            'userid' => $filterAll['userid'],
            'status' => $filterAll['status'],
            'created_at' => date('Y-m-d H:i:s'),
            'file' => !empty($filterAll['file']) ? $filterAll['file'] : null,
            'image' => !empty($filterAll['image']) ? $filterAll['image'] : null
        ];

        $insertStatus = insert('tasks', $dataInsert);
        if ($insertStatus) {
            // Gửi email thông báo
            $userDetail = getRow("SELECT email FROM users WHERE id = '{$filterAll['userid']}'");
            if ($userDetail) {
                $subject = 'New Task Assigned';
                $content = 'You have been assigned a new task: ' . $filterAll['title'] . '<br>Description: ' . $filterAll['description'];
                
                // Gọi hàm sendMail với tệp đính kèm
                sendMail($userDetail['email'], $subject, $content, $attachments);
            }

            setFlashData('msg', 'Add task successfully!');
            setFlashData('msg_type', 'success');
            redirect('?module=task&action=list');
        } else {
            setFlashData('msg', 'The system is experiencing an error, please try again later!');
            setFlashData('msg_type', 'danger');
            redirect('?module=task&action=add');
        }   
    } else {
        setFlashData('msg', 'Please check your information and try again!');
        setFlashData('msg_type', 'danger');
        setFlashData('errors', $errors);
        setFlashData('old_data', $filterAll);
        redirect('?module=task&action=add');
    }
}

layouts('header', $title);

$msg = getFlashData('msg');
$msg_type = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old_data');

// Kết nối database và lấy danh sách user
$users = getAll("SELECT * FROM users");

?>

<div class="container">
    <div class="row" style="margin: 50px auto">
        <h2 class="text-center text-uppercase">Add new task</h2>
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
                        <label for="">Image</label>
                        <input name="image" type="file" class="form-control">
                        <?php
                        echo form_error('image', $errors, '<span class="error">', '</span>');
                        ?>
                    </div>
                    <div class="form-group mg-form">
                        <label for="">File</label>
                        <input name="file" type="file" class="form-control">
                        <?php
                        echo form_error('file', $errors, '<span class="error">', '</span>');
                        ?>
                    </div>
                </div>
            </div>
            <button type="submit" class="mg-btn-add btn btn-primary bin-block">Add Task</button>
            <a href="?module=task&action=list" class="mg-btn-add btn btn-success bin-block"> Back </a>
            <hr>
        </form>
    </div>
</div>

<?php
    layouts('footer');
?>
