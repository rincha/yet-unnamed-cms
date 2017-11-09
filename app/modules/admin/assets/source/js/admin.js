var adminApi={
    setIframeHeight:function(height){
        $('iframe.tmp-auto-height').height(height+15);
    },
    iframeAutoHeightInit:function(){
        $(document).on('load click scroll ready',function(){
            window.top.adminApi.setIframeHeight($(document.body).height()+15);
        });        
        function onElementHeightChange(elm, callback){
            var lastHeight = elm.clientHeight, newHeight;
            (function run(){
                newHeight = elm.clientHeight;
                if( lastHeight != newHeight )
                    callback();
                lastHeight = newHeight;

                if( elm.onElementHeightChangeTimer )
                    clearTimeout(elm.onElementHeightChangeTimer);

                elm.onElementHeightChangeTimer = setTimeout(run, 200);
            })();
        }
        onElementHeightChange(document.body, function(){
            window.top.adminApi.setIframeHeight($(document.body).height()+15);
        });
    },
    translit: function(text) {
        var
        rus = "щ   ш  ч  ц  ю  я  ё  ж  ъ  ы  э  а б в г д е з и й к л м н о п р с т у ф х ь".split(/ +/g),
        eng = "shh sh ch cz yu ya yo zh `` y' e` a b v g d e z i j k l m n o p r s t u f x `".split(/ +/g)
        ;
        var x;
        for(x = 0; x < rus.length; x++) {
                text = text.split(rus[x]).join(eng[x]);
                text = text.split(rus[x].toUpperCase()).join(eng[x].toUpperCase());	
        }
        return text;        
    },
    safeStr:function(str,rep) {
        rep=typeof(rep)==='undefined'?'_':rep;
        str=adminApi.translit(str);
        return str.replace(/[^a-z0-9_]/gi,rep).replace(/__/gi,rep);
    }
};
jQuery(document).ready(function () {
    $('#adm-menu-maximize').click(function(){
        if ($(this).hasClass('adm-menu-minimized'))
            $('#adm-menu').animate({width:300,paddingRight:15},{
                complete:function(){
                    $('#adm-menu a > span.text').show();
                    $('#adm-menu .glyphicon-menu-right').hide();
                    $('#adm-menu-maximize').removeClass('adm-menu-minimized');
                }
            });
        else {
            $('#adm-menu').animate({width:14,paddingRight:0},{
                complete:function(){
                    $('#adm-menu a > span.text').hide();
                    $('#adm-menu .glyphicon-menu-right').show();
                    $('#adm-menu-maximize').addClass('adm-menu-minimized');
                }
            });
        }        
        return false;
    });    
});