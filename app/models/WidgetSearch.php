<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Widget;

/**
 * WidgetSearch represents the model behind the search form about `app\models\Widget`.
 */
class WidgetSearch extends Widget
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['widget_id', 'sort_order'], 'integer'],
            [['type', 'title', 'content', 'position', 'options', 'allow', 'deny', 'created_at', 'updated_at'], 'safe'],
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
        $query = Widget::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'widget_id' => $this->widget_id,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'options', $this->options])
            ->andFilterWhere(['like', 'allow', $this->allow])
            ->andFilterWhere(['like', 'deny', $this->deny]);

        return $dataProvider;
    }
}
