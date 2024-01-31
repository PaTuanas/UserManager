<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'pageTitle' => 'List',
];

layouts('header', $data);
if (!isLogin()) {
    redirect('?module=auth&action=login');
}

$listUsers = getAll("SELECT * FROM users");

$msg = getFlashData('msg');
$msg_type = getFlashData('msg_type');
// $errors = getFlashData('errors');
// $old = getFlashData('old_data');
?>

<div class="container">
    <hr>
    <h2>User Management</h2>
    <p>
        <a href="?module=users&action=add" class="btn btn-success btn-sm"><i class="fa-solid fa-plus"></i> Add user</a>
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
                <th>Fullname</th>
                <th>Email</th>
                <th>Number</th>
                <th>Status</th>
                <th width="5%">Edit</th>
                <th width="5%">Remove</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($listUsers)):
                $count = 0;
                foreach ($listUsers as $user):
                    $count++;
                    ?>
                    <tr>
                        <td>
                            <?php echo $count; ?>
                        </td>
                        <td>
                            <?php echo $user['fullname']; ?>
                        </td>
                        <td>
                            <?php echo $user['email']; ?>
                        </td>
                        <td>
                            <?php echo $user['phone']; ?>
                        </td>
                        <td>
                            <?php echo $user['status'] == 1 ? '<button class="btn btn-success btn-sn"> Activated </button>' : '<button class="btn btn-danger btn-sn"> Not yet activated </button>'; ?>
                        </td>
                        <td><a href="<?php echo _WEB_HOST; ?>?module=users&action=edit&id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sn"><i class="fa-solid fa-pen-to-square"></i></a></td>
                        <td><a href="<?php echo _WEB_HOST; ?>?module=users&action=delete&id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')"
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