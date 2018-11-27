<?php defined('_JEXEC') or die; 
$name           = $displayData['name'];              
$username       = $displayData['username'];         
$password_clear = $displayData['password_clear'];    
$registerDate   = $displayData['registerDate'];      


?>
<div>
    <div class="haedRegComplite"> Параметры регистрации для <?= $name ?> </div>
    <div>
        <div class=""><?= JText::_('Параметры для входа на сайт') ?></div>
        <div class="">Логин:<span><?= $username ?></span></div>
        <div class="">Пароль:<span><?= $password_clear ?></span></div>
    </div>
    <div class="registerDate">Дата регистрации: <span><?=$registerDate ?></span></div>
</div>
