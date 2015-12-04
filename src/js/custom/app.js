'use strict';

var app = angular.module('twista', ['ngRoute', 'ngAnimate', 'ngSanitize', 'ngStorage']).
config(['$routeProvider', '$httpProvider', function($routeProvider, $httpProvider) {
	
	$httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
	$httpProvider.defaults.transformRequest = [function(data) {
		return angular.isObject(data) && String(data) !== '[object File]' ? jQuery.param(data) : data;
	}];
	
}]).
run(['$rootScope', '$location', function($rootScope, $location) {
	$rootScope.$on("$routeChangeError", function(event, current, previous, rejection) {
		$location.path('/').replace();
	});
}]);

app.filter('range', function() {
  return function(input, min, max) {
    min = parseInt(min); //Make string input int
    max = parseInt(max);
    for (var i=min; i<max; i++)
      input.push(i);
    return input;
  };
});