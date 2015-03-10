<?php
session_start();

if (!isset($_SESSION['user_data']['login'])) { //sprawdzamy czy jestesmy zalogowani
    header('Location: index.php');
    exit();
} else {
    if ($_SESSION['user_data']['user_status'] == "status_user") {
        header('Location: home.php');
    }
}
include 'tool_functions.php';
__autoload('dbconfig.php');
__autoload('core/admin_functions.php');
__autoload('core/users_functions.php');
__autoload('core/kateg_func.php');

__autoload('core/article_functions.php');
__autoload('core/search.php');
?>

<!DOCTYPE html >
<head>

    <meta name="Description" content="Information architecture, Web Design, Web Standards." />
    <meta name="Keywords" content="your, keywords" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="" >
    <meta name="Robots" content="index,follow" />
    <link rel="stylesheet" type="text/css" href="css/admin_bgr.css">
    <title>Blog Site - Administracja</title>
    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="js/jquery-ui-1.10.4.js"></script>
    <link href="css/jquery-ui-1.10.4.custom.css" rel="stylesheet">

    <script src="js/del_info.js" type="text/javascript"></script>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

    <script type="text/javascript">

        $(function () {

            $("#button").click(function () {
                // getting the value that user typed
                var searchString = $("#search_box").val();
                var type = $("#searchtype").val();
                // forming the queryString
                var data = 'search=' + searchString + '&&type=' + type;

                // if searchString is not empty
                if (searchString) {
                    // ajax call
                    $.ajax({
                        type: "POST",
                        url: "core/search_functions.php",
                        data: data,
                        beforeSend: function (html) { // this happens before actual call
                            $("#results").html('');
                            $("#searchresults").show();
                            $(".word").html(searchString);
                        },
                        success: function (html) { // this happens after we get results
                            $("#results").show();
                            $("#results").append(html);
                        }
                    });
                }
                return false;
            });
        });

  
    </script>

</head>

<body >

    <div class="wrapper">

        <header class="header">
            <nav class="menu1">
                <ul class="main">
                    <li class="nazwa"><img  src="image/user_ico_2.png" alt="user">
<?php
switch ($_SESSION['user_data']['user_status']) {
    case 'user_mainadmin':
        echo '<span style="color:red;">' . $_SESSION['user_data']['nazwa_wys'] . '</span>';
        break;
    case 'user_admin':
        echo '<span style="color:orange;">' . $_SESSION['user_data']['nazwa_wys'] . '</span>';
        break;

    case 'status_user':
        echo $_SESSION['user_data']['nazwa_wys'];
        break;
}
?> </li>
                    <li> <a href="acces_admin.php"><img src="image/admin_ico_2.png" alt="administracja">Administracja</a> </li>
                    <li> <a href="home.php" ><img src="image/home_ico_2.png" alt="strona domowa">Home</a> </li>

                </ul>



                <ul class="log">

                    <li > <a href="acces_admin.php?f=searchpage"><img src="image/search_ico.png" alt="szukaj">Wyszukaj</a> </li>
                    <li > <a href="acces_admin.php?f=profile"><img src="image/profile_ico.png" alt="profil">Profil</a> </li>
                    <li > <a href="logout.php"><img src="image/exit_ico.png" alt="exit">Wyloguj</a> </li>

                </ul>
            </nav>

        </header><!-- .header-->

        <div class="middle">
            <aside class="left-sidebar">
                <div class='cssmenu'>
                    <ul>
                        <ul >
                            <li class="nazwa"><img  src="image/options_ico.png" alt="option"><span style="color:white;">Opcje administracji:</span></li>

                            <?php
                            if ($_SESSION['user_data']['user_status'] == 'user_mainadmin') {
                                echo '<li> <a href="acces_admin.php?f=view"><img src="image/users_ico_2.png" alt="users">Użytkownicy</a> </li>';
                            }
                            ?>

                            <li> <a href="acces_admin.php?f=kategorie"><img src="image/menu_ico_2.png" alt="menu">Zarządzanie menu</a> </li>


                            <li><a href="acces_admin.php?f=articles"><img src="image/article_ico.png" alt="artykuły">Artykuły</a></li>
                        </ul>
                    </ul>
                </div>
            </aside><!-- .left-sidebar -->
            <div class="container">
                <div style="width:100%;height:2px;background:blue;"></div>
<?php
if (isset($_GET['f'])) {
    main_admin_functions();
} else {
    menu_admin();
}
?>
              
            </div><!-- .container-->



        </div><!-- .middle-->



    </div><!-- .wrapper -->

    <div id="dialog-confirm"></div>
</body>
</html>