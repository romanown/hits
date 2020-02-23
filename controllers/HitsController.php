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
        $this->render('index', ['isOnlyProspective' => false]);
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
        if (isset($_POST['id'])) {
            $model = ApiHits::model()->findByPk($_POST['id']);
        } else {
            $model = new ApiHits();
        }
        $model->name = isset($_POST['name']) ? $_POST['name'] : '';
        $model->is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 0;
        $model->city = isset($_POST['city']) ? $_POST['city'] : '';
        $model->address = isset($_POST['address']) ? $_POST['address'] : '';
        $model->work_time = isset($_POST['work_time']) ? $_POST['work_time'] : '';
        $model->contacts = isset($_POST['contacts']) ? $_POST['contacts'] : '';
        $model->save();

        $model->refresh();

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

        $bFile->owner_type = 'cert';
        $bFile->owner_type_prop = 'cert_photo';
        $bFile->owner_id = $model->id;

        $bFile->save();

        $model->refresh();

        header('Content-Type: application/json');
        echo json_encode($model->toArray());
    }
}
