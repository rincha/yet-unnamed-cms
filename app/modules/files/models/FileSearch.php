<?php

namespace app\modules\files\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\files\models\File;

/**
 * FileSearch represents the model behind the search form about `app\modules\files\models\File`.
 */
class FileSearch extends File
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_id', 'folder_id'], 'integer'],
            [['name', 'description', 'info', 'type', 'special', 'created_at', 'updated_at'], 'safe'],
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
        $query = File::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
		
		$query->where(['folder_id'=>$this->folder_id]);

        $query->andFilterWhere([
            'file_id' => $this->file_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'special', $this->special]);

        return $dataProvider;
    }
}
