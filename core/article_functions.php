<?php
/**
 * Klasa z funkcjami do zarządznia artykułami
 */
class article {

    private $connection;

    public function __construct(PDO $connection = null) {
        $this->connection = $connection;
        if ($this->connection === null) {
            try {
                $this->connection = new PDO(DB_SERVER, DB_USERNAME, DB_PASSWORD);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo 'Połączenie nie mogło zostać utworzone.<br />';
            }
        }
    }
/**
 * Funkcja do wyświetlania dostepnych artykułów
 * @param int $page - parametr do stronicowania
 * @param string $rank - ranga użytkownika
 * @param int $userid - id użytkownia
 */
    function artykuly_view($page, $rank, $userid) {

        $user = array();
        $kateg = array();
        $stmt = $this->connection->prepare("SELECT * FROM user");

        $stmt->execute();

        $data = $stmt->fetchAll(); // fetching rows
        $count = $stmt->rowCount();

        if ($count) {


            $count = count($data); // getting count

            foreach ($data as $row) { // iterating over rows
                $user[$row['user_id']] = $row['username'];
            }
        } else {
            
        }

        $stmt = $this->connection->prepare("SELECT * FROM category");

        $stmt->execute();

        $data = $stmt->fetchAll(); // fetching rows
        $count = $stmt->rowCount();

        if ($count) {


            $count = count($data); // getting count

            foreach ($data as $row) { // iterating over rows
                $kateg[$row['category_id']] = $row['name'];
            }
        } else {
            
        }

        $limit = 5;
        if ($page)
            $start = ($page - 1) * $limit;    //first item to display on this page
        else
            $start = 0;
        $sql = "SELECT * FROM posts";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute();

        //$data = $stmt->fetchAll(); // fetching rows

        $count = $stmt->rowCount();
        $rowsperpage = 10;
// całkowita ilość stron
        $totalpages = ceil($count / $rowsperpage);

// pobieranie strony bądź ustawianie domyślnej
        if ($page) {
            
            $currentpage = $page;
        } else {
           
            $currentpage = 1;
        } 

        if ($currentpage > $totalpages) {
            
            $currentpage = $totalpages;
        } 

        if ($currentpage < 1) {
    
            $currentpage = 1;
        } 

        $offset = ($currentpage - 1) * $rowsperpage;

        if ($rank == 'user_admin') {
            $sql = 'SELECT * FROM posts WHERE user_id=' . $userid . ' ORDER BY create_date DESC Limit ' . $offset . ', ' . $rowsperpage;
        } elseif ($rank == 'user_mainadmin') {
            $sql = 'SELECT * FROM posts ORDER BY create_date DESC Limit ' . $offset . ', ' . $rowsperpage;
        } else {
            
        }


        $stmt = $this->connection->prepare($sql);

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
                //$txt
                // if (strlen($row['text']) > 30) {
                //     $txt = substr($row['text'], 0, 29) . "...";
                //  }
                //  else
                //  	{ $txt = $row['text'];}
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
            $range = 3;
            echo '<div class="pagination">';

            if ($currentpage > 1) {
            
                echo " <a href='acces_admin.php?f=articles&&page=1'><<</a> ";
            
                $prevpage = $currentpage - 1;
      
                echo " <a href='acces_admin.php?f=articles&&page=$prevpage'><</a> ";
            } 

            for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
              
                if (($x > 0) && ($x <= $totalpages)) {
                  
                    if ($x == $currentpage) {
                      
                        echo ' <span class="current">' . $x . '</span> ';
                       
                    } else {
                        
                        echo " <a href='acces_admin.php?f=articles&&page=$x'>$x</a> ";
                    }
                } 
            } 
      
            if ($currentpage != $totalpages) {
          
                $nextpage = $currentpage + 1;
             
                echo " <a href='acces_admin.php?f=articles&&page=$nextpage'>></a> ";
              
                echo " <a href='acces_admin.php?f=articles&&page=$totalpages'>>></a> ";
            } 

            echo '</div>';
            echo '';
        } else {
            echo '<div class="right"><div class="out_cont"><h2 class="logout_info">Brak artykułów.</h2></div></div>';
        }
    }
