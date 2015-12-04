/* File: gulpfile.js */
var gulp  = require('gulp'),
	concat = require('gulp-concat'),
	uglify = require('gulp-uglify'),
	minify = require('gulp-minify-css'),
	sass = require('gulp-sass'),
	prefixer = require('gulp-autoprefixer'),
	sourcemaps = require('gulp-sourcemaps');

gulp.task('default', ['watch']);

gulp.task('build-scss', function() {
	return gulp.src('src/css/sass/**/*.scss').
		pipe(concat('custom.min.css')).
		pipe(sass()).
		pipe(prefixer({ browser: ['> 5%'], cascade: false })).
		pipe(minify()).
		pipe(gulp.dest('css'));
});

gulp.task('build-tools-css', function() {
	return gulp.src('src/css/tools/**/*.css').
		pipe(concat('tools.min.css')).
		pipe(minify()).
		pipe(gulp.dest('css'));
});

gulp.task('build-js', function() {
	return gulp.src('src/js/custom/**/*.js').
		pipe(sourcemaps.init()).
			pipe(concat('custom.min.js')).
			pipe(uglify()).
		pipe(sourcemaps.write()).
		pipe(gulp.dest('js'));
});

gulp.task('build-tools-js', function() {
	return gulp.src('src/js/tools/**/*.js').
		pipe(concat('tools.min.js')).
		pipe(uglify()).
		pipe(gulp.dest('js'));
});

gulp.task('build-helpers-js', function() {
	return gulp.src('src/js/helpers/**/*.js').
		pipe(concat('helpers.min.js')).
		pipe(uglify()).
		pipe(gulp.dest('js'));
});

gulp.task('watch', function() {
	gulp.watch('src/css/sass/**/*.scss', ['build-scss']);
	gulp.watch('src/css/tools/**/*.css', ['build-tools-css']);
	gulp.watch('src/js/custom/**/*.js', ['build-js']);
	gulp.watch('src/js/tools/**/*.js', ['build-tools-js']);
	gulp.watch('src/js/helpers/**/*.js', ['build-helpers-js']);
});