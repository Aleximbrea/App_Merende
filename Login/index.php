<!DOCTYPE html>
<html>
<?php 
include_once('../other/php/utils.php')
?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1" />
  <meta name="csrf_token" content="<?php echo createToken(); ?>" />
  <title>Login</title>
  <script src="https://kit.fontawesome.com/48c9af8c84.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="mobile.css">
</head>

<body>
  <form method="post" id="loginForm" novalidate>
    <h1>Login</h1>
    <div class="input_block">
      <input name='email' id="email" type="text" autoComplete="off" / required>
      <span class="error_message" id="err_email">
      </span>
      <label for="email">Email</label>
    </div>
    <div class="input_block password-container">
      <input name='password' id="password" type="password" autoComplete="off" / required>
      <span class="error_message" id="err_psswd">
      </span>
      <label for="password">Password</label>
      <i class="fa-solid fa-eye" id="eye" onclick="show_psswd()"></i>
    </div>
    <input type="submit" value="Login" onclick="login(event)">
    <p>Non hai un account? <a href="../Signup/" class="btn">Iscriviti</a></p>
    <p style = "margin-top: 5px;">Password dimenticata? <a href="RecuperaPassword.php" class="btn">Recupera password</a></p>
  </form>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../other/js/script.js"></script>
</body>

</html>