var gulp = require('gulp'),
    // 启动参数获取
    yargs = require('yargs').argv,
    // css压缩
    css = require("gulp-clean-css"),
    // sass编译
    sass = require('gulp-sass'),
    // js压缩
    js = require('gulp-uglify'),
    // nunjucks模板解析
    // nunjucks  = require('gulp-nunjucks-render'),
    // html压缩
    html = require('gulp-htmlmin'),
    // 多个文件合并
    concat = require('gulp-concat'),
    // 重命名
    rename = require('gulp-rename'),
    // 删除文件
    del = require('del'),
    // 浏览器同步刷新
    sync = require('browser-sync');

// 路径配置
var path = {
    // js原文件路径
    jsSrc: "./src/**/js",
    // js编译后路径
    jsDist: "./dist/**/js",
    // sass源文件
    sassDist: "./src/**/scss",
    // css编译后路径
    cssDist: "./dist/**/css"
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
gulp.task('compileCSS', ["cleanCSS"], function() {
    gulp.src("./src/sass/mui.scss")
        .pipe(sass())
        .pipe(gulp.dest(path.cssDist))
        .pipe(rename('mui.min.css'))
        .pipe(css())
        .pipe(gulp.dest(path.cssDist));
});

// 删除css文件
gulp.task("cleanCSS", function(cb) {
    return del(["./dist/css/mui.css","./dist/css/mui.min.css"], cb);
});

// 合并压缩js文件
gulp.task('compileJS', ["cleanJS"], function() {
    gulp.src(path.jsSrc)
        .pipe(concat('mui.js'))
        .pipe(gulp.dest(path.jsDist))
        .pipe(rename('mui.min.js'))
        .pipe(js())
        .pipe(gulp.dest(path.jsDist));
});

// 删除js文件
gulp.task("cleanJS", function(cb) {
    return del(["./dist/js/mui.js","./dist/js/mui.min.js"], cb);
});

// 编译并复制html
gulp.task("compileHtml",function(){
    gulp.src("./src/html/*.html","!./src/html/_*.html"])
        .pipe(nunjucks({}))
        .pipe(html(options))
        .pipe(gulp.dest("./dist/html/"));
});

// 复制图片
gulp.task("copyImg",function(){
	gulp.src("./src/img/*")
		.pipe(gulp.dest("./dist/img/"));
});

// 复制字体文件
gulp.task("copyFont",function(){
	gulp.src("./src/font/*")
		.pipe(gulp.dest("./dist/font/"));
});

// 复制静态资源
gulp.task("copy",["","copyImg","copyFont"]);

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
    gulp.watch(['./src/js/*.js'], ["compileJS"]);
    gulp.watch(['./src/sass/*.scss',], ["compileCSS"]);
    gulp.watch(["./src/*.html"], ["compileHtml"]);
    gulp.watch(["./src/img/*"], ["copyImg"]);
    gulp.watch(["./src/font/*"], ["copyFont"]);
    gulp.watch(["./gulpfile.js"], ["default"]);
}); 

// 默认任务
gulp.task('default',["compileCSS", "compileJS", "compileHtml", "copy"], function() {
    if (yargs.s){
        gulp.start("server");
        gulp.start("monitor");
    }
    if (yargs.w){
        gulp.start("monitor");
    }
});
