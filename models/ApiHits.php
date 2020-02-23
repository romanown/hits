<?php

/**
 * @property int $id
 * @property string $date_create
 * @property string $name
 *
 * @property ApiCertBrand[] $apiCertBrands
 */
class ApiHits extends CActiveRecord
{
    public function tableName()
    {
        return 'api_cert';
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['date_create', 'safe'],
        ];
    }

    public function relations()
    {
        return [
            'apiHitsText' => [self::HAS_MANY, 'ApiHitsText', 'hits_id'],
			'apiHitsImage' => [self::HAS_MANY, 'ApiHitsImage', 'hits_id'],
        ];
    }

    /**
     * @param string $className active record class name.
     * @return \ApiCert the static model class
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

    public function getIdsByBrandAndRootSectionCode($brand, $rootSectionCode)
    {
        $hitsIds = [];
        $hitsId = self::getIdByName($brand);
        if ($hitsId) {
            $hitsIds[] = $hitsId;
        }

        $brandLowered = trim(mb_strtolower($brand, 'UTF-8'));
        if ($rootSectionCode == 'shoes') {
            if (in_array($brandLowered, ['marc jacobs', 'nina ricci', 'chloe', 'jil sander', 'viktor & rolf', 'john gilliano'])) {
                $hitsIds[] = self::getIdByName("O.L.G.(обувь M Jacobs N Ricci J Sander)");
            }

            if (in_array($brandLowered, ['emillio pucci', 'marc by marc jacobs', 'christian lacroix', 'donna karan', 'kenzo', 'givenchy'])) {
                $hitsIds[] = self::getIdByName("Rossimoda ( M by M Jacobs Kenzo Givenchy)");
            }
        }

        return $hitsIds;
    }

    private static function getIdByName($name)
    {
        $command = Yii::app()->db->createCommand("SELECT id FROM api_hits WHERE name=:name");
        $command->bindValue(':name', $name);
        return $command->queryScalar();
    }

    public function getBrands()
    {
        $brandNames = [];
        if ($this->apiCertBrands) {
            foreach ($this->apiCertBrands as $apiCertBrand) {
                $brandNames[] = $apiCertBrand->getBrandName();
            }
        }
        return $brandNames;
    }

    public function getDataIndexedByBrandIds()
    {
        $data = [];
        if ($this->apiCertBrands) {
            foreach ($this->apiCertBrands as $apiCertBrand) {
                if (!isset($data[$apiCertBrand->brand_id])) {
                    $data[$apiCertBrand->brand_id] = [
                        'sections' => [],
                        'brand' => $apiCertBrand->brand,
                    ];
                }
                $data[$apiCertBrand->brand_id]['sections'][] = $apiCertBrand->rootSection;
            }
        }
        return $data;
    }
}
