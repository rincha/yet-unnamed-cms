Rhelper={
    window: function(el) {
	$(el).addClass('sys_window').show();
	$(el).append('<a href="#" onclick="Rhelper.windowClose($(this).parents(\'.sys_window:first\')); return false;" class="close"><img src="img/close.png"></a>');
    },
    windowClose:function(el) {
	$(el).find('a.close').remove();
	$(el).hide().removeClass('sys_window');
    },
    edump: function (obj,n,max) {
	max=max==undefined?2:max;
	if (n>=max) return '';
	n=n==undefined?0:n;
	var out = "";
	if(obj && typeof(obj) == "object"){
	    out +="\n"+n+'----Object----' + "\n";
	    m=0;
	    for (var i in obj) {
		out += i + ": " + ((typeof(obj[i])=="object")?this.edump(obj[i],n+1,max):obj[i]) + "\n";
		m++;
		if (m>50) return out;
	    }
	    out +="\n"+n+'----Object----' + "\n";
	} else {
	    out += "X:"+obj;
	}
	n++;
	return out;
    },
    objToString: function (obj,text) {
	var out = "";
	if(obj && typeof(obj) == "object"){
	    if (!text) out +="{ ";
	    for (var i in obj) {
		out += (text?(isNaN(i)?i+': ':''):i+': ') + ((typeof(obj[i])=="object")?this.objToString(obj[i],text):"'"+obj[i]+"'") + " ";
	    }
	    if (!text) out +="}, "; else out+='; ';
	} else {
	    out += "'"+obj+"' ,";
	}
	return out;
    }
};

