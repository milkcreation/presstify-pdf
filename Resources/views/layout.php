<?php
/**
 * PDF - Gabarit de page.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var App\Viewer $this
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <?php foreach ($this->get('stylesheet', []) as $path) : ?>
        <link type="text/css" rel="stylesheet" href="<?php echo $path; ?>">
    <?php endforeach; ?>
</head>
<body>
<?php echo $this->section('content'); ?>
</body>
</html>