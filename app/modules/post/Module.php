<?php

namespace app\modules\post;

use Yii;
use app\modules\post\models\Post;
use app\modules\post\models\Comment;
use app\modules\post\models\SettingsForm;

/**
 * post module definition class
 */
class Module extends \yii\base\Module {

    public $migrations = true;

    public $migrationsDemo = false;

    const FORMAT_DATE='date';
    const FORMAT_TIME='time';
    const FORMAT_DATETIME='datetime';

    const ANSWER_PERMIT_GUEST=2;
    const ANSWER_PERMIT_USER=4;
    const ANSWER_PERMIT_AUTHOR=8;

    const ROLE_USER='Authenticated';
    const ROLE_AUTHOR='PostAuthor';
    const ROLE_ADMIN='PostAdmin';

    public $dateControl=[
        'display'=>[
            self::FORMAT_DATE=>'php:d.m.Y',
            self::FORMAT_TIME=>'php:H:i:s',
            self::FORMAT_DATETIME=>'d.m.Y H:i:s',
        ],
        'save'=>[
            self::FORMAT_DATE=>'php:Y-m-d',
            self::FORMAT_TIME=>'php:H:i:s',
            self::FORMAT_DATETIME=>'php:Y-m-d H:i:s',
        ],
    ];

    public $displayStatus=[
        Post::STATUS_NEW,
        Post::STATUS_APPROVED,
    ];

    public $commentGuestStatus=Comment::STATUS_NEW;
    public $commentUserStatus=Comment::STATUS_PUBLISHED;
    public $commentDisplayStatus=[
        Comment::STATUS_PUBLISHED,
        Comment::STATUS_APPROVED,
    ];

    public $commentAnswerPermit=self::ANSWER_PERMIT_GUEST;

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\post\controllers';

    /**
     * @inheritdoc
     */
    public $layout = '@app/modules/post/views/layouts/post';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        if (Yii::$app instanceof \yii\web\Application) {
            $settings=new SettingsForm();
            foreach ($this->getCustomzbleProperties() as $attr) {
                if ($settings->{$attr}!==null) {
                    $this->{$attr}=$settings->{$attr};
                }
            }
        }
    }

    public function getCustomzbleProperties() {
        return [
            'module.post.display.status'=>'displayStatus',
            'module.post.comment.guest.status'=>'commentGuestStatus',
            'module.post.comment.user.status'=>'commentUserStatus',
            'module.post.comment.display.status'=>'commentDisplayStatus',
            'module.post.comment.answer.permit'=>'commentAnswerPermit',
        ];
    }

}
