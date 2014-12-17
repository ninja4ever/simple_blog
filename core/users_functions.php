<?php
/**
 * Klasa związana z częścią odpowiedzialną za zarządznie użytkownikami
 */
class user_functions {

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
 * Funkcja wyświetlająca użytkowników
 */
    public function users_view() {

        $stmt = $this->connection->prepare("SELECT * FROM user");

        $stmt->execute();

        $data = $stmt->fetchAll();
        $count = $stmt->rowCount();

        if ($count) {


            $count = count($data);
            echo '<table class="user_table">
	 <thead>
	 <tr>
	  <th>Nazwa użytkownika</th><th>Status</th><th>Uprawnienia</th><th>Opcja usuwania</th></tr></thead>
                    <tbody>';
            foreach ($data as $row) {
                echo '<tr>';
                if ($_SESSION['user_data']['iduser'] == $row['user_id']) {
                    echo '<td>' . $row['visible_name'] . '<span class="active"> Zalogowany</span></td>';
                } else
                { echo '<td>' . $row['visible_name'] . '</td>';}
                switch ($row['rank']) {
                    case 'user_mainadmin':
                        echo '<td><span style="color:red;padding-left:25px;padding-right:25px;">Administrator</span></td>';
                        break;
                    case 'user_admin':
                        echo '<td><span style="color:orange;padding-left:25px;padding-right:25px;">Redaktor</span></td>';
                        break;
                    case 'status_user':
                        echo '<td><span style="padding-left:25px;padding-right:25px;">Użytkownik</span></td>';

                        break;
                }
                if ($_SESSION['user_data']['iduser'] == $row['user_id']) {
                    echo '<td><img class="down_in"><img class="up_in"></td>';
                } else{
                    switch ($row['rank']) {
                        case 'user_mainadmin':

                            echo '<td><a class="down_cfg" href="acces_admin.php?f=userstatus&&id=' . $row['user_id'] . '&&r=1" >down</a>  <img class="up_in"></td>';
                            break;
                        case 'user_admin':

                            echo '<td><a class="down_cfg" href="acces_admin.php?f=userstatus&&id=' . $row['user_id'] . '&&r=0">down</a>  
        			<a class="up_cfg" href="acces_admin.php?f=userstatus&&id=' . $row['user_id'] . '&&r=2">up</a></td>';
                            break;
                        case 'status_user':

                            echo '<td><img class="down_in"> <a class="up_cfg" href="acces_admin.php?f=userstatus&&id=' . $row['user_id'] . '&&r=1">up</a></td>';
                            break;
                }}

                if ($_SESSION['user_data']['iduser'] == $row['user_id'] || $row['rank'] == "user_mainadmin") {
                    echo '<td> <img src="./image/delinactiv_ico.png" class="del_inactiv" alt="usuń"> </td>';
                } else
                { echo '<td><a class="del_cfg" href="acces_admin.php?f=deluser&&id=' . $row['user_id'] . '" title="Użytkownika: ' . $row['visible_name'] . '"> usuń </a></td>';}
                echo '</tr>';
            }
            echo '</table>';
        }
    }
/**
 * Funkcja usuwająca użytkowników
 * @param int $id
 */
    public function userdel($id) {


   
        $stmt = $this->connection->prepare("SELECT * FROM user WHERE user_id=:id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if (isset($a)) {
            $a = '';
        }
        $data = $stmt->fetchAll(); // fetching rows
        $count = $stmt->rowCount();

        if ($count) {

            $count = count($data); // getting count
            foreach ($data as $row) { // iterating over rows
                $a = $row['rank'];
            }

            if ($a != 'user_mainadmin') {

                $stmt = $this->connection->prepare("DELETE FROM user WHERE user_id=:id");

                $stmt->bindValue(':id', $id, PDO::PARAM_INT);

                $stmt->execute();

                error("acces_admin.php?f=view", 2, "Usunięto użytkownika.");
            } else {
                error("acces_admin.php?f=view", 2, "Usunięcie użytkownika niemożliwe z powodu rangi administratora.");
            }
        }
    }
/**
 * Funkcja pokazująca profil użytkownika
 * @param int $id
 * @param string $site
 */
    public function profile($id, $site) {


        $stmt = $this->connection->prepare("SELECT * FROM user WHERE user_id=:id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(); // fetching rows
        $count = $stmt->rowCount();

        if ($count) {
            echo '<form class="form-signin" method="post" action="' . $site . '?f=profilesave&&id=' . $id . '">
           <div class="head">';
            $count = count($data); // getting count
            foreach ($data as $row) {
                echo '<label>Użytkownik: </label>';
                switch ($row['rank']) {
                    case 'user_mainadmin':
                        echo '<span class="admin">' . $row['username'] . '</span>';
                        break;
                    case 'user_admin':
                        echo '<span class="mod">' . $row['username'] . '</span>';
                        break;
                    case 'status_user':
                        echo '<span class="user">' . $row['username'] . '</span>';
                        break;
                }

                if (strlen($row['email']) > '0') {
                    $email = $row['email'];
                } else
                    $email = 'nie podano emaila.';

                echo '<br>
            		<label>Nazwa wyświetlana: </label>	
            		<span class="user"> ' . $row['visible_name'] . ' </span><br>
                    <label>email: </label>
                    <span class="user"> ' . $email . '</span>
                    <hr style="margin-top:10px;">
                    <label>Zmiana emaila: </label>
                    <input type="email" name="email" class="inputnormal email" placeholder="email" >
                    <input type="hidden" name="wyslany" value="1" />
                    <button class="button" style="margin-left:150px;" name="zapisz_email" type="submit"><img src="./image/save_ico.png">Zapisz</button>	
            		<hr style="margin-top:10px;">
                	<label>Zmiana nazwy: </label>
                	<input type="text" name="nazwa" class="inputnormal username" placeholder="nazwa" >
                	<input type="hidden" name="wyslany" value="1" />
                	<button class="button" style="margin-left:150px;" name="zapisz_nazwa" type="submit"><img src="./image/save_ico.png">Zapisz</button>	
               		<hr style="margin-top:40px;">
                	<label>Zmiana hasła: </label>
                	<br>
                	<label >Nowe hasło: </label>
                	<input type="password" name="password" class="inputnormal password" style="margin-left:40px;" placeholder="hasło">
            		<br>
                	<label>Powtórz hasło: </label>
                	<input type="password" name="password2" class="inputnormal password"  placeholder="powtórz hasło">
    				<input type="hidden" name="wyslany" value="1" />
                	<button class="button" style="margin-left:150px;" name="zapisz_haslo" type="submit"><img src="./image/save_ico.png">Zapisz</button>
                                	
                </div>
                ';
            }
            echo '</form>';
        }
    }
/**
 * Funkcja zapisująca zmiany w profilu użytkownika
 * @param int $id
 * @param string $site
 */
    public function profilesave($id, $site) {


        if (isset($_POST['zapisz_haslo'])) {
            if (!empty($_POST['password']) && !empty($_POST['password2'])) {
                $password = htmlspecialchars($_POST['password']);
                $password2 = htmlspecialchars($_POST['password2']);
                $password = sha1(trim($password));
                $password2 = sha1(trim($password2));
                if ($password == $password2) {

                    $sql = 'update `user` set `password`=:pass where user_id = ' . $_SESSION['iduser'];
                    $stmt = $this->connection->prepare($sql);
                    $stmt->bindValue(':pass', $password, PDO::PARAM_STR);
                    $stmt->execute();

                    error($site . '?f=profile', 2, "Dane profilu zapisano.");
                } else {
                    error($site . '?f=profile', 2, "Wpisane hasła nie są identyczne");
                }
            } else {

                error($site . '?f=profile', 2, "Nie wpisano wymaganych danych");
            }
        } elseif (isset($_POST['zapisz_nazwa'])) {

            if (!empty($_POST['nazwa'])) {
                $nazwa = htmlspecialchars($_POST['nazwa']);
                $nazwa = trim($nazwa);
                $sql = 'update `user` set `visible_name`=:nazwa where user_id = ' . $_SESSION['user_data']['iduser'];
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':nazwa', $nazwa, PDO::PARAM_STR);
                $stmt->execute();
                $_SESSION['user_data']['nazwa_wys'] = $nazwa;

                error($site . '?f=profile', 2, "Dane profilu zapisano.");
            } else {

                error($site . '?f=profile', 2, "Nie wpisano wymaganych danych");
            }
        } elseif (isset($_POST['zapisz_email'])) {

            if (!empty($_POST['email'])) {
                $email = htmlspecialchars($_POST['email']);
                $email = trim($email);
                $sql = 'update `user` set `email`=:email where user_id = ' . $_SESSION['user_data']['iduser'];
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->execute();

                error($site . '?f=profile', 2, "Dane profilu zapisano.");
            } else {

                error($site . '?f=profile', 2, "Nie wpisano wymaganych danych");
            }
        }
    }
/**
 * Fukcja pozwalająca na dodanie nowego użytkownika
 */
    public function add_user() {

        echo '
            <form class="form-signin" method="post" action="acces_admin.php?f=user_save">
           	<div class="head">
                		<label>Login: </label>
                		<input type="text" name="login" class="inputnormal username" style="margin-left:120px;" placeholder="login" required>
    					<br>
                		<label >Hasło: </label>
                		<input type="password" name="password" class="inputnormal password" style="margin-left:120px;" placeholder="hasło" required>
                		<br>
                		<label >Powtórz hasło: </label>
                		<input type="password" name="password2" class="inputnormal password" style="margin-left:60px;" placeholder="powtórz hasło" required>
                		<br>
                		<label>Wyświetlana nazwa: </label>               		
                		<input type="text" name="nazwa" class="inputnormal username" style="margin-left:25px;" placeholder="nazwa" required>         
                	</div>
                <div class="footer">
                		<input type="hidden" name="wyslany" value="1" />
                		<button class="button" style="margin-left:200px;" name="zapisz_user" type="submit"><img src="./image/save_ico.png">Zapisz</button>
       </div>
       </form>';
    }
/**
 * Funkcja zapisująca nowego użytkownika
 */
    public function user_save() {


        if (isset($_POST['zapisz_user'])) {

            if (!empty($_POST['login']) and ! empty($_POST['password']) and ! empty($_POST['password2']) and ! empty($_POST['nazwa'])) {
                $password = htmlspecialchars($_POST['password']);
                $password2 = htmlspecialchars($_POST['password2']);
                $password = sha1(trim($password));
                $password2 = sha1(trim($password2));
                $nazwa = htmlspecialchars($_POST['nazwa']);
                $nazwa = trim($nazwa);

                if ($password == $password2) {

                    $sql = 'INSERT INTO USER (`user_id`, `username`, `password`, `visible_name`, `rank`) VALUES (:id, :login, :haslo, :nazwa, :ranga)';
                    $stmt = $this->connection->prepare($sql);
                    $stmt->bindValue(':id', '', PDO::PARAM_STR);
                    $stmt->bindValue(':login', $_POST['login'], PDO::PARAM_STR);
                    $stmt->bindValue(':haslo', $password, PDO::PARAM_STR);
                    $stmt->bindValue(':nazwa', $nazwa, PDO::PARAM_STR);
                    $stmt->bindValue(':ranga', 'status_user', PDO::PARAM_STR);
                    $stmt->execute();

                    error("acces_admin.php?f=view", 2, "Utworzono nowego użytkownika");
                } else {

                    error("acces_admin.php?f=adduser", 2, "Wpisane hasła nie są identyczne");
                }
            } else {

                error("acces_admin.php?f=adduser", 2, "Nie wpisano wymaganych danych");
            }
        }
    }
/**
 * Funkcja pozwalająca na nadanie nowych / obniżenie uprawnien danego użytkownika
 * @param int $id
 * @param string $nrranga
 */
    public function update_ranga($id, $nrranga) {


        if (isset($rank)) {
            $rank = "";
        }

        switch ($nrranga) {
            case '0':
                $rank = 'status_user';
                break;
            case '1':
                $rank = 'user_admin';
                break;
            case '2':
                $rank = 'user_mainadmin';
                break;
        }

        $sql = 'update `user` set `rank`=:status where user_id = ' . $id;
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':status', $rank, PDO::PARAM_STR);
        $stmt->execute();

        error("acces_admin.php?f=view", 2, "Zmieniono rangę użytkownika");
    }

}

?>