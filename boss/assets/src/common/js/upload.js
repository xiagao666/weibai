var Upload = function() {

    function _upload(config) {
        config = config || {};
        $(config.content).fileupload({
                url: config.url,
                dataType: 'json',
                autoUpload: false,
                // acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                acceptFileTypes: config.type,
                maxNumberOfFiles: 1,
                maxFileSize: 999000,
                // Enable image resizing, except for Android and Opera,
                // which actually support image resizing, but fail to
                // send Blob objects via XHR requests:
                disableImageResize: /Android(?!.*Chrome)|Opera/
                    .test(window.navigator.userAgent),
                previewMaxWidth: 100,
                previewMaxHeight: 100,
                previewCrop: true
            }).on('fileuploadadd', function(e, data) {
                var _show = $(data.fileInputClone[0]).data("show");
                data.context = $('<div/>').appendTo($(_show));
                $.each(data.files, function(index, file) {
                    var node = $('<p/>')
                        .append($('<span/>').text(file.name));
                    if (!index) {
                        node
                            .append(uploadButton.clone(true).data(data));
                    }
                    node.appendTo(data.context);
                });
            }).on('fileuploadprocessalways', function(e, data) {
                var index = data.index,
                    file = data.files[index],
                    node = $(data.context.children()[index]);
                if (file.preview) {
                    node
                        .prepend('<br>')
                        .prepend(file.preview);
                }
                if (file.error) {
                    node
                        .append('<br>')
                        .append($('<span class="text-danger"/>').text(file.error));
                }
                if (index + 1 === data.files.length) {
                    data.context.find('button')
                        .text('上传')
                        .prop('disabled', !!data.files.error);
                }
            }).on('fileuploaddone', function(e, data) {
                if (data.result.status === 'success') {
                    var closeBtn = $("<i/>").
                    addClass("btn-close").
                    text("x").
                    on("click", function() {
                        var $this = $(this);
                        $this.parent().parent().remove();
                    });
                    var link = "<img src='" + data.result.minUrl + "'><input type='hidden' name='imgUrl[]' value='" + data.result.bgUrl + "'>";
                    $(data.context.children()).append(link);
                    $(data.context.children()).append(closeBtn);
                    $(data.context).find("span").remove();
                }
            }).on('fileuploadfail', function(e, data) {
                $.each(data.files, function(index) {
                    var error = $('<span class="text-danger"/>').text('File upload failed.');
                    $(data.context.children()[index])
                        .append('<br>')
                        .append(error);
                });
            }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');
    }

    return {
        upload: _upload
    };
}();