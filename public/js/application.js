
// Login

// Make sure there is a login and password
function checkLogin() {
  console.log("check login");
  if (document.login.login_username.value === '') {
    document.login.login_username.focus();
    document.login.login_username.select();
    return false;
  }
  if (document.login.login_password.value === '') {
    document.login.login_password.focus();
    document.login.login_password.select();
    return false;
  }
  return true;
}
// Connecte l'utilisateur s'il a entré un pseudo et un mot de passe
function submitLogin() {
  console.log("submit login");
  if (checkLogin()) document.login.submit();
}
// Valide la connexion si 'enter' est pressé
function checkEnter(e) {
  console.log("check enter");
  if (e.which === 13 || e.keyCode === 13) submitLogin();
}