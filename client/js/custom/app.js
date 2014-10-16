'use strict';

var app = angular.module('abm', ['ngCookies', 'ngRoute', 'ngAnimate', 'ngMap']).
config(['$routeProvider', '$httpProvider', function($routeProvider, $httpProvider) {
	$routeProvider.
	when('/login', { templateUrl: 'html/login.html', controller: LoginCtrl }).
	when('/home', { templateUrl: 'html/home.html', controller: HomeCtrl }).
	otherwise({ redirectTo: '/login' });
	
	$httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
	$httpProvider.defaults.headers.put['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
	$httpProvider.defaults.transformRequest = [function(data) {
		return angular.isObject(data) && String(data) !== '[object File]' ? jQuery.param(data) : data;
	}];
}]).

run(['$rootScope', '$location', function($rootScope, $location, $cookies) {
	/*
	 * ---------- setting server -----------
	 */
	var protocol 	= 'http',
		host		= 'localhost',
		port		= '80';
	$rootScope.server = protocol + '://' + host + (port != '80' ? ':' + port : '');
	
	$rootScope.$on("$routeChangeError", function(event, current, previous, rejection) {
		$location.path('/login').replace();
	});
}]);