<?php

/**
 * ImagenesService.php
 */

namespace App\Services;

use Psr\Http\Message\UploadedFileInterface;
use PDO;

class ImagenesService {

    private $storage;
    private $validation;
    private $rutaParaArchivos = "user-uploads";

    /**
     * ImagenesService constructor.
     */
    public function __construct() {
        $this->storage = new PersistenciaService();
        $this->validation = new ValidacionesService();
    }

    /**
     * Método privado, únicamente esta clase puede llamar al método.
     * Encargado de tomar un archivo y retornar la ruta en donde fue subido.
     *
     * @param UploadedFileInterface $archivo
     * @return array
     */
    private function subir($archivo) {
        $resultado = [];

        // Verificamos que el archivo se subió al servidor sin errores
        if ($archivo->getError() === UPLOAD_ERR_OK) {
            $nombreFinal = $this->limpiarNombreArchivo($archivo->getClientFilename());

            try {
                $ruta = "$this->rutaParaArchivos/$nombreFinal";
                $archivo->moveTo($ruta);
                $resultado["message"] = "Se subió exitosamente el archivo";
                $resultado["meta"]["url"] = $this->getRutaArchivo($nombreFinal);
                $resultado["meta"]["titulo"] = "$this->rutaParaArchivos/$nombreFinal";
            } catch (\Exception $e) {
                // En caso de que exista un error al mover el archivo, enviamos el error de vuelta
                $resultado["error"] = true;
                $resultado["message"] = $e->getMessage();
            }
        }

        return $resultado;
    }

    /**
     * Normaliza el nombre del archivo, remueve espacios en blancos y caracteres extraños.
     * Como paso final le agrega la hora/fecha actual al inicio del nombre archivo para evitar colisiones.
     *
     * @param string $inicial
     * @return string
     */
    private function limpiarNombreArchivo($inicial) {
        // Basado en http://stackoverflow.com/questions/2021624/string-sanitizer-for-filename
        // Remove anything which isn't a word, whitespace, number
        // or any of the following caracters -_~,;[]().
        // If you don't need to handle multi-byte characters
        // you can use preg_replace rather than mb_ereg_replace
        // Thanks @Łukasz Rysiak!
        $final = strtolower($inicial);
        $final = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $final);
        // Remove any runs of periods (thanks falstro!)
        $final = mb_ereg_replace("([\.]{2,})", '', $final);
        $final = str_replace(" ", "-", $final);
        $timestamp = time();
        $final = "$timestamp-$final";

