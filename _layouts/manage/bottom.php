                            <?php include_once($root.'_partials/manage/footer.php') ?>
                        </main>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once($root.'_partials/manage/js.php'); ?>
        <?php echo IS_DEBUG === 'TRUE' ? $debugbarRenderer->render() : '' ?>
    </body>
</html>