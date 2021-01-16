<?php
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\User;
use app\models\Session;
use yii\filters\VerbFilter;
use App\Factory\ApiResponseDataFactory;

use yii\filters\ContentNegotiator;
use yii\web\Controller;

use yii\web\Response;
/**
 * Site controller
 */

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'doc' => [
                'class' => 'light\swagger\SwaggerAction',
                'restUrl' => \yii\helpers\Url::to(['/site/api'], true),
            ],            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                'scanDir' => [
                    Yii::getAlias('@app/modules/v1/controllers'),
                    Yii::getAlias('@app/models'),
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->redirect(["site/doc"]);
    }
}