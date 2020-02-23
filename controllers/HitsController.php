<?php

class HitsController extends Controller
{
    public function filters()
    {
        return ['accessControl'];
    }

    public function accessRules()
    {
        return [
            ['allow',
                'roles' => ['catalog', 'stylist', 'marketer', 'operator'],
            ],
            ['allow',
                'actions' => ['hits'],
                'roles' => ['stylist', 'marketer'],
            ],
            ['deny',
                'users' => ['*'],
            ],
        ];
    }

    public function actionIndex()
    {
        $this->render('index', ['isOnly' => false]);
    }
	
	public function actionHits()
    {
        $this->render('hits', ['isOnly' => false]);
    }
	
    public function actionList()
    {
        /** @var \ApiHits[] $models */
        $models = ApiHits::model()->findAll();

        $result = [];
        foreach ($models as $model) {
            $result[] = $model->toArray();
        }

        header('Content-Type: application/json');
        echo json_encode([
            'items' => $result,
        ]);
    }

    public function actionDelete()
    {
        ApiHits::model()->deleteByPk($_POST['id']);
    }

    public function actionView($id)
    {
        $model = ApiHits::model()->findByPk($id);
        header('Content-Type: application/json');
        echo json_encode($model->toArray());
    }

    public function actionSave()
    {
        if (isset($_POST['id']) && !is_null($_POST['id'])&& !empty($_POST['id'])) {
            $model = ApiHits::model()->findByPk($_POST['id']);
        } else {
            $model = new ApiHits();
        }
        $model->name = isset($_POST['name']) ? $_POST['name'] : 'NewHit';
		//$model->url = isset($_POST['url']) ? $_POST['url'] : '';
        $model->is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 0;
        $model->city = isset($_POST['city']) ? $_POST['city'] : '';
        $model->text = isset($_POST['text']) ? $_POST['text'] : '';
		$model->title = isset($_POST['title']) ? $_POST['title'] : '';
		$model->h1 = isset($_POST['h1']) ? $_POST['h1'] : '';
        $model->description = isset($_POST['description']) ? $_POST['description'] : '';
        $model->contacts = isset($_POST['contacts']) ? $_POST['contacts'] : '';
        $model->save();

        $model->refresh();

        header('Content-Type: application/json');
        echo json_encode($model->toArray());
    }
	
	public function actionCreate()
    {
        if (isset($_POST['id'])) {
            $model = ApiHits::model()->findByPk($_POST['id']);
        } else {
            $model = new ApiHits();
        }
        $model->name = isset($_POST['name']) ? $_POST['name'] : '';
		//$model->url = isset($_POST['url']) ? $_POST['url'] : '';
        $model->is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 0;
        $model->city = isset($_POST['city']) ? $_POST['city'] : '';
        $model->text = isset($_POST['text']) ? $_POST['text'] : '';
		$model->title = isset($_POST['title']) ? $_POST['title'] : '';
		$model->h1 = isset($_POST['description']) ? $_POST['h1'] : '';
        $model->description = isset($_POST['description']) ? $_POST['description'] : '';
        $model->contacts = isset($_POST['contacts']) ? $_POST['contacts'] : '';

        header('Content-Type: application/json');
        echo json_encode($model->toArray());
    }

    public function actionDeletePhoto()
    {
        if (!isset($_POST['id'])) {
            throw new CHttpException(500);
        }
        $model = ApiHits::model()->findByPk($_POST['id']);

        if (!isset($_POST['file_id'])) {
            throw new CHttpException(500);
        }
        $bFile = BFile::model()->findByPk($_POST['file_id']);

        $bFile->delete();

        header('Content-Type: application/json');
        echo json_encode($model->toArray());
    }

    public function actionUpload()
    {
        if (!isset($_POST['id'])) {
            throw new CHttpException(500);
        }
        $model = ApiHits::model()->findByPk($_POST['id']);

        if (isset($_POST['file_id'])) {
            $bFile = BFile::model()->findByPk($_POST['file_id']);
        } else {
            $bFile = null;
        }

        $bFile = BFile::model()->createAndSaveByUploadFileInfo($_FILES['file'], $bFile);

        $bFile->owner_type = 'hits';
        $bFile->owner_type_prop = 'hits_photo';
        $bFile->owner_id = $model->id;

        $bFile->save();

        $model->refresh();

        header('Content-Type: application/json');
        echo json_encode($model->toArray());
    }


}
