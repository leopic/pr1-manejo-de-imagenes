/**
 * Definición del módulo.
 */
angular.module('imagenesApp', [
        'ngRoute',
        'ngFileUpload',
        'imagenesApp.controllers',
        'imagenesApp.services'
    ])
    /**
     * Configuración del enrutamiento.
     */
    .config(function($routeProvider) {
        $routeProvider
            .when('/', {
                controller: 'InicioController',
                templateUrl: 'front-end/partials/inicio.html'
            })
            .otherwise({
                redirectTo: '/'
            })
    });

// Definimos todos los sub-módulos para evitar conflictos.
angular.module('imagenesApp.controllers', []);
angular.module('imagenesApp.services', []);
