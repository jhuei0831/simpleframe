<?php if (isset($errors)): ?>
    <?php foreach($gump->get_readable_errors() as $error_message): ?>
        <li><font color="red"><?php echo $error_message?></font></li>
    <?php endforeach; ?>
<?php endif; ?>