
                    </div>
                </main>
            </div>
        </div>
        <?php include_once($root.'_partials/reception/footer.php') ?>
        <?php include_once($root.'_partials/reception/js.php'); ?>
        <?php echo (IS_DEBUG === 'TRUE' && in_array($_SERVER["REMOTE_ADDR"], $except_ip_list)) ? $debugbarRenderer->render() : '' ?>
    </body>
</html>