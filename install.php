<!DOCTYPE html >
<head>

    <meta name="Description" content="Information architecture, Web Design, Web Standards." />
    <meta name="Keywords" content="your, keywords" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="">
    <meta name="Robots" content="index,follow" />

    <title>Blog Site</title>

    <link href="css/ind_bgr.css" rel="stylesheet">

</head>

<body>
    <?php
    require('tool_functions.php');
    if (file_exists('dbconfig.php')) {
        require('dbconfig.php');
    }
    $install = new install;

    if (isset($_GET['step'])) {
        $step = $_GET['step'];
    } else
    {$step = '0';}

    switch ($step) {
        case '0':
            echo '<div class="wrapper">

			<header class="header">
			<h2>Blog Site - install</h2>
	
			</header><!-- .header-->';
            echo '<div class="middle">
				<div class="container">
				<p class="info">Kreator instalacji aplikacji blogowej.</p><br>
				<a href="install.php?step=1" class="button link_button">Start</a>

				</div>
			</div>';
            break;

        case '1':

            echo '<div class="wrapper">

			<header class="header">

			<h2>Blog Site - install</h2>
			</header><!-- .header-->
			<div class="middle">


			<form class="form-signin" method="post" action="install.php?step=2">
            <div class="form-head">
            <label>Krok 1/3, dane połączenia z bazą danych.</label><br><br>
            <label>host bazy danych (domyślnie localhost):</label>
            <input type="text" name="host" class="input" placeholder="host bazy danych" value="localhost">
            <label>port połączenia z bazą (domyślnie 3306):</label>
            <input type="number" min="1" max="65535" name="port" class="input" placeholder="port" value="3306">
            <label>nazwa użytkownika bazy danych:</label>
            <input type="text" name="login" class="input" placeholder="login" required>
            <label>hasło:</label>
            <input type="password" name="password" class="input" placeholder="hasło" >
            
			</div>
            <div class="footer">
            <input type="hidden" name="wyslany" value="1" /><br>
            <button class="button normal" name="submit" type="submit">Dalej</button>
				</div>
			</form>

			</div>';

            break;

        case '2':

            $install->dbconfig_create();
            $install->create_tables();

            break;

        case '3':

            echo '<div class="wrapper">

			<header class="header">
				<h2>Blog Site - install</h2>
				
			</header><!-- .header-->
			<div class="middle">
			<div class="container">
				

				';
            if (file_exists('dbconfig.php')) {
                echo '<p class="info" >Zapis danych pomyślny</p>';
                echo '<a class="button link_button" href="install.php?step=4">Kolejny krok</a>';
            } else {
                echo '<p class="info" >wystąpił błąd podczas zapisu danych</p>';
                echo '<a class="button link_button" href="install.php">ponowne rozpoczęcie</a>';
            }

            echo '</div></div>';
            break;
        case '4':

            echo '<div class="wrapper">

			<header class="header">
				<h2>Blog Site - install</h2>
	
			</header><!-- .header-->
			<div class="middle">



			<form class="form-signin" method="post" action="install.php?step=5">
            <div class="form-head">
            <label>Krok 2/3, dane administratora</label><br><br>   
            <label>nazwa użytkownika:</label>
            <input type="text" name="login" class="input username" placeholder="login" required>
            <label>hasło:</label>
            <input type="password" name="password" class="input password" placeholder="hasło" required>
            <label>powtórz hasło:</label>
            <input type="password" name="password2" class="input password" placeholder="hasło" required>
            <label>nazwa użytkownika (wyświetlana):</label>
            <input type="text" name="nazwa" class="input username" placeholder="nazwa użytkownika" required>
			<label>email (opcionalnie):</label>
            <input type="email" name="email" class="input email" placeholder="email" >
            </div>
            <div class="footer">
            <input type="hidden" name="wyslany" value="1" />
            <button class="button normal" name="submit" type="submit">Dalej</button>
            </div>
        	</form>

			</div>';

            break;

        case '5':

            $install->createuser();

            break;

        case '6':

            echo '<div class="wrapper">

			<header class="header">
				<h2>Blog Site - install</h2>
	
			</header><!-- .header-->
			<div class="middle">



			<form class="form-signin" method="post" action="install.php?step=7">
            <div class="form-head">
            <label>Krok 3/3, finalizowanie instalacji</label><br><br>     
           	<label >
           	<p style="text-align:center;margin-bottom:20px;">
           	Po kliknięciu przycisku "Zakończ" nastąpi przeniesienie na stronę logowania i usunięcie pliku instalacji!</p>
           	</label>
            </div>
            <div class="footer">
            <input type="hidden" name="wyslany" value="1" />
            <button class="button normal" name="submit" type="submit">Zakończ</button>
            </div>
        	</form>
			</div>';

            break;

        case '7':

            if (file_exists('install.php')) {

                try {
                    //unlink('install.php');
                    error('index.php', 1, 'Usunięto plik instalacji, teraz nastąpi przeniesienie na stronę logowania.');
                } catch (Excetion $e) {
                    error('index.php', 2, $e->getMessage() . ', teraz nastąpi przeniesienie na stronę logowania');
                }
            } else {}
                break;
    }
    ?>

