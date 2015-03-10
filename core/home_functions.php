<?php
/**
 * Klasa ze zbiorem funkcji do obsługi strony głównej
 */
class home_functions{

    private $connection;
    public function __construct(PDO $connection = null)
    {
        $this->connection = $connection;
            if ($this->connection === null) {
                try{
            $this->connection = new PDO(DB_SERVER,DB_USERNAME,DB_PASSWORD);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            }
                catch(PDOException $e){
                        echo 'Połączenie nie mogło zostać utworzone.<br />';
                    }
        }
    }

/**
 * Funkcja wyświetlająca dynamiczne menu
 */
    public function menu()
    {

        $nodeList = array();
        $tree     = array();
        $sql      = 'SELECT category_id, name, parent_id FROM category ORDER BY name';
        
        $stmt = $this->connection->prepare($sql);
        //$stmt->bindValue(':pass', $password, PDO::PARAM_STR);
        $stmt->execute();
        $data  = $stmt->fetchAll(); // fetching rows
        $count = $stmt->rowCount();
        if ($count) {
            $count = count($data);
            foreach ($data as $row) {
                
                
                $nodeList[$row['category_id']] = array_merge($row, array(
                    'children' => array()
                ));
            }
            
            foreach ($nodeList as $nodeId => &$node) {
                if (!$node['parent_id'] || !array_key_exists($node['parent_id'], $nodeList)) {
                    $tree[] =& $node;
                } else {
                    $nodeList[$node['parent_id']]['children'][] =& $node;
                }
            }
            unset($node);
            unset($nodeList);
            //print_r($tree);
            //var_dump($tree);
            
            echo '<div class="cssmenu"><ul>';
            foreach ($tree as $key) {
                
                if ($key['parent_id'] == 0) {
                    echo '<li><a href="home.php?f=sekcja&&id=' . $key['category_id'] . '"><span>' . $key['name'] . '</span></a>';
                }
                echo '<ul>';
                //echo $key['children']['1']['nazwa'];
                foreach ($key['children'] as $key2) {
                    echo '<li class="child"><a href="home.php?f=kategoria&&id=' . $key2['category_id'] . '"><span>' . $key2['name'] . ' </span></a></li>';
                    
                }
                echo '</ul></li>';
                
                
                
            }
            echo '</ul></div>';
        }
        
    }
/**
 * Fukcja wyświetlająca artykuły na stronie
 * @param string $type
 * @param int $id
 * @param int $page
 */
    public function articles_view($type, $id, $page)
    {
        

        $user  = array();
        $kateg = array();
        $stmt  = $this->connection->prepare("SELECT * FROM user");
        
        $stmt->execute();
        
        $data  = $stmt->fetchAll(); // fetching rows
        $count = $stmt->rowCount();
        
        if ($count) {
            
            
            $count = count($data); // getting count
            
            foreach ($data as $row) { // iterating over rows
                $user[$row['user_id']] = $row['username'];
            }
            
        } else {
            
            
        }
        //var_dump($user);
        $stmt = $this->connection->prepare("SELECT * FROM category");
        
        $stmt->execute();
        
        $data  = $stmt->fetchAll(); // fetching rows
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
            $start = ($page - 1) * $limit; //first item to display on this page
        else
            $start = 0;
        
        switch ($type) {
            case 'all':
                $sql = "SELECT * FROM posts WHERE visible=:pub";
                
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':pub', 'opublikowany', PDO::PARAM_STR);
                $stmt->execute();
                break;
            
            case 'kategoria':
                $sql = "SELECT * FROM posts WHERE category_id=:id AND visible=:pub";
                
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':id', $id, PDO::PARAM_STR);
                $stmt->bindValue(':pub', 'opublikowany', PDO::PARAM_STR);
                $stmt->execute();
                break;
            case 'sekcja':
                $sql = "SELECT * FROM posts WHERE category_parent_id=:idr AND visible=:pub";
                
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':idr', $id, PDO::PARAM_STR);
                $stmt->bindValue(':pub', 'opublikowany', PDO::PARAM_STR);
                $stmt->execute();
                break;
        }
        
        
        $count       = $stmt->rowCount();
        $rowsperpage = 16;

        $totalpages  = ceil($count / $rowsperpage);
        
   
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
        
        
        switch ($type) {
            case 'all':
                $sql = 'SELECT * FROM posts WHERE visible=:pub ORDER BY create_date DESC Limit ' . $offset . ', ' . $rowsperpage;
                
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':pub', 'opublikowany', PDO::PARAM_STR);
                $stmt->execute();
                break;
            
            case 'kategoria':
                $sql = 'SELECT * FROM posts WHERE category_id=:id AND visible=:pub ORDER BY create_date DESC Limit ' . $offset . ', ' . $rowsperpage;
                
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':id', $id, PDO::PARAM_STR);
                $stmt->bindValue(':pub', 'opublikowany', PDO::PARAM_STR);
                $stmt->execute();
                break;
            case 'sekcja':
                $sql = 'SELECT * FROM posts WHERE category_parent_id=:idr AND visible=:pub ORDER BY create_date DESC Limit ' . $offset . ', ' . $rowsperpage;
                
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':idr', $id, PDO::PARAM_STR);
                $stmt->bindValue(':pub', 'opublikowany', PDO::PARAM_STR);
                $stmt->execute();
                break;
        }
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
                }
                
                
                else {
                    $kat = 'brak kategorii';
                }

                $postdata = new DateTime($row['create_date']);
                
                if (isset($kateg[$row['category_parent_id']])) {
                    if (strlen($kateg[$row['category_parent_id']]) > 60) {
                        $kat2 = substr($kateg[$row['category_parent_id']], 0, 59) . "...";
                    } else {
                        $kat2 = $kateg[$row['category_parent_id']];
                    }
                }
                
                else {
                    $kat2 = 'brak kategorii';
                }
                echo ' <div class="post">';
            

        
                echo '<div class="post_item"> <span class="title">
                <a href="home.php?f=artykul&&id=' . $row['idposts'] . '">' . $tytul . '</a><br>' . $kat2 . ' / ' . $kat . ' </span>';
               
                echo '<div class="image_bgr"><img src="' . $row['image_link'] . '"  alt="image" width="578" height="276"/></div>';

                echo '<span class="opis">Opis</span><div class="main_text">'.$txt . '</div>';
                
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
            
            echo '<div class="clearer"></div>';
            $range = 3;
            switch ($type) {
                case 'all':
                    $link = 'home.php?f=articles';
                    break;
                
                case 'kategoria':
                    $link = 'home.php?f=kategoria';
                    break;
                case 'sekcja':
                    $link = 'home.php?f=sekcja';
                    break;
            }
            
            echo '<div class="clearer"></div>';
            echo '<div class="pagination">';

            if ($currentpage > 1) {
         
                echo ' <a class="next" href="' . $link . '&&page=1"><<</a> ';
       
                $prevpage = $currentpage - 1;
           
                echo ' <a class="next" href="' . $link . '&&page=$prevpage"><</a> ';
            } 
            
            for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
        
                if (($x > 0) && ($x <= $totalpages)) {
                
                    if ($x == $currentpage) {
                
                        echo '<span class="current"> ' . $x . ' </span>';
                  
                    } else {
                    
                        echo '<a href="' . $link . '&&page=' . $x . '">' . $x . '</a> ';
                    } 
                } 
            } 
            
                
            if ($currentpage != $totalpages) {
     
                $nextpage = $currentpage + 1;
     
                echo ' <a class="next" href="' . $link . '&&page=' . $nextpage . '">></a> ';
                
                echo ' <a class="next" href="' . $link . '&&page=' . $totalpages . '">>></a> ';
            } 
            
            echo '</div>';
        } else {
            echo '<div class="right"><div class="out_cont"><h2 class="logout_info">Brak artykułów.</h2></div></div>';
            
        }

    }
