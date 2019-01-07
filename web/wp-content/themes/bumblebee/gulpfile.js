'use strict';
 
var gulp = require('gulp');
var sass = require('gulp-sass');
var eslint = require('gulp-eslint');
var minify = require('gulp-minify');
//var sourcemaps = require('gulp-sourcemaps');
//var uglify = require('gulp-uglify');
var del = require('del');
var es = require('event-stream');

sass.compiler = require('node-sass');

var component = ['homepage','listicle','article','header','footer'];
var bundles = ['./js/ad-stack.js','./js/customizer.js','./js/navigation.js','./js/skip-link-focus-fix.js'];

gulp.task('sass', function () {
	return gulp.src('./sass/**/style.scss')
		.pipe(sass().on('error', sass.logError))
		.pipe(gulp.dest('./'));
});

gulp.task('sass:watch', function () {
	gulp.watch('./sass/**/*.scss', ['sass']);
});

// create a css per feature
gulp.task('saas:component', function () {
	return es.merge(component.map(function (item) {
		return gulp.src('./sass/' + item + '.scss')
			.pipe(sass().on('error', sass.logError))
			.pipe(gulp.dest('./'));
	}));
});

//This task is to run the es linting
gulp.task('js:linting', function () {
	return gulp.src(['./js/*.js', '!./js/*-min.js'])
	// eslint() attaches the lint output to the "eslint" property
	// of the file object so it can be used by other modules.
		.pipe(eslint())
		// eslint.format() outputs the lint results to the console.
		// Alternatively use eslint.formatEach() (see Docs).
		.pipe(eslint.format())
		// To have the process exit with an error code (1) on
		// lint error, return the stream and pipe to failAfterError last.
		.pipe(eslint.failAfterError());
});

gulp.task('js:task', ['js:linting', 'clean:scripts'], function () {
	return es.merge(bundles.map(function (item) {
		return gulp.src(item)
			//.pipe(sourcemaps.init())
			.pipe(minify())
			//.pipe(sourcemaps.write('.'))
			.pipe(gulp.dest('./js'));
	}));
});

gulp.task('clean:scripts', function () {
	return del(['./js/*-min.js']);
});

//@todo need to add saas:component to the task
gulp.task('default', ['sass', 'js:task']);