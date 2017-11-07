<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserSearch represents the model behind the search form about `app\modules\user\models\User`.
 */
class UserSearch extends User {

    public $_authentication;

    public $_auth_assigment_role;

    public function scenarios() {
        return [
            'default' => ['username', 'status', 'created_at', '_authentication','_auth_assigment_role'],
            'none' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = [
            [['id', 'status'], 'integer'],
            [['username', 'password', 'reset_token', 'auth_key', 'created_at', 'updated_at', '_authentication'], 'safe'],
        ];
        return $rules;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = User::find()->joinWith(['authentications']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->sort->attributes['_authentication'] = [
            'asc' => [UserAuthentication::tableName() . '.uid' => SORT_ASC],
            'desc' => [UserAuthentication::tableName() . '.uid' => SORT_DESC],
        ];

        if ($this->_authentication) {
            $a_parts = explode(':', $this->_authentication);
            if (count($a_parts) == 1)
                $query->andFilterWhere(['like', UserAuthentication::tableName() . '.uid', $this->_authentication]);
            elseif (count($a_parts) == 2) {
                if ($a_parts[0])
                    $query->andFilterWhere(['like', UserAuthentication::tableName() . '.type', $account_parts[0]]);
                if ($a_parts[1])
                    $query->andFilterWhere(['like', UserAuthentication::tableName() . '.uid', $account_parts[1]]);
            }
        }

        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            self::tableName() . '.status' => $this->status,
            self::tableName() . '.created_at' => $this->created_at,
            self::tableName() . '.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', self::tableName() . '.username', $this->username])
                ->andFilterWhere(['like', self::tableName() . '.password', $this->password])
                ->andFilterWhere(['like', self::tableName() . '.auth_key', $this->auth_key]);



        if ($this->_auth_assigment_role) {
            $query->joinWith('authItems ai')
                   ->andWhere(['ai.name'=>$this->_auth_assigment_role]);
        }

        return $dataProvider;
    }

}
