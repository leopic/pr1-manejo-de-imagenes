<?php

/**
 * index.php
 * Inicia la aplicación y sirve como enrutador para el back-end.
 */

require "bootstrap.php";

use App\Controllers\ImagenesController;
use Slim\Http\Response;

$app = new \Slim\App();

// Usado para subir imágenes al servidor
$app->post(
    "/imagenes/servidor",
    function ($request, $response) {
        /** @var Response $response */
        $controlador = new ImagenesController();
        $resultado = $controlador->servidor($request);
        return $response->withJson($resultado);
    }
);

// Usado para agregar imágenes a la BD
$app->post(
    "/imagenes/bd",
    function ($request, $response) {
        /** @var Response $response */
        $controlador = new ImagenesController();
        $resultado = $controlador->bd($request);
        return $response->withJson($resultado);
    }
);

/**
 * Debido a que las imágenes no viven el el sistema de archivos, debemos proveer alguna forma al front-end de
 * consumirlas. En este caso cuando entre una petición a http://localhost/back-end/imagenes/bd/1, iremos a la BD a
 * solicitar la imagen con el ID 1. La respuesta que le daremos al usuario será PHP creando la imagen basado en el
 * registro o en caso de no encontrar el registro, un error 404.
 */
$app->get(
    "/imagenes/bd/{id}",
    function ($request, $response) {
        /** @var Response $response */
        $controlador = new ImagenesController();
        $resultado = $controlador->bdDesplegarImagen($request);

        /**
         * En este caso no podemos responder únicamente con un JSON, ya tenemos que responder con el tipo de imagen
         * junto con la imagen en si.
         */
        if (array_key_exists("data", $resultado)) {
            // Debemos responder con el tipo de archivo correcto
            $nuevaRespuesta = $response->withAddedHeader("Content-type", $resultado["data"]["tipo"]);
            // Escribimos en la respuesta el contenido binario del archivo
            return $nuevaRespuesta->write($resultado["data"]["imagen"]);
        } else {
            // Si no encontramos la imagen en la BD
            $nuevaRespuesta = $response->withStatus(404);
            return $nuevaRespuesta;
        }
    }
);

// Sube imágenes a un servicio externo
$app->post(
    "/imagenes/servicio",
    function ($request, $response) {
        /** @var Response $response */
        $controlador = new ImagenesController();
        $resultado = $controlador->servicio($request);
        return $response->withJson($resultado);
    }
);

// Corremos la aplicación.
$app->run();
