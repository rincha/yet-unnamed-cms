var pluginStyle={
    el:null,
    getHexRGBColor:function (color) {
      color = color.replace(/\s/g,"");
      var aRGB = color.match(/^rgb\((\d{1,3}[%]?),(\d{1,3}[%]?),(\d{1,3}[%]?)\)$/i);

      if(aRGB)
      {
        color = '';
        for (var i=1;  i<=3; i++) color += Math.round((aRGB[i][aRGB[i].length-1]=="%"?2.55:1)*parseInt(aRGB[i])).toString(16).replace(/^(.)$/,'0$1');
      }
      else color = color.replace(/^#?([\da-f])([\da-f])([\da-f])$/i, '$1$1$2$2$3$3');
      return '#'+color;
    },
    initForm:function(){
        var tmp_style, tmp_style_arr={}, tmp;
        if ($(top.tinymce.activeEditor.selection.getNode()).attr('style')) {
            tmp_style=$(top.tinymce.activeEditor.selection.getNode()).attr('style');
        } 
        else
            tmp_style='';

        $('.el-type').text(top.tinymce.activeEditor.selection.getNode().nodeName);

        $('#stylelist').val(tmp_style.replace(' ',"").replace(';',"; "))
        $('#classlist').val($(top.tinymce.activeEditor.selection.getNode()).prop('class'));
        
        tmp_style=tmp_style.replace(' ',"").split(';');
        
        for (var i=0; i<tmp_style.length; i++) {
            tmp=tmp_style[i].split(':');
            tmp[0]=$.trim(tmp[0]);
            tmp[1]=$.trim(tmp[1]);
            
            if (tmp[0]==='margin' || tmp[0]==='padding') {
                var tmp_val=tmp[1].split(' ');
                if (tmp_val.length===4) {
                    tmp_style.push(tmp[0]+'-top:'+tmp_val[0]);
                    tmp_style.push(tmp[0]+'-right:'+tmp_val[1]);
                    tmp_style.push(tmp[0]+'-bottom:'+tmp_val[2]);
                    tmp_style.push(tmp[0]+'-left:'+tmp_val[3]);
                }
                if (tmp_val.length===1) {
                    tmp_style.push(tmp[0]+'-top:'+tmp_val[0]);
                    tmp_style.push(tmp[0]+'-right:'+tmp_val[0]);
                    tmp_style.push(tmp[0]+'-bottom:'+tmp_val[0]);
                    tmp_style.push(tmp[0]+'-left:'+tmp_val[0]);
                }
                if (tmp_val.length===2) {
                    tmp_style.push(tmp[0]+'-top:'+tmp_val[0]);
                    tmp_style.push(tmp[0]+'-right:'+tmp_val[1]);
                    tmp_style.push(tmp[0]+'-bottom:'+tmp_val[0]);
                    tmp_style.push(tmp[0]+'-left:'+tmp_val[1]);
                }
                if (tmp_val.length===3) {
                    tmp_style.push(tmp[0]+'-top:'+tmp_val[0]);
                    tmp_style.push(tmp[0]+'-right:'+tmp_val[1]);
                    tmp_style.push(tmp[0]+'-bottom:'+tmp_val[2]);
                    tmp_style.push(tmp[0]+'-left:'+tmp_val[1]);
                }
            }
            else if (tmp[0]==='border') { 
                //console.log(tmp[0]+'|'+tmp[1]);
                var tmp_val=tmp[1].split(' ',3);
                tmp_style.push(tmp[0]+'-top-width:'+tmp_val[0]);
                tmp_style.push(tmp[0]+'-top-style:'+tmp_val[1]);
                tmp_style.push(tmp[0]+'-top-color:'+tmp_val[2]);
                
                tmp_style.push(tmp[0]+'-right-width:'+tmp_val[0]);
                tmp_style.push(tmp[0]+'-right-style:'+tmp_val[1]);
                tmp_style.push(tmp[0]+'-right-color:'+tmp_val[2]);
                
                tmp_style.push(tmp[0]+'-bottom-width:'+tmp_val[0]);
                tmp_style.push(tmp[0]+'-bottom-style:'+tmp_val[1]);
                tmp_style.push(tmp[0]+'-bottom-color:'+tmp_val[2]);
                
                tmp_style.push(tmp[0]+'-left-width:'+tmp_val[0]);
                tmp_style.push(tmp[0]+'-left-style:'+tmp_val[1]);
                tmp_style.push(tmp[0]+'-left-color:'+tmp_val[2]);
            }
            else if (tmp[0].search(/^border-(top|left|right|bottom)$/i)!==-1) {
                var tmp_val=tmp[1].split(' ',3);
                //console.log(tmp[0]+'|'+tmp[1]);
                tmp_style.push(tmp[0]+'-width:'+tmp_val[0]);
                tmp_style.push(tmp[0]+'-style:'+tmp_val[1]);
                tmp_style.push(tmp[0]+'-color:'+tmp_val[2]);
                
            }
            else if (tmp[0].search(/^border-(width|style|color)$/i)!==-1) {
                var tmp_key=tmp[0].split('-');               
                //console.log(tmp[0]+'|'+tmp[1]);
                tmp_style.push(tmp_key[0]+'-top-'+tmp_key[1]+':'+tmp[1]);
                tmp_style.push(tmp_key[0]+'-right-'+tmp_key[1]+':'+tmp[1]);
                tmp_style.push(tmp_key[0]+'-bottom-'+tmp_key[1]+':'+tmp[1]);
                tmp_style.push(tmp_key[0]+'-left-'+tmp_key[1]+':'+tmp[1]);
            }
            var el=$('form *[name="'+tmp[0]+'"]');
            if (el.length) {
               var u='', v='';
               if ($(el).hasClass('style-unit')) {
                   u=tmp[1].replace(/\d/ig,'');
                   v=tmp[1].replace(/\D/ig,'');                   
                   $(el).parents('.style-group:first').find('.unit').val(u);
                   $(el).val(v);
               }
               else if ($(el).hasClass('minicolors') && tmp[1].indexOf('rgb')!==-1) {
                   $(el).val(pluginStyle.getHexRGBColor(tmp[1]));
               }
               else {
                   $(el).val(tmp[1]);
               }
            }
        }
        $('input.minicolors').minicolors({
            theme: 'bootstrap',
            position: 'bottom right', 
            format:'hex',
            changeDelay:500,
            change:function(){
                //var val=$(this).val();
                //console.log($(this).val()); 
                //if (val.length==)
                pluginStyle.styleChange(this);
            }
        });
        var classes=[];
        //console.log(top.tinymce.activeEditor.settings.allowed_classes);
        if (top.tinymce.activeEditor.settings.allowed_classes)
        for (i in top.tinymce.activeEditor.settings.allowed_classes) {
            if (i==='*' || i.toUpperCase===top.tinymce.activeEditor.selection.getNode().nodeName) {
                var line=top.tinymce.activeEditor.settings.allowed_classes[i].split(' ');
                for (var j in line) {
                    if (classes.indexOf(line[j])===-1 && line[j]) {
                        classes.push(line[j]);
                        $('#classlist_allowed').append('<option value="'+line[j]+'">'+line[j]+'</option>');
                    }
                }
            }
        }
        $('#class_add').click(function(){
            $('#classlist').val($('#classlist').val()+' '+$('#classlist_allowed').val());
            return false;
        });
    },
    cssToJsName:function(name){
        var jsname=name;
        if (jsname.indexOf('-')!==-1) {
            var temp=jsname.split('-');
            for (var i=1; i<temp.length; i++) {
                var first=temp[i].charAt(0).toUpperCase();
                var part=temp[i].substring(1,temp[i].length);                
                temp[i]=first+part;
            }
            jsname=temp.join('');
        }
        return jsname;
    },
    styleChange:function(el){        
        var name=$(el).attr('name');
        var jsname=name;
        var u='';
        var v=$(el).val();
        if ($(el).hasClass('style-unit')) {
            u=$(el).parents('.style-group:first').find('.unit').val();
        }
        jsname=pluginStyle.cssToJsName(name);
        var data={};
        if (v)
            data[jsname]=v+u;
        else
            data[jsname]='';
        //console.log(data);
        $(pluginStyle.el).css(data);
        $('#stylelist').val($(pluginStyle.el).attr('style'));
    },
    apply:function(){
        top.tinymce.activeEditor.undoManager.add();
        
        top.tinymce.activeEditor.dom.setAttrib(top.tinymce.activeEditor.selection.getNode(), 'class', $('#classlist').val());
        top.tinymce.activeEditor.dom.setAttrib(top.tinymce.activeEditor.selection.getNode(), 'style', $('#stylelist').val());

        top.tinymce.activeEditor.windowManager.close();
    },
    close:function(){
        top.tinymce.activeEditor.windowManager.close();
    }
};

$(document).ready(function(){
    $('.tab').hide();
    $('.tabs a').click(function(){
        var c=$(this).attr('href');
        c=c.substring(1,c.length);
        $('.tab').hide();
        $('#tab-'+c).show();
        return false;
    });
    
    pluginStyle.el=$(top.tinymce.activeEditor.selection.getNode()).clone();
    
    pluginStyle.initForm();
    
    $('form .style, form .style-unit').change(function(){
        pluginStyle.styleChange(this);
    });
    
    $('form .unit').change(function(){
       $(this).parents('.style-group:first').find('.style, style-unit').change();
    });
            
    $('form').submit(function(){
        return false;
    });
    
    $('#apply').click(function(){
        pluginStyle.apply();
    });
    
    $('#close').click(function(){
        pluginStyle.close();
    });
});
    