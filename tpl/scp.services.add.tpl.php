<h2><?php   echo   $title ?></title></h2>
<form action="" method="post">
<input type="hidden" name="action" value="save_service">
<table id="add_service">
    <tr>
        <td>
            <label for="service_name">Service Name</label>
            <br>
            <input type="text" name="service_name" value="" required>
        </td>
    </tr>
    <tr>
        <td>
            <label for="service_details">Service Details</label>
            <br>
            <textarea name="service_details" required></textarea>
        </td>
    </tr>
    <tr>
        <td><button class="save" type="submit">Save</td>
    </tr>
</table>
</form>

<script type="text/javascript">
    $('table#add_service input, table#add_service textarea').css('width', '300px');
</script>