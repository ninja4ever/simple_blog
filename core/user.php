<?php
/**
 * Klasa odpowiedzialna za logowanie i wylogowywanie użytkownika
 */
class User {

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
 * Funkcja odpowiedzialna za logowanie użytkownika
 * @param string $username
 * @param string $password
 */
    public function login($username, $password) {

        $username = htmlspecialchars($username);
        $username = trim($username);
        $password = htmlspecialchars($password);
        $password = sha1(trim($password));
        $stmt = $this->connection->prepare("SELECT * FROM user WHERE username=:user AND password=:pass");
        $stmt->bindValue(':user', $username, PDO::PARAM_STR);
        $stmt->bindValue(':pass', $password, PDO::PARAM_STR);

        $stmt->execute();

        $data = $stmt->fetchAll(); 

        $count = $stmt->rowCount();
        if($count>1){
            $_SESSION['info'] = 'Wystąpił błąd.';
        }
        elseif ($count===1) {
            session_start();
            $user_data = array();
          
            $user_data['login'] = $username;

            $count = count($data); 
            foreach ($data as $row) { 
              
                $user_data['iduser'] = $row['user_id'];
                $user_data['nazwa_wys'] = $row['visible_name'];
                $user_data['user_status'] = $row['rank'];
                
            }
            $_SESSION['user_data'] = $user_data;
            header('Location: home.php');
        } else {

            $_SESSION['info'] = 'Logowanie nieudane.';
        }
    }
/**
 * Funkcja odpowiedzialna za wylogowanie użytkownika
 */
    public function logout() {

        session_start();
        if (isset($_SESSION['user_data'])) {
            unset($_SESSION['user_data']);
        }
        session_destroy();

        echo '<!DOCTYPE html >
        <head>

    <meta name="Description" content="Information architecture, Web Design, Web Standards." />
    <meta name="Keywords" content="your, keywords" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="">
    <meta name="Robots" content="index,follow" />
    <meta http-equiv="refresh" content="2; url=index.php">

    <title>Blog Site- Wylogowanie</title>
    
    
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="css/ind_bgr.css" rel="stylesheet">

    </head>

    <body>

    <div class="wrapper">
        <header class="header"><h2>Blog Site</h2></header>

        <form class="form-signin" >
                <div class="form-head">
           
            <label> Wylogowanie pomyślne. Powrót na stronę logowania.</label>
            </div>
           
        </form>
    </div>

    </body>
    </html>';
    }

}

?>