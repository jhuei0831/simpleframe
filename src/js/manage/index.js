// node_modules
require('jquery');
require('alpinejs');
require('datatables.net');
require('sweetalert2');
require('jquery-validation');
require('jquery-validation/dist/additional-methods');

// custom js
require('../validation');
require('../datatable');
require('../init-alpine');

import Swal from 'sweetalert2';
window.Swal = Swal;
window.$ = jQuery;