/**
 * Created by Lenovo on 2017/3/2.
 */
+function ($) {

    $.fn.webuploader = function (options) {
        var opts = $.extend({},$.fn.webuploader.DEFAULTS,options);
        return this.each(function () {
            var _this = $(this);
            var _list = _this.find('.uploader-list');
            //每个文件的额外参数
            var extraData = {};
            //每个文件的上传的进度。安装文件分片的数量来模拟进度，不是精度模拟（按文件的大小）
            var fileUploadedParts={};
            //每个文件上传到服务端返回的文件名称
            var _files = {};
            opts.options.formData.guid = WebUploader.guid();
            opts.options.formData.extraData = '{}';
            var uploader = WebUploader.create(opts.options);
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
                fileUploadedParts[file.id] = 0;
                var _li = $(
                        '<div id="' + file.id + '"  class="list-group-item file-item">' +
                        '<div class="info h4">' + file.name + '['+WebUploader.formatSize(file.size)+']'+
                        '<button type="button" class="close" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div></div>'
                    );
                //删除文件
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
                var _li = _this.find( '#'+file.id ),
                    _percent = _li.find('.progress span');

                // 避免重复创建
                if ( !_percent.length ) {
                    //_percent =
                    $(
                        '<p class="progress">' +
                        '<span class="progress-bar progress-bar-success" style="width: 5%" role="progressbar" >5%</span>' +
                        '</p>'
                    ).appendTo( _li ).find('span');
                }
                // var value = percentage * 100 + '%';
                // _percent.css('width', value ).html(parseInt(percentage * 100) + '%');
            });

            // 当某个文件的分块在发送前触发，主要用来询问是否要添加附带参数，大文件在开起分片上传的前提下此事件可能会触发多次
            uploader.on('uploadBeforeSend',function (obj,data) {
                // 除本地驱动，其他驱动可能需要在客户端和服务端来回传递而外的参数
                // 发送
                if (extraData[obj.file.id]) {
                    data.extraData = extraData[obj.file.id];
                }
            });
            //当某个文件上传到服务端响应后，会派送此事件来询问服务端响应是否有效。
            //如果此事件handler返回值为false, 则此文件将派送server类型的uploadError事件。
            uploader.on('uploadAccept',function (obj,ret) {
                if (ret.error) {
                    uploader.cancelFile(obj.file);
                    alert(obj.file.name + ret.error.message);
                    return false;
                }
                //除本地驱动，其他驱动可能需要在客户端和服务端来回传递而外的参数
                //接受
                if (ret.extraData) {
                    extraData[obj.file.id] = ret.extraData;
                }
                if (ret.chunks) {
                    fileUploadedParts[obj.file.id] +=1;
                    var value = (parseInt(fileUploadedParts[obj.file.id]) / parseInt(ret.chunks)) * 100;
                    var _percent = _this.find('#'+obj.file.id+'> .progress span');
                    _percent.css('width', parseInt(value) + '%' ).html(parseInt(value) + '%');
                }

                return true;
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
            
            uploader.on('error',function (type) {
                var args = [].slice.call( arguments, 1 );
                var msg = false;
                switch (type){
                    case 'Q_EXCEED_NUM_LIMIT':
                        msg = args[1].name + '添加失败，最多添加的文件数量为：'+args[0];
                        break;
                    case 'Q_EXCEED_SIZE_LIMIT':
                        msg = args[1].name + '添加失败，文件总大小超出，总大小为：'+ WebUploader.formatSize(args[0]);
                        break;
                    case 'F_EXCEED_SIZE':
                        msg = args[1].name + '添加失败，该文件大小超出' + WebUploader.formatSize(args[0]);
                        break;
                    case 'Q_TYPE_DENIED':
                        msg = args[1].name + '添加失败，文件类型不满足';
                        break;
                    case 'F_DUPLICATE':
                        msg = args[1].name + '添加失败，文件重复了';
                        break;
                }
                if (msg) {
                    alert(msg);
                }
            });

            // 文件上传成功，给item添加成功class, 用样式标记上传成功。
            uploader.on('uploadSuccess',function (file,response) {
                _this.find( '#'+file.id ).addClass('upload-state-done');
                if (response.result) {
                    if (opts.options.chunked) {
                        //if (response.chunks - response.chunk == 1) {
                            //_this.find('input[type=hidden]').val(response.result);
                            _files[file.id] = response.result;
                        //}
                    } else {
                        //_this.find('input[type=hidden]').val(response.result);
                        _files[file.id] = response.result;
                    }
                }
            });

            //不管成功或者失败，文件上传完成时触发。
            // 完成上传完了，成功或者失败，先删除进度条。
            uploader.on('uploadComplete', function( file ) {
                //_this.find('#'+file.id + '> .progress').remove();
            });

            //当所有文件上传结束时触发。
            uploader.on('uploadFinished',function () {
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