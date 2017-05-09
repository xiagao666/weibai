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
    // nunjucks模板解析
    nunjucks  = require('gulp-nunjucks-render'),
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
var config = {
    // js原文件路径
    jsSrc: ["./"+yargs.build+"/assets/src/**/js/**/*.js",!"./"+yargs.build+"/assets/src/lib/**/*"],
    // sass源文件
    sassSrc: "./"+yargs.build+"/assets/src/**/css/*.scss",
    // 图片源地址
    imgSrc: "./"+yargs.build+"/assets/src/**/img/*",
    // 字体源地址
    fontSrc: "./"+yargs.build+"/assets/src/**/font/*",
    // 模板源地址
    htmlSrc: "./"+yargs.build+"/assets/src/**/*.html",
    // 依赖库文件地址
    libSrc: "./"+yargs.build+"/assets/src/lib/**/*",
    // 目标地址
    dist: "./"+yargs.build+"/assets/dist",
    // 入口文件
    entry: "./gulpfile.js",
    // html压缩选项
    options:{
        removeComments: true,//清除HTML注释
        collapseWhitespace: true,//压缩HTML
        collapseBooleanAttributes: true,//省略布尔属性的值 <input checked="true"/> ==> <input />
        removeEmptyAttributes: true,//删除所有空格作属性值 <input id="" /> ==> <input />
        removeScriptTypeAttributes: true,//删除<script>的type="text/javascript"
        removeStyleLinkTypeAttributes: true,//删除<style>和<link>的type="text/css"
        minifyJS: true,//压缩页面JS
        minifyCSS: true//压缩页面CSS
    }
};

// 编译Sass后合并压缩css文件
gulp.task('compileCSS', function() {
    gulp.src(config.sassSrc)
        .pipe(change(config.sassSrc))
        .pipe(sass())
        .pipe(autoprefixer())
        .pipe(css())
        .pipe(gulp.dest(config.dist));
});

// 删除css文件
gulp.task("cleanCSS", function(cb) {
    return del(["./dist/css/*.css","./dist/css/*.min.css"], cb);
});

// 合并压缩js文件
gulp.task('compileJS', function() {
    gulp.src(config.jsSrc)
        .pipe(js())
        .pipe(gulp.dest(config.dist));
});

// 删除js文件
gulp.task("cleanJS", function(cb) {
    return del(["./dist/js/*.js","./dist/js/*.min.js"], cb);
});

// 编译并复制html
gulp.task("compileHtml",function(){
    gulp.src(config.htmlSrc)
        .pipe(nunjucks({}))
        .pipe(html(config.options))
        .pipe(gulp.dest(config.dist));
});

// 复制图片
gulp.task("copyImg",function(){
	gulp.src(config.imgSrc)
		.pipe(gulp.dest(config.dist));
});

// 复制字体文件
gulp.task("copyFont",function(){
	gulp.src(config.fontSrc)
		.pipe(gulp.dest(config.dist));
});

// 复制依赖库文件
gulp.task("copyLib",function(){
    gulp.src(config.libSrc)
        .pipe(gulp.dest(config.dist+"/lib"));
});

// 复制静态资源
gulp.task("copy",["copyImg","copyFont","copyLib"]);

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
    gulp.watch(config.jsSrc, ["compileJS"]);
    gulp.watch(config.sassSrc, ["compileCSS"]);
    gulp.watch(config.htmlSrc, ["compileHtml"]);
    gulp.watch(config.imgSrc, ["copyImg"]);
    gulp.watch(config.fontSrc, ["copyFont"]);
    gulp.watch(config.entry, ["default"]);
});

// 默认任务
gulp.task('default', function() {
    // 增加项目时以中竖线分割
    var _project = /www|boss/;

    if (!yargs.build){
        console.warn("缺少编译参数，使用方法：gulp --build 项目名[www|boss]");
        return;
    }
    if (!_project.test(yargs.build)){
        console.warn("项目名称不存在，当前已有项目为："+_project+"");
        return;
    }
    gulp.start(["compileCSS", "compileJS", "compileHtml", "copy"]);

    if (yargs.s){
        // gulp.start("server");
        gulp.start("monitor");
    }
    if (yargs.w){
        gulp.start("monitor");
    }
});
