'use strict';

var app = angular.module('twista', ['ngRoute', 'ngAnimate', 'ngSanitize', 'ngStorage']).
config(function($routeProvider, $httpProvider) {
	$routeProvider
	.when('/', { 
		templateUrl: 'html/index.html', 
		controller: 'MainCtrl' 
	})
	.otherwise({ redirectTo: '/' });
	
	$httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
	$httpProvider.defaults.transformRequest = [function(data) {
		return angular.isObject(data) && String(data) !== '[object File]' ? jQuery.param(data) : data;
	}];
}).
run(['$rootScope', '$location', function($rootScope, $location) {
	// server url
	var protocol 	= 'http',
		host		= 'server.me',
		port		= '80';
	$rootScope.server = protocol + '://' + host + (port != '80' ? ':' + port : '');
	
	$rootScope.$on("$routeChangeError", function(event, current, previous, rejection) {
		$location.path('/').replace();
	});
}]);
