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

/** autosuggest */
app.directive('autosuggest', function() {
	return function($scope, elm, attrs) {
		var xhr;
		elm.autoComplete({
			minChars: 1, delay: 50,
			source: function(term, response){
				try { xhr.abort(); } catch(e){}
				xhr = $.getJSON(attrs.autosuggest, { q: term }, function(data){ response(data); });
			}
		});
	};
});

/** detail info siswa */
app.directive('detailSiswa', ['$http', function($http) {
	return function($scope, elm, attrs) {
		elm.on('click', function(e) {
			$http.get('/api/siswa/' + attrs.detailSiswa)
			.success(function(d) { 
				$scope.setInfoSiswa(d.siswa);
				$('#modal-info-siswa').modal('show');
			});
		});
	};
}]);