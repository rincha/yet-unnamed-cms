<?php

namespace app\modules\post\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\post\models\Comment;

/**
 * CommentSearch represents the model behind the search form about `app\modules\post\models\Comment`.
 */
class CommentSearch extends Comment {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['comment_id', 'post_id', 'author_id'], 'integer'],
            [['author_nickname', 'author_email', 'content', 'created_date', 'created_time', 'updated_at'], 'safe'],
            [['branch_id', 'parent_id', 'status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Comment::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            self::tableName().'.comment_id' => $this->comment_id,
            self::tableName().'.post_id' => $this->post_id,
            self::tableName().'.author_id' => $this->author_id,
            self::tableName().'.status' => $this->status,
            self::tableName().'.created_date' => $this->created_date,
            self::tableName().'.created_time' => $this->created_time,
            self::tableName().'.updated_at' => $this->updated_at,
        ]);

        if ($this->branch_id===false) {
            $query->andWhere([self::tableName().'.branch_id'=>null]);
        }
        else {
            $query->andFilterWhere([self::tableName().'.branch_id' => $this->branch_id]);
        }

        if ($this->parent_id===false) {
            $query->andWhere([self::tableName().'.parent_id'=>null]);
        }
        else {
            $query->andFilterWhere([self::tableName().'.parent_id' => $this->parent_id]);
        }

        $query->andFilterWhere(['like', self::tableName().'.author_nickname', $this->author_nickname])
                ->andFilterWhere(['like', self::tableName().'.author_email', $this->author_email])
                ->andFilterWhere(['like', self::tableName().'.content', $this->content]);

        return $dataProvider;
    }

}