/**
 * Funkcja wyświetlająca dany artykuł 
 * @param type $id
 */
    public function article_single($id)
    {
        
       
        $user  = array();
        $kateg = array();
        $stmt  = $this->connection->prepare("SELECT * FROM user");
        
        $stmt->execute();
        
        $data  = $stmt->fetchAll(); 
        $count = $stmt->rowCount();
        
        if ($count) {
            
            
            $count = count($data); 
            
            foreach ($data as $row) { 
                $user[$row['user_id']] = $row['username'];
            }
            
        } else {
            
            
        }

        $stmt = $this->connection->prepare("SELECT * FROM category");
        
        $stmt->execute();
        
        $data  = $stmt->fetchAll(); 
        $count = $stmt->rowCount();
        
        if ($count) {
            
            
            $count = count($data); 
            
            foreach ($data as $row) { 
                $kateg[$row['category_id']] = $row['name'];
            }
            
        } else {
            
            
        }
   
        $sql = 'SELECT * FROM posts WHERE idposts=:id ';
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
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

                $txt = $row['text'];

                 if (isset($kateg[$row['category_id']])) {
     
                    if (strlen($kateg[$row['category_id']]) > 60) {
                        $kat = substr($kateg[$row['category_id']], 0, 59) . "...";
                    } else {
                        $kat = $kateg[$row['category_id']];
                    }
                }
        
                else {
                    $kat = 'brak kategorii';
                }
                 $postdata = new DateTime($row['create_date']);
               if (isset($kateg[$row['category_parent_id']])) {
                    if (strlen($kateg[$row['category_parent_id']]) > 60) {
                        $kat2 = substr($kateg[$row['category_parent_id']], 0, 59) . "...";
                    } else {
                        $kat2 = $kateg[$row['category_parent_id']];
                    }
                }              
                else {
                    $kat2 = 'brak kategorii';
                }
                echo ' <div class="post">';
  
                echo '<div class="post_item"> <span class="title">
                <a href="home.php?f=artykul&&id=' . $row['idposts'] . '">' . $tytul . '</a><br>' . $kat2 . ' / ' . $kat . ' </span>';
               
                echo '<div class="image_bgr"><img src="' . $row['image_link'] . '"  alt="image" width="578" height="276"/></div>';

                echo '<span class="opis">Opis</span><div class="main_text">'.$txt . '</div></div>';

                echo '<div class="data"><p>' . $postdata->format("d-m-Y") . '<span>';
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
            
        }
    }

}


?>