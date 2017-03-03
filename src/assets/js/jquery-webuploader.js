/**
 * Created by Lenovo on 2017/3/2.
 */
+function ($) {

    $.fn.webuploader = function (options) {
        var opts = $.extend({},$.fn.webuploader.DEFAULTS,options);
        return this.each(function () {
            var _this = $(this);
            var _list = _this.find('.uploader-list');
            opts.options.formData.guid = WebUploader.guid();
            var uploader = WebUploader.create(opts.options);
            var _files = {};
            var _hidden = _this.find('input[type=hidden]');
            _hidden.on('update',function () {
                var results = [];
                for(var f in _files) {
                    results.push(_files[f]);
                }
                _hidden.val(results.join(','));
            });
            // 当有文件添加进来的时候
            uploader.on('fileQueued', function( file ) {
                var _li = $(
                        '<div id="' + file.id + '"  class="list-group-item file-item">' +
                        '<div class="info h4">' + file.name + '['+WebUploader.formatSize(file.size)+']'+
                        '<button type="button" class="close" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div></div>'
                    );
                _li.find('button').click(function (event) {
                    event.preventDefault();
                    switch (file.getStatus()) {
                        case 'inited':
                        case 'queued':
                            uploader.removeFile(file,true);
                            break;
                        case 'error':
                        case 'invalid':
                        case 'cancelled':
                        case 'progress':
                            uploader.cancelFile(file);
                            uploader.removeFile(file,true);
                            _hidden.trigger('update');
                            break;
                        case 'complete':
                            uploader.removeFile(file,true);
                            if (opts.delPoint) {
                                $.post(opts.delPoint,{
                                    fileObj:_files[file.id],
                                    id:file.id
                                });
                                _files[file.id] = null;
                                _hidden.trigger('update');
                            }
                            break;
                    }
                    _li.remove();
                });
                // list为容器jQuery实例
                _list.append(_li);

            });

            // 文件上传过程中创建进度条实时显示。
            uploader.on('uploadProgress', function( file, percentage ) {
                console.log('uploadProgress:'+file.id);
                console.log('uploadProgress:'+percentage);
                var _li = _this.find( '#'+file.id ),
                    _percent = _li.find('.progress span');

                // 避免重复创建
                if ( !_percent.length ) {
                    _percent = $(
                        '<p class="progress">' +
                        '<span class="progress-bar progress-bar-success" role="progressbar" ></span>' +
                        '</p>'
                    )
                        .appendTo( _li )
                        .find('span');
                }
                var value = percentage * 100 + '%';
                _percent.css('width', value ).html(parseInt(percentage * 100) + '%');
            });

            // 文件上传失败，显示上传出错。
            uploader.on('uploadError', function( file ) {
                console.log('uploadError:'+file.id);
                var _li = _this.find( '#'+file.id ),
                    _error = _li.find('div.error');

                // 避免重复创建
                if ( !_error.length ) {
                    _error = $('<div class="error"></div>').appendTo( _li );
                }

                _error.text('上传失败');
            });

            // 文件上传成功，给item添加成功class, 用样式标记上传成功。
            uploader.on('uploadSuccess',function (file,response) {
                console.log('uploadSuccess:'+file.id);
                _this.find( '#'+file.id ).addClass('upload-state-done');
                if (!response.error) {
                    if (opts.options.chunked) {
                        if (response.chunks - response.chunk == 1) {
                            //_this.find('input[type=hidden]').val(response.result);
                            _files[file.id] = response.result;
                            console.log('chunk:'+response.result);
                        }
                    } else {
                        //_this.find('input[type=hidden]').val(response.result);
                        _files[file.id] = response.result;
                        console.log('chunk:'+response.result);
                    }
                }
            });

            // 完成上传完了，成功或者失败，先删除进度条。
            uploader.on('uploadComplete', function( file ) {
                console.log('uploadComplete:'+file.id);
                //_this.find('#'+file.id + '> .progress').remove();
            });

            uploader.on('uploadFinished',function () {
                console.log('uploadFinished:'+_files);

                _hidden.trigger('update');
            });
        });
    };
    $.fn.webuploader.DEFAULTS = {
        delPoint:'',
        options:{
            pick:'#filePicker',
            auto:true,
            fileNumLimit:1
        }
    };
}(jQuery);