<?php

namespace app\modules\info\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\info\models\Info;

/**
 * InfoSearch represents the model behind the search form about `app\modules\info\models\Info`.
 */
class InfoSearch extends Info
{
     public $typeName;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['info_id', 'type_id'], 'integer'],
            [['uid', 'name', 'h1', 'meta_title', 'meta_description', 'keywords',
                'content', 'images', 'params', 'date', 'created_at', 'updated_at', 'typeName'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = Info::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->defaultOrder=[
                'created_at'=>SORT_DESC,
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'info_id' => $this->info_id,
            'type_id' => $this->type_id,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'uid', $this->uid])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'h1', $this->h1])
            ->andFilterWhere(['like', 'meta_title', $this->meta_title])
            ->andFilterWhere(['like', 'meta_description', $this->meta_description])
            ->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'images', $this->images])
            ->andFilterWhere(['like', 'params', $this->params]);

        if ($this->typeName) {
            $query->joinWith('type');
            $query->andFilterWhere([Type::tableName().'.name'=>$this->typeName]);
        }

        return $dataProvider;
    }
}
