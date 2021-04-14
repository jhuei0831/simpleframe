<?php
    $root = "./";
    include($root.'_config/settings.php');

    use _models\message as MG;

    include($root.'_layouts/reception/top.php');
    MG::show_flash();
?>
<?php include($root.'_layouts/reception/bottom.php') ?>
