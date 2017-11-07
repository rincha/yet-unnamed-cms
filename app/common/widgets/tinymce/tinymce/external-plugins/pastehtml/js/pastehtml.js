Array.prototype.in_array = function(p_val) {
	    for(var i = 0, l = this.length; i < l; i++)  {
	        if(this[i] == p_val) {
	            return true;
	        }
	    }
	    return false;
	};
var PasteHTMLDialog = {
	init : function(ed) {
		var el = document.getElementById('iframecontainer'), ifr, doc, css, cssHTML = '';

		// Create iframe
		el.innerHTML = '<iframe id="iframe" src="javascript:\'\';" frameBorder="0" style="border: 1px solid gray; width:100%; height:300px"></iframe>';
		ifr = document.getElementById('iframe');
		doc = ifr.contentWindow.document;

		// Force absolute CSS urls
		css = [ed.baseURI.toAbsolute("themes/" + ed.settings.theme + "/skins/" + ed.settings.skin + "/content.css")];
		/*css = css.concat(tinymce.explode(ed.settings.content_css) || []);
		tinymce.each(css, function(u) {
			cssHTML += '<link href="' + ed.documentBaseURI.toAbsolute('' + u) + '" rel="stylesheet" type="text/css" />';
		});*/

		// Write content into iframe
		doc.open();
		doc.write('<html><head>' + cssHTML + '</head><body class="mceContentBody" spellcheck="false"></body></html>');
		doc.close();

		doc.designMode = 'on';
		this.resize();

		window.setTimeout(function() {
			ifr.contentWindow.focus();
		}, 10);
	},

        str_replace: function (search, replace, subject) {
		return subject.split(search).join(replace);
	},

        minhtml : function () {
            var end=true;
            var min_tags=Array('B','I','STRONG','EM','H1','H2','H3','H4','TABLE','TR','TD','TBODY','P','BR');
                    a=$('iframe#iframe').contents().find('body');
                    a=$(a).find('*');
                    
                    $(a).each(function (index, el) {
                        if (!min_tags.in_array(el.tagName)) {
                            $(el).replaceWith($(el).html());
                            end=false;
                        }
                    });
            if (!end) {PasteHTMLDialog.minhtml();}
        },

         midhtml : function () {
            var end=true;
           var mid_tags=Array('B','I','STRONG','EM','H1','H2','H3','H4','TABLE','TR','TD','TBODY','P','BR','A','IMG','HR','UL','OL','LI');
                    a=$('iframe#iframe').contents().find('body');
                    a=$(a).find('*');
                    $(a).each(function (index, el) {
                        if (!mid_tags.in_array(el.tagName)) {
                            //alert(el.tagName);
                            $(el).replaceWith($(el).html());
                            end=false;
                        }
                    });
            if (!end) {PasteHTMLDialog.midhtml();}
        },

        convertdiv : function () {
           var end=true;
                    a=$('iframe#iframe').contents().find('body');
                    div=$(a).find('div');
                    $(div).each(function (index, el) {
                        $(el).replaceWith('<p>'+$(el).html()+'</p>');
                        end=false;
                    });
            if (!end) {PasteHTMLDialog.convertspan();}
        },

        convertspan : function () {
           var end=true;
                    a=$('iframe#iframe').contents().find('body');
                    span=$(a).find('span');
                    $(span).each(function (index, el) {
                        if ($(el).css('font-weigtht')=='bold') {
                            $(el).replaceWith('<strong>'+$(el).html()+'</strong>');
                            end=false;
                        }
                        else {
                           if ($(el).css('font-style')=='italic') {
                            $(el).replaceWith('<em>'+$(el).html()+'</em>');
                            end=false;
                            }
                        }
                    });
            if (!end) {PasteHTMLDialog.convertspan();}
        },

        startclear : function() {
            //alert('clearing');
            var a;
            var temp;
            var mes=Array();
            var normal_attr={
                A: Array('href','target','name','title','style'),
                IMG: Array('src','alt','title','style'),
                P: Array('align','style'),
                TABLE: Array('border','style'),
                TD: Array('align','colspan','rowspan')
            };
            var true_empty_tags=Array('BR','IMG','HR');
            PasteHTMLDialog.convertspan();

            if ($('#min_html').prop('checked')) {
                PasteHTMLDialog.convertspan();
                 PasteHTMLDialog.convertdiv();
                mes.push('Очистка до минимального форматирования');
                PasteHTMLDialog.minhtml();
             }

             if ($('#mid_html').prop('checked')) {
                 //alert('mid');
                 PasteHTMLDialog.convertspan();
                 PasteHTMLDialog.convertdiv();
                 mes.push('Очистка до основного форматирования');
                 PasteHTMLDialog.midhtml();
             }

            a=$('iframe#iframe').contents().find('body');
            temp=$(a).html();
            while (temp.indexOf('  ')>1) {
                temp=PasteHTMLDialog.str_replace('  ', ' ', temp);
            }
            while (temp.indexOf('&nbsp;&nbsp;')>1) {
                temp=PasteHTMLDialog.str_replace('&nbsp;&nbsp;', ' ', temp);
            }
            $(a).html(temp);

            if ($('#del_empty').prop('checked')) {
            a=$(a).find('*');
            $(a).each(function (index, el) {
                if ((!true_empty_tags.in_array(el.tagName))&&($(el).html()=='')) {
                    $(el).replaceWith('');
                }
            });
            $(a).each(function (index, el) {
                if ($(el).html()==' ') {
                    $(el).replaceWith(' ');
                }
            });
            }


             if ($('#del_a').prop('checked')) {
                    a=$('iframe#iframe').contents().find('body');
                    a=$(a).find('a');
                    mes.push(''+$(a).length+' ссылок удалено');
                    $(a).each(function (index, el) {$(el).replaceWith($(el).html());});
             }
             
             if ($('#del_p_in_td').prop('checked')) {
                    a=$('iframe#iframe').contents().find('body');
                    a=$(a).find('td > p');
                    mes.push(''+$(a).length+' абзацев в таблицах');
                    $(a).each(function (index, el) {$(el).replaceWith($(el).html()+'<br>');});
             }

             if ($('#del_img').prop('checked')) {
                    a=$('iframe#iframe').contents().find('body');
                    a=$(a).find('img');
                    mes.push(''+$(a).length+' изображений удалено');
                    $(a).each(function (index, el) {$(el).replaceWith('');});
             }
             
                if ($('#a_blank').prop('checked')) {
                    a=$('iframe#iframe').contents().find('body');
                    a=$(a).find('a');
                    mes.push('Для '+$(a).length+' ссылок тобавлен атрибут [открыть в новом окне]');
                    $(a).each(function (index, el) {$(el).attr('target','_blank');});
                }
                if ($('#clear_style').prop('checked')) {
                    a=$('iframe#iframe').contents().find('body');
                    a=$(a).find('*');
                    mes.push('Стили отчищены');
                    $(a).each(function (index, el) {$(el).removeAttr('style');});
                }
                
                if ($('#clear_attr').prop('checked')) {
                    a=$('iframe#iframe').contents().find('body');
                    a=$(a).find('*');
                    mes.push('Аттрибуты очищены');
                    $(a).each(function (index, el) {
                        var flag=true;
                        var flagin=true;
                        var n=0;
                        while (flag) {
                            flag=false;
                            n=0;
                            while (flagin) {
                                if ((el.attributes.length>0) && (n<el.attributes.length)) {
                                    if ((normal_attr[el.tagName]==undefined) || !normal_attr[el.tagName].in_array(el.attributes[n].name)) {
                                        el.removeAttribute(el.attributes[n].name) ;
                                        flag=true;
                                        n=0;
                                    }
                                    else {
                                        n++;
                                    }
                                }
                                else {
                                    flagin=false;

                                }
                                
                            }
                        }
                        
                    });
                }
                temp='';
                for (i=0;i<mes.length;i++) {
                    temp=temp+mes[i]+'<br>';
                }
                $('#message').html(temp);
		$('#message').show();
	},

	insert : function() {
            var ed = top.tinymce.activeEditor;
            var temp=$('iframe#iframe').contents().find('body');
            ed.execCommand('mceInsertContent', false, $(temp).html(), {skip_undo : 1});
	},

	resize : function() {
		/*var vp = tinyMCEPopup.dom.getViewPort(window), el;

		el = document.getElementById('content');

		el.style.width  = (vp.w - 20) + 'px';
		el.style.height = (vp.h - 90) + 'px';*/
	}
};
$(document).ready(function(){PasteHTMLDialog.init(top.tinymce.activeEditor);});
//top.tinymce.activeEditor.onInit.add(PasteHTMLDialog.init, PasteHTMLDialog);