Rfiles={
    //'jpg, jpeg, gif, png, flv, mp3, mp4, avi, swf, txt, rtf, doc, docx, odt, csv, xls, xlsx, xml, ods, pdf';
    types:{
	image:['jpg', 'jpeg', 'gif', 'png'],
	media:['ogv','webm','flv','mkv','mp4'],
	flash:['swf'],
	document:['txt', 'rtf', 'doc', 'docx', 'odt', 'pdf'],
	table:['csv', 'xls', 'xlsx', 'xml', 'ods']	
    },
    temp:null,
    tempFiles:{},
    logInterval:0,
    editor:null,
    folders:{},
    subFolders:{},
    foldersSort:[],
    subFoldersSort:[],
    currentFolder:0,
    upFolder:0,
    currentPath:[],
    currentFile:0,
    files:{},
    filesSort:[],
    log:[],
    sub_folders_allow:true,
    rules:{
        getFolders:'',
        getFiles:'',
        deleteFile:'',
        deleteFolder:'',
        createFolder:'',
        createFile:'',
        updateFile:'',
        additionalFormsParams:{},
    },
    getThumbnail:function(file,type,thumbnail){
        return file.substr(0,file.lastIndexOf('/'))+
                '/.tmb/'+thumbnail+
                '/'+file.substr(file.lastIndexOf('/')+1);
    },
    thumbnails:{
        enabled:false,
        extensions:['jpg','png','gif'],
        thumbnail:['default',0],
        sizes:{
            default:['50x50']
        }
    },
    base_url:'',
    init: function(ed) {
	this.editor=ed;
	var config=ed.getParam('rfiles');
	Rfiles.thumbnails=config.thumbnails;
        if (!Rfiles.thumbnails.enabled) {
            $('#sys_images_options .sizes').hide();
        }
        Rfiles.sub_folders_allow=config.sub_folders_allow===undefined?'':config.sub_folders_allow;
        if (!Rfiles.sub_folders_allow) {
            $('#sys_folder_parents_list_cont').hide();
        }
	Rfiles.base_url=config.base_url===undefined?'':config.base_url;
	for (k in config.actions) {this.rules[k]=config.actions[k];}
	if (config.additionalFormsParams) {
                Rfiles.rules.additionalFormsParams=config.additionalFormsParams;
		$('#rfiles form').each(function(){
			for (i in Rfiles.rules.additionalFormsParams) {
				$(this).append('<input name="'+i+'" value="'+Rfiles.rules.additionalFormsParams[i]+'" type="hidden">')
			}
		});		
	}	
	this.loadFolders();    
    },
    getUrl: function(type,params) {
	p=[];
	if (params)
		for (i in params) p.push(i+'='+params[i]);
	p=p.join('&');
	//if (p) p='&'+p;
	switch(type) {
		case 'getFolders':
		return this.rules.getFolders+(this.rules.getFolders.indexOf('?')>0?'&':'?')+p;
		case 'getFiles':
		return this.rules.getFiles+(this.rules.getFolders.indexOf('?')>0?'&':'?')+p;
	    case 'deleteFile':
		return this.rules.deleteFile+(this.rules.getFolders.indexOf('?')>0?'&':'?')+p;
	    case 'deleteFolder':
		return this.rules.deleteFolder+(this.rules.getFolders.indexOf('?')>0?'&':'?')+p;
	    case 'createFolder':
		return this.rules.createFolder+(this.rules.getFolders.indexOf('?')>0?'&':'?')+p;
		case 'createFile':
		return this.rules.createFile+(this.rules.getFolders.indexOf('?')>0?'&':'?')+p;
	    case 'updateFile':
		return this.rules.updateFile+(this.rules.getFolders.indexOf('?')>0?'&':'?')+p;
	}
    },
    
    loadFolders: function(id) {	
		$('#sys_folders .panel .sys_iloader').html('<img src="img/loader.gif">');
		if (id) {
			$.getJSON(this.getUrl('getFolders',{id:id}), function(data) {
				Rfiles.initFolders(data,id);
			});
		}
		else {
			Rfiles.currentPath=[];
			$.getJSON(this.getUrl('getFolders'), function(data) {
				Rfiles.initFolders(data,0);
			});
		}
    },
	loadSubFolders: function(id) {	
		$.getJSON(this.getUrl('getFolders',{id:id}), function(data) {
			Rfiles.initSubFolders(data);
		});
    },
	
    initFolders : function (data,parent_id) {
	this.folders={};
	this.foldersSort=[];
	cur=-1;
	var len=0;
	try {len=data.length;}
	catch(e) {len=0;}
	for (i=0; i<len; i++) {
		if ((i==0)) {cur=data[i].id;}
		this.folders[data[i].id]=data[i];
		this.folders[data[i].id].selected=false;
		this.folders[data[i].id].current=false;
		this.foldersSort.push(data[i].id);
	}
	if (!parent_id)
		Rfiles.currentPath.push(this.folders[cur]);
	this.initFoldersBtn();
	this.setCurrentFolder(cur);
	
    },
			
	initSubFolders : function (data) {
	this.subFolders={};
	this.subFoldersSort=[];
	if (this.folders[this.currentFolder].parent_id) {
		this.subFolders[0]={id:0, title:'..', up:this.folders[this.currentFolder].parent_id}
		this.subFoldersSort=[0];
	}
	var len=0;
	try {len=data.length;}
	catch(e) {len=0;}
	for (i=0; i<len; i++) {
		this.subFolders[data[i].id]=data[i];
		this.subFolders[data[i].id].selected=false;
		this.subFolders[data[i].id].up=false;
		this.subFoldersSort.push(data[i].id);
	}
	this.initSubFoldersBtn();
    },
			
    setCurrentFolder: function(id) {
	$('#sys_frame_uploader').hide();
	this.currentFolder=id;
	$('#sys_folders .list .folder').removeClass('current');
	$('#sys_fld_'+id).addClass('current');
	this.files={};
	if (this.folders[id].has_children) {
            this.loadSubFolders(this.folders[id].id);
	}
	this.loadFiles(id);
        this.currentPath=[this.folders[id]];
	$('#sys_files .panel .folder_name').html('');
	for (i in this.currentPath) {
		$('#sys_files .panel .folder_name').append(
			$('<div/>').text(this.currentPath[i].title).html()+'/'
		);
	}
	//$('#sys_files .panel .folder_name').html($('<div/>').text(temp+'/'+this.folders[id].title).html());
	
	/*настройка загрузчика файлов*/
	var url = Rfiles.getUrl('createFile',{id:id});
	$('#sys_fileuploader').fileupload({
	    url: url,
	    type: 'post',
	    dataType: 'json',
	    param_name: 'Filedata',
	    singleFileUploads:false, //загружать по одному
	    limitMultiFileUploads:10, //максимум файлов одновременно
	    sequentialUploads:false, //последовательная загрузка
	    limitConcurrentUploads:3, //максимум одновременно
	    multipart:true,
	    filesContainer:$('#sys_panel .add_file .files'),
	    done: function (e,data) {
		for (i in data.result) {
		    if (data.result[i].success) {
				$('#sys_file_up_'+i).addClass('success');
				Rfiles.toLog(Rhelper.objToString(data.result[i].success,true),'success');
		    }
		    else {
				$('#sys_file_up_'+i).addClass('error');
				if (data.result[i].error) {			    
					Rfiles.toLog($('#sys_file_up_'+i).text()+': '+Rhelper.objToString(data.result[i].error,true),'error');
				} else {
					if (i=='error') $('#sys_panel .files div').addClass('error');
					Rfiles.toLog(Rhelper.objToString(data.result[i],true),'error');
				}
		    }
		}
		$('#sys_panel .add_file .files .progress_all').css({background:'#d4ff82'}).html('загрузка файлов завершена');
	    },
	    fail: function (e,data) {
		$('#sys_panel .add_file .files .progress_all').css({background:'#d4ff82'}).html('загрузка файлов завершилась неудачей');
		Rfiles.toLog(Rhelper.objToString(data.result),'error');
	    },
	    always:function(e, data) {
		Rfiles.loadFiles(Rfiles.currentFolder);
	    },
	    start:function(){
		Rfiles.toLog('Начата загрузка файлов');
		$('#sys_panel .add_file .files').html('');
		$('#sys_panel .add_file .files').append('<sapn class="progress_all"></span>');
		for (i in Rfiles.tempFiles) {
		    $('#sys_panel .add_file .files').append(
			'<div id="sys_file_up_'+i+'">'+
			    '<strong>'+Rfiles.tempFiles[i].name+' ('+parseInt(Rfiles.tempFiles[i].size/1024)+'Kb)</strong>'+
			'</div>'
		    );
		}
	    },
	    progressall: function (e, data) {
		var progress = parseInt(data.loaded / data.total * 100, 10);
		$('#sys_panel .add_file .files .progress_all').css({
		    width:progress+'%'
		})
	    },
	    change:function (e, data) {
		Rfiles.tempFiles={};
		$.each(data.files, function (index, file) {
		    Rfiles.toLog('Выбран файл: '+file.name);
		    Rfiles.tempFiles[index]={};
		    Rfiles.tempFiles[index].name=file.name;
		    Rfiles.tempFiles[index].size=file.size;
		});
	    }
	});
    },
    selectFolder: function(id) {
	this.folders[id].selected=true;
	$('#sys_fld_'+id).addClass('selected');
    },
    unselectFolder: function(id) {
	this.folders[id].selected=false;
	$('#sys_fld_'+id).removeClass('selected');
    },
	selectSubFolder: function(id) {
	this.subFolders[id].selected=true;
	$('#sys_folder_'+id).addClass('selected');
    },
    unselectSubFolder: function(id) {
	this.subFolders[id].selected=false;
	$('#sys_folder_'+id).removeClass('selected');
    },
    initFoldersBtn:function () {
	$('#sys_folders .list').html('');	
	for (i in this.foldersSort) {
	    id=this.foldersSort[i];
	    $('#sys_folders .list').append('<div id="sys_fld_'+id+'" class="folder"><span class="check"></span><a href="#'+id+'" title="'+$('<div/>').text(this.folders[id].title).html()+'">'+this.folders[id].title+'</a></div>');
	}
	
	$('#sys_folders .list .folder .check').click(function(){
	    if ($(this).parents('.folder:first').hasClass('selected')) {Rfiles.unselectFolder($(this).parents('.folder:first').attr('id').replace(/[^0-9]*/g,''));}
	    else {Rfiles.selectFolder($(this).parents('.folder:first').attr('id').replace(/[^0-9]*/g,''));}
	});
	$('#sys_folders .list .folder a').click(function(){
	    Rfiles.setCurrentFolder($(this).parents('.folder:first').attr('id').replace(/[^0-9]*/g,''));
	    return false;
	});
	$('#sys_folders .panel .sys_iloader').html('');
    },
			
	initSubFoldersBtn:function () {
	$('#sys_files .list .subfolders').html('');
	for (i in this.subFoldersSort) {		
	    id=this.subFoldersSort[i];
	    $('#sys_files .list .subfolders').append('<div id="sys_folder_'+id+'" class="folder '+
		(this.subFolders[id].up?'up':'')+'"><span class="check"></span><a href="#" title="'+
		$('<div/>').text(this.subFolders[id].title).html()+'">'+$('<div/>').text(this.subFolders[id].title).html()+
	    '</a></div>');
	}
	$('#sys_files .panel .sys_iloader').html('');
	$('#sys_files .list .folder .check').click(function(){
	    if ($(this).parents('.folder:first').hasClass('selected')) {Rfiles.unselectSubFolder($(this).parents('.folder:first').attr('id').replace(/[^0-9]*/g,''));}
	    else {Rfiles.selectSubFolder($(this).parents('.folder:first').attr('id').replace(/[^0-9]*/g,''));}
	});
	$('#sys_files .list .folder a').click(function(){
		var id=$(this).parents('.folder:first').attr('id').replace(/[^0-9]*/g,'');
		if (Rfiles.subFolders[id].up) { 
			//console.log(Rfiles.currentPath);
			var temp=Rfiles.currentPath[Rfiles.currentPath.length-2];
			Rfiles.loadFolders(temp?temp.parent_id:0);			
			Rfiles.currentPath.splice(Rfiles.currentPath.length-1,1);
			//console.log(Rfiles.currentPath);
		}
		else {
			Rfiles.currentPath.push(Rfiles.subFolders[id]);
			//console.log(Rfiles.currentPath);
			//Rfiles.upFolder=Rfiles.folders[Rfiles.subFolders[id].parent_id].parent_id;
			Rfiles.loadFolders(Rfiles.subFolders[id].parent_id);
		}
		return false;
	});
    },
    genFolderTitle: function(title,n) {
	if (n && n>20) return title;
	if (title=='') title='Новая папка';
	n=n==undefined?1:n;
	for (i in Rfiles.folders) {
	    if (Rfiles.folders[i].title==title) {
		title='Новая папка ('+n+')';
		title=Rfiles.genFolderTitle(title, n+1);
	    }
	}
	return title;
    },
	initFolderParents:function(){
		$('#sys_folder_parents_list').html('<option value="">--нет--</option>');
		//console.log(Rfiles.folders);
		for (i in Rfiles.folders) {
			$('#sys_folder_parents_list').append('<option value="'+Rfiles.folders[i].id+'">'+Rfiles.folders[i].title+'</option>');
		}
	},
    createFolder : function(form) {
	var title=$(form).find('input[name="Folder[title]"]').val();
	if (title=='')
	    $(form).find('input[name="Folder[title]"]').val(Rfiles.genFolderTitle(title));
	$('#sys_folders .panel .sys_iloader').html('<img src="img/loader.gif">');
	Rfiles.toLog('Отправлена команда создания папки.');
	$.post(
	    encodeURI(Rfiles.getUrl('createFolder')),
	    $(form).serialize(),
	    function(data){
		if (data.success)
		    Rfiles.toLog('Папка успешно создана.','success');
		else {
		    if (data.error) {
			Rfiles.toLog('ОШИБКА создания папки: '+Rhelper.objToString(data.error, true));
		    }
		    else {
			Rfiles.toLog('ОШИБКА создания папки: неизвестная ошибка.');
		    }			
		}
	    }, 
	    'json'
	).
	fail(function(xqr){Rfiles.toLog('ОШИБКА создания папки: ошибка ответа сервера.'+xqr.responseText);}).
	always(function(){$('#sys_folders .panel .sys_iloader').html(''); Rfiles.loadFolders();});
	$(form).find('input[name="Collection[title]"]').val('');
	$(form).find('input[name="Collection[description]"]').val('');
    },
    deleteFolders:function() {
	if (Rfiles.temp && Rfiles.temp.del_id) {
	    alert('В настоящий момент уже начато удаление! Необходимо дождаться окончания операции.');
	}
	else
	    Rfiles.temp={};
	for (i in Rfiles.folders) {
	    if (Rfiles.folders[i].selected) {
			Rfiles.deleteFolder(Rfiles.folders[i],0);
			return;
	    }
	}
	for (i in Rfiles.subFolders) {
	    if (Rfiles.subFolders[i].selected) {
			Rfiles.deleteFolder(Rfiles.subFolders[i],1);
			return;
	    }
	}
	Rfiles.loadFolders();
    },
    deleteFolder:function(folder,sub) {
		Rfiles.temp.del_log='Удаление папки "'+$('<div/>').text(folder.title).html()+'": ';
		Rfiles.temp.del_id=i;
		$('#sys_folders .panel .sys_iloader').html('<img src="img/loader.gif">');
		data={id:i};
		$.ajax({
			url:this.getUrl('deleteFolder',{id:folder.id}),
			data:Rfiles.rules.additionalFormsParams,
			type:'post',
			tempSub:sub,
		}).done(function(data){			
			if (this.tempSub)
				Rfiles.unselectSubFolder(Rfiles.temp.del_id);
			else
				Rfiles.unselectFolder(Rfiles.temp.del_id);
		    if (!data || !data.error) {
			Rfiles.toLog(Rfiles.temp.del_log+' УСПЕШНО '+Rhelper.objToString(data,true));
			$('#sys_fld_'+Rfiles.temp.del_id).remove();
		    } else {
			Rfiles.toLog(Rfiles.temp.del_log+' ОШИБКА '+Rhelper.objToString(data,true));			
		    }
		    Rfiles.temp=null;
		    $('#sys_folders .panel .sys_iloader').html('');
		    Rfiles.deleteFolders();
		}).
		fail(function(){
		    Rfiles.toLog(Rfiles.temp.del_log+' ОШИБКА ответа сервера');	
		    $('#sys_folders .panel .sys_iloader').html('');
		    Rfiles.temp=null;
		});
		return;
	},
    
    loadFiles: function(id) {
	Rfiles.unsetCurrentFile(Rfiles.currentFile);
	$('#sys_files .panel .sys_iloader').html('<img src="img/loader.gif">');
	$.getJSON(this.getUrl('getFiles',{id:id}), function(data) {
	    Rfiles.initFiles(data);
	});
    },
    initFiles : function (data) {
	Rfiles.files={}; Rfiles.filesSort=[];
	if (data) {
	    for (i=0; i<data.length; i++) {
		    Rfiles.files[data[i].id]=data[i];
		    Rfiles.files[data[i].id].selected=false;
		    Rfiles.files[data[i].id].current=false;
		    ext=data[i].file;
		    ext=ext.substr(ext.lastIndexOf('.')+1);
		    Rfiles.files[data[i].id].ext=ext;
		    Rfiles.filesSort.push(data[i].id)
	    }
	}
	this.initFilesBtn();
    },
    initFilesBtn:function () {
	$('#sys_files .list .files_in').html('');
	for (i in this.filesSort) {
	    id=this.filesSort[i];
	    $('#sys_files .list .files_in').append('<div id="sys_file_'+id+'" class="file '+Rfiles.getFileType(id)+
		'"><span class="check"></span><a href="#" title="'+
		$('<div/>').text(this.files[id].title).html()+'">'+'<span></span>'
		+$('<div/>').text(this.files[id].title+'.'+this.files[id].ext).html()+
	    '</a></div>');
	}
	$('#sys_files .panel .sys_iloader').html('');
	$('#sys_files .list .file .check').click(function(){
	    if ($(this).parents('.file:first').hasClass('selected')) {Rfiles.unselectFile($(this).parents('.file:first').attr('id').replace(/[^0-9]*/g,''));}
	    else {Rfiles.selectFile($(this).parents('.file:first').attr('id').replace(/[^0-9]*/g,''));}
	});
	$('#sys_files .list .file a').click(function(){
	    Rfiles.setCurrentFile($(this).parents('.file:first').attr('id').replace(/[^0-9]*/g,'')); return false;
	});
    },
    selectFile: function(id) {
	this.files[id].selected=true;
	$('#sys_file_'+id).addClass('selected');
    },
    unselectFile: function(id) {
	if (id!=0 && this.files[id]) {
	    this.files[id].selected=false;
	    $('#sys_info .info').html('');
	    $('#sys_file_'+id).removeClass('selected');
	}
    },
    setCurrentFile: function(id) {
	$('#sys_files .list .file').removeClass('current');
	$('#sys_file_'+id).addClass('current');
	Rfiles.currentFile=id;
	Rinsert.init();
    },
    unsetCurrentFile:function(id) {
	$('#sys_files .list .file').removeClass('current');
	Rfiles.currentFile=0;
	$('#sys_info .info').html('');
	Rinsert.init();
    },
    getFileType:function(id) {
	if ($.inArray(this.files[id].ext.toLowerCase(), this.types.image)!=-1) return 'image';
	if ($.inArray(this.files[id].ext.toLowerCase(), this.types.document)!=-1) return 'document';
	if ($.inArray(this.files[id].ext.toLowerCase(), this.types.table)!=-1) return 'table';
	if ($.inArray(this.files[id].ext.toLowerCase(), this.types.media)!=-1) return 'media';
	if ($.inArray(this.files[id].ext.toLowerCase(), this.types.flash)!=-1) return 'flash';
	return 'default'
    },
    deleteFiles:function() {
	if (Rfiles.temp && Rfiles.temp.del_id) {
	    alert('В настоящий момент уже начато удаление! Необходимо дождаться окончание операции.');
	}
	else
	    Rfiles.temp={};
	for (i in Rfiles.files) {
	    if (Rfiles.files[i].selected) {
		Rfiles.temp.del_log='Удаление файла "'+$('<div/>').text(this.files[i].title).html()+'": ';
		Rfiles.temp.del_id=i;
		$('#sys_files .panel .sys_iloader').html('<img src="img/loader.gif">');
		$.post(this.getUrl('deleteFile',{id:i}), Rfiles.rules.additionalFormsParams, function(data){
		    //Rfiles.toLog(temp+Rhelper.objToString(data));
		    Rfiles.unselectFile(Rfiles.temp.del_id);
		    if (!data || data.success) {
			Rfiles.toLog(Rfiles.temp.del_log+' УСПЕШНО ');
			$('#sys_file_'+Rfiles.temp.del_id).remove();
		    } else {
			if (data.error)
			    Rfiles.toLog(Rfiles.temp.del_log+' ОШИБКА '+Rhelper.objToString(data,true));
			else
			    Rfiles.toLog(Rfiles.temp.del_log+' Состояние неизвестно');
		    }
		    Rfiles.temp=null;
		    $('#sys_files .panel .sys_iloader').html('');
		    Rfiles.deleteFiles();
		}, 'json').fail(function(){
		    Rfiles.toLog(Rfiles.temp.del_log+' ОШИБКА ответа сервера');	
		    $('#sys_files .panel .sys_iloader').html('');
		    Rfiles.temp=null;
		});
		return;
	    }
	}
	Rfiles.loadFiles(Rfiles.currentFolder);
    },
    updateFile: function(form) {
	$('#sys_files .panel .sys_iloader').html('<img src="img/loader.gif">');
	$.post(Rfiles.getUrl('updateFile', {id:Rfiles.currentFile}), $(form).serialize(), function(data){
	    if (data.error) {
		Rfiles.toLog('ОШИБКА смены имени файла: '+Rhelper.objToString(data.error, true));	
	    }
	    else {
		Rfiles.toLog('Операция смены имени файла прошла успешно','success');	
		Rfiles.loadFiles(Rfiles.currentFolder);
	    }
	    $('#sys_files .panel .sys_iloader').html('');
	}, 'json').fail(function(){
		    Rfiles.toLog('ОШИБКА ответа сервера');	
		    $('#sys_files .panel .sys_iloader').html('');
		}).always(function(){
		    Rhelper.windowClose('#sys_file_update');
		});
    },
    initFileForm:function() {
	if (Rfiles.currentFile>0) {$('#sys_file_update').find('input[name="File[title]"]').val(Rfiles.files[Rfiles.currentFile].title); return true;}
	else {alert('Файл не выбран!'); return false;}
    },
    
    del:function() {
	    f='';
	    for (i in Rfiles.files) {
		if (Rfiles.files[i].selected) {
		    f+=$('<div/>').text(Rfiles.files[i].title).html()+'<br>';
		}
	    }
	    if (f) {
		$('#sys_panel .del .form .files .list').html(f);
		$('#sys_panel .del .form .files').show();
	    } else $('#sys_panel .del .form .files').hide();
	    fld='';
	    for (i in Rfiles.folders) {
		if (Rfiles.folders[i].selected) {
		    fld+=$('<div/>').text(Rfiles.folders[i].title).html()+'<br>';
		}
	    }
	    if (fld) {
		$('#sys_panel .del .form .folders .list').html(fld).show();
		$('#sys_panel .del .form .folders').show();
	    } else $('#sys_panel .del .form .folders').hide();
		
		sfld='';
	    for (i in Rfiles.subFolders) {
		if (Rfiles.subFolders[i].selected) {
		    sfld+=$('<div/>').text(Rfiles.subFolders[i].title).html()+'<br>';
		}
	    }
	    if (sfld) {
			if (fld)
				$('#sys_panel .del .form .folders .list').append(sfld).show();
			else
				$('#sys_panel .del .form .folders .list').html(sfld).show();
		$('#sys_panel .del .form .folders').show();
	    }
	    
    },
    
    toLog: function(text,type) {
	clearInterval(Rfiles.logInterval);
	if ($('#sys_log .line').length>50) {$('#sys_log .line:last').remove();}
	
	type=type?' '+type:'';
	if (!type && text.indexOf('ОШИБКА')!==-1) type=' error'; 
	if (!type && text.indexOf('УСПЕШНО')!==-1) type=' success';
	
	$('#sys_log').html("<div class='line"+type+"'>"+$('<div/>').text(text).html()+"</div>"+$('#sys_log').html());
	$('#sys_log').fadeIn();
	$('#sys_panel>div.log').addClass('active');
	Rfiles.logInterval=setInterval(function(){
	    $('#sys_log').fadeOut(); clearInterval(Rfiles.logInterval);
	    $('#sys_panel>div.log').removeClass('active');
	},10000);
    }
};

