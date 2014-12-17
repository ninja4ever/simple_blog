<?php
/**
 * Funkcja zbiorcza do wyświetlania różnych informacji
 * @param string $back_site
 * @param int $back_time
 * @param string $message
 */
function error($back_site, $back_time, $message){


echo '
        <meta http-equiv="refresh" content="'.$back_time.'; url='.$back_site.'">
   
      <div class="out_cont">

       

            <p class="information" ><label>'.$message.'</label></p>

       </div>';

}
/**
 * autoloader do klass
 * @param string $classname
 * @throws Exception
 */
function __autoload($classname){
    try {
        if (class_exists($classname, false) || interface_exists($classname, false) || is_readable($classname)){
        require_once($classname);
           
        }else{
             throw new Exception('Class cannot be found ( ' . $classname . ' )');
        }
    } catch (Exception $e){
        echo $e->getMessage()."\n"; 
    }
}
?>