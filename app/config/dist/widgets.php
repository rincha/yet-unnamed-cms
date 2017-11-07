<?php
return [
    'items'=>[
        'menu'=>[
            'class'=>'app\modules\menu\widgets\SiteWgtMenu',
            'name'=>'Меню',
            'hasContent'=>false,
        ],
        'html'=>[
            'class'=>'app\common\widgets\SiteWgtHtml',
            'name'=>'HTML',
        ],
        'bxSlider'=>[
            'class'=>'app\common\widgets\SiteBxSlider\SiteBxSlider',
            'name'=>'Слайдер',
            'hasContent'=>false,
        ],
        'news'=>[
            'class'=>'app\modules\news\widgets\SiteWgtNews',
            'name'=>'Последние новости',
            'hasContent'=>false,
        ],
        'info'=>[
            'class'=>'app\modules\info\widgets\SiteWgtInfo',
            'name'=>'Список последних материалов по типу',
            'hasContent'=>false,
        ],
        'infoSlaves'=>[
            'class'=>'app\modules\info\widgets\SiteWgtInfoSlaves',
            'name'=>'Список подчиненных материалов по типу связи',
            'hasContent'=>false,
        ],
        'banner'=>[
            'class'=>'app\modules\banner\widgets\SiteWgtBanner',
            'name'=>'Баннер',
            'hasContent'=>false,
        ],
    ],
    'positions'=>[
        'none'=>'',
        'beforeHeader'=>'Before header',
        'header'=>'In header',
        'afterHeader'=>'After header',
        'left'=>'Left sidebar',
        'top'=>'Top content',
        'bottom'=>'Bottom content',
        'beforeFooter'=>'Before footer',
        'footer'=>'In footer',
        'end'=>'End of site',
    ],
];