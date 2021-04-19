<?php
    $root = "./";
    include($root.'_config/settings.php');

    use _models\framework\message as MG;
    use _models\framework\Database as DB;
    use _models\framework\Auth;
    use _models\Test;

    include($root.'_layouts/reception/top.php');
    MG::show_flash();
    // $_SESSION['USER_ID'] = 'af4fde9d-8576-487b-a245-eef0f7f0d6c7';
    $user = Test::table('users')->where('id=1')->first();
    print_r(Auth::user());
    print_r(IS_DEBUG);
?>
<?php include($root.'_layouts/reception/bottom.php') ?>
