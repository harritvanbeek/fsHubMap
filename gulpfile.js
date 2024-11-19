'use strict';

import gulp             from 'gulp';
import rename           from 'gulp-rename';
import * as dartSass    from 'sass';
import gulpSass         from 'gulp-sass';
import autoprefixer     from 'gulp-autoprefixer';
import sourcemaps       from 'gulp-sourcemaps';
import uglify           from 'gulp-uglify';
import plumber          from 'gulp-plumber';
import concat           from 'gulp-concat';
import browserSync      from 'browser-sync';

const sass              = gulpSass(dartSass);
       
        //styles
var     styleDEV		=	"dev/scss/style.scss",
        styleWatch		=	"dev/scss/**/*.scss",
		
		
        //js routing
		jsDEV			=	"./dev/js/**/*.js",
        jsWatch			=	"./dev/js/**/*.js",

		//controllers
		controlerDEV	=	"./dev/controller/**/*.js",
		controlerWatch	=	"./dev/controller/**/*.js",
		

        //Online folders
		styleLOC		=	"./templates/css/",
		jsLOC			=	"./templates/js/";       
		

function browser_sync(){
    browserSync.init();	
}

function reload(done){
    browserSync.reload();
    done();
}

function style(done){
    gulp.src(styleDEV)
        .pipe(sourcemaps.init())
        .pipe(plumber())
        .pipe( sass({ errorLogToConsole: true, outputStyle: 'compressed'}))
        .on('error', console.error.bind( console ) )
        .pipe( autoprefixer({ browers:['last 2 versions'], cascade:false }))
        .pipe( rename({suffix:'.min'}) )
        .pipe(sourcemaps.write('./sourcemaps/'))		
        .pipe(gulp.dest(styleLOC))
        .pipe(browserSync.stream({injectChanges:true}) );
    done();  
}


function scripts(done){        
    gulp.src([jsDEV, "!./dev/js/**/*.min.js"])
        .pipe(sourcemaps.init())
        .pipe(concat("klm.js"))
		   .pipe(uglify())
		   .pipe(rename({suffix:'.min'}))
		   .pipe(sourcemaps.write('./sourcemaps/'))		
        .pipe(gulp.dest(jsLOC))
	    .pipe( browserSync.stream() );
	done();
}

function controllers(done){
	gulp.src([controlerDEV, "!./dev/controller/**/*.min.js"])
		.pipe(sourcemaps.init())
		.pipe(concat("klm.controller.js"))
		.pipe(uglify())
		.pipe(rename({suffix:'.min'}))
		.pipe(sourcemaps.write('./sourcemaps/'))
		.pipe(gulp.dest(jsLOC))
		.pipe( browserSync.stream() );
	done();
}

function watch_files(){
    gulp.watch(styleWatch, gulp.series(style));
    gulp.watch(jsWatch, gulp.series(scripts, reload));
    gulp.watch(controlerWatch, gulp.series(controllers, reload));
    gulp.watch("**/**/*.html", gulp.series(reload));
    gulp.watch("**/**/*.php", gulp.series(reload));
}

gulp.task("style", style);
gulp.task("scripts", scripts);
gulp.task("controllers", controllers);
gulp.task("browser_sync", browser_sync);
gulp.task("watch", gulp.parallel(browser_sync, watch_files));
gulp.task("default", gulp.parallel(style, scripts, controllers, "watch"));
