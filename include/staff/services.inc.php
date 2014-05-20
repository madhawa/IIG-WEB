<ul id="services_nav">
    <!--
    <li><a href="services.php?page=iig">IIG</a></li>
    <li><a href="services.php?page=itc">ITC</a></li>
    -->
    <li><a href="services.php?page=add_service">Add Service</a></li>
</ul>


<div>
    <?php if ($errors['err']) { ?>
        <p align="center" id="errormessage"><?php   echo   $errors['err'] ?></p>
    <?php } ?>

    <?php if ($msg) { ?>
        <p align="center" id="infomessage"><?php   echo   $msg ?></p>
    <?php } ?>

    <?php if ($warn) { ?>
        <p id="warnmessage"><?php   echo   $warn ?></p>
    <?php } ?>
</div>


<div id="services_content">
    <?php
    if ($tpl) {
        include TEMPLATE_DIR . $tpl;
    }
    ?>
</div>