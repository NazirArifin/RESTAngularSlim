/* main controller */
app.controller('MainCtrl', ['$scope', '$http', function($scope, $http) {
	/**
	 * untuk pagination
	 */
	$scope.range = function(s, e) {
		var r = [];
		if ( ! e) { e = s; s = 0; }
		for (var i = s; i < e; i++) r.push(i);
		return r;
	};
	
	// untuk upload terserah
	$scope.file = null;
}]);
