<?php 
//to manage main site

?>
<br>
<h2 align="center">Manage 1asia-ahl.com</h2>
<br>

<div id="manage_parent_site_div">
    <form enctype="multipart/form-data" action="" method="post">
        <div id="parent_site_intro_text_div">
            <span>highlighted text</span>
            <br>
            <br>
            <textarea name="parent_site_intro_text" >
            </textarea>
            <br>
        </div>
        
        <div id="parent_site_img_up_div">
                <span class="title">upload images</span>
                <br>
                <br>
                <input type="file" name="parent_site_images[]" multiple>
        </div>
        
        <input type="submit" name="upload" value="send">
        
    </form>
</div>