/* File: gulpfile.js */

var gulp  = require('gulp'),
	/*jshint = require('gulp-jshint'),*/
	concat = require('gulp-concat'),
	uglify = require('gulp-uglify'),
	minify = require('gulp-minify-css'),
	sourcemaps = require('gulp-sourcemaps');

gulp.task('default', ['watch']);

gulp.task('build-js', function() {
	return gulp.src('js/custom/**/*.js').
		pipe(sourcemaps.init()).
			pipe(concat('bundle.js')).
			pipe(uglify()).
		pipe(sourcemaps.write()).
		pipe(gulp.dest('js/custom'));
});

gulp.task('build-css', function() {
	return gulp.src('css/custom.css').
		pipe(concat('custom.min.css')).
		pipe(minify({ compability: 'ie8' })).
		pipe(gulp.dest('css'));
});

gulp.task('watch', function() {
	gulp.watch('js/custom/**/*.js', ['build-js']);
	gulp.watch('css/custom.css', ['build-css']);
});