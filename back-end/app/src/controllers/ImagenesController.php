<?php

/**
 * ImagenesController.php
 */

namespace App\Controllers;

use App\Services\ImagenesService;
use Slim\Http\Request;

class ImagenesController {

    /**
     * ImagenesController constructor.
     */
    public function __construct() {
        $this->imagenesService = new ImagenesService();
    }

    /**
     * Sube una imagen al servidor y guarda en la BD la referencia al URL.
     *
     * @param Request $request
     * @return array
     */
    public function servidor($request) {
        $resultado = [];
        $listaDeArchivos = $request->getUploadedFiles();
        $datosDelFormulario = $request->getParsedBody();

        if (empty($listaDeArchivos)) {
            $resultado["error"] = true;
            $resultado["message"] = "No hay archivos para procesar";
        } else {
            $totalDeArchivos = count($listaDeArchivos);
            $archivosExitosos = 0;

            // PHP maneja los archivos subidos como un array, por lo que tenemos que iterar sobre él
            foreach ($listaDeArchivos as $archivo) {
                $titulo = $datosDelFormulario["tituloImagenServidor"];
                $resultadoArchivo = $this->imagenesService->guardarImagenEnServidor($archivo, $titulo);
                $exito = array_key_exists("meta", $resultadoArchivo) && $resultadoArchivo["meta"]["url"];

                if ($exito) {
                    $resultado["urls"][] = $resultadoArchivo["meta"]["url"];
                    $archivosExitosos++;
                }
            }

            if ($archivosExitosos == $totalDeArchivos) {
                $resultado["message"] = "Se cargaron exitosamente todos los archivos";
            }
        }

        return $resultado;
    }

    /**
     * Sube la imagen temporal al servidor, la persiste en la BD y luego elimina la imagen temporal.
     *
     * @param Request $request
     * @return array
     */
    public function bd($request)
    {
        $resultado = [];
        $listaDeArchivos = $request->getUploadedFiles();
        $datosDelFormulario = $request->getParsedBody();

        if (empty($listaDeArchivos)) {
            $resultado["error"] = true;
            $resultado["message"] = "No hay archivos para procesar";
        } else {
            $totalDeArchivos = count($listaDeArchivos);
            $archivosExitosos = 0;

            foreach ($listaDeArchivos as $archivo) {
                $titulo = $datosDelFormulario["tituloImagenBD"];
                $resultadoArchivo = $this->imagenesService->guardarImagenEnBD($archivo, $titulo);
                $exito = array_key_exists("meta", $resultadoArchivo) && $resultadoArchivo["meta"]["id"];

//                LoggingService::logVariable($resultadoArchivo);

                if ($exito) {
                    $resultado["ids"][] = $resultadoArchivo["meta"]["id"];
                    $archivosExitosos++;
                }
            }

            if ($archivosExitosos == $totalDeArchivos) {
                $resultado["message"] = "Se cargaron exitosamente todos los archivos";
            }
        }

        return $resultado;
    }

    /**
     * Permite acceder a imágenes como si vivieran en el sistema de archivos.
     *
     * @param Request $request
     * @return array
     */
    public function bdDesplegarImagen($request) {
        $id = $request->getAttribute("id", null);
        return $this->imagenesService->leerImagenBd($id);
    }

    /**
     * Sube imágenes a un servicio servicio.
     *
     * @param Request $request
     * @return array
     */
    public function servicio($request) {
        $resultado = [];
        $listaDeArchivos = $request->getUploadedFiles();
        $datosDelFormulario = $request->getParsedBody();

        if (empty($listaDeArchivos)) {
            $resultado["error"] = true;
            $resultado["message"] = "No hay archivos para procesar";
        } else {
            $totalDeArchivos = count($listaDeArchivos);
            $archivosExitosos = 0;

            foreach ($listaDeArchivos as $archivo) {
                $titulo = $datosDelFormulario["tituloImagenServicio"];
                $resultadoArchivo = $this->imagenesService->servicio($archivo, $titulo);
                $exito = array_key_exists("meta", $resultadoArchivo) && $resultadoArchivo["meta"]["id"];

//                LoggingService::logVariable($resultadoArchivo, __FILE__, __LINE__);

                if ($exito) {
                    $resultado["ids"][] = $resultadoArchivo["meta"]["id"];
                    $resultado["urls"][] = $resultadoArchivo["meta"]["url"];
                    $archivosExitosos++;
                }
            }

            if ($archivosExitosos == $totalDeArchivos) {
                $resultado["message"] = "Se cargaron exitosamente todos los archivos";
            }
        }

        return $resultado;
    }

}
