(function($) {
	jQuery.fn.filesBrowser = function(options) {
		options = $.extend({
                        initEl:null,
			reciverEl: null,
			foldersGetUrl: "", //url для получения списка каталогов
			folderParamName: "id", //параметр - идентификатор каталога (передается в filesGetUrl)
			folderCreateUrl: "", //url для создания папок
			folderDeleteUrl: "", //url для удаления папок
			folderVarName: "Folder[title]",
			folderVarParent: "Folder[parent_id]",
			filesGetUrl: "", //url для получения списка файлов
			filesCreateUrl: "", //url для загрузки файлов
			filesDeleteUrl: "", //url для удаления папок
			fileCreateFieldName: "Filedata[]", //имя переменной для отправки файлов
			csrfTokenName: '',
			csrfToken: '',
			foldersParent: false,
			tmbSize: '50e50', //размер превью
			tmbSizeIcon: '32e32', //размер превью иконки
                        insert:function(link,el){
                            $(el.reciverEl).val($(link).attr('href'));
                            $(el.reciverEl).change();
                            $(el.infoCont).find('.file_insert .cont').hide();
                            $(el.dialog).modal('hide');
                        },
                        thumbnails:{
                            enabled:false,
                            extensions:['jpg','png','gif'],
                            thumbnail:['default',0],
                            sizes:{
                                default:['50x50']
                            }
                        },
                        getThumbnail:function(file,type,thumbnail){
                            return file.substr(0,file.lastIndexOf('/'))+
                                    '/.tmb/'+thumbnail+
                                    '/'+file.substr(file.lastIndexOf('/')+1);
                        },
			lang:'en'
		}, options);

		var make = function() {
			$(this).data('rFiles', {				
				locales: {
					en:{
					'browse': 'browse', 'title': 'Select file', 'insertType': 'Select insert type',
					'close': 'close', 'insertAs': 'Insert as...', 'adaptive': 'Adaptive',
					'file': 'File', 'size': 'Size', 'resolution': 'Resolution', 'type': 'Type',
					'compact': 'compact', 'list': 'list', 'icons': 'icons',
					'save': 'save', 'createFolder': 'create folder', 'folder': 'folder', 'success': 'Operation complete successfuly.',
					'create': 'create', 'del': 'delete', 'sure': 'Are you sure?', 'folders': 'folders', 'files': 'files',
					'deleting': 'This items will be deleted', 'reciverNotActive': 'Reciver element is not active!',
					'upload': 'upload','thumbnail':'Thumbnail','successDeleted': 'successfully removed','createFiles':'upload files'
					},
					ru:{
					'browse': 'обзор', 'title': 'Выбрать файл', 'insertType': 'Выбрать тип вставки',
					'close': 'закрыть', 'insertAs': 'Вставить как...', 'adaptive': 'Адаптивный',
					'file': 'Файл', 'size': 'Размер', 'resolution': 'Разрешение', 'type': 'Тип',
					'compact': 'компактный', 'list': 'список', 'icons': 'иконки',
					'save': 'сохранить', 'createFolder': 'создать каталог', 'folder': 'каталог', 'success': 'Операция выполнена успешно.',
					'create': 'создать', 'del': 'удалить', 'sure': 'Вы уверены?', 'folders': 'каталоги', 'files': 'файлы',
					'deleting': 'Следующие элементы будут удалены', 'reciverNotActive': 'Элемент - приемник не активен!',
					'upload': 'загрузить','thumbnail':'Миниатюра','successDeleted': 'успешно удален','createFiles':'загрузить файлы'
					}
				},
				locale:{},
				options: options,
				folders: {}, files: {}, currentFolder: -1, currentFile: -1,
				filesIdsSort: [],
				conteiner: false,
				foldersCont: false,
				filesCont: false,
				infoCont: false,
				actionsCont: false,
				initEl: '',
				reciverEl: null,
				filesView: 'compact', //compact,list,icons
				dialog: null,
				init: function(el) {
					this.locale=this.locales[this.options.lang];					                                        
					if (!this.options.reciverEl)
						this.reciverEl = el;
					else {
						this.reciverEl = this.options.reciverEl;
					}
                                        if (!this.options.initEl)
						this.initEl = el;
					else {
						this.initEl = this.options.initEl;
					}
                                        if (!$(this.reciverEl).attr('id')) {
                                            $(this.reciverEl).attr('id','rfiles-'+(Math.round(Math.random()*10000)));
                                        }
                                        this.id=$(this.reciverEl).attr('id');
                                        
					$(this.conteiner).hide();
					$(this.initEl).on(
                                            'click',
                                            el,
                                            function(e) {
                                                    if ($(e.data).data('rFiles').dialog) {
                                                        $($(e.data).data('rFiles').dialog).modal('show');
                                                        return false;
                                                    }
                                                    if ($(e.data).is(':disabled'))
                                                            alert($(e.data).data('rFiles').locale.reciverNotActive);
                                                    else
                                                            $(e.data).data('rFiles').initDlg();
                                                    return false;
                                            }
					);
				},
				initDlg: function() {
                                        $('body').append(
                                        '<div class="modal fade" id="modal-' + $(this.reciverEl).attr('id') + '">' +
                                        '<div class="modal-dialog rfiles_cont_dialog">' +
                                        '<div class="modal-content">' +
                                        '<div class="modal-header">' +
                                        '<button type="button" class="close" data-dismiss="modal" aria-label="' + this.locale.close + '"><span aria-hidden="true">&times;</span></button>' +
                                        '<h4>' + this.locale.title + '</h4>' +
                                        '</div>' +
                                        '<div class="modal-body rfiles_cont">' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>'
                                        );
                                        this.conteiner=$('#modal-' + $(this.reciverEl).attr('id')).find('.modal-body');
					$(this.conteiner).html('');
					$(this.conteiner).append('<div class="panel" />');
					var temp_cont = $('<div />');
					$(temp_cont).addClass('view');
					$(temp_cont).append('<a herf="#" class="active" data-view="compact" title="' + this.locale.compact + '"><span class="fa fa-columns"></span></a>');
					$(temp_cont).append('<a herf="#" data-view="list" title="' + this.locale.list + '"><span class="fa fa-list"></span></a>');
					$(temp_cont).append('<a herf="#" data-view="icons" title="' + this.locale.icons + '"><span class="fa fa-th"></span></a>');

					$(temp_cont).find('a').bind(
							'click',
							this,
							function(e) {
								$(e.data.reciverEl).data('rFiles').setFileView($(this).attr('data-view'));
								return false;
							}
					);
					$(this.conteiner).find('.panel').append(temp_cont);

					temp_cont = $('<div />');
					$(temp_cont).addClass('action').addClass('create_folder');
					$(temp_cont).append('<a href="#" class="btn" title="' + this.locale.createFolder + '"><span></span></a>')
					$(temp_cont).append('<div class="cont" />');
					$(temp_cont).find('.cont').append('<form class="form">' +
							'<span class="rfiles_fld_p_n"></span>' +
                                                        '<div class="input-group">'+
							'<input type="text" name="' + this.options.folderVarName + '" class="form-control" placeholder="Введите имя папки">' +
                                                        '<span class="input-group-btn">'+
                                                        '<button class="btn btn-primary">' +this.locale.createFolder+'</button>'+
                                                        '</span>'+
                                                        '</div>'+
							'<input type="hidden" class="parent" name="' + this.options.folderVarParent + '">' +
							'<input type="hidden" name="' + this.options.csrfTokenName + '" value="' + this.options.csrfToken + '">' +							
							'</form>'
							);
					$(temp_cont).find('form').on(
							'submit',
							this,
							function(e) {
								$.ajax({
									url: e.data.options.folderCreateUrl,
									processData: e.data,
									type: 'post',
                                                                        dataType:'json',
									data: $(this).serialize()
								}).
								done(function(data) {
                                                                    if (data.error !== undefined) {
                                                                        var err='';
                                                                        for (attr in data.error)
                                                                            for (i in data.error[attr])
                                                                                err+=data.error[attr][i];
                                                                        $(this.processData.reciverEl).data('rFiles').setMessage('error', err, false);
                                                                    }
                                                                    else {
                                                                        $(this.processData.reciverEl).data('rFiles').getFolders();
                                                                        $(this.processData.reciverEl).data('rFiles').setMessage('success', $(this.processData.reciverEl).data('rFiles').locale.success, true);
                                                                    }
								}).
								fail(function(data) {
									$(this.processData.reciverEl).data('rFiles').setMessage('error', data.responseText, false);
								});
                                                                return false;
							});
					$(this.conteiner).find('.panel').append(temp_cont);

					temp_cont = $('<div />');
					$(temp_cont).addClass('action').addClass('create_files');
					$(temp_cont).append('<a href="#" class="btn" title="' + this.locale.createFiles + '"><span></span></a>')
					$(temp_cont).append('<div class="cont" />');
					$(temp_cont).find('.cont').append('<form method="post" enctype="multipart/form-data">' +
                                                        '<div class="input-group">'+
							'<input type="file" name="' + this.options.fileCreateFieldName + '" multiple class="form-control">' +
                                                        '<span class="input-group-btn">'+
                                                        '<input type="submit" class="btn btn-primary" value="' + this.locale.upload + '">' +
                                                        '</span>'+
                                                        '</div>'+
                                                        '<input type="hidden" class="folder" name="' + this.options.filesVarFolder + '">' +
							'<input type="hidden" name="' + this.options.csrfTokenName + '" value="' + this.options.csrfToken + '">' +
							'</form>'
							);
					$(temp_cont).find('form').bind(
							'submit',
							this,
							function(e) {
								$(e.data.reciverEl).data('rFiles').uploadFilesInit(this);
							}
					);
					$(this.conteiner).find('.panel').append(temp_cont);

					temp_cont = $('<div />');
					$(temp_cont).addClass('action').addClass('delete');
					$(temp_cont).append('<a href="#" class="del_btn" title="' + this.locale.del + '"><span></span></a>')
					$(this.conteiner).find('.panel').append(temp_cont);

					$(this.conteiner).find('.panel .action a.btn').bind(
							'click',
							this,
							function(e) {
								$(e.data.reciverEl).data('rFiles').panelBtn(this);
								return false;
							}
					);

					$(this.conteiner).find('.panel .action a.del_btn').bind(
							'click',
							this,
							function(e) {
								$(e.data.reciverEl).data('rFiles').deleteSelected();
								return false;
							}
					);

					$(this.conteiner).append('<div class="messages" />');
					$(this.conteiner).append('<div class="folders" />');
					$(this.conteiner).append('<div class="files" />');
					$(this.conteiner).append('<div class="info" />');
					$(this.conteiner).append('<div class="actions" />');
                                        
					this.foldersCont = $(this.conteiner).find('.folders');
					this.filesCont = $(this.conteiner).find('.files');
					this.infoCont = $(this.conteiner).find('.info');
					this.actionsCont = $(this.conteiner).find('.actions');
					this.dialog = $('#modal-' + $(this.reciverEl).attr('id'));
                                        
					$(this.dialog).modal('show');
					$('#'+this.id).data('rFiles').getFolders();
				},
				panelBtn: function(el) {
					var a = $(el).parents('.action:first');
					var p = $(a).prevAll('.action');
					var n = $(a).nextAll('.action');
					$(p).animate({width: 49});
					$(n).animate({width: 49});
					$(a).animate({width: 433});
				},
				setMessage: function(type, message, autohide) {
					var m = $(this.conteiner).find('.messages');
					$(m).show();
					$(m).append('<div class="mes mes_' + type + '">' + message + '</div>');
					if (!$(m).find('.close').length) {
						$(m).append('<a class="close" href="#" onclick="$(this).parents(\'div:first\').html(\'\').hide(); return false;"><span class="fa fa-close"></a>');
					}
					if (autohide) {
						setTimeout(function() {
							$('.rfiles_cont .messages').slideUp();
						}, 3000);
					}
				},
				getFolders: function() {
					$.ajax({
						url: this.options.foldersGetUrl,
						processData: this,
						dataType: 'json',
						success: function(data) {
							$(this.processData.reciverEl).data('rFiles').initFolders(data);
						}
					});
				},
				initFolders: function(data, ret) {
					if (!ret)
						this.folders = [];
					var cur = -1;
					var len = 0;
					var cont = $('<div />')
					try {
						len = data.length;
					}
					catch (e) {
						return;
					}
					for (i in data) {
						if ((cur == -1)) {
							cur = data[i].id;
						}
						this.folders[data[i].id] = data[i];
						var a = $('<a />');
						$(a).attr('title', data[i].title);
						$(a).html('<span class="fa fa-folder"></span> '+data[i].title);
						$(a).attr('href', '#' + data[i].id);
						$(a).attr('data-id', data[i].id);
						$(a).addClass('folder');
						$(a).bind(
								'click',
								this,
								function(e) {
									$(e.data.reciverEl).data('rFiles').setFolder($(this).attr('data-id'));
									return false;
								}
						);
						$(cont).append(a);
						if (data[i].children)
							$(cont).append(this.initFolders(data[i].children, true));
					}
					if (ret) {
						return cont;
					}
					else {
						$(this.foldersCont).html(cont);
						if (cur != -1)
							this.setFolder(cur);
						return;
					}
				},
				setFolder: function(id) {
					var a = $(this.foldersCont).find('a[data-id="' + id + '"]');
					if ($(a).hasClass('fselected')) {
						$(this.foldersCont).find('a').removeClass('fselected');
						$('.rfiles_fld_p_n').text('');
						$(this.conteiner).find('.panel .create_folder .parent').val('');
					}
					else {
						$(this.foldersCont).find('a').removeClass('fselected');
						$(a).addClass('fselected');
						if (this.options.foldersParent) {
						var t = this.getFolderPath(this.folders[id]);
						$('.rfiles_fld_p_n').text(t.join('/') + '/');
						$(this.conteiner).find('.panel .create_folder .parent').val(id);
						}
					}
					$(this.foldersCont).find('a').removeClass('factive');
					$(a).addClass('factive');
					this.currentFolder = id;
					$(this.conteiner).find('.panel .create_files .folder').val(id);
					this.getFiles(id);
				},
				deleteSelected: function() {
					var sfld = $(this.conteiner).find('.folders .fselected');
					var sfld_text = [];
					var sfld_ids = {};
					if (sfld.length)
						for (i = 0; i < sfld.length; i++) {
							if ($(sfld[i]).attr('data-id')) {
								sfld_text.push(this.folders[$(sfld[i]).attr('data-id')].title);
                                                                sfld_ids[i]={};
								sfld_ids[i]['id'] = $(sfld[i]).attr('data-id');
                                                                sfld_ids[i]['title'] = $(sfld[i]).text();
							}
						}

					var sfil = $(this.conteiner).find('.files .fselected');
					var sfil_text = [];
					var sfil_ids = {};
					if (sfil.length)
						for (i = 0; i < sfil.length; i++) {
							if ($(sfil[i]).attr('data-id')) {
								sfil_text.push(this.files[$(sfil[i]).attr('data-id')].title);
                                                                sfil_ids[i]={};
								sfil_ids[i]['id'] = $(sfil[i]).attr('data-id');
                                                                sfil_ids[i]['title'] = $(sfil[i]).text();
							}
						}

					sfil_text = sfil_text.join(', ');
					sfld_text = sfld_text.join(', ');

					var q = this.locale.deleting + ":\n";
					if (sfld_text)
						q += this.locale.folders + ': ' + sfld_text + "\n";
					if (sfil_text)
						q += this.locale.files + ': ' + sfil_text + "\n";
					q += this.locale.sure;

					var rfld = this.options.folderDeleteUrl;
					var rfil = this.options.filesDeleteUrl;

					if (confirm(q)) {                                            
                                            var data={};
                                            data[this.options.csrfTokenName]=this.options.csrfToken;
                                            for (i in sfil_ids) {                                                    
                                                    $.ajax({
                                                            url: rfil+'?id='+sfil_ids[i]['id'],
                                                            type: 'post',
                                                            processData: {browser:this,file:sfil_ids[i]['title']},
                                                            data: data,
                                                            dataType: 'json'
                                                    }).done(function(data) {
                                                            $(this.processData.browser.reciverEl).data('rFiles').setMessage('success', this.processData.browser.locale.file+' &quot;'+this.processData.file+'&quot;: '+this.processData.browser.locale.successDeleted, false);	
                                                            $(this.processData.browser.reciverEl).data('rFiles').getFiles($(this.processData.browser.reciverEl).data('rFiles').currentFolder);
                                                    }).fail(function(data) {
                                                            $(this.processData.browser.reciverEl).data('rFiles').setMessage('error', data.responseText);
                                                            $(this.processData.browser.reciverEl).data('rFiles').setFolder($(this.processData.browser.reciverEl).data('rFiles').currentFolder);
                                                    });
                                            }
                                            for (i in sfld_ids) {
                                                $.ajax({
                                                        url: rfld+'?id='+sfld_ids[i]['id'],
                                                        type: 'post',
                                                        processData: {browser:this,folder:sfld_ids[i]['title']},
                                                        data: data,
                                                        dataType: 'json'
                                                }).done(function(data) {
                                                        $(this.processData.browser.reciverEl).data('rFiles').setMessage('success', this.processData.browser.locale.folder+' &quot;'+this.processData.folder+'&quot;: '+this.processData.browser.locale.successDeleted, false);	
                                                        $(this.processData.browser.reciverEl).data('rFiles').getFolders();
                                                }).fail(function(data) {
                                                        $(this.processData.browser.reciverEl).data('rFiles').setMessage('error', data.responseText);
                                                        $(this.processData.browser.reciverEl).data('rFiles').getFolders();
                                                });
                                            }
                                                
					}
					return false;
				},
				getFolderPath: function(folder) {
					var res = [folder.title];
					if (folder.parent_id) {
						var t = this.getFolderPath(this.folders[folder.parent_id]);
						for (i in t)
							res.unshift(t[i]);
					}
					return res;
				},
				getFiles: function(id) {
					var r = '';
					if (this.options.filesGetUrl.indexOf('?') == -1) {
						r = this.options.filesGetUrl + '?' + this.options.folderParamName + '=' + id;
					}
					else
						r = this.options.filesGetUrl + '&' + this.options.folderParamName + '=' + id;
					$.ajax({
						url: r,
						processData: this,
						dataType: 'json',
						success: function(data) {
                                                    $(this.processData.reciverEl).data('rFiles').initFiles(data);
						}
					});
				},
				initFiles: function(data) {
                                    //console.log('initFiles');
					var len = 0;
					this.files = {};
					this.filesIdsSort = [];
					try {
						len = data.length;
					}
					catch (e) {
						return;
					}
					for (i in data) {
						this.files[data[i].id] = data[i];
						this.filesIdsSort.push(data[i].id);
					}
					this.setFileView();
				},
				setFileView: function(type) {
					$(this.filesCont).html('');
					this.filesView = (type == undefined) ? this.filesView : type;
                                        $(this.dialog).find('.panel .view a').removeClass('active');
                                        $(this.dialog).find('.panel .view a[data-view="'+this.filesView+'"]').addClass('active');
					var cont, a, temp_data, i;
					if (this.filesView == 'list') {
						cont = $('<table class="list table .table-striped"><tbody></tbody></table>');

						for (n in this.filesIdsSort) {
							i = this.filesIdsSort[n];
							a = $('<a />');
							$(a).attr('title', this.files[i].title);
							$(a).text(this.files[i].title + '.' + this.files[i].ext);
							$(a).attr('href', '#' + this.files[i].id);
							$(a).attr('data-id', this.files[i].id);
							$(a).addClass('file');
							$(cont).find('tbody').append('<tr>' +
									'<td>' + '</td>' +
									'<td>' + this.files[i].mime.substring(0, 20) + '</td>' +
									'<td class="text-right">' + Math.round(this.files[i].size/1024) + ' Kb</td>' +
									'</tr>');
							$(cont).find('tr:last').find('td:first').append(a);
						}
					} else if (this.filesView == 'icons') {
						cont = $('<div />');
						$(cont).addClass('icons')
						for (n in this.filesIdsSort) {
							i = this.filesIdsSort[n];
							a = $('<a />');
							$(a).attr('title', this.files[i].title);
							$(a).append('<span />');
							if (this.files[i].type == 'image') {
								$(a).find('span').css({backgroundImage: 'url(' + this.options.getThumbnail(this.files[i].file, this.options.thumbnails.thumbnail[0],this.options.tmbSizeIcon) + ')'});
							}
							else {
								$(a).find('span').addClass('ftype').addClass('ftype_' + this.files[i].type);
							}
							temp_data = this.files[i].title.substring(0, Math.floor(this.files[i].title.length / 2));
							temp_data += '' + this.files[i].title.substring(Math.floor(this.files[i].title.length / 2), this.files[i].title.length);
							$(a).append(temp_data + '.' + this.files[i].ext);
							$(a).attr('href', '#' + this.files[i].id);
							$(a).attr('data-id', this.files[i].id);
							$(a).addClass('file');
							$(cont).append(a);
						}
					}
					else {
						cont = $('<div />');
						$(cont).addClass('compact')
                                                var t='file-o';
						for (n in this.filesIdsSort) {
							i = this.filesIdsSort[n];
                                                        if (this.files[i].type=='image') t='file-photo-o';
                                                        else if (this.files[i].type=='video') t='file-video-o';
                                                        else if (this.files[i].type=='text') t='file-text-o';
                                                        else if (this.files[i].type=='excel') t='file-excel-o';
                                                        else t='file-o';
							a = $('<a />');
							$(a).attr('title', this.files[i].title);
							$(a).html('<span class="fa fa-'+t+'"></span>'+this.files[i].title + '.' + this.files[i].ext);
							$(a).attr('href', '#' + this.files[i].id);
							$(a).attr('data-id', this.files[i].id);
							$(a).addClass('file');
                                                        //$(a).addClass('ftype_'+this.files[i].type);
							$(cont).append(a);
						}
					}
					$(this.filesCont).append(cont);
					$(this.filesCont).find('a').bind(
							'click',
							this,
							function(e) {
								$(e.data.reciverEl).data('rFiles').setFile(this);
								return false;
							}
					);
				},
				setFile: function(el) {
					$(this.foldersCont).find('a').removeClass('fselected');
					if ($(el).hasClass('fselected')) {
						$(this.filesCont).find('a').removeClass('fselected');
					}
					else {
						$(this.filesCont).find('a').removeClass('fselected');
						$(el).addClass('fselected');
					}
					$(this.filesCont).find('a').removeClass('factive');
					$(el).addClass('factive');

					this.currentFile = $(el).attr('data-id');
					var f = this.files[this.currentFile];
					;
					var id = $(el).attr('data-id');
					$(this.infoCont).html('');
					var temp;
					temp = $('<div />');
					$(temp).append('<div class="file_insert" />');
					$(temp).find('.file_insert').append('<div class="cont" />');
					$(temp).find('.file_insert .cont').append(
							'<div class="header">' +
							'<a href="#" onclick="$(this).parents(\'.cont:first\').hide(); return false;"><span class="fa fa-close"></span></a>' +
							this.locale.insertType +
							'</div>');
					if (f.type == 'image') {
						$(temp).append('<img class="tmb" src="' + this.options.getThumbnail(f.file, this.options.thumbnails.thumbnail[0], this.options.tmbSize) + '" alt="">');
						$(temp).append('<div class="file_info" />');
						$(temp).find('.file_info').append('<div>' + this.locale.file + ': <a href="' + f.file + '" target="_blank">' + f.title + '.' + f.ext + '</a></div>');
						$(temp).find('.file_info').append('<div>' + this.locale.size + ': ' + Math.round(f.size / 1024) + 'Kb; ' +
								this.locale.type + ': ' + f.type + ' (' + f.mime + '); ' + this.locale.resolution + ': ' +
								f.w + 'x' + f.h + '</div>');
						var temp_inserts = this.options.thumbnails.sizes;
						for (var i in temp_inserts) {
                                                    for (var j in temp_inserts[i]) {
							$(temp).find('.file_insert .cont').append('<a class="ins" href="' + this.options.getThumbnail(f.file,i, temp_inserts[i][j]) + '" data-id="' + f.id + '" target="_blank">'+this.locale.thumbnail + ': ' + temp_inserts[i][j] + '</a>');
                                                    }
						}

					}
					else {
						$(temp).append('<div class="tmb filetype_' + f.type + '" />');
						$(temp).append('<div class="file_info" />');
						$(temp).find('.file_info').append('<div>' + this.locale.file + ': <a href="' + f.file + '" target="_blank">' + f.title + '.' + f.ext + '</a></div>');
						$(temp).find('.file_info').append('<div>' + this.locale.size + ': ' + Math.round(f.size / 1024) + 'Kb; ' + this.locale.type + ': ' + f.type + ' (' + f.mime + ');</div>');
					}

					$(temp).find('.file_insert .cont').append('<a class="ins" href="' + f.file + '" data-id="' + f.id + '" target="_blank">' + this.locale.file + '</a>');

					$(temp).find('.file_insert a.ins').bind(
							'click',
							this,
							function(e) {
								$(e.data.reciverEl).data('rFiles').insertFile(this);
								return false;
							}
					);
					$(temp).find('.file_insert').append('<a href="#" class="btn btn-primary as">' + this.locale.insertAs + '</a>');

					$(temp).find('.file_insert').find('a.as:first').bind(
							'click',
							this,
							function(e) {
								$(e.data.reciverEl).data('rFiles').insertsFileShow(this);
								return false;
							}
					);
					$(this.infoCont).append(temp);
					$(this.infoCont).find('.file_insert .cont').hide();
				},
				insertsFileShow: function(el) {
					$(this.infoCont).find('.file_insert .cont').show();
				},
				insertFile: function(el) {
                                    this.options.insert(el,this);
				},
				uploadFilesInit: function(form) {
					$('#rfiles_ifarme').remove();
					this.setMessage('', '<iframe name="rfiles_ifarme" id="rfiles_ifarme"><iframe>', false);
					$('#rfiles_ifarme').hide();
					$(form).attr('target', 'rfiles_ifarme');
					var r = this.options.filesCreateUrl;
					if (r.indexOf('?') == -1)
						r += '?';
					else
						r += '&';
					r += this.options.folderParamName + '=' + this.currentFolder;
					r += '&filebrowser=1&el='+$(this.reciverEl).attr('id');
					$(form).attr('action', r);

					$('#rfiles_ifarme').on(
                                            'load',
                                            this,
                                            function(e) {
                                                $(e.data.reciverEl).data('rFiles').setMessage('alert', 'Загрузка файла(ов) завершена.', false);
                                                $(e.data.reciverEl).data('rFiles').getFiles(e.data.currentFolder);
                                            }
					);
				},
				uploadFiles: function(form) {

				}
			});
			$(this).data('rFiles').init(this);
		}
		return this.each(make);
	};
})(jQuery);
function filesBrowserIframeAPI(el, type, message) {	
    $(el).data('rFiles').setMessage(type,message, false);		
}