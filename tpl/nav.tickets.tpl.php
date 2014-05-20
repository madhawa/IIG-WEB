<ul id="tickets_nav">
    <li><a href="view.php?status=open">View Open Tickets</a></li>
    <li><a href="view.php?status=closed">View Closed Tickets</a></li>
    <li><a href="view.php?status=sla">View SLA Claim Tickets</a></li>
    <li><a href="view.php?status=no_sla">View SLA Non Claim Tickets</a></li>
</ul>

<script type="text/javascript">

    $('ul#tickets_nav').css({
        'position': 'fixed',
        'list-style-type': 'none',
        'margin-top': '50px'
    });

    $('ul#tickets_nav li').css({
        'width': '200px',
        'font-size': '1.1em',
        'text-align': 'center',
        'padding': '10px',
        'margin-top': '10px',
        'background-color': '#FDF3E7'
    });

</script>