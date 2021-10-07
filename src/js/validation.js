$(document).ready(function () {
    // roles
    $("#form_role").validate({
        rules: {
            name: {
                required: true
            }   
        },
        messages: {
            name: "名稱必填"
        }
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
        messages: {
            name: "名稱必填",
            email: {
                required: "電子郵件必填",
                email: "電子郵件格式必須類似name@domain.com"
            }
        }
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
        messages: {
            password: {
                required: "密碼必填",
                rangelength: "密碼長度必須介於8~30"
            },
            password_confirm: {
                required: "確認密碼必填",
                rangelength: "確認密碼長度必須介於8~30"
            }
        }
    });

    $("#form_permission").validate({
        rules: {
            name: {
                required: true
            },
            description: {
                required: true
            },
        },
        messages: {
            name: "名稱必填",
            description: "敘述必填",
        }
    });
});