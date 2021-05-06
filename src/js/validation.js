// roles
$("#form_role").validate({
    rules: {
        name: {
            required: true
        }   
    },
});

// users
$("#form_profile").validate({
    rules: {
        name: {
            required: true
        },
        email: {
            required: true,
            email: true
        },
        role: {
            required: true
        }        
    },
});
$("#form_password").validate({
    rules: {
        password: {
            required: true,
            rangelength: [8, 30]
        },
        password_confirm: {
            required: true,
            rangelength: [8, 30]
        }      
    },
});

// auth/password/password_reset.php
$("#form_reset").validate({
    rules: {
        password: {
            required: true,
            rangelength: [8, 30]
        },
        password_confirm: {
            required: true,
            rangelength: [8, 30]
        }       
    },
});

// auth/register.php
$("#form_register").validate({
    rules: {
        name: {
            required: true
        },
        email: {
            required: true,
            email: true
        },
        password: {
            required: true,
            rangelength: [8, 30]
        },
        password_confirm: {
            required: true,
            rangelength: [8, 30]
        }       
    },
});