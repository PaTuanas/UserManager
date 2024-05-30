<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$filterAll = filter();
if (!empty($filterAll['id'])) {
    $taskId = $filterAll['id'];
    $taskDetail = getRow("SELECT * FROM tasks WHERE id ='$taskId'");
    if ($taskDetail) {
        // Xóa các tệp hình ảnh và tệp đính kèm
        if (!empty($taskDetail['image'])) {
            $imagePath = $taskDetail['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        if (!empty($taskDetail['file'])) {
            $filePath = $taskDetail['file'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Xóa task từ cơ sở dữ liệu
        $deleteTask = delete('tasks', "id = $taskId");
        if ($deleteTask) { 
            setFlashData('msg', 'Delete task successfully!');
            setFlashData('msg_type', 'success');
        } else {
            setFlashData('msg', 'The system is experiencing an error, please try again later!');
            setFlashData('msg_type', 'danger');
        }
    } else {
        setFlashData('msg', 'Task does not exist!');
        setFlashData('msg_type', 'danger');
    }
} else {
    setFlashData('msg', 'Link does not exist!');
    setFlashData('msg_type', 'danger');
}

redirect('?module=task&action=list');
