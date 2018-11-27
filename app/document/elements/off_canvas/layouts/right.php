<?php   
// use Joomla\Registry\Registry;
defined('_JEXEC') or die;
$html = $displayData['html'] ; 

?>
<div id="mySidenavRight" class="sidenav elSrv-offCan" >
    <div id="offCanHead">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <h3>Получить скидку</h3>
    </div>
    <div id="wrMes"></div>
    <!-- CONTENT -->
    <?= $html ?>
</div>
<div id="main" class="elSrv-offCan" onclick="openNav()">
    <span class="off-c-right">%</span>
</div> 
<!--<script src="<?= JURI::base() ?>/libraries/xzlib/app/document/elements/off_canvas/assets/js/c-offcanvas.js?<?= $mediaVersion ?>" id="scriptid" defer ></script>-->
<script>
    loadCSS('/assets/css/c-offcanvas.css');  
    
    
</script>    