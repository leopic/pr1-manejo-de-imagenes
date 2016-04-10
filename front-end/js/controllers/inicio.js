angular.module('imagenesApp.controllers')
    .controller('InicioController', ['$scope', 'ImagenesService',
        function ($scope, ImagenesService) {
            $scope.init = function init() {
                console.debug('Inicio');

                $scope.rutas = {
                    bd: null,
                    servicio: null,
                    servidor: null
                };
            };

            $scope.subirImagenServidor = function subirImagenServidor() {
                if ($scope.formImagenServidor.imagenServidor.$valid && $scope.imagenServidor) {
                    ImagenesService.subir({
                            url: 'back-end/imagenes/servidor',
                            imagen: $scope.imagenServidor,
                            datos: {
                                tituloImagenServidor: $scope.tituloImagenServidor
                            }
                        }, function exito(data) {
                            if (data.error) {
                                console.debug(data);
                            } else {
                                $scope.rutas.servidor = data.urls[0];
                            }
                        }, function error(data) {
                            console.debug(data);
                        }
                    );
                }
            };

            $scope.subirImagenBD = function subirImagenBD() {
                if ($scope.formImagenBD.imagenBD.$valid && $scope.imagenBD) {
                    ImagenesService.subir({
                            url: 'back-end/imagenes/bd',
                            imagen: $scope.imagenBD,
                            datos: {
                                tituloImagenBD: $scope.tituloImagenBD
                            }
                        }, function exito(data) {
                            if (data.error) {
                                console.debug(data);
                            } else {
                                $scope.rutas.bd = data.ids[0];
                            }
                        }, function error(data) {
                            console.debug(data);
                        }
                    );
                }
            };

            $scope.subirImagenServicio = function subirImagenServicio() {
                if ($scope.formServicio.imagenServicio.$valid && $scope.imagenServicio) {
                    ImagenesService.subir({
                            url: 'back-end/imagenes/servicio',
                            imagen: $scope.imagenServicio,
                            datos: {
                                tituloImagenServicio: $scope.tituloImagenServicio
                            }
                        }, function exito(data) {
                            if (data.error) {
                                console.debug(data);
                            } else {
                                $scope.rutas.servicio = data.urls[0];
                            }
                        }, function error(data) {
                            console.debug(data);
                        }
                    );
                }
            };

            $scope.init();
        }]);
