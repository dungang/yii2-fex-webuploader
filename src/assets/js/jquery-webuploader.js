/**
 * Created by Lenovo on 2017/3/2.
 */
+function ($) {

    $.fn.webuploader = function (options) {
        var opts = $.extend({},$.fn.webuploader.DEFAULTS,options);
        return this.each(function () {
            var _this = $(this);
            var _list = _this.find('.uploader-list');
            var uploader = WebUploader.create(opts.options);
            // 当有文件添加进来的时候
            uploader.on('fileQueued', function( file ) {
                var _li = $(
                        '<div id="' + file.id + '" class="file-item">' +
                        '<div class="info">' + file.name + '</div>' +
                        '</div>'
                    );

                // $list为容器jQuery实例
                _list.append(_li);

            });

            // 文件上传过程中创建进度条实时显示。
            uploader.on('uploadProgress', function( file, percentage ) {
                var _li = _this.find( '#'+file.id ),
                    _percent = _li.find('.progress span');

                // 避免重复创建
                if ( !_percent.length ) {
                    _percent = $('<p class="progress"><span></span></p>')
                        .appendTo( _li )
                        .find('span');
                }
                _percent.css('width', percentage * 100 + '%' );
            });

            // 文件上传成功，给item添加成功class, 用样式标记上传成功。
            uploader.on('uploadSuccess', function( file ) {
                _this.find( '#'+file.id ).addClass('upload-state-done');
            });

            // 文件上传失败，显示上传出错。
            uploader.on('uploadError', function( file ) {
                var _li = _this.find( '#'+file.id ),
                    _error = _li.find('div.error');

                // 避免重复创建
                if ( !_error.length ) {
                    _error = $('<div class="error"></div>').appendTo( _li );
                }

                _error.text('上传失败');
            });

            // 完成上传完了，成功或者失败，先删除进度条。
            uploader.on('uploadComplete', function( file ) {
                _this.find('#'+file.id + '> .progress').remove();
            });
        });
    };
    $.fn.webuploader.DEFAULTS = {
        options:{
            pick:'#filePicker',
            auto:true
        }
    };
}(jQuery);