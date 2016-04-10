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
 * consumirlas, en este caso cuando entre una petición a http://localhost/back-end/imagenes/bd/1, iremos a la BD a
 * solicitar la imagen y la respuesta que le daremos al usuario será PHP creando la imagen basado en el registro.
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
            // Preparamos la respuesta
            $nuevaRespuesta = $response->withAddedHeader("Content-type", $resultado["data"]["tipo"]);
            // Escribimos en ella la imagen
            return $nuevaRespuesta->write($resultado["data"]["imagen"]);
        } else {
            // Si no encontramos la imagen en la BD
            $nuevaRespuesta = $response->withStatus(404);
            return $nuevaRespuesta;
        }
    }
);

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
