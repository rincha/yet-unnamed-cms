<?php

namespace app\modules\post\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\common\helpers\AppHelper;
use app\models\User;
use app\modules\post\Module;

/**
 * This is the model class for table "{{%post_comment}}".
 *
 * @property integer $comment_id
 * @property integer $post_id
 * @property string $author_nickname
 * @property string $author_email
 * @property integer $author_id
 * @property integer $branch_id
 * @property integer $parent_id
 * @property integer $status
 * @property string $content
 * @property string $created_date
 * @property string $created_time
 * @property string $updated_at
 *
 * @property string $statusText
 * @property boolean $hasBranch
 * @property boolean $hasParent
 *
 * @property User $author
 * @property Post $post
 * @property Comment $branchParent
 * @property Comment[] $branch
 * @property Comment $parent
 * @property Comment[] $childs
 */
class Comment extends \yii\db\ActiveRecord {

    const STATUS_NEW=1;
    const STATUS_PUBLISHED=2;
    const STATUS_APPROVED=3;
    const STATUS_ARCHIVED=4;

    const MAX_DEPTH=10;

    const SCENARIO_GUEST='guest';
    const SCENARIO_USER='user';
    const SCENARIO_POST_OWNER='post_owner';
    const SCENARIO_ADMIN='admin';

    public $verifyCode;

    //for agregate query
    public $_has_branch;
    public $_has_parent;
    public $_has_childs;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%post_comment}}';
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        $res=[
            self::SCENARIO_GUEST=>['author_nickname', 'author_email', 'content', 'verifyCode'],
            self::SCENARIO_USER=>['content'],
            self::SCENARIO_POST_OWNER=>['status','parent_id'],
            self::SCENARIO_ADMIN=>['status','author_nickname','author_email','content','parent_id'],
        ];
        if (Yii::$app->getModule('post')->commentAnswerPermit==Module::ANSWER_PERMIT_GUEST) {
            $res[self::SCENARIO_GUEST][]='parent_id';
            $res[self::SCENARIO_USER][]='parent_id';
        }
        elseif (Yii::$app->getModule('post')->commentAnswerPermit==Module::ANSWER_PERMIT_USER) {
            $res[self::SCENARIO_USER][]='parent_id';
        }
        return $res;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['author_nickname', 'content','verifyCode'], 'required'],
            [['verifyCode'], 'captcha', 'captchaAction' => 'site/captcha'],
            [['author_id', 'branch_id', 'parent_id','post_id','status'], 'integer'],
            [['content'], 'string', 'max'=>32*1024],
            [['content'], '_vHtml'],
            [['author_nickname'], 'string', 'max' => 64],
            [['author_email'], 'string', 'max' => 255],
            [['author_email'], 'email'],
            [['status'], 'in', 'range'=> array_keys($this->getStatusList())],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'post_id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comment::className(), 'targetAttribute' => ['parent_id' => 'comment_id']],
            [['parent_id'], '_vBranch'],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comment::className(), 'targetAttribute' => ['branch_id' => 'comment_id']],
        ];
    }

    public function _vHtml($attribute) {
        $this->{$attribute}= AppHelper::htmlPurify($this->{$attribute}, 'simple_links', [
            'HTML.AllowedAttributes'=>'*.style,*.class,a.href,a.name,a.target',
        ]);
    }

    public function _vBranch() {
        if ($this->parent_id && !$this->hasErrors('parent_id')) {
            if ($this->parent->post_id!=$this->post_id) {
                $this->addError('parent_id', Yii::t('post', 'The comment to which your answer points indicates belongs to another article.'));
                return;
            }
            //assign a branch with a root message
            $this->branch_id=$this->parent_id;
            //
            $model=$this->parent; $n=0;
            //select the top message in the branch
            while ($model && $model->parent_id && $n<self::MAX_DEPTH) {
                $this->branch_id=$model->parent_id;
                $model= Comment::findOne($model->parent_id);
                $n++;
            }
            if ($n>=self::MAX_DEPTH) {
                $this->addError('reply_id', Yii::t('post','You can not reply to a message with more than {max} nesting.',['max'=>self::MAX_DEPTH]));
            }
        }
        else {
            $this->branch_id=null;
        }
    }

    public function behaviors() {
        return [
            TimestampBehavior::className() => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_time', 'updated_at', 'created_date'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'comment_id' => Yii::t('post', 'ID'),
            'author_nickname' => Yii::t('post', 'Nickname'),
            'author_email' => Yii::t('post', 'Email'),
            'author_id' => Yii::t('post', 'Author'),
            'branch_id' => Yii::t('post', 'Branch'),
            'parent_id' => Yii::t('post', 'Parent'),
            'content' => Yii::t('post', 'Content'),
            'created_date' => Yii::t('post', 'Created date'),
            'created_time' => Yii::t('post', 'Created time'),
            'updated_at' => Yii::t('post', 'Updated time'),
            'verifyCode' => Yii::t('post', 'Verify code'),
        ];
    }

    /**
     * @return array
     */
    public static function getStatusList() {
        return [
            self::STATUS_NEW => Yii::t('post', 'new'),
            self::STATUS_PUBLISHED => Yii::t('post', 'published'),
            self::STATUS_APPROVED => Yii::t('post', 'approved'),
            self::STATUS_ARCHIVED => Yii::t('post', 'archived'),
        ];
    }

    /**
     * @return string
     */
    public function getStatusText() {
        return ArrayHelper::getValue(self::getStatusList(), $this->status);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor() {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Comment::className(), ['branch_id' => 'comment_id'])
                ->alias('branch')
                ->orderBy(['branch.created_date'=>SORT_ASC, 'branch.created_time'=>SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranchParent() {
        return $this->hasOne(Comment::className(), ['comment_id' => 'branch_id'])
                ->alias('branchParent');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent() {
        return $this->hasOne(Comment::className(), ['comment_id' => 'parent_id'])
                ->alias('parent');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChilds() {
        return $this->hasMany(Comment::className(), ['parent_id' => 'comment_id'])
                ->orderBy(['childs.created_date'=>SORT_ASC, 'childs.created_time'=>SORT_ASC])
                ->alias('childs');;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost() {
        return $this->hasOne(Post::className(), ['post_id' => 'post_id']);
    }

    /**
     * @return boolean
     */
    public function getHasParent() {
        if ($this->_has_parent===null) {
            $this->_has_parent=$this->getParent()->exists();
        }
        return $this->_has_parent?true:false;
    }

    /**
     * @return boolean
     */
    public function getHasBranch() {
        if ($this->_has_branch===null) {
            die($this->comment_id.'!');
            $this->_has_branch=$this->getBranch()->exists();
        }
        return $this->_has_branch;
    }

    /**
     * @return boolean
     */
    public function getHasChilds() {
        if ($this->_has_childs===null) {
            $this->_has_childs=$this->getChilds()->exists();
        }
        return $this->_has_childs;
    }

    public static function queryAddHasBranch(\yii\db\ActiveQuery $query, $alias=null) {
        $alias=$alias?$alias:self::tableName();
        if (!$query->select) {
            $query->addSelect($alias.'.*');
        }
        $query->addSelect(new Expression('IF(tmp_branch.comment_id,1,0) as _has_branch'));
        $query->leftJoin(self::tableName().' tmp_branch','tmp_branch.branch_id='.$alias.'.comment_id');
    }

}
