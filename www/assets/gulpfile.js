var gulp = require('gulp'),
    // 启动参数获取
    yargs = require('yargs').argv,
    // css压缩
    css = require("gulp-clean-css"),
    // sass编译
    sass = require('gulp-sass'),
    // 自动添加浏览器前缀
    autoprefixer = require('gulp-autoprefixer'),
    // js压缩
    js = require('gulp-uglify'),
    // html压缩
    html = require('gulp-htmlmin'),
    // 多个文件合并
    concat = require('gulp-concat'),
    // 重命名
    rename = require('gulp-rename'),
    // 删除文件
    del = require('del'),
    // 只传递修改过的文件
    change = require("gulp-changed"),
    // 浏览器同步刷新
    sync = require('browser-sync');

// 路径配置
var path = {
    // js原文件路径
    jsSrc: "./src/**/js/*.js",
    // sass源文件
    sassSrc: "./src/**/css/*.scss",
    // 图片源地址
    imgSrc: "./src/**/img/*",
    // 字体源地址
    fontSrc: "./src/**/font/*",
    // 模板源地址
    htmlSrc: ["./src/**/html/*.html","!./src/**/html/_*.html"],
    // 目标地址
    dist: "./dist",
    // 入口文件
    entry: "./gulpfile.js"
},
// html压缩选项
options = {
    removeComments: true,//清除HTML注释
    collapseWhitespace: true,//压缩HTML
    collapseBooleanAttributes: true,//省略布尔属性的值 <input checked="true"/> ==> <input />
    removeEmptyAttributes: true,//删除所有空格作属性值 <input id="" /> ==> <input />
    removeScriptTypeAttributes: true,//删除<script>的type="text/javascript"
    removeStyleLinkTypeAttributes: true,//删除<style>和<link>的type="text/css"
    minifyJS: true,//压缩页面JS
    minifyCSS: true//压缩页面CSS
};

// 编译Sass后合并压缩css文件
gulp.task('compileCSS', function() {
    gulp.src(path.sassSrc)
        .pipe(change(path.sassSrc))
        .pipe(sass())
        .pipe(autoprefixer())
        .pipe(css())
        .pipe(gulp.dest(path.dist));
});

// 删除css文件
gulp.task("cleanCSS", function(cb) {
    return del(["./dist/css/*.css","./dist/css/*.min.css"], cb);
});

// 合并压缩js文件
gulp.task('compileJS', function() {
    gulp.src(path.jsSrc)
        .pipe(js())
        .pipe(gulp.dest(path.dist));
});

// 删除js文件
gulp.task("cleanJS", function(cb) {
    return del(["./dist/js/*.js","./dist/js/*.min.js"], cb);
});

// 编译并复制html
gulp.task("compileHtml",function(){
    gulp.src(path.htmlSrc)
        .pipe(html(options))
        .pipe(gulp.dest(path.dist));
});

// 复制图片
gulp.task("copyImg",function(){
	gulp.src(path.imgSrc)
		.pipe(gulp.dest(path.dist));
});

// 复制字体文件
gulp.task("copyFont",function(){
	gulp.src(path.fontSrc)
		.pipe(gulp.dest(path.dist));
});

// 复制静态资源
gulp.task("copy",["copyImg","copyFont"]);

// 启动服务
gulp.task("server",function(){
    yargs.p = yargs.p || 8080;
    sync.init({
        server : {
            baseDir : "./"
        },
        ui: {
            port: yargs.p + 1,
            weinre : {
                port: yargs.p+2
            }
        },
        port: yargs.p,
        startPath:"./dist/html/"
    });
});

// 监听任务
gulp.task("monitor",function(){
    gulp.watch(path.jsSrc, ["compileJS"]);
    gulp.watch(path.sassSrc, ["compileCSS"]);
    gulp.watch(path.htmlSrc, ["compileHtml"]);
    gulp.watch(path.imgSrc, ["copyImg"]);
    gulp.watch(path.fontSrc, ["copyFont"]);
    gulp.watch(path.entry, ["default"]);
});

// 默认任务
gulp.task('default',["compileCSS", "compileJS", "compileHtml", "copy"], function() {
    if (yargs.s){
        // gulp.start("server");
        gulp.start("monitor");
    }
    if (yargs.w){
        gulp.start("monitor");
    }
});