/**
 * Funkcja odpowiedzialna za dodawnie nowego artykułu
 * @param int $step - aktualny krok
 */
    function add_article($step) {

 
       
        switch ($step) {
            case '1':

                echo '<form action="acces_admin.php?f=addarticle&&step=2"  method="post" id="upl_form">
        			<label>Zdjęcia miniaturka dla artykułu:</label><br>
              <label>Proszę wybrać jedno zdjęcie:</label>';
                echo '<br><br>
              <input type="radio" name="obraz" value="data/Chrysanthemum.jpg"><img src="data/Chrysanthemum.jpg" width=100 height=100 alt="">
              <input type="radio" name="obraz" value="data/Desert.jpg"><img src="data/Desert.jpg" width=100 height=100 alt="">
              <input type="radio" name="obraz" value="data/Hydrangeas.jpg"><img src="data/Hydrangeas.jpg" width=100 height=100 alt=""><br>
              <input type="radio" name="obraz" value="data/Jellyfish.jpg"><img src="data/Jellyfish.jpg" width=100 height=100 alt="">
              <input type="radio" name="obraz" value="data/Koala.jpg"><img src="data/Koala.jpg" width=100 height=100 alt="">
              <input type="radio" name="obraz" value="data/Lighthouse.jpg"><img src="data/Lighthouse.jpg" width=100 height=100 alt=""><br><br>
              <label>lub wkleić link do obrazka:</label><br>
                    <input type="text" class="input email" name="obraz_1" placeholder="link do obrazka">';
                echo '<div style="width:500px;margin-top:50px; padding-bottom:20px;padding-top:20px;float:left;background:#fff;">';


                echo '<button class="button" style="margin-left:15px;">Dalej</button><br><br><br>
          <a class="button" style="margin-left:15px;" href="acces_admin.php?f=articles">Powrót do listy artykułów</a>';

                echo '</div></form>';

                break;

            case '2':

                if (!empty($_POST['obraz_1'])) {
                    $_SESSION['obrazek'] = $_POST['obraz_1'];
                    error('acces_admin.php?f=addarticle&&step=3', 0, 'Przejście do następnego kroku.');
                } elseif (!empty($_POST['obraz'])) {
                    $_SESSION['obrazek'] = $_POST['obraz'];
                    error('acces_admin.php?f=addarticle&&step=3', 0, 'Przejście do następnego kroku.');
                } elseif (empty($_POST['obraz_1']) && empty($_POST['obraz'])) {
                    error('acces_admin.php?f=addarticle&&step=1', 2, 'Brak danych.');
                }

                break;

            case '3':
                echo '<script type="text/javascript" src="js/tinymce/tinymce.min.js"></script>';
                echo '<script type="text/javascript">
          tinymce.init({
            selector: "textarea",
          language : "pl",
  theme: "modern",
   
    plugins: [
         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
         "save table contextmenu directionality emoticons template paste textcolor"
   ],
   content_css: "css/content.css",
   toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons", 
   style_formats: [
        {title: \'Bold text\', inline: \'b\'},
        {title: \'Red text\', inline: \'span\', styles: {color: \'#ff0000\'}},
        {title: \'Red header\', block: \'h1\', styles: {color: \'#ff0000\'}},
        {title: \'Example 1\', inline: \'span\', classes: \'example1\'},
        {title: \'Example 2\', inline: \'span\', classes: \'example2\'},
        {title: \'Table styles\'},
        {title: \'Table row 1\', selector: \'tr\', classes: \'tablerow1\'}
    ]
 }); 
      
        </script>';
                echo '<form class="form-signin" method="post" style="text-align:left;" action="acces_admin.php?f=savearticle">
				<div class="head">';
                echo '<label>Podgląd obrazka:</label> <img src="' . $_SESSION['obrazek'] . '" width=600 height=200><br>';
                echo '<label>Tytuł:</label> <input type="text" class="input username" name="tytul" placeholder="tytuł artykułu" /><br>
				<label>Opis:</label><textarea name="opis" style="resize: none; " name="opis" cols="67" rows="15"></textarea><br>';
                echo '<br>';
                $nodeList = array();
                $tree = array();
                $sql = 'SELECT category_id, name, parent_id FROM category ORDER BY name';

                $stmt = $this->connection->prepare($sql);
                //$stmt->bindValue(':pass', $password, PDO::PARAM_STR);
                $stmt->execute();
                $data = $stmt->fetchAll(); // fetching rows
                $count = $stmt->rowCount();
                if ($count) {
                    $count = count($data);
                    foreach ($data as $row) {


                        $nodeList[$row['category_id']] = array_merge($row, array('children' => array()));
                    }

                    foreach ($nodeList as $nodeId => &$node) {
                        if (!$node['parent_id'] || !array_key_exists($node['parent_id'], $nodeList)) {
                            $tree[] = &$node;
                        } else {
                            $nodeList[$node['parent_id']]['children'][] = &$node;
                        }
                    }
                    unset($node);
                    unset($nodeList);

                    echo 'Kategoria: <select name="kateg">';
                    foreach ($tree as $key) {

                        if ($key['parent_id'] == 0) {
                            echo '<option value="' . $key['category_id'] . '.' . $key['category_id'] . '">' . $key['name'] . '</option>';
                            $k = $key['category_id'];

                            foreach ($key['children'] as $key2) {
                                echo '<option value="' . $key2['category_id'] . '.' . $k . '"> -' . $key2['name'] . ' </option>';
                            }
                        }
                    }
                    echo '</select><br>';
                }

                echo '<input type="hidden" name="typ" value="insert">';
                echo '<button class="button" style="margin-left:200px;" name="zapisz_post" type="submit"><img src="./image/save_ico.png">Zapisz</button>';
                echo '</div></form>';


                break;
        }
    }
/**
 * Funkcja służąca do zapisywanie artykułu, nowego bądź edytowanego
 */
    function save_article() {

  
        date_default_timezone_set("Europe/Berlin");
        if (isset($_POST['zapisz_post'])) {
            if (!empty($_POST['tytul']) && !empty($_POST['opis'])) {
                $title = htmlspecialchars($_POST['tytul']);
                $title = trim($title);

                $opis = trim($_POST['opis']);
                list($a, $b) = explode('.', $_POST['kateg']);


                if ($_POST['typ']) {

                    switch ($_POST['typ']) {
                        case 'insert':
                            $obr = $_SESSION['obrazek'];
                            $sql = 'INSERT INTO posts (`idposts`, `user_id`, `category_id`, 
              `category_parent_id`, `topic`, `text`, `create_date`,  `image_link`, `visible`)
             VALUES (:id, :user, :kateg, :kr, :tytul, :opis,  :data,  :obr, :wid)';
                            $stmt = $this->connection->prepare($sql);
                            $stmt->bindValue(':id', '', PDO::PARAM_STR);
                            $stmt->bindValue(':user', $_SESSION['user_data']['iduser'], PDO::PARAM_STR);
                            $stmt->bindValue(':kateg', $a, PDO::PARAM_STR);
                            $stmt->bindValue(':kr', $b, PDO::PARAM_STR);
                            $stmt->bindValue(':tytul', $title, PDO::PARAM_STR);
                            $stmt->bindValue(':opis', $opis, PDO::PARAM_STR);

                            $stmt->bindValue(':data', date('Y-m-d H:i:s', strtotime("now")), PDO::PARAM_STR);

                            $stmt->bindValue(':obr', $obr, PDO::PARAM_STR);
                            $stmt->bindValue(':wid', 'niewidoczny', PDO::PARAM_STR);
                            $stmt->execute();
                            break;

                        case 'update':
                            if (isset($_POST['obrazek'])) {
                                $obr = $_POST['obrazek'];
                            }


                            $sql = 'update `posts` set `user_id`=:user, `category_id`=:kateg, 
            `category_parent_id`=:kr, `topic`=:tytul, `text`=:opis,  
            `update_date`=:data,  `image_link`=:obr where idposts = ' . $_POST['id'];

                            $stmt = $this->connection->prepare($sql);

                            $stmt->bindValue(':user', $_SESSION['user_data']['iduser'], PDO::PARAM_STR);
                            $stmt->bindValue(':kateg', $a, PDO::PARAM_STR);
                            $stmt->bindValue(':kr', $b, PDO::PARAM_STR);
                            $stmt->bindValue(':tytul', $title, PDO::PARAM_STR);
                            $stmt->bindValue(':opis', $opis, PDO::PARAM_STR);

                            $stmt->bindValue(':data', date('Y-m-d H:i:s', strtotime("now")), PDO::PARAM_STR);

                            $stmt->bindValue(':obr', $obr, PDO::PARAM_STR);

                            $stmt->execute();
                            break;
                    }
                }
                if (isset($_SESSION['obrazek'])) {
                    unset($_SESSION['obrazek']);
                }
                error("acces_admin.php?f=articles", 2, "Zapisano artykuł!!");
            } else {
                error("acces_admin.php?f=addarticle&&step=2", 2, "Brak wpisanych danych");
            }
        }
    }
/**
 * Funkcja służąca do usuwania artykułu
 * @param int $id
 */
    function del_article($id) {

   
        $stmt = $this->connection->prepare("DELETE FROM posts WHERE idposts=:id");

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        error("acces_admin.php?f=articles", 2, "Usunieto artykuł");
    }
/**
 * Funkcja służąca do edycji artykułu
 * @param int $id
 */
    function edit_article($id) {



        echo '<script type="text/javascript" src="js/tinymce/tinymce.min.js"></script>';
        echo '<script type="text/javascript">
					tinymce.init({
    				selector: "textarea",
					language : "pl",
	theme: "modern",
   
    plugins: [
         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
         "save table contextmenu directionality emoticons template paste textcolor"
   ],
   content_css: "css/content.css",
   toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons", 
   style_formats: [
        {title: \'Bold text\', inline: \'b\'},
        {title: \'Red text\', inline: \'span\', styles: {color: \'#ff0000\'}},
        {title: \'Red header\', block: \'h1\', styles: {color: \'#ff0000\'}},
        {title: \'Example 1\', inline: \'span\', classes: \'example1\'},
        {title: \'Example 2\', inline: \'span\', classes: \'example2\'},
        {title: \'Table styles\'},
        {title: \'Table row 1\', selector: \'tr\', classes: \'tablerow1\'}
    ]
 }); 
 			
				</script>';

        echo '<form class="form-signin" method="post" action="acces_admin.php?f=savearticle" style="text-align:left;">
				<div class="head">';
        $sql = 'SELECT * FROM posts WHERE idposts=:id';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(); // fetching rows
        $count = $stmt->rowCount();
        if ($count) {
            $count = count($data);
            foreach ($data as $row) {

                echo '<label>Podgląd obrazka:</label> <img src="' . $row['image_link'] . '" width=556 height=200><br>';
                echo '<label>Src obrazu:</label>  <input type="text" class="input email" name="obrazek" value="' . $row['image_link'] . '"/><br>';
                echo '<label>Tytuł:</label> <input type="text" class="input email" name="tytul" value="' . $row['topic'] . '" placeholder="tytuł artykułu" /><br>
				<label>Opis:</label><textarea style="resize: none; " name="opis" cols="67" rows="15">' . $row['text'] . '</textarea>';
                echo '<input type="hidden" name="id" value="' . $row['idposts'] . '">';
                $k = $row['category_id'];
            }
        }


        echo '<br>';
        $nodeList = array();
        $tree = array();
        $sql = 'SELECT category_id, name, parent_id FROM category ORDER BY name';

        $stmt = $this->connection->prepare($sql);
        //$stmt->bindValue(':pass', $password, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(); // fetching rows
        $count = $stmt->rowCount();
        if ($count) {
            $count = count($data);
            foreach ($data as $row) {


                $nodeList[$row['category_id']] = array_merge($row, array('children' => array()));
            }

            foreach ($nodeList as $nodeId => &$node) {
                if (!$node['parent_id'] || !array_key_exists($node['parent_id'], $nodeList)) {
                    $tree[] = &$node;
                } else {
                    $nodeList[$node['parent_id']]['children'][] = &$node;
                }
            }
            unset($node);
            unset($nodeList);


            echo '<label>Kategoria:</label> <select name="kateg">';
            foreach ($tree as $key) {

                if ($key['parent_id'] == 0) {
                    if ($key['category_id'] == $k) {

                        echo '<option value="' . $key['category_id'] . '.' . $key['category_id'] . '" selected="selected">' . $key['name'] . '</option>';
                    } else
                        echo '<option value="' . $key['category_id'] . '.' . $key['category_id'] . '" >' . $key['name'] . '</option>';
                    $ka = $key['category_id'];
                    foreach ($key['children'] as $key2) {
                        if ($key2['category_id'] == $k) {
                            echo '<option value="' . $key2['category_id'] . '.' . $ka . '" selected="selected"> -' . $key2['name'] . ' </option>';
                        } else
                            echo '<option value="' . $key2['category_id'] . '.' . $ka . '" > -' . $key2['name'] . ' </option>';
                    }
                }
            }
            echo '</select>';
        }
        echo '<br><br>';
        echo '<input type="hidden" name="typ" value="update">';
        echo '<button class="button" style="margin-left:200px;" name="zapisz_post" type="submit"><img src="./image/save_ico.png">Zapisz</button>';
        echo '</div></form>';
    }
/**
 * Funkcja służąca do publikacji artykułu
 * @param int $id
 * @param string $status
 */
    function publikacja_article($id, $status) {

        $stmt = $this->connection->prepare("SELECT * FROM posts WHERE idposts=:id");
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);

        $stmt->execute();

        switch ($status) {
            case '0':
                $status = 'niewidoczny';
                break;
            case '1':
                $status = 'opublikowany';
                break;
        }

        $data = $stmt->fetchAll(); // fetching rows
        $count = $stmt->rowCount();


        if ($count) {

            $count = count($data); // getting count

            foreach ($data as $row) {
                $title = $row['topic'];
                $stmt = $this->connection->prepare("UPDATE posts set visible=:status WHERE idposts=:id");
                $stmt->bindValue(':id', $row['idposts'], PDO::PARAM_STR);
                $stmt->bindValue(':status', $status, PDO::PARAM_STR);
                $stmt->execute();
            }
            switch ($status) {
                case 'niewidoczny':
                    error('acces_admin.php?f=articles', 2, 'Artykuł: ' . $title . ' ustawiono na nieopublikowany');
                    break;
                case 'opublikowany':
                    error('acces_admin.php?f=articles', 2, 'Opublikowano artykuł: ' . $title);
                    break;
            }
        }
    }

   
}

?>