var app = angular.module('proyectoalcaldia');
app.directive('mayusculastodo', function() {
    return {
        require: 'ngModel',
        link: function(scope, element, attrs, modelCtrl) {
            var capitalize = function(inputValue) {
                if (inputValue == undefined) inputValue = '';
                var capitalized = inputValue.toUpperCase();
                if (capitalized !== inputValue) {
                    modelCtrl.$setViewValue(capitalized);
                    modelCtrl.$render();
                }
                return capitalized;
            }
            modelCtrl.$parsers.push(capitalize);
            capitalize(scope[attrs.ngModel]); // capitalize initial value
        }
    };
});
app.directive('disallowSpaces', function() {
    return {
        restrict: 'A',
        link: function($scope, $element) {
            $element.bind('input', function() {
                $(this).val($(this).val().replace(/ /g, ''));
            });
        }
    };
});
app.controller('registroController', ['$scope', '$http', function($scope, $http, serviceLogin) {
    $scope.regrif = '';
    $scope.recuperarrif = '';
    $scope.mensaje = '';
    $scope.regpatente;
    $scope.regusuario = '';
    $scope.regpassw = '';
    $scope.regcpassw = '';
    $scope.regemail = '';
    $scope.patente_existe = false;
    $scope.idcliente;
    $scope.email = false;
    $scope.registrosinusuario = 0;
    $scope.usuario_existe = false;
    $scope.nuevousuario = {};
    $scope.buscarRif = function() {
        var obj = {
            rif: $scope.regrif
        };
        $http.post('scripts/buscar_rif.php', {
            datos: obj
        }).then(function success(user) {
            //console.log(user.data);
            $scope.idcliente = user.data.resultado.id;
            $scope.mensaje = user.data.resultado.mensaje;
            $scope.registrosinusuario = user.data.resultado.registrosinusuario;
            //console.log($scope.registrosinusuario);
        }, function error(art) {
            console.log("Se ha producido un error al recuperar la información");
        });
    };
    $scope.buscarPatente = function() {
        console.log($scope.regpatente);
        $http.get('scripts/buscar_patente.php?id=' + $scope.idcliente + '&num=' + $scope.regpatente, {}).then(function success(e) {
            //console.log(e.data.patente.permitido);
            $scope.patente_existe = e.data.patente.permitido;
        }, function error(art) {
            console.log("Se ha producido un error al recuperar la información");
        });
    };
    $scope.buscarUsuario = function() {
        $http.get('scripts/buscar_user.php?user=' + $scope.regusuario, {}).then(function success(e) {
            $scope.usuario_existe = e.data.user.usuario;
        }, function error(art) {
            console.log("Se ha producido un error al recuperar la información");
        });
    };
    $scope.addNuevoUsuario = function(form) {
        if (form.$valid) {
            if ($scope.regpassw === $scope.regcpassw) {
                var obj = {
                    id: $scope.idcliente,
                    rif: $scope.regrif,
                    registrosinusuario: $scope.registrosinusuario,
                    user: $scope.regusuario,
                    passw: $scope.regpassw,
                    email: $scope.regemail,
                    usuario: $scope.regrif
                };
                //console.log(obj);
                $http.post('scripts/add_usuario.php', {
                    usuarionuevo: obj
                }).then(function success(e) {
                    //console.log(e.data);
                    alertify.success('Registro exitoso');
                    $('#myModal').modal('hide');
                    $('#ModalRegistro').modal('hide');
                    $scope.regrif = '';
                    $scope.regpatente = '';
                    $scope.regusuario = '';
                    $scope.regpassw = '';
                    $scope.regcpassw = '';
                    $scope.regemail = '';
                    $scope.patente_existe = false;
                    $scope.idcliente = '';
                    $scope.email = false;
                    $scope.usuario_existe = false;
                    $scope.nuevousuario = {};
                }, function error(e) {
                    console.log("Se ha producido un error al recuperar la información");
                });
            } else {
                console.log('Las contraseñas no coiciden');
            }
        } else {
            alertify.error('Datos requeridos vacios, verifique');
        }
    };
    $scope.recuperarUsuario = function() {
        var obj = {
            rif: $scope.recuperarrif
        };
        $http.post('scripts/recuperar_usuario.php', {
            usuario: obj
        }).then(function success(e) {
            //console.log(e.data.user.mensaje);
            alertify.success('CEBG - Recuperación de usuario; ' + e.data.user.mensaje);
            $('#myModal').modal('hide');
            $('#ModalRegistro').modal('hide');
            $('#ModalRecuperar').modal('hide');
            $scope.recuperarrif = '';
        }, function error(e) {
            console.log("Se ha producido un error al recuperar la información");
        });
    };
}]);