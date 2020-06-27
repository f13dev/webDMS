<?php 
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- jQuery -->
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <!-- Light Case --> 
  <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>inc/js/lightcase/lightcase.css">
  <script type="text/javascript" src="<?php echo SITE_URL; ?>inc/js/lightcase/lightcase.js"></script>
  <script type="text/javascript">
    jQuery(document).ready(function($) {
      $('a[data-rel^=lightcase]').lightcase({
        showTitle: false,
        showCaption: false,
        fullscreenModeForMobile: true,
        useKeys: true,
        shrinkFactor: 0.85,
        maxWidth: '100%',
        maxHeight: '100%',
        iframe: {
          width: function () { return $(window).outerWidth() },
          height: function () { return $(window).outerHeight() },
        },
        image: {
          maxWidth: '800',
          maxHeight: '500',
          width: '800',
          height: '500'
        }
      });
    });
  </script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
  <!-- Main stylesheet -->
  <link rel="stylesheet" href="<?php echo SITE_URL; ?>inc/theme/style.css">
  <title>webDMS</title>
</head>
<body>
