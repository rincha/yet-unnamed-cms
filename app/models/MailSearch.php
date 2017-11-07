<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SettingsSearch represents the model behind the search form about `app\modules\settings\models\Settings`.
 */
class MailSearch extends Mail {

    public $_search_status;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['to', 'reply_to', 'html_body', 'created_date', '_search_status'], 'safe'],
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
        $query = Mail::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'created_date' => $this->created_date,
        ]);

        $query->andFilterWhere(['like', 'to', $this->to])
                ->andFilterWhere(['like', 'reply_to', $this->reply_to])
                ->andFilterWhere(['like', 'mailer', $this->mailer])
                ->andFilterWhere(['like', 'html_body', $this->html_body]);

        if ($this->_search_status!==null) {
            $query->andWhere(new \yii\db\Expression('(status&:status)=:status'),[':status'=> $this->_search_status]);
        }

        return $dataProvider;
    }

}
