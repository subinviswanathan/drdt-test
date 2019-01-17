'use strict';
 
var gulp = require('gulp');
var sass = require('gulp-sass');
var es = require('event-stream');
var eslint = require('gulp-eslint');
//var minify = require('gulp-minify');
//var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');
var del = require('del');
 
sass.compiler = require('node-sass');

gulp.task('sass', function () {
  return gulp.src('./sass/**/style_main.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./'));
});

var component = ['header', 'homepage', 'listicle', 'article', 'archive', 'footer'];

sass.compiler = require('node-sass');

var bundles = ['./js/ad-stack.js','./js/customizer.js','./js/navigation.js','./js/skip-link-focus-fix.js'];

gulp.task('sass:watch', function () {
	gulp.watch('./sass/**/*.scss', ['sass']);
});

// create a css per feature
gulp.task('saas:component',['clean:saas'], function () {
	return es.merge(component.map(function (item) {
		return gulp.src('./sass/' + item + '.scss')
			.pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
			.pipe(gulp.dest('./'));
	}));
});

gulp.task('clean:saas', function () {
	return del(['./*.css','!./rtl.css']);
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
			.pipe(uglify())
			//.pipe(sourcemaps.write('.'))
			.pipe(gulp.dest('./js/src'));
	}));
});

gulp.task('clean:scripts', function () {
	return del(['./js/src/*-min.js']);
});

gulp.task('default', ['sass','saas:component', 'js:task']);