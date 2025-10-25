const togglePassword = document.getElementById('togglePassword');
  const passwordField = document.getElementById('password');

  togglePassword.addEventListener('click', function () {
    const isPassword = passwordField.type === 'password';
    passwordField.type = isPassword ? 'text' : 'password';
    this.innerHTML = isPassword
    ? '<img src="assets/image/hide.png" alt="Hide" width="20" height="20">'
    : '<img src="assets/image/view.png" alt="Show" width="20" height="20">';
});  