<?php
/**
 * Obsługa wyszukiwarki na stronie
 */
session_start();
include '../tool_functions.php';
__autoload('../dbconfig.php');

if (isset($_POST['search'])) {


    try {
        $pdo = new PDO(DB_SERVER, DB_USERNAME, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $p = $pdo->prepare("SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
        $p->execute();
   
    } catch (PDOException $e) {
        echo 'Połączenie nie mogło zostać utworzone.<br />';
    }

    $data = $_POST['type'];

    switch ($data) {

        case 'article':
            $word = $_POST['search'];
            $word = htmlentities($word);

            $user = array();
            $kateg = array();
            $stmt = $pdo->prepare("SELECT * FROM user");

            $stmt->execute();

            $data = $stmt->fetchAll(); 
            $count = $stmt->rowCount();

            if ($count) {


                $count = count($data); 

                foreach ($data as $row) { 
                    $user[$row['user_id']] = $row['username'];
                }
            } else {
                
            }

            $stmt = $pdo->prepare("SELECT * FROM category");

            $stmt->execute();

            $data = $stmt->fetchAll(); 
            $count = $stmt->rowCount();

            if ($count) {


                $count = count($data); 

                foreach ($data as $row) { 
                    $kateg[$row['category_id']] = $row['name'];
                }
            }
            if ($_SESSION['user_data']['user_status'] == 'user_admin') {


                $sql = "SELECT * FROM posts WHERE (user_id='" . $_SESSION['user_data']['iduser'] . "' AND topic LIKE '%" . $word . "%') ORDER BY create_date DESC";
            } elseif ($_SESSION['user_data']['user_status'] == 'user_mainadmin') {
                $sql = "SELECT * FROM posts WHERE topic LIKE '%" . $word . "%' ORDER BY create_date DESC";
            } else {
                
            }
          

            $stmt = $pdo->prepare($sql);

            $stmt->execute();

            $data = $stmt->fetchAll(); // fetching rows

            $count = $stmt->rowCount();

            if ($count) {


                $count = count($data); // getting count
                echo '<table class="user_table">
     <thead>
     <tr>
      <th>Tytul</th>

      <th>Sekcja / Kategoria</th>

      <th>Data dodania / Data zmiany</th>
      <th>Autor</th>
      <th>Obrazek</th>
      <th>Publikacja</th>
      <th>Edycja</th>
      <th>Usuń</th>
     </tr>
     </thead>
                    <tbody>';
                foreach ($data as $row) { // iterating over rows
                    echo '<tr>';
                    if (strlen($row['topic']) > 20) {
                        $tytul = substr($row['topic'], 0, 19) . "...";
                    } else {
                        $tytul = $row['topic'];
                    }
                   
                    if (isset($kateg[$row['category_id']])) {


                        if (strlen($kateg[$row['category_id']]) > 20) {
                            $kat = substr($kateg[$row['category_id']], 0, 19) . "...";
                        } else {
                            $kat = $kateg[$row['category_id']];
                        }
                    } else {
                        $kat = 'brak kategorii';
                    }


                    if (isset($kateg[$row['category_parent_id']])) {


                        if (strlen($kateg[$row['category_parent_id']]) > 20) {
                            $kat2 = substr($kateg[$row['category_parent_id']], 0, 19) . "...";
                        } else {
                            $kat2 = $kateg[$row['category_parent_id']];
                        }
                    } else {
                        $kat2 = 'brak kategorii';
                    }

                    echo '<td>' . $tytul . '</td><td>';

                    echo $kat2 . ' / ' . $kat;

                    echo '</td><td>' . $row['create_date'] . ' / ' . $row['update_date'] . '</td><td>';

                    if (isset($user[$row['user_id']])) {
                        echo $user[$row['user_id']];
                    } else {
                        echo 'user nieznany';
                    }



                    echo '</td><td><img src="' . $row['image_link'] . '" width="50" height="50"></td>';
                    if ($row['visible'] == 'opublikowany') {
                        echo '<td><img src="./image/ok_ico.png" style="display:inline;float:left;margin-top:10px;">
            <a class="publikacja_off" href="acces_admin.php?f=articlepub&&id=' . $row['idposts'] . '&&status=0">wyłącz publikację</a></td>';
                    } else {

                        echo '<td><a class="publikacja" href="acces_admin.php?f=articlepub&&id=' . $row['idposts'] . '&&status=1">opublikuj</a></td>';
                    }

                    echo '<td><a class="edit" href="acces_admin.php?f=articleedit&&id=' . $row['idposts'] . '">edycja</a></td>
            <td><a class="del_cfg" href="acces_admin.php?f=articledel&&id=' . $row['idposts'] . '" title="Artykuł ' . $tytul . '">usuń</a></td>
            </tr>';
                }
                echo '</tbody></table>';

                
            } else {
                echo '<div style="text-align:center;"><label> Brak artykułów</label></div>';
            }
            break;

        case 'articles':

            $word = $_POST['search'];
            $word = htmlentities($word);

            $user = array();
            $kateg = array();
            $stmt = $pdo->prepare("SELECT * FROM user");

            $stmt->execute();

            $data = $stmt->fetchAll(); 
            $count = $stmt->rowCount();

            if ($count) {


                $count = count($data); 

                foreach ($data as $row) { 
                    $user[$row['user_id']] = $row['username'];
                }
            } else {
                
            }

            $stmt = $pdo->prepare("SELECT * FROM category");

            $stmt->execute();

            $data = $stmt->fetchAll(); 
            $count = $stmt->rowCount();

            if ($count) {


                $count = count($data); 

                foreach ($data as $row) { 
                    $kateg[$row['category_id']] = $row['name'];
                }
            }
            $sql = "SELECT * FROM posts WHERE topic LIKE '%" . $word . "%' ORDER BY create_date DESC";

            $stmt = $pdo->prepare($sql);

            $stmt->execute();

            $data = $stmt->fetchAll(); 

            $count = $stmt->rowCount();



            if ($count) {


                echo '<div class="posts">';
                $count = count($data); 

                foreach ($data as $row) { 
                    if (strlen($row['topic']) > 30) {
                        $tytul = substr($row['topic'], 0, 29) . "...";
                    } else {
                        $tytul = $row['topic'];
                    }

                    if (strlen($row['text']) > 600) {
                        $txt = substr($row['text'], 0, 599) . "...";
                    } else {
                        $txt = $row['text'];
                    }
                    if (isset($kateg[$row['category_id']])) {


                        if (strlen($kateg[$row['category_id']]) > 60) {
                            $kat = substr($kateg[$row['category_id']], 0, 59) . "...";
                        } else {
                            $kat = $kateg[$row['category_id']];
                        }
                    } else {
                        $kat = 'brak kategorii';
                    }
                    $postdata = new DateTime($row['create_date']);

                    if (isset($kateg[$row['category_parent_id']])) {
                        if (strlen($kateg[$row['category_parent_id']]) > 60) {
                            $kat2 = substr($kateg[$row['category_parent_id']], 0, 59) . "...";
                        } else {
                            $kat2 = $kateg[$row['category_id']];
                        }
                    } else {
                        $kat2 = 'brak kategorii';
                    }
                    echo ' <div class="post">';



                    echo '<div class="post_item"> <span class="title">
                <a href="home.php?f=artykul&&id=' . $row['idposts'] . '">' . $tytul . '</a><br>' . $kat2 . ' / ' . $kat . ' </span>';

                    echo '<div class="image_bgr"><img src="' . $row['image_link'] . '"  alt="image" width="578" height="276"/></div>';

                    echo '<span class="opis">Opis</span><div class="main_text">' . $txt . '</div>';

                    echo '<div class="read_more"><a href="home.php?f=artykul&&id=' . $row['idposts'] . '">Czytaj więcej</a></div></div>

                <div class="data"><p>' . $postdata->format("d-m-Y") . '<span>';
                    if (isset($user[$row['user_id']])) {
                        echo $user[$row['user_id']];
                    } else {
                        echo 'user nieznany';
                    }
                    echo '

                </span></p>
                </div>
                </div>';
                }

                echo '</div>';
            } else {
                echo '<br><div class="file_bgr"><div class="out_cont"><h2 class="logout_info">Brak artykułów.</h2></div></div>';
            }

            break;
    }
}
?>