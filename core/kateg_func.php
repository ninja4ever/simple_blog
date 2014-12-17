<?php
/**
 * Klasa odpowiedzialna za zarządznie menu na stronie głównej
 * i tworzenie oraz dodawnie kategorii i sekcji menu
 */
class leftmenu {

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
 * Wyświetlanie kategorii i sekcji
 */
    function view_categ() {

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


            echo '<div class="inner"><div class="menu_kateg"><ul>';
            foreach ($tree as $key) {

                if ($key['parent_id'] == 0) {
                    echo '<li class="has-sub"><a  href="acces_admin.php?f=katedit&&id=' . $key['category_id'] . '">' . $key['name'] . '</a>       
        <ul>
	    <li ><a class="del_s" href="acces_admin.php?f=delkat&&id=' . $key['category_id'] . '" title="Sekcję: ' . $key['name'] . ' i kategorie"> Usuń sekcje</a></li>
        <li ><a  href="acces_admin.php?f=addpkat&&idr=' . $key['category_id'] . '"><img src="./image/add_ico.png">Dodaj kategorię</a></li>
        </ul>
        </li>';
                }
                foreach ($key['children'] as $key2) {
                    echo '
            <li class="child has-sub"><a  href="acces_admin.php?f=katedit&&id=' . $key2['category_id'] . '">' . $key2['name'] . ' </a>
            <ul>
			<li ><a class="del_s" href="acces_admin.php?f=delpkat&&id=' . $key2['category_id'] . '" title="Kategorie: ' . $key2['name'] . '"> Usuń kategorię</a></li>
            </ul>
            </li>';
                }
            }
            echo '</ul></div></div>';
        } else
            echo '<div class="inner"><ul class="menu_kateg">
		<li> Brak kategorii menu </li>
	</ul></div>';
    }
/**
 * Funkcja usuwająca daną sekcję
 * @param int $id
 */
    function del_kateg($id) {

    
        $stmt = $this->connection->prepare("SELECT * FROM category WHERE parent_id=:id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(); // fetching rows
        $count = $stmt->rowCount();

        if ($count) {

            $count = count($data); // getting count
            foreach ($data as $row) { // iterating over rows
                $sql = 'DELETE FROM category WHERE parent_id=' . $row['parent_id'];
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
            }

            $stmt = $this->connection->prepare("DELETE FROM category WHERE category_id=:id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            error("acces_admin.php?f=kategorie", 2, "Usunięto sekcję i wszystkie kategorie");
        } else {
            $stmt = $this->connection->prepare("DELETE FROM category WHERE category_id=:id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            error("acces_admin.php?f=kategorie", 2, "Usunięto kategorię");
        }
    }
/**
 * Funkcja usuwająca daną kategorię
 * @param int $id
 */
    function del_pkateg($id) {

 

        $stmt = $this->connection->prepare("DELETE FROM category WHERE category_id=:id");

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        error("acces_admin.php?f=kategorie", 1, "Usunięto kategorię");
    }
/**
 * Fukcja odpowiedzialna za zapisywanie odpoiednio: sekcji, kategorii 
 * oraz edycję już istniejącej sekcji / kategorii 
 */
    function katsave() {


  

        if (isset($_POST['zapisz_nazwa'])) {

            if (!empty($_POST['nazwa'])) {
                $nazwa = htmlspecialchars($_POST['nazwa']);

                $sql = 'INSERT INTO category VALUES (:id, :nazwa, :idrodzic)';
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':id', '', PDO::PARAM_STR);
                $stmt->bindValue(':nazwa', $nazwa, PDO::PARAM_STR);
                $stmt->bindValue(':idrodzic', 0, PDO::PARAM_INT);
                $stmt->execute();

                error("acces_admin.php?f=kategorie", 2, "Zapisano nową sekcję");
            } else {

                error("acces_admin.php?f=addkategory", 2, "Nie wpisano wymaganych danych");
            }
        } elseif (isset($_POST['zapisz_pnazwa'])) {

            if (!empty($_POST['nazwa']) && !empty($_POST['wyslany'])) {

                $nazwa = htmlspecialchars($_POST['nazwa']);
                $sql = 'INSERT INTO category VALUES (:id, :nazwa, :idrodzic)';
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':id', '', PDO::PARAM_STR);
                $stmt->bindValue(':nazwa', $nazwa, PDO::PARAM_STR);
                $stmt->bindValue(':idrodzic', $_POST['wyslany'], PDO::PARAM_INT);
                $stmt->execute();

                error("acces_admin.php?f=kategorie", 2, "Zapisano nową kategorie menu");
            } else {

                error("acces_admin.php?f=addkategory", 2, "Nie wpisano wymaganych danych");
            }
        } elseif (isset($_POST['zapisz_enazwa'])) {

            if (!empty($_POST['nazwa']) && !empty($_POST['wyslany'])) {

                $nazwa = htmlspecialchars($_POST['nazwa']);
                $sql = 'update category set name=:nnazwa where category_id=:id';
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':nnazwa', $nazwa, PDO::PARAM_STR);
                $stmt->bindValue(':id', $_POST['wyslany'], PDO::PARAM_STR);

                $stmt->execute();

                error("acces_admin.php?f=kategorie", 2, "Zapisano nową nazwę");
            } else {

                error("acces_admin.php?f=kategorie", 2, "Nie wpisano wymaganych danych");
            }
        }
    }
