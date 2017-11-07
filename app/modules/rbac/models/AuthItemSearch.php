<?php

namespace app\modules\rbac\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\rbac\models\AuthItem;
use app\helpers\SearchHelper;

/**
 * UserSearch represents the model behind the search form about `app\modules\user\models\User`.
 */
class AuthItemSearch extends AuthItem {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['type'], 'integer'],
            [['name', 'type', 'rule_name', 'created_at', 'updated_at',], 'safe', 'on' => 'search'],
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
        $this->scenario = 'search';

        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'name',
                'type',
                'rule_name',
                'created_at',
                'updated_at',
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['name' => $this->name]);
        $query->andFilterWhere(['type' => $this->type]);

        return $dataProvider;
    }

}
