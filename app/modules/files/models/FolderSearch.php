<?php

namespace app\modules\files\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\files\models\Folder;

/**
 * FolderSearch represents the model behind the search form about `app\modules\files\models\Folder`.
 */
class FolderSearch extends Folder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['folder_id'], 'integer'],
            [['name', 'description', 'type', 'special'], 'safe'],
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
        $query = Folder::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

		$query->where(['parent_id'=>$this->parent_id]);
		
        $query->andFilterWhere([
            'folder_id' => $this->folder_id,        
        ]);	

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'special', $this->special]);

        return $dataProvider;
    }
}
