<?php

namespace app\modules\post\models;

use Yii;
use app\modules\post\Module;
use yii\data\ActiveDataProvider;
use app\modules\post\models\Post;

/**
 * PostSearch represents the model behind the search form about `app\modules\post\models\Post`.
 */
class PostSearch extends Post {

    public $_created_at;
    public $_date_start;
    public $_date_end;

    const SCENARIO_USER='user';
    const SCENARIO_OWNER='owner';
    const SCENARIO_ADMIN='admin';

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['post_id', 'author_id', 'status'], 'integer'],
            [['uid', 'title', 'h1', 'description', 'content', 'keywords'], 'string'],
            [['_created_at','_date_start','_date_end'], 'date', 'format'=>Yii::$app->getModule('post')->dateControl['display'][Module::FORMAT_DATE]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        return [
            self::SCENARIO_USER=>['author_id','_created_at','_date_start','_date_end'],
            self::SCENARIO_OWNER=>['_created_at','status','title','uid'],
            self::SCENARIO_ADMIN=>['author_id','_created_at'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Post::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->sort->attributes['_created_at']=[
            'asc' => [self::tableName().'.created_date' => SORT_ASC, self::tableName().'.created_time'=>SORT_ASC],
            'desc' => [self::tableName().'.created_date' => SORT_DESC, self::tableName().'.created_time'=>SORT_DESC],
        ];

        // grid filtering conditions
        $query->andFilterWhere([
            'post_id' => $this->post_id,
            'author_id' => $this->author_id,
            'status' => $this->status,
            'updated_at' => $this->updated_at,
        ]);

        if ($this->_created_at) {
            $query->andFilterWhere([
                'created_date' => Yii::$app->formatter->asDate($this->_created_at, Yii::$app->getModule('post')->dateControl['save'][Module::FORMAT_DATE])
            ]);
        }

        if ($this->_date_start) {
            $query->andFilterWhere([
                '>=', 'created_date', Yii::$app->formatter->asDate($this->_date_start, Yii::$app->getModule('post')->dateControl['save'][Module::FORMAT_DATE])
            ]);
        }

        if ($this->_date_end) {
            $query->andFilterWhere([
                '<=', 'created_date', Yii::$app->formatter->asDate($this->_date_end, Yii::$app->getModule('post')->dateControl['save'][Module::FORMAT_DATE])
            ]);
        }

        $query->andFilterWhere(['like', 'uid', $this->uid])
                ->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'h1', $this->h1])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'content', $this->content])
                ->andFilterWhere(['like', 'keywords', $this->keywords]);

        return $dataProvider;
    }

}
