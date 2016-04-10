<?php

/**
 * index.php
 * HTML desplegado al usuario.
 */

// Lista de archivos de JavaScript a cargar en la página.
$scripts = [
    'lib/angular.min.js',
    'lib/angular-route.min.js',
    'lib/ng-file-upload-all.min.js',
    'app.js',
    'controllers/inicio.js',
    'services/imagenes.js'
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Proyecto Uno: Ejemplo Imagenes</title>
    <link rel="stylesheet" href="front-end/css/bootstrap.min.css">
</head>
<body>

<div class="col-md-8 col-md-offset-2">
    <div ng-app="noticiasApp" ng-view></div>
</div>

<?php
// Carga de los archivos de JavaScript
foreach ($scripts as $script) {
    echo "<script src='front-end/js/$script'></script> \n";
}
?>
</body>
</html>