Rinsert={
    init:function() {
	id=Rfiles.currentFile;
	if (Rfiles.currentFile==0) {
	    $('#sys_info .insert').hide();
	    $('#sys_info .info').hide();
	}
	else {
		//console.log(Rfiles.img_tmb_allowed);
	    $('#sys_info .insert').show();
	    $('#sys_info .info').show();
	    var type=Rfiles.getFileType(Rfiles.currentFile);
	    $('#sys_info .actions a').hide();
	    $('#sys_info .actions a.default').show();
	    $('#sys_info .actions a.'+type).show();
	    $('#sys_info .options a').hide();
	    $('#sys_info .options a.default').show();
	    $('#sys_info .options a.'+type).show();
	    $('#sys_info .options').show();
	    $('#sys_info .actions').show();	    
	    var str='';
	    if (type=='image') {
                $('#rfiles #sys_images_options .sizes').html('');
                console.log(Rfiles.thumbnails.sizes[Rfiles.thumbnails.thumbnail[0]][Rfiles.thumbnails.thumbnail[1]]);
                str+='<img src="'+Rfiles.getThumbnail(
                        Rfiles.files[id].file, 
                        Rfiles.thumbnails.thumbnail[0],
                        Rfiles.thumbnails.sizes[Rfiles.thumbnails.thumbnail[0]][Rfiles.thumbnails.thumbnail[1]]
                )+'">';
                for (var i in Rfiles.thumbnails.sizes) {                            
                    $('#rfiles #sys_images_options .sizes').append(
                            '<div class="tmp_img_tmb tmp_img_tmb_'+i+'">'+
                            '<div class="example"><div><img src="'+Rfiles.getThumbnail(Rfiles.files[id].file,i,Rfiles.thumbnails.sizes[i][0])+'"></div></div>'+
                            '</div>'
                    );                            
                }
                var res=[];
                var temp='';
                for (var i in Rfiles.thumbnails.sizes) {
                    for (var j in Rfiles.thumbnails.sizes[i]) {
                        temp=Rfiles.thumbnails.sizes[i][j].replace(/\D/,'x');
                        $('#rfiles #sys_images_options .sizes .tmp_img_tmb_'+i).append(
                                '<div><a href="#" onclick="Rinsert.insert(\'image\',\''+i+'\',\''+Rfiles.thumbnails.sizes[i][j]+'\'); return false;">'+temp+'</a></div>'
                        );
                    }
                }
            }
	    str+='<div class="title"><strong>Имя:</strong> '+Rfiles.files[id].title+' ('+Rfiles.files[id].ext+')</div>';
	    str+='<div class="filename"><strong>Файл:</strong> '+Rfiles.files[id].file+'</div>';
	    if (Rfiles.files[id].params && Rfiles.files[id].params.info ) {
		if (Rfiles.files[id].params.info.Size) str+='<div class="fsize"><strong>Размер:</strong> '+Math.round(parseInt(Rfiles.files[id].params.info.Size)/1024)+' Kb</div>';
		if (Rfiles.files[id].params.info.Dimensions) str+='<div class="dimensions"><strong>Ширина x Высота:</strong> '+Rfiles.files[id].params.info.Dimensions.width+'x'+Rfiles.files[id].params.info.Dimensions.height+'</div>';
	    }
	    $('#sys_info .info').html(str);
	}
    },
    insert:function(type,tmb_type,tmb_size){
	var file=Rfiles.files[Rfiles.currentFile];
	var str='';
        var url='';
	switch (type) {
	    case 'image':
		var src=file.file;
		if (tmb_type) {		    
		   src=Rfiles.getThumbnail(src, tmb_type, tmb_size);
		}
		var dop='';
		if ($('#sys_info .options .titled').hasClass('selected') && file.params && file.params.info) {
		    dop=' captionable'
		}
		if (tmb_type && $('#sys_info .options .zoom').hasClass('selected'))
		    str='<a href="'+Rfiles.base_url+file.file+'" target="_blank" class="img-zoom'+dop+'">'+
		    '<img src="'+Rfiles.base_url+src+'" alt="'+$('<div/>').text(file.title).html()+'">'+
		    '</a>';
		else
		    str='<img src="'+Rfiles.base_url+src+'" alt="'+$('<div/>').text(file.title).html()+'">';
                url=Rfiles.base_url+src;
		break;
	    case 'flash':
		str="<img height='200' width='200' title='src="+
		    encodeURIComponent(Rfiles.base_url+file.file)+
		    "&flashvars=' class='sys_media' _mce_src='/images/flash_icon.jpg' src='/images/flash_icon.jpg' _moz_resizing='true'>";
                url=Rfiles.base_url+file.file;
		break;
	    case 'media':
		var url=document.location+'';
		url=url.substr(0,url.indexOf("/",url.indexOf("//")+2));
		str='<video style="backgroud:#000; border:1px solid #CCC; border-radius:3px; padding:3px; overflow:hidden;" controls="controls" width="200" height="200">';
		str+='<source src="'+url+'/'+file.file+'">';
		str+='</video>';
                url=Rfiles.base_url+file.file;
		break;
	    default:
		str='<a href="'+Rfiles.base_url+file.file+'">'+$('<div/>').text(file.title).html()+'</a>';
		if ($('#sys_info .options .titled').hasClass('selected') && file.params && file.params.info) {
		    str+=' ('+file.ext+' '+Math.round(parseInt(file.params.info.Size)/1024)+' Kb)';
		}
                url=Rfiles.base_url+file.file;
		break;
		
	}
        var wparams=Rfiles.editor.windowManager.getParams();
        if (typeof wparams.callback === "function") {
            wparams.callback(url);
        } 
        else {
            Rfiles.editor.execCommand('mceInsertContent', false, ' '+str+' ', {skip_undo : 1});
        }
    },
    
	showImageTmb:function(el){
		$('#sys_images_options').show();
		$('#sys_images_options').css({
			bottom:$(el).position().top,
			left:$(el).position().left
		});
	}
}

