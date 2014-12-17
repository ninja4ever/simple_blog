<?php
/**
 * Funkcja wywołująca odpowiednią akcję w zależności od parametrów,
 * funkcja ta działa w panelu administracyjnym.
 */
function main_admin_functions() {


    $user = new user_functions;

    $menu = new leftmenu;

    $article = new article;

    if (isset($_GET['f'])) {
        $funkcja = $_GET['f'];


        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }

        if (isset($_GET['r'])) {
            $nrranga = $_GET['r'];
        }

        if (isset($_GET['idr'])) {
            $idr = $_GET['idr'];
        }

        if (isset($_GET['nazwa'])) {
            $nazwa = $_GET['nazwa'];
        }

        if (isset($_GET['status'])) {
            $status = $_GET['status'];
        }

        if (isset($_GET['step'])) {
            $step = $_GET['step'];
        }

        if (isset($_GET['type'])) {
            $type = $_GET['type'];
        } else
            $type = 'null';

        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = '0';
        }

        switch ($funkcja) {
            case 'view':

                echo '<div id="content3"><div id="leftcolumn">';
                echo '<nav class="cssmenu_min">
				    <ul>	        
				        <li> <a href="acces_admin.php?f=adduser"><img src="./image/add_ico.png" alt="dodaj">Dodaj użytkownika</a> </li>			              
				    </ul>
				</nav>';
                echo '</div>';

                echo '<div id="content4">';
                $user->users_view();
                echo '</div></div>';
                break;
            case 'deluser':
                echo '<div id="content3">';
                $user->userdel($id);
                echo '</div>';
                break;
            case 'profile':
                echo '<div id="content3">';
                $user->profile($_SESSION['user_data']['iduser'], 'acces_admin.php');
                echo '</div>';
                break;
            case 'profile_save':
                echo '<div id="content3">';
                $user->profilesave($_SESSION['user_data']['iduser'], 'acces_admin.php');
                echo '</div>';
                break;
            case 'adduser':
                echo '<div id="content3">';
                $user->add_user();
                echo '</div>';
                break;
            case 'user_save':
                echo '<div id="content3">';
                $user->user_save();
                echo '</div>';
                break;
            case 'userstatus':
                echo '<div id="content3">';
                $user->update_ranga($id, $nrranga);
                echo '</div>';
                break;


            case 'kategorie':

                echo '<div id="content3"><div id="leftcolumn">';
                echo '<nav class="cssmenu_min">
				 <ul> 
				        <li> <a href="acces_admin.php?f=addkategory"><img src="./image/add_ico.png" alt="dodaj">dodaj sekcję</a> </li>          
				    </ul>
				</nav>';
                echo '</div>';
                echo '<div class="right">';
                $menu->view_categ();
                echo '</div></div>';
                break;
            case 'delpkat':
                echo '
        		<div id="content3">';
                $menu->del_pkateg($id);
                echo '</div>';
                break;
            case 'delkat':
                echo '<div id="content3">';
                $menu->del_kateg($id);
                echo '</div>';
                break;
            case 'addkategory':
                echo '<div id="content3">';
                $menu->add_kateg();
                echo '</div>';
                break;
            case 'addpkat':
                echo '<div id="content3">';
                $menu->add_pkateg($idr);
                echo '</div>';
                break;
            case 'katedit':
                echo '<div id="content3">';
                $menu->kateg_edit($id);
                echo '</div>';
                break;
            case 'katsave':
                echo '<div id="content3">';
                $menu->katsave();
                echo '</div>';
                break;
          
            case 'articles':

                echo '<div id="content3"><div id="leftcolumn">';
                echo '<nav class="cssmenu_min">
				 <ul>
				        
				        <li> <a href="acces_admin.php?f=articles"><img src="./image/article_ico.png" alt="dodaj">lista artykulów</a> </li>
				        <li> <a href="acces_admin.php?f=addarticle&&step=1"><img src="./image/add_ico.png" alt="dodaj">dodaj artykuł</a> </li>
				              
				    </ul>

				</nav>';
                echo '</div>';
                echo '<div id="content4">';
                $article->artykuly_view($page, $_SESSION['user_data']['user_status'], $_SESSION['user_data']['iduser']);
                echo '</div>
        		</div>';
                break;
            case 'addarticle':


                echo '<div id="content3">';
                $article->add_article($step);
                echo '</div>';
                break;

            case 'savearticle':
                echo '
        				<div id="content3">';
                $article->save_article();
                echo '</div>';
                break;
            case 'articledel':
                echo '
        				<div id="content3">';
                $article->del_article($id);
                echo '</div>';
                break;
            case 'articleedit':

                echo '<div id="content3">
        	<div id="leftcolumn">';
                echo '<nav class="cssmenu_min">
				 <ul>
				        
				        <li> <a href="acces_admin.php?f=articles"><img src="./image/add_ico.png" alt="dodaj">lista artykulów</a> </li>
				        <li> <a href="acces_admin.php?f=addarticle&&step=1"><img src="./image/add_ico.png" alt="dodaj">dodaj artykuł</a> </li>
				              
				    </ul>

				</nav>';
                echo '</div>';
                echo ' 
        				<div id="content2">';
                $article->edit_article($id);
                echo '</div>
        		</div>';
                break;
            case 'articlepub':
                echo '
        				<div id="content3">';
                $article->publikacja_article($id, $status);
                echo '</div>';
                break;

            case 'searchpage':
                
                search();
               
                break;
        }
    }
}
/**
 * Funkcja pokazująca menu główne w panelu administracyjnym
 * z szybkim dostępem do danych modułów.
 */
function menu_admin() {

    echo '
    	<div class="out">
    <div class="in">
        <div class="image"><img src="./image/user_img_ico.png" alt=""/></div>
        <div class="links">
            <ul class="lista">
                <li><a href="acces_admin.php?f=view">Pokaż użytkowników</a></li>
                <li><a href="acces_admin.php?f=adduser">Dodaj użytkownika</a></li>
                
            </ul>
        </div>        
    </div>
    
     <div class="in">
        <div class="image"><img src="./image/menu_img_ico.png" alt=""/></div>
        <div class="links">
            <ul class="lista">
                 <li><a href="acces_admin.php?f=kategorie">Zarządzanie menu</a></li>
                <li><a href="acces_admin.php?f=addkategory">Dodaj sekcję</a></li>
            </ul>
        </div>
    </div>
     
     <div class="in">
        <div class="image"><img src="./image/news_img_ico.png" alt=""/></div>
        <div class="links">
            <ul class="lista">
                <li><a href="acces_admin.php?f=articles">Artykuły</a></li>
                <li><a href="acces_admin.php?f=addarticle&&step=1">Dodaj artykuł</a></li>
            </ul>
        </div>
    </div>
</div>
	';
}
?>




