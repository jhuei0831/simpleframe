<?php
    $root = "./";
    include($root.'_config/settings.php');

    use _models\database as DB;
    use _models\message as MG;
    use _models\Security as SC;

    // DB::table('users')->where('id = 2')->update(['token' =>TOKEN, 'role' => '2']);
    
    $users = DB::table('users')->limit(5)->get();
    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $data = SC::defend_filter($_POST);
        $update = DB::table('users')->where('id = 2')->update($data);
        if ($update) {
            MG::show_message('修改成功，謝謝。');
            MG::redirect(APP_ADDRESS);
        }
        else {
            MG::show_message('修改失敗。');
            MG::redirect(APP_ADDRESS);
        }
    }
    MG::show_flash();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TEST</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
</head>
<body style="font-family: Microsoft JhengHei;">
    <div class="container mt-4">
        <table class="table">
            <thead>
                <tr>
                    <th>姓名</th>
                    <th>Email</th>
                    <th>角色</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                <? foreach($users as $user): ?>
                <tr>
                    <td><?=$user['name']?></td>
                    <td><?=$user['email']?></td>
                    <td><?=$user['role']?></td>
                    <td><a href="./delete.php?id=<?=$user['id']?>" class="btn btn-danger">刪除</a></td>
                </tr>
                <? endforeach; ?>
            </tbody>
        </table>
        <form method="POST">
            <input type="hidden" name="token" value="<?=TOKEN?>">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="name" name="name" class="form-control" id="name" value="<?=$user['name']?>">
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp" value="<?=$user['email']?>">
                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <input type="number" name="role" class="form-control" id="role" value="<?=$user['role']?>">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>