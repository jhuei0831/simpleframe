// 註冊頁面
function password() {
    return {
        loading: false, 
        password: '', 
        password_confirm: '',
        /* 密碼與確認密碼相符 */
        passwordConfirmIcon() {
            return {'bg-green-200 text-green-700': this.password == this.password_confirm && this.password.length > 0, 'bg-red-200 text-red-700':this.password != this.password_confirm || this.password.length == 0}
        },
        passwordConfirmText() {
            return this.password == this.password_confirm && this.password.length > 0 ? '密碼與確認密碼相符' : '密碼與確認密碼不相符' 
        },
        passwordConfirmTextColor() {
            return {'text-green-700': this.password == this.password_confirm && this.password.length > 0, 'text-red-700':this.password != this.password_confirm || this.password.length == 0} 
        },
        /* 密碼長度至少8碼 */
        passwordLengthIcon() {
            return {'bg-green-200 text-green-700': this.password.length > 7, 'bg-red-200 text-red-700':this.password.length <= 7 }
        },
        passwordLengthText() {
            return this.password.length > 7 ? '密碼符合最小長度' : '密碼長度至少8碼'
        },
        passwordLengthTextColor() {
            return {'text-green-700': this.password.length > 7, 'text-red-700':this.password.length <= 7 } 
        },
        /* 密碼至少要有一個數字 */
        passwordDigitIcon() {
            return {'bg-green-200 text-green-700': this.password.search(/[0-9]/) >= 0, 'bg-red-200 text-red-700':this.password.search(/[0-9]/) < 0 }
        },
        passwordDigitText() {
            return this.password.search(/[0-9]/) >= 0 ? '密碼包含數字' : '密碼至少需要1個數字'
        },
        passwordDigitTextColor() {
            return {'text-green-700': this.password.search(/[0-9]/) >= 0, 'text-red-700':this.password.search(/[0-9]/) < 0 }
        },
        /* 密碼至少要有一個大寫英文字母 */
        passwordUpperCaseIcon() {
            return {'bg-green-200 text-green-700': this.password.search(/[A-Z]/) >= 0, 'bg-red-200 text-red-700':this.password.search(/[A-Z]/) < 0 }
        },
        passwordUpperCaseText() {
            return this.password.search(/[A-Z]/) >= 0 ? '密碼包含大寫英文字母' : '密碼至少需要1個大寫英文字母'
        },
        passwordUpperCaseTextColor() {
            return {'text-green-700': this.password.search(/[A-Z]/) >= 0, 'text-red-700':this.password.search(/[A-Z]/) < 0 }
        },
        /* 密碼至少要有一個小寫英文字母 */
        passwordLowerCaseIcon() {
            return {'bg-green-200 text-green-700': this.password.search(/[a-z]/) >= 0, 'bg-red-200 text-red-700':this.password.search(/[a-z]/) < 0 }
        },
        passwordLowerCaseText() {
            return this.password.search(/[a-z]/) >= 0 ? '密碼包含小寫英文字母' : '密碼至少需要1個小寫英文字母'
        },
        passwordLowerCaseTextColor() {
            return {'text-green-700': this.password.search(/[a-z]/) >= 0, 'text-red-700':this.password.search(/[a-z]/) < 0 }
        },
        /* 密碼至少要有一個特殊符號 */
        passwordSpecialCharacterIcon() {
            return {'bg-green-200 text-green-700': this.password.search(/[!@#$%^&*+-]/) >= 0, 'bg-red-200 text-red-700':this.password.search(/[!@#$%^&*+-]/) < 0 }
        },
        passwordSpecialCharacterText() {
            return this.password.search(/[!@#$%^&*+-]/) >= 0 ? '密碼包含特殊符號' : '密碼至少需要1個特殊符號'
        },
        passwordSpecialCharacterTextColor() {
            return {'text-green-700': this.password.search(/[!@#$%^&*+-]/) >= 0, 'text-red-700':this.password.search(/[!@#$%^&*+-]/) < 0 }
        }
    }
}

window.password = password;