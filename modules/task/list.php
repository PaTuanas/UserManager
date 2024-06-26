<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'pageTitle' => 'List',
];

layouts('header', $data);

$loginStatus = isLogin();
if (!$loginStatus['isLoggedIn']) {
    redirect('?module=auth&action=login');
    exit;
}

if (!$loginStatus['isAdmin']) {   
    setFlashData('msg', 'Access denied. Only admins can access this page.');
    setFlashData('msg_type', 'danger');
    redirect('?module=home&action=dashboard');
}

$listTasks = getAll("SELECT tasks.*, users.fullname AS username FROM tasks LEFT JOIN users ON tasks.userid = users.id ");

$msg = getFlashData('msg');
$msg_type = getFlashData('msg_type');
// $errors = getFlashData('errors');
// $old = getFlashData('old_data');
?>

<div class="container">
    <hr>
    <h2>Task Management</h2>
    <p>
        <a href="?module=task&action=add" class="btn btn-success btn-sm"><i class="fa-solid fa-plus"></i> Add Task</a>
    </p>
    <?php
    if (!empty($msg)) {
        getMsg($msg, $msg_type);
    }
    ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order</th>
                <th>Title</th>
                <th>Description</th>
                <th>Assigned to</th>
                <th>Status</th>
                <th width="5%">Edit</th>
                <th width="5%">Remove</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($listTasks)):
                $count = 0;
                foreach ($listTasks as $task):
                    $count++;
                    ?>
                    <tr>
                        <td>
                            <?php echo $count; ?>
                        </td>
                        <td>
                            <?php echo $task['title']; ?>
                        </td>
                        <td>
                            <?php echo $task['description']; ?>
                        </td>
                        <td>
                            <?php echo $task['username']; ?>
                        </td>
                        <td>
                            <?php 
                            if ($task['status'] == "Done") {
                                echo '<button class="btn btn-success btn-sn"> Done </button>';
                            } elseif ($task['status'] == "In Progress") {
                                echo '<button class="btn btn-warning btn-sn"> In Progress </button>';
                            } else {
                                echo '<button class="btn btn-danger btn-sn">' . $task['status'] . '</button>';
                            }; 
                            ?>
                        </td>
                        <td><a href="<?php echo _WEB_HOST; ?>?module=task&action=edit&id=<?php echo $task['id']; ?>" class="btn btn-warning btn-sn"><i class="fa-solid fa-pen-to-square"></i></a></td>
                        <td><a href="<?php echo _WEB_HOST; ?>?module=task&action=delete&id=<?php echo $task['id']; ?>" onclick="return confirm('Are you sure you want to delete this task?')"
                                class="btn btn-danger btn-sn"><i class="fa-solid fa-trash"></i></a></td>
                    </tr>
                    <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="7">
                        <div class="alert alert-danger text-center">Don't have any users</div>
                    </td>
                </tr>
                <?php
            endif;
            ?>
        </tbody>
    </table>
</div>

<?php
layouts('footer');
?>