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
class ApiController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    // 'Access-Control-Allow-Origin' => ['*', 'http://haikuwebapp.local.com:81','http://localhost:81'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => null,
                    'Access-Control-Max-Age' => 86400,
                    'Access-Control-Expose-Headers' => []
                ]

            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['GET'],
                    'login' => ['POST'],
                    'register' => ['POST'],
                    'list-session' => ['GET'],
                    'delete-session' => ['GET'],
                    'create-session' => ['POST'],
                    'update-session' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionListSession()
    {
        $sessions = Session::find()->select(['ID', 'name'])->all();
        $result = Yii::$app->apiFactory->createSuccessResponse($sessions, "Successfully Get List Session")->toArray();
        return $this->asJson($result);
    }

    public function actionDetailSession()
    {
        $request = Yii::$app->request;
        $id= $request->get('id');
        $sessions = Session::findOne([
            'ID' => $id
        ]);

        $user = $sessions->user;
        $result = Yii::$app->apiFactory->createSuccessResponse([
            'user' => $user,
            'session' => $sessions
        ], "Successfully Get Detail Session")->toArray();
        return $this->asJson($result);
    }

    public function actionLogin()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $email = $request->post('email');
        $password = $request->post('password');
        $user = User::findByEmail($email);
        if (!$user) {
            return $this->asJson(Yii::$app->apiFactory->createErrorResponse("No such user found")->toArray());
            
        }

        $valid = $user->validatePassword($password);

        if (!$valid) {
            return $this->asJson(Yii::$app->apiFactory->createErrorResponse("Incorect Password")->toArray());
        }

        
        $session->set('hasLogin', $user);

        return $this->asJson(Yii::$app->apiFactory->createSuccessResponse([$user], "Successfully Login" )->toArray());
        
    }

    public function actionCreateSession()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;

        if($session->has('hasLogin')){


            $user = User::findIdentity($request->post('user_id'));
            if (!$user) {
                return $this->asJson(Yii::$app->apiFactory->createErrorResponse("No such user found")->toArray());
                
            }

            $session = new Session();
            $session->userID = $request->post('user_id');
            $session->name = $request->post('name');
            $session->description = $request->post('description');
            $session->start = $request->post('start');
            $session->duration = $request->post('duration');

            if (!$session->save()) {
                    return $this->asJson(Yii::$app->apiFactory->createErrorResponse("Failed Save Session")->toArray());
            
            }else{

                return $this->asJson(Yii::$app->apiFactory->createSuccessResponse([$session], "Successfully Save Session")->toArray());
            }
        }else{
            return $this->asJson(Yii::$app->apiFactory->createErrorResponse("Failed to access, Please login")->toArray());
        }
    }

    public function actionUpdateSession()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;

        if($session->has('hasLogin')){


            $user = User::findIdentity($request->post('user_id'));
            if (!$user) {
                return $this->asJson(Yii::$app->apiFactory->createErrorResponse("No such user found")->toArray());
                
            }

            $session = Session::findOne([
                'ID' => $request->post('id')
            ]);

            if($user_id = $request->post('user_id')){
                $session->userID = $request->post('user_id');
            }

            if($name = $request->post('name')){
                $session->name = $request->post('name');
            }

            if($description = $request->post('description')){
                $session->description = $request->post('description');
            }

            if($start = $request->post('start')){
                $session->start = $request->post('start');
            }

            if($duration = $request->post('duration')){
                $session->duration = $request->post('duration');
            }

            if (!$session->save()) {
                    return $this->asJson(Yii::$app->apiFactory->createErrorResponse("Failed Update Session")->toArray());
            
            }else{

                return $this->asJson(Yii::$app->apiFactory->createSuccessResponse([$session], "Successfully Update Session")->toArray());
            }
        }else{
            return $this->asJson(Yii::$app->apiFactory->createErrorResponse("Failed to access, Please login")->toArray());
        }
    }

    public function actionDeleteSession()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;

        if($session->has('hasLogin')){

            $session = Session::deleteAll(['ID' => $request->get('id')]);

            if ($session) {
                    return $this->asJson(Yii::$app->apiFactory->createErrorResponse("Failed Delete Session")->toArray());
            
            }else{
                return $this->asJson(Yii::$app->apiFactory->createSuccessResponse([], "Successfully Delete Session")->toArray());
            }
        }else{
            return $this->asJson(Yii::$app->apiFactory->createErrorResponse("Failed to access, Please login")->toArray());
        }
    }

    public function actionRegister()
    {
        $request = Yii::$app->request;

        $user = new User();
        $user->name = $request->post('name');
        $user->email = $request->post('email');
        $user->setPassword($request->post('password'));

        if (!$user->save()) {
            return $this->asJson(Yii::$app->apiFactory->createErrorResponse("Failed Login")->toArray());
            
        }else{

            return $this->asJson(Yii::$app->apiFactory->createSuccessResponse([$user], "Successfully Login")->toArray());
        }
        
    }

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
        ];
    }

    public function actionLogout()
    {
        $session = Yii::$app->session;
        $session->remove('hasLogin');
        return $this->redirect(['api/session']);
    }
}