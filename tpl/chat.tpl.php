<div id="chatContainer">
    <div id="chatTopBar" class="rounded"></div>
    <div id="chatLineHolder"></div>
    
    <div id="chatUsers" class="rounded"></div>
    <div id="chatBottomBar" class="rounded">
    	<div class="tip"></div>
        
        <form id="loginForm" method="post" action="">
            <input id="name" name="name" value="<?php   echo   $thisuser->getName(); ?>" class="rounded" maxlength="16" readonly />
            <input id="email" name="email" value="<?php   echo   $thisuser->getEmail(); ?>" class="rounded" readonly />
            <input type="hidden" name="id" value="<?php   echo   $thisuser->getId(); ?>">
            <input type="submit" class="blueButton" value="Start Chat" />
        </form>
        
        <form id="submitForm" method="post" action="">
            <input id="chatText" name="chatText" class="rounded" maxlength="255" />
            <input type="submit" class="blueButton" value="Submit" />
        </form>
        
    </div>
    
</div>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script src="./chat/js/jScrollPane/jquery.mousewheel.js"></script>
<script src="./chat/js/jScrollPane/jScrollPane.min.js"></script>
<script src="./chat/js/script.js"></script>