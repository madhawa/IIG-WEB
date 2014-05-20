<?php
/**
 * header.order.tpl.php, header template for create order page
 * @author Minhaj <polarglow06@gmail.com>
 * @copyright (c) 2013, Minhaj
 * @package HelpDeskConnected
 */
?>
<div id = "order_full_form">
<div id = "create_order_head">
<form name = "order_form_head" action = "" method = ""post>
<div id = "order_form_head_data_fields">
Customer Relationship No:<input type = "text" value = "<?php ?>" name = "customer_rel_no">
Customer Name:<input type = "text" name = "customer_name" value = "<?php ?>">
Customer Email ID:<input type = "text" name = "customer_email" value = "<?php ?>">
Customer Type:
<select name = "customer_type">
<?php
?>
</select>
Service Type:
<select name="service_type">
    <?php ?>
</select>
Circuit Type:
<select name="circuit_type">
    <?php ?>
</select>
</div>

<div id="asiaahl_logo">

</div>

</div>