</body>
</html>


    <?php
/**
 * Klasa ze zbiorem funkcji służąca do instalacji bazy danych oraz utworzeniu administratora
 */
    class install {

        private $connection;

        public function __construct(PDO $connection = null) {
            if (file_exists('dbconfig.php')) {
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
        }

        public function dbconfig_create() {
            if ($_POST) {

                if (empty($_POST['host'])) {
                    $host = 'localhost';
                } else
                    $host = $_POST['host'];
                if (!empty($_POST['port'])) {
                    $port = $_POST['port'];
                }
                if (empty($_POST['login'])) {
                    $login = 'root';
                } else
                    $login = htmlspecialchars($_POST['login']);
                if (empty($_POST['password'])) {
                    $pass = '';
                } else
                    $pass = htmlspecialchars($_POST['password']);
                try {
                    $dbh = new PDO("mysql:host=$host", $login, $pass);
                    $dbh->exec("CREATE SCHEMA IF NOT EXISTS `blog_base` "
                            . "DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci;")
                            or die(print_r($dbh->errorInfo(), true));
                } catch (PDOException $e) {
                    die("DB ERROR: " . $e->getMessage());
                }
                if (file_exists('dbconfig.php')) {
                    unlink('dbconfig.php');
                }
                $mysql_host = $host;
                $port = $port;
                $username = $login;
                $password = $pass;
                $database = 'blog_base';
                $def1 = 'define( "DB_SERVER", "mysql:host=' . $mysql_host . ';dbname=' . $database . ';port=' . $port . ';" );';
                $def2 = 'define( "DB_USERNAME", "'.$username.'" );';
                $def3 = 'define( "DB_PASSWORD", "'.$password.'");';
                $my_file = 'dbconfig.php';
                $handle = fopen($my_file, 'w')
                        or die(error("install.php?step=0", 2, "Wystąpił problem z tworzeniem pliku dbconfig.php, powrót do strony głównej instalacji."));
                $data = "<?php 
					$def1
					$def2
					$def3\n?>";
                fwrite($handle, $data);
                fclose($handle);
            }
        }
/**
 * Funkcja służąca do stworzenia bazy danych
 */
        public function create_tables() {

            try {

                $sql_main = "
		-- MySQL Script generated by MySQL Workbench
		-- 11/26/14 15:12:53
		-- Model: New Model    Version: 1.0
		SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
		SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
		SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

		-- -----------------------------------------------------
		-- Schema blog_base
		-- -----------------------------------------------------
		DROP SCHEMA IF EXISTS `blog_base` ;
		CREATE SCHEMA IF NOT EXISTS `blog_base` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
		USE `blog_base` ;

		-- -----------------------------------------------------
		-- Table `blog_base`.`user`
		-- -----------------------------------------------------
		DROP TABLE IF EXISTS `blog_base`.`user` ;

		CREATE TABLE IF NOT EXISTS `blog_base`.`user` (
		  `user_id` INT NOT NULL AUTO_INCREMENT,
		  `username` VARCHAR(16) NOT NULL,
		  `email` VARCHAR(255) NULL,
		  `password` VARCHAR(255) NOT NULL,
		  `visible_name` VARCHAR(45) NULL,
		  `rank` VARCHAR(60) NOT NULL,
		  PRIMARY KEY (`user_id`));


		-- -----------------------------------------------------
		-- Table `blog_base`.`category`
		-- -----------------------------------------------------
		DROP TABLE IF EXISTS `blog_base`.`category` ;

		CREATE TABLE IF NOT EXISTS `blog_base`.`category` (
		  `category_id` INT NOT NULL AUTO_INCREMENT,
		  `name` VARCHAR(255) NOT NULL,
		  `parent_id` INT NULL,
		  PRIMARY KEY (`category_id`));


		-- -----------------------------------------------------
		-- Table `blog_base`.`posts`
		-- -----------------------------------------------------
		DROP TABLE IF EXISTS `blog_base`.`posts` ;

		CREATE TABLE IF NOT EXISTS `blog_base`.`posts` (
		  `idposts` INT NOT NULL AUTO_INCREMENT,
		  `user_id` INT NOT NULL,
		  `category_id` INT NOT NULL,
		  `category_parent_id` INT NOT NULL,
		  `topic` VARCHAR(255) NOT NULL,
		  `text` TEXT NOT NULL,
		  `create_date` DATETIME NOT NULL,
		  `update_date` DATETIME NULL,
		  `image_link` VARCHAR(255) NULL,
		  `visible` VARCHAR(60) NOT NULL,
		  PRIMARY KEY (`idposts`),
		  INDEX `fk_posts_user_idx` (`user_id` ASC),
		  INDEX `fk_posts_category1_idx` (`category_id` ASC))
		ENGINE = InnoDB;


		SET SQL_MODE=@OLD_SQL_MODE;
		SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
		SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;";
				$stmt = $this->connection->prepare($sql_main);
                $stmt->execute();
                $sql = "INSERT INTO `category` (`category_id`,`name`,`parent_id`) VALUES (1,'Home',0);";
                $stmt = $this->connection->prepare($sql);
                $stmt->execute();
                error('install.php?step=3', 0, '');
            } catch (PDOException $e) {
                die("DB ERROR: " . $e->getMessage());
            }
        }
/**
 * Funkcja towrząca nowego użytkownika - administratora
 */
        public function createuser() {
            if ($_POST) {

                if (!empty($_POST['login']) and ! empty($_POST['password']) and ! empty($_POST['password2']) and ! empty($_POST['nazwa'])) {
                    $password = htmlspecialchars($_POST['password']);
                    $password2 = htmlspecialchars($_POST['password2']);
                    $password = sha1(trim($password));
                    $password2 = sha1(trim($password2));
                    $nazwa = htmlspecialchars($_POST['nazwa']);
                    $nazwa = trim($nazwa);
                    $email = $_POST['email'];

                    if ($password == $password2) {

                        $sql = 'INSERT INTO USER VALUES (:id, :login,:email,:haslo, :nazwa, :ranga)';
                        $stmt = $this->connection->prepare($sql);
                        $stmt->bindValue(':id', '', PDO::PARAM_STR);
                        $stmt->bindValue(':login', $_POST['login'], PDO::PARAM_STR);
                        $stmt->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                        $stmt->bindValue(':haslo', $password, PDO::PARAM_STR);
                        $stmt->bindValue(':nazwa', $nazwa, PDO::PARAM_STR);
                        $stmt->bindValue(':ranga', 'user_mainadmin', PDO::PARAM_STR);
                        $stmt->execute();

                        header('Location: install.php?step=6');
                    } else {

                        error("install.php?step=4", 2, "Wpisane hasła nie są identyczne.");
                    }
                } else {

                    error("install.php?step=4", 2, "Nie wpisano wymaganych danych.");
                }
            } else{
            die();}
        }
    }
?>
