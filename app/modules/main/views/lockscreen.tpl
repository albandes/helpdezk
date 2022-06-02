<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{$title|default:'Helpdezk | Open Source'}</title>

    <!-- Mainly scripts -->
    {head_item type="js" src="$path/includes/js/" files="$jquery_version"}
    {head_item type="css" src="$path/includes/bootstrap/css/" files="bootstrap.min.css"}
    {head_item type="js"  src="$path/includes/bootstrap/js/" files="bootstrap.min.js"}

    {head_item type="css" src="$path/css/font-awesome/css/" files="font-awesome.css"}
    {head_item type="css" src="$path/css/" files="animate.css"}


    {head_item type="css" src="$path/css/" files="$theme.css"}



</head>

<body class="gray-bg">

<div class="lock-word animated fadeInDown">
    <span class="first-word">LOCKED</span><span>SCREEN</span>
</div>
<div class="middle-box text-center lockscreen animated fadeInDown">
    <div>
        <div class="m-b-md">
            <img alt="image" class="img-circle circle-border" src="{$person_photo}" height="128" width="128">
        </div>
        <h3>{$person_login}</h3>
        <p>{$smarty.config.Lock_text}</p>

        <a href="{$login}" id="btnModalAlert" class="btn btn-primary block full-width" >

            <span class="fa fa-check"></span>  &nbsp;
            {$smarty.config.Lock_unlock}

        </a>


    </div>
</div>



</body>

</html>