        return $final;
    }

    /**
     * Retorna la ruta básica para crear URLs públicos
     *
     * @return string
     */
    private function getRutaAplicacion() {
        return "back-end/$this->rutaParaArchivos";
    }

    /**
     * Retorna la ruta basada en la ruta pública de un archivo.
     *
     * @param string $nombreArchivo
     * @return string
     */
    private function getRutaArchivo($nombreArchivo) {
        $aplicacion = $this->getRutaAplicacion();
        return "$aplicacion/$nombreArchivo";
    }

    /**
     * Sube una imagen al servidor
     *
     * @param UploadedFileInterface $archivo
     * @param string $titulo
     *
     * @return array
     */
    public function guardarImagenEnServidor($archivo, $titulo) {
        $resultado = [];
        $subirImagen = $this->subir($archivo);

//        LoggingService::logVariable($subirImagen, __FILE__, __LINE__);

        $imagenSeSubio = array_key_exists("meta", $subirImagen) && $subirImagen["meta"]["url"];

        if ($imagenSeSubio) {
            $url = $subirImagen["meta"]["url"];
            if ($this->validation->isValidString($url)) {
                if ($this->validation->isValidString($titulo)) {

                    $query = "INSERT INTO imagenes_en_servidor (titulo, url_imagen) VALUES (:titulo, :url_imagen)";
                    $parametros = [
                        ":titulo" => $titulo,
                        ":url_imagen" => $url,
                    ];

                    $resultadoDelQuery = $this->storage->query($query, $parametros);
                    $seCreoLaImagen = array_key_exists("meta", $resultadoDelQuery) && $resultadoDelQuery["meta"]["count"] == 1;

//                    LoggingService::logVariable($resultadoDelQuery);

                    if ($seCreoLaImagen) {
                        $resultado["message"] = "Imagen insertada exitosamente";
                        $resultado["meta"]["url"] = $subirImagen["meta"]["url"];
                    } else {
                        $resultado["message"] = "Imposible crear entrada para la imagen";
                        $resultado["error"] = true;
                    }
                } else {
                    $resultado["message"] = "Titulo invalido para la imagen";
                    $resultado["error"] = true;
                }
            } else {
                $resultado["message"] = "URL invalido para la imagen";
                $resultado["error"] = true;
            }
        } else {
            $resultado["message"] = "Error subiendo la imagen";
            $resultado["error"] = true;
        }

        return $resultado;
    }

    /**
     * @param UploadedFileInterface $archivo
     * @param string $titulo
     *
     * @return array
     */
    public function guardarImagenEnBD($archivo, $titulo) {
        $resultado = [];
        $subirImagen = $this->subir($archivo);
        $imagenSeSubio = array_key_exists("meta", $subirImagen) && $subirImagen["meta"]["url"];

        if ($imagenSeSubio) {
            $url = $subirImagen["meta"]["titulo"];
            $pwd = getcwd();
            $path = "$pwd/$url";

            if ($this->validation->isValidString($path)) {
                if ($this->validation->isValidString($titulo)) {
                    $query = "INSERT INTO imagenes_en_bd (titulo, imagen_tipo, imagen) VALUES (:titulo, :imagen_tipo, :imagen)";
                    $imagenEnBase64 = base64_encode(file_get_contents($path));
                    $tipoDeImagen = mime_content_type($path);

                    /**
                     * Debido a que la imagen ocupa un tipo de parámetro distinto
                     * Ocupamos modificar nuestra sentencia de query para manejar estos casos
                     * Y no hacer el enlace de parámetros automáticamente.
                     */
                    $parametros = [
                        ":titulo" => [$titulo, PDO::PARAM_STR],
                        ":imagen_tipo" => [$tipoDeImagen, PDO::PARAM_STR],
                        ":imagen" => [$imagenEnBase64, PDO::PARAM_LOB]
                    ];

                    $resultadoDelQuery = $this->storage->query($query, $parametros, true);
                    $seCreoLaImagen = array_key_exists("meta", $resultadoDelQuery) && $resultadoDelQuery["meta"]["count"] == 1;

                    LoggingService::logVariable($resultadoDelQuery, __FILE__, __LINE__);

                    if ($seCreoLaImagen) {
                        $resultado["message"] = "Imagen insertada exitosamente";
                        $resultado["meta"]["id"] = $resultadoDelQuery["meta"]["id"];
                        // Después de insertar el archivo, lo debemos borrar del servidor
                        unlink($path);
                    } else {
                        $resultado["message"] = "Imposible crear entrada para la imagen";
                        $resultado["error"] = true;
                    }
                } else {
                    $resultado["message"] = "Titulo invalido";
                    $resultado["error"] = true;
                }
            } else {
                $resultado["message"] = "Imagen invalida";
                $resultado["error"] = true;
            }
        }

        return $resultado;
    }

    /**
     * Busca una imagen en la BD y retorna su tipo y representación binaria.
     *
     * @param int $id
     *
     * @return array
     */
    public function leerImagenBd($id) {
        $respuesta = [];

        if ($this->validation->isValidInt($id)) {
            // El query que vamos a ejecutar en la BD
            $query = "SELECT imagen, imagen_tipo FROM imagenes_en_bd WHERE id = :id";

            // Los parámetros de ese query
            $parametros = [":id" => intval($id)];

            // El resultado de de ejecutar la sentencia se almacena en la variable `resultado`
            $resultadoDelQuery = $this->storage->query($query, $parametros);

            // Si la setencia tiene por lo menos una fila, quiere decir que encontramos nuestra noticia
            $seEncontroLaImagen = array_key_exists("meta", $resultadoDelQuery) && $resultadoDelQuery["meta"]["count"] == 1;

//            LoggingService::logVariable($seEncontroLaImagen, __FILE__, __LINE__);

            if ($seEncontroLaImagen) {
                $respuesta["message"] = "Imagen encontrada.";
                $imagen = $resultadoDelQuery["data"][0];
                $respuesta["data"]["imagen"] = base64_decode($imagen["imagen"]);
                $respuesta["data"]["tipo"] = $imagen["imagen_tipo"];
            } else {
                $respuesta["message"] = "Imposible encontrar imagen con el id $id.";
                $respuesta["error"] = true;
            }
        } else {
            $respuesta["message"] = "El campo id es requerido.";
            $respuesta["error"] = true;
        }

        return $respuesta;
    }

    /**
     * Sube una imagen a un servicio servicio.
     *
     * @param UploadedFileInterface $archivo
     * @param string $titulo
     *
     * @return array
     */
    public function servicio($archivo, $titulo) {
        $resultado = [];
        $subirImagen = $this->subir($archivo);

        /**
         * Configuración del servicio servicio:
         * Credenciales personales, idóneamente se manejan como las credenciales de la BD, fuera del repositorio.
         */
        \Cloudinary::config([
            "cloud_name" => "leopic",
            "api_key" => "399732983768298",
            "api_secret" => "rpPXQkAaZlsjfMZSkYpQfMCEhkg"
        ]);

        $imagenSeSubio = array_key_exists("meta", $subirImagen) && $subirImagen["meta"]["url"];

        // Si la imagen se subió a nuestro servidor
        if ($imagenSeSubio) {
            $url = $subirImagen["meta"]["titulo"];
            $pwd = getcwd();
            $path = "$pwd/$url";
            LoggingService::logVariable($subirImagen, __FILE__, __LINE__);
            $respuestaDelServicio = \Cloudinary\Uploader::upload($path);

            // Verificamos si la imagen fue efectivamente cargada al servicio
            if (array_key_exists("url", $respuestaDelServicio)) {
                $url = $respuestaDelServicio["url"];
                $idExterno = $respuestaDelServicio["public_id"];

                $query = "INSERT INTO imagenes_en_servicio_externo (titulo, id_externo, url_imagen) VALUES (:titulo, :id_externo, :url_imagen)";
                $parametros = [
                    ":titulo" => $titulo,
                    ":id_externo" => $idExterno,
                    ":url_imagen" => $url
                ];

                // En este punto lo que procede es hacer la inserción en la BD de la ruta que retornó el servicio
                $resultadoDelQuery = $this->storage->query($query, $parametros);
                $seCreoLaImagen = array_key_exists("meta", $resultadoDelQuery) && $resultadoDelQuery["meta"]["count"] == 1;

                LoggingService::logVariable($resultadoDelQuery, __FILE__, __LINE__);

                if ($seCreoLaImagen) {
                    $resultado["message"] = "Imagen insertada exitosamente";
                    $resultado["meta"]["id"] = $resultadoDelQuery["meta"]["id"];
                    $resultado["meta"]["url"] = $url;
                    // Después de insertar el registro, debemos borrar la imagen de nuestro servidor
                    unlink($path);
                } else {
                    $resultado["message"] = "Imposible crear entrada para la imagen";
                    $resultado["error"] = true;
                }
            } else {
                $resultado["message"] = "Error subiendo la imagen al servicio externo";
                $resultado["error"] = true;
            }

        } else {
            $resultado["message"] = "Error subiendo la imagen";
            $resultado["error"] = true;
        }

        return $resultado;
    }

}
