<?php
/**
 * Example de PDF.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var App\Viewer $this
 * @var string $charset
 * @var array $stylesheets
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php echo $charset; ?>"/>
    <?php foreach ($stylesheets as $path) : ?>
        <link type="text/css" rel="stylesheet" href="<?php echo $path; ?>">
    <?php endforeach; ?>
</head>
<body>
    <table>
        <tr>
            <td>
                <h1>Hello World !</h1>
            </td>
        </tr>
    </table>
</body>
</html>