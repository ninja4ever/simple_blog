<?php
/**
 * Forma z wyszukiwarką dla panelu administracji
 */
function search() {

    echo '<div style="padding-top:20px; text-align: center; background:#ffffff;overflow:auto;" >
<form method="post" action="core/search_functions.php">
    <input type="text" name="search" id="search_box"  autofocus/>
    <button type="submit" id="button" class="search_button" > Szukaj</button>
    <input type="hidden"  name="type" id="searchtype" value="article"> 
	
</form>
';

    echo '<br><div id="searchresults" style="text-align:center;"><label>Wyniki wyszukiwania:</label><br></div>
<br><div id="results"></div>';
}
/**
 * Forma z wyszukiwarką dla strony głównej
 */
function search_home() {

    echo '<div class="right"><div class="out_cont"><div style="padding-top:20px; text-align: center;">
<form method="post" action="core/search_functions.php">

    <input type="text" name="search" id="search_box"  autofocus/>
    <button type="submit" class="search_button" > Szukaj</button>
    <input type="hidden" name="type" id="searchtype" value="articles"> 
	
</form>
</div> ';

    echo '<br><div id="searchresults" style="text-align:center;"><label>Wyniki wyszukiwania:</label><br></div></div>
<div id="results"></div>';
}

?>