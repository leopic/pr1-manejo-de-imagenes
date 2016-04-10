angular.module('noticiasApp.services')
    .service('ImagenesService', ['Upload', function (Upload) {
        var subir = function subir(data, exito, error) {
            return Upload.upload({
                url: data.url,
                method: 'POST',
                sendFieldsAs: 'form',
                // Archivo a subir
                file: data.imagen,
                // Datos adicionales al archivo a enviar al back-end, resto del formulario
                fields: data.datos
            }).then(function(response) {
                var data = response.data;

                if (data.error) {
                    console.debug('subir.servicio: error');
                    error(data);
                } else {
                    console.debug('subir.servicio: éxito');
                    exito(data);
                }
            }, error);
        };
        
        return {
            subir: subir
        };
    }]);
