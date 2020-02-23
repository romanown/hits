<?php

/**
 * @property int $id
 * @property string $date_create
 * @property string $name
 *

 */
class ApiHits extends CActiveRecord
{
    public function tableName()
    {
        return 'api_hits';
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['created', 'safe'],
        ];
    }

    public function relations()
    {
        return [
           
        ];
    }
			// 'apiHitsText' => [self::HAS_MANY, 'ApiHitsText', 'hits_id'],
			//'apiHitsImage' => [self::HAS_MANY, 'ApiHitsImage', 'hits_id'],
    /**
     * @param string $className active record class name.
     * @return \ApiHits the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getHitsListByHitsIds($hitsIds)
    {
        sort($hitsIds);
        return ParadCache::registry(__METHOD__, implode('_', $hitsIds), function () use ($hitsIds) {
            $result = [];

            /** @var ApiHits[] $models */
            $models = ApiHits::model()->findAllByPk($hitsIds);

            foreach ($models as $model) {
                $result[] = $model->getArrayForCache();
            }

            return $result;
        });
    }

    public function getArrayForCache()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->getUrl(),
            'imageUrls' => $this->getImageUrls(),
        ];
    }

    public function getUrl()
    {
        return '/hits/index.php?id=' . $this->id;
    }

    public function getImageUrls()
    {
        /** @var BFile[] $fileModels */
        $fileModels = BFile::model()->findAllByAttributes([
            'owner_id' => $this->id,
            'owner_type' => 'hits',
            'owner_type_prop' => 'hits_photo',
        ]);

        $imageUrls = [];
        foreach ($fileModels as $fileModel) {
            $imageUrls[] = $fileModel->getUrl();
        }
        return $imageUrls;
    }

    private static function getIdByName($name)
    {
        $command = Yii::app()->db->createCommand("SELECT id FROM api_hits WHERE name=:name");
        $command->bindValue(':name', $name);
        return $command->queryScalar();
    }
	
	public static function getIdByUrl($pageurl)
    {
        $command = Yii::app()->db->createCommand("SELECT id FROM api_hits WHERE pageurl=:pageurl");
        $command->bindValue(':pageurl', $pageurl);
        return $command->queryScalar();
    }

	public function toArray()
    {
        $bFiles = BFile::model()->findAllByAttributes(['owner_id' => $this->id, 'owner_type' => 'hits', 'owner_type_prop' => 'hits_photo']);

        $photos = [];
        foreach ($bFiles as $bFile) {
            $photos[] = [
                'url' => $bFile->getResizedPhotoUrl('very_huge'),
                'id' => $bFile->ID,
            ];
        }

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'is_active' => $this->is_active,
            'text' => $this->text,
            'url' => '/hits/?id='.$this->id,
            'city' => $this->city,
            'title' => $this->title,
			'h1' => $this->h1,
            'contacts' => $this->contacts,
            'description' => $this->description,
            'photos' => $photos,
        ];

        $data['site_url'] = Yii::app()->params['frontendUrl'] . $data['url'];

        return $data;
    }
}
