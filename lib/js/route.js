var app = angular.module('proyectoalcaldia');
app.config(['$locationProvider',
    function($locationProvider) {
        $locationProvider.hashPrefix('');
        $locationProvider.html5Mode({
            enabled: false,
            requireBase: true
        });
    }
]);
app.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/', {
        templateUrl: "templates/inicio.php",
        controller: "MainController"
    }).when('/registro', {
        templateUrl: "templates/registro.php",
        controller: "registroController"
    }).otherwise({
        redirectTo: '/'
    });
}]);