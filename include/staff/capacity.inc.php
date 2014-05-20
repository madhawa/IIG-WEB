<ul class="left_nav">
    <li><a href="?do=view_total_capacity">View Total Cacacity</a></li>
    <li class="has-sub"><a href="#">Inhouse</a>
        <ul>
            <li><a href="capacity.php?page=cap_add">Capacity add</a></li>
            <li><a href="capacity.php?page=view_active">Active</a></li>
            <li><a href="capacity.php?page=view_discontinued">Discontinued</a></li>
        </ul>
    </li>
    <li><a href="capacity.php?do=view_alloc_customer">Allocated to customer</a></li>
    <li><a href="capacity.php?do=view_available_capacity">Available capacity</a></li>
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