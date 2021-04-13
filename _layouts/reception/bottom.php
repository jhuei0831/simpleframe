
                    </div>
                </main>
            </div>
        </div>
        <?php include_once($root.'_partials/reception/footer.php') ?>
        <?php include_once($root.'_partials/reception/js.php'); ?>
        <script>
            function dropdown () {  
                return { 
                    show : false , 
                    target: null,
                    open(target) { this.show = true, this.target = target } ,     
                    close() { this.show = false } ,     
                    isOpen(target) { return (this.show === true && this.target == target) } ,      
                }
            }
        </script>
    </body>
</html>