/**
 * Forma do dodania nowej sekcji
 */
    function add_kateg() {
        echo '

        <form class="form-signin" method="post" action="acces_admin.php?f=katsave">
       <div class="head">		
            		<label>Nazwa sekcji: </label>           	
            		<input type="text" name="nazwa" class="input email" placeholder="nazwa" >              
            	</div>           
            	<div class="footer">
            		<input type="hidden" name="wyslany" value="1" />
            		<button class="button" style="margin-left:200px;" name="zapisz_nazwa" type="submit"><img src="./image/save_ico.png">Zapisz</button>             
            		</div>';
        echo '</form>';
    }
/**
 * Forma do dodania nowej kategorii
 * @param int $idrodzica 
 */
    function add_pkateg($idrodzica) {

        echo '<form class="form-signin" method="post" action="acces_admin.php?f=katsave">
            <div class="head">
            	<label>Nazwa kategorii: </label>
            	<input type="text" name="nazwa" class="input email" placeholder="nazwa" >  
            </div>
            <div class="footer">
            	<input type="hidden" name="wyslany" value="' . $idrodzica . '" />
            	<button class="button" style="margin-left:200px;" name="zapisz_pnazwa" type="submit"><img src="./image/save_ico.png">Zapisz</button>    
            </div>';
        echo '</form>';
    }
/**
 * Funkcja odpowiedzialna za edycję kategorii / sekcji
 * @param int $id
 */
    function kateg_edit($id) {

      
        $stmt = $this->connection->prepare("SELECT * FROM category WHERE category_id=:id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(); // fetching rows
        $count = $stmt->rowCount();

        if ($count) {
            echo '<form class="form-signin" method="post" action="acces_admin.php?f=katsave">
                    <div class="head">';
            $count = count($data); // getting count
            foreach ($data as $row) { // iterating over rows
                echo '
            			<label>Obecna nazwa kategorii: </label>
            		<span class="user">' . $row['name'] . '</span>';
            }

            echo '<br>
            		<label>Nowa nazwa sekcji / kategorii: </label>        		
          		<input type="text" name="nazwa" class="input email" placeholder="nazwa" >
            	<div class="footer">
            			<input type="hidden" name="wyslany" value="' . $id . '" />
            		<button class="button" name="zapisz_enazwa" type="submit"><img src="./image/save_ico.png">Zapisz</button>
           		</div>               
                </div></form>';
        }
    }

}

?>