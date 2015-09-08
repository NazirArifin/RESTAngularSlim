'use strict';

/** tooltips **/
app.directive('tooltips', function() {
	return {
		restrict: 'CA',
		link: function($scope, elm, attrs) { 
			return attrs.$observe('title', function(v) {
				elm.tooltip({ placement: (attrs.placement || 'top') }); 
			});
		}
	}
});

/**
 * Popover
 */
app.directive('popovers', function() {
	return {
		restrict: 'CA',
		link: function($scope, elm, attrs) {
			return attrs.$observe('content', function(v) {
				elm.popover({
					placement: attrs.placement || 'top',
					animation: false,
					trigger: 'hover',
					html: true,
				});
			});
		}
	}
});

/**
 * Select2
 */
app.directive('select2', ['$timeout', function($timeout) {
	return {
		restrict: 'CA',
		require: '?ngModel',
		scope: { val: '=' },
		link: function(scope, elm, attrs, ctrl) {
			elm.select2();
			scope.$watch('val', function(newValue, oldValue) {
				if (newValue) {
					$timeout(function() {
						elm.select2('val', newValue);
					}, 0, false);
				}
			});
		}
	};
}]);

/**
 * material init
 */
app.directive('materialInit', function() {
	return function($scope, elm, attrs) {
		$.material.init();
	};
});

/** Masked input */
app.directive('maskedInput', function() {
	return function($scope, elm, attrs) { elm.mask(attrs.maskedInput); };
});

/** Number input */
app.directive('numberInput', function() {
	return function($scope, elm, attrs) { 
		elm.priceFormat({ prefix: '', thousandsSeparator: '', centsLimit: 0 });
	};
});

/** Price input */
app.directive('priceInput', function() {
	return function($scope, elm, attrs) { 
		elm.priceFormat({ prefix: '', thousandsSeparator: '.', centsLimit: 0 });
	};
});

/** datepicker */
app.directive('datepicker', function() {
	return function($scope, elm, attrs) {
		elm.bootstrapMaterialDatePicker({ format : 'DD/MM/YYYY', time: false, lang : 'id', weekStart : 1, cancelText : 'BATAL' });
	};
});

/** timepicker */
app.directive('timepicker', function() {
	return function($scope, elm, attrs) {
		elm.bootstrapMaterialDatePicker({ date: false, shortTime: false, format: 'HH:mm' });
	};
});

/** autosuggest */
app.directive('autosuggest', function() {
	return {
		restrict: 'CA',
		require: '?ngModel',
		scope: { val: '=' },
		link: function(scope, elm, attrs, ngModel) {
			var xhr;
			elm.autoComplete({
				minChars: 1, delay: 50,
				source: function(term, response){
					try { xhr.abort(); } catch(e){}
					xhr = $.getJSON(attrs.autosuggest, { q: term }, function(data){ response(data); });
				},
				onSelect: function(e, term, item) {
					ngModel.$setViewValue(term);
					ngModel.$render();
				}
			});
		}
	};
});

/**
 * Input file sederhana, diset $scope.file
 */
app.directive('simpleFileInput', function() {
	return function($scope, elm, attrs) {
		elm.on('change', function(e) {
			var valid = true, temp = [];
			for (var i in e.target.files) {
				var f = e.target.files[i];
				if (i.match(/[^0-9]/)) continue;
				if (temp.length == 5) break;
				
				temp.push(f);
				var	a = f.name.split('.');
				if (a.length === 1 || (a[0] == "" && a.length === 2)) { valid = false; break; } 
				else {
					var b = '.' + a.pop().toLowerCase();
					if (attrs.accept.split(',').indexOf(b) === -1) { valid = false; break; }
					if (f.size > (1 * 1024 * 1024)) { valid = false; break; }
				}
			}
			
			if ( ! valid) {
				alertify.error('File terlalu besar atau tidak valid, gunakan file lain');
				return false;
			}
			if (temp.length == 0) {
				e.val('');
				$scope.file = null;
				return false;
			}
		});
	}
});

