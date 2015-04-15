'use strict';

/* main controller */
app.controller('MainCtrl', function($scope, $http) {
	$scope.greeting = "Hai.. selamat datang!";
	$scope.cds = [];
	$scope.loadCds = function() {
		$http.get($scope.server + '/cds').
		success(function(d) {
			$scope.cds = d;
		});
	}; $scope.loadCds();
});