Rfiles.init(top.tinymce.activeEditor);

$(document).ready(function(){
   $('#sys_panel>div.btn>a.act').click(function() {
		if ($(this).parent('.btn').hasClass('add_file')) {
		   if (Rfiles.currentFolder<=0) {
			   alert('Сначала нужно выбрать папку!'+"\n"+'Если папок нет - создайте ее нажав на кнопку [добавить папку].');
			   return false;
		   }
		}
		if ($(this).parent('.btn').hasClass('add_folder')) {
			Rfiles.initFolderParents();
		}
		$('#sys_log').hide();
		els=$(this).parent('.btn').nextAll('div.btn'); 
		$(els).each(function(){
			$(this).find('.form:first').hide(); 
			$(this).removeClass('active');
		});
		els=$(this).parent('.btn').prevAll('div.btn'); 
		$(els).each(function(){
			$(this).find('.form:first').hide(); 
			$(this).removeClass('active');
		});
       
       
	$(this).parent('.btn').find('.form:first').toggle(); 
	$(this).parent('.btn').toggleClass('active');
       
       if ($(this).parent('.btn').hasClass('log')) {
	   if ($(this).parent('.btn').hasClass('active')) $('#sys_log').show();
	   else  $('#sys_log').hide();
       }
       
       if ($(this).parent('.btn').hasClass('del')) {
	   if ($(this).parent('.btn').hasClass('active')) Rfiles.del(false);
       }
       
       
       
       return false;
    }
   );
});