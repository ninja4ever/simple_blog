<?php
/**
 *  Strona główna z logowaniem
 */
session_start();
include 'tool_functions.php';
__autoload('core/user.php');
__autoload('dbconfig.php');
$user = new User();
if (isset($_POST['submit'])) {

    $login = $user->login($_POST['login'], $_POST['password']);
}
?>
<!DOCTYPE html >
<head>

    <meta name="Description" content="Information architecture, Web Design, Web Standards." />
    <meta name="Keywords" content="your, keywords" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="">
    <meta name="Robots" content="index,follow" />

    <title>Blog Site - Logowanie</title>

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="css/ind_bgr.css" rel="stylesheet">

</head>

<body>
    <div class="wrapper">

        <header class="header">

            <h2>Blog Site - Login</h2>
        </header><!-- .header-->
        <div class="middle">



            <form class="form-signin" method="post" action="index.php" onsubmit="if (this.login.value === '' || this.password.value === '') {
                        alert('podaj login i hasło');
                        return false;
                    }">
                <div class="form-head">
                    <label>Login: </label>
                    <input type="text" name="login" class="inputnormal username" placeholder="login" required >
                    <label>Hasło: </label>
                    <input type="password" name="password" class="inputnormal password" placeholder="hasło" required>

                </div>
                <div class="footer">
                    <input type="hidden" name="wyslany" value="1" />
                    <button class="button normal" name="submit" type="submit">Logowanie</button>
                </div>
                <?php
                if (isset($_SESSION['info'])){
                    echo '<br><br><label style="margin-left:130px;">' . $_SESSION['info'] . '</style>';
                unset($_SESSION['info']);
                }
                ?>
            </form>

        </div>



</body>
</html>