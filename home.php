<?php
session_start();

if (!isset($_SESSION['user_data']['login'])) { //sprawdzamy czy jestesmy zalogowani
    header('Location: index.php');
    exit();
}
include 'tool_functions.php';
__autoload('dbconfig.php');
__autoload('core/home_functions.php');
__autoload('core/users_functions.php');
__autoload('core/search.php');

?>
<!DOCTYPE html >
<head>

    <meta name="Description" content="Information architecture, Web Design, Web Standards." />
    <meta name="Keywords" content="your, keywords" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="" >
    <meta name="Robots" content="index,follow" />   
    <script src="js/jquery-1.11.0.min.js"></script>

    <link rel="stylesheet" href="css/home_bgr.css" type="text/css" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    
    <script type="text/javascript">

        $(function () {

            $(".search_button").click(function () {
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


    <title>Blog Site - Home</title>

</head>
<body>

    <div class="wrapper">

        <header class="header">
            <nav class="menu1">
                <ul class="main">
                    <li class="nazwa"><img class="obr" src="image/user_ico_2.png">
                        <?php
                        switch ($_SESSION['user_data']['user_status']) {
                            case 'user_mainadmin':
                                echo '<span style="color:red;">' . $_SESSION['user_data']['nazwa_wys'] . '</span>';
                                break;
                            case 'user_admin':
                                echo '<span style="color:orange;">' . $_SESSION['user_data']['nazwa_wys'] . '</span>';
                                break;

                            case 'status_user':
                                echo '<span style="color:white;">' . $_SESSION['user_data']['nazwa_wys'] . '</span>';
                                break;
                        }
                        ?> </li>
                    <li> <a href="home.php"><img src="image/home_ico_2.png">Home</a> </li>
                    <?php
                    switch ($_SESSION['user_data']['user_status']) {
                        case 'user_mainadmin':
                            echo '<li> <a href="acces_admin.php" ><img src="image/admin_ico_2.png">Administracja</a> </li>';
                            break;
                        case 'user_admin':
                            echo '<li> <a href="acces_admin.php"><img src="image/admin_ico_2.png">Administracja</a> </li>';
                            break;
                    }
                    ?>

                </ul>

                <ul class="log">
                    <li > <a href="home.php?f=search"><img src="image/search_ico.png">Wyszukaj</a> </li>
                    <li > <a href="home.php?f=profile"><img src="image/profile_ico.png">Profil</a> </li>
                    <li > <a href="logout.php"><img src="image/exit_ico.png">Wyloguj</a> </li>


                </ul>
            </nav>

        </header><!-- .header-->

        <div class="middle">  
            <?php
            $home = new home_functions;
            $user = new user_functions;
            echo '<aside class="left-sidebar">';
            $home->menu();
            echo '</aside><!-- .left-sidebar -->';


            if (isset($_GET['f'])) {
                $funkcja = $_GET['f'];

                if (isset($_GET['page'])) {
                    $page = $_GET['page'];
                } else {
                    $page = '0';
                }

                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                }

                switch ($funkcja) {

                    case 'profile':

                        echo '<div class="container">
			                 ';
                        $user->profile($_SESSION['user_data']['iduser'], 'home.php');
                        echo '</div>';
                        break;
                    case 'profilesave':

                        echo '<div class="container">
                            ';
                        $user->profilesave($_SESSION['user_data']['iduser'], 'home.php');
                        echo '</div>';
                        break;
                    case 'articles':

                        echo '<div class="container">
			                 ';
                        $home->articles_view('all', 'null', $page);
                        echo '</div>';
                        break;
                    case 'sekcja':

                        echo '<div class="container">
			                 ';
                        $home->articles_view('sekcja', $id, $page);
                        echo '
                          </div>';
                        break;
                    case 'kategoria':

                        echo '<div class="container">
			                 ';
                        $home->articles_view('kategoria', $id, $page);
                        echo '
                          </div>';
                        break;
                    case 'artykul':

                        echo '<div class="container">
			                 ';
                        $home->article_single($id);
                        echo '
                          </div>';
                        break;
                    case 'search':

                        echo '<div class="container">
			                ';
                        search_home();
                        echo '
                          </div>';
                        break;
                }
            } else {

                if (isset($_GET['page'])) {
                    $page = $_GET['page'];
                } else {
                    $page = '0';
                }


                echo '<div class="container">
			';


                $home->articles_view('all', 'null', $page);
                echo '
                          </div>';
            }
            ?>

        </div><!-- .middle-->

    </div><!-- .wrapper -->
    <div class="clearer"></div>
</body>
</html>