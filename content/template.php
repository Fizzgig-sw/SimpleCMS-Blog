<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo site_name().' | '.page_title(); ?></title>
    <link href="<?php echo site_url(); ?>/style.css?v=<?php echo time();?>" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="wrap">

    <header>
        <h1><?php echo site_name(); ?></h1>
        <?php nav_menu(); ?>
    </header>

    <?php get_article(); ?>

    <footer>
        <?php if(config('footer')){echo '<small>&copy;'.date('Y').' '.site_credit().'<br>'.version.'</small>';} ?>
    </footer>

</div>
</body>
</html>