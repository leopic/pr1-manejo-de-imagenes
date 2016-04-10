/**
 * Definición del módulo.
 */
angular.module('noticiasApp', [
        'ngRoute',
        'ngFileUpload',
        'noticiasApp.controllers',
        'noticiasApp.services'
    ])
    /**
     * Configuración del enrutamiento.
     */
    .config(function($routeProvider) {
        $routeProvider
            .when('/', {
                controller: 'InicioController',
                templateUrl: 'front-end/partials/indice.html'
            })
            .otherwise({
                redirectTo: '/'
            })
    });

// Definimos todos los sub-módulos para evitar conflictos.
angular.module('noticiasApp.controllers', []);
angular.module('noticiasApp.services', []);
