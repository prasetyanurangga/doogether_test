<?php
namespace app\modules\v1\controllers;

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
 * Api controller
 */

/**
 * @SWG\Swagger(
 *     schemes={"http","https"},
 *     host="localhost:2004",
 *     basePath="/",
 *     @SWG\Info(
 *         version="1.0",
 *         title="Documetation Rest Api",
 *         termsOfService="",
 *         @SWG\Contact(
 *             email="angganurprasetya4@gmail.com"
 *         ),
 *     ),
 * )
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

    
    /**
    * @SWG\Post(
    *    path = "/register",
    *    tags = {"register"},
    *    operationId = "register",
    *    summary = "Регистрация устройства",
    *    description = "Регистрация устройства",
    *    produces = {"application/json"},
    *    consumes = {"application/json"},
    *    @SWG\Parameter(
    *          name="user",
    *          in="body",
    *          required=true,
    *          @SWG\Schema(ref="#/definitions/UserRegister"),
    *      ),
    *    @SWG\Response(
    *        response = 200, 
    *        description = "success",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=200),
    *             @SWG\Property(property="message", type="string", example="Successfully Register"),
    *             @SWG\Property(property="data", type="array",
    *               @SWG\Items(
    *                   type="object",
    *                   @SWG\Property(property="ID", type="integer", example=1),
    *                   @SWG\Property(property="name", type="string", example="Angga"),
    *                   @SWG\Property(property="email", type="string", example="angga@gmail.com"),
    *                   @SWG\Property(property="password", type="string", example="$2y$13$cZTgVLuME1/xYS5gUD7SC.955rI10CDdfqMsoV07hwkfFVV4WYEuS"),
    *                   @SWG\Property(property="created", type="datetime", example="2021-01-16 06:24:48"),
    *                   @SWG\Property(property="updated", type="datetime", example="2021-01-16 06:24:48"),
    *               )
    *             ),
    *        )
    *    ),
    *    @SWG\Response(
    *        response = 500, 
    *        description = "Failed",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=500),
    *             @SWG\Property(property="message", type="string", example="Failed Register"),
    *                
    *        )
    *    )
    *)
    * @throws HttpException
    */

    public function actionRegister()
    {
        $request = Yii::$app->request;

        $user = new User();
        $user->name = $request->post('name');
        $user->email = $request->post('email');
        $user->setPassword($request->post('password'));

        if (User::findByEmail($user->name)) {
            return $this->asJson(Yii::$app->apiFactory->createErrorResponse("Email has Register")->toArray());
            
        }

        if (!$user->save()) {
            return $this->asJson(Yii::$app->apiFactory->createErrorResponse("Failed Login")->toArray());
            
        }else{

            return $this->asJson(Yii::$app->apiFactory->createSuccessResponse([$user], "Successfully Register")->toArray());
        }
        
    }

    /**
    * @SWG\Post(
    *    path = "/login",
    *    tags = {"login"},
    *    operationId = "login",
    *    summary = "Регистрация устройства",
    *    description = "Регистрация устройства",
    *    produces = {"application/json"},
    *    consumes = {"application/json"},
    *    @SWG\Parameter(
    *          name="user",
    *          in="body",
    *          required=true,
    *          @SWG\Schema(ref="#/definitions/UserLogin"),
    *      ),
    *    @SWG\Response(
    *        response = 200, 
    *        description = "success",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=200),
    *             @SWG\Property(property="message", type="string", example="Successfully Login"),
    *             @SWG\Property(property="data", type="array",
    *               @SWG\Items(
    *                   type="object",
    *                   @SWG\Property(property="ID", type="integer", example=1),
    *                   @SWG\Property(property="name", type="string", example="Angga"),
    *                   @SWG\Property(property="email", type="string", example="angga@gmail.com"),
    *                   @SWG\Property(property="password", type="string", example="$2y$13$cZTgVLuME1/xYS5gUD7SC.955rI10CDdfqMsoV07hwkfFVV4WYEuS"),
    *                   @SWG\Property(property="created", type="datetime", example="2021-01-16 06:24:48"),
    *                   @SWG\Property(property="updated", type="datetime", example="2021-01-16 06:24:48"),
    *               )
    *             ),
    *        )
    *    ),
    *    @SWG\Response(
    *        response = 500, 
    *        description = "Failed",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=500),
    *             @SWG\Property(property="message", type="string", example="Failed Login"),
    *                
    *        )
    *    )
    *)
    * @throws HttpException
    */

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

    /**
    * @SWG\GET(
    *    path = "/session",
    *    tags = {"session"},
    *    operationId = "session",
    *    summary = "Регистрация устройства",
    *    description = "Регистрация устройства",
    *    produces = {"application/json"},
    *    consumes = {"application/json"},
    *    @SWG\Response(
    *        response = 200, 
    *        description = "success",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=200),
    *             @SWG\Property(property="message", type="string", example="Successfully Get List Session"),
    *             @SWG\Property(property="data", type="array",
    *               @SWG\Items(
    *                   type="object",
    *                   @SWG\Property(property="ID", type="integer", example=1),
    *                   @SWG\Property(property="name", type="string", example="First Session"),
    *               )
    *             ),
    *        )
    *    ),
    *    @SWG\Response(
    *        response = 500, 
    *        description = "Failed",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=500),
    *             @SWG\Property(property="message", type="string", example="Failed Get List Session"),
    *                
    *        )
    *    )
    *)
    * @throws HttpException
    */

    public function actionListSession()
    {
        $sessions = Session::find()->select(['ID', 'name'])->all();
        $result = Yii::$app->apiFactory->createSuccessResponse($sessions, "Successfully Get List Session")->toArray();
        return $this->asJson($result);
    }

    /**
    * @SWG\GET(
    *    path = "/session/detail/{id}",
    *    tags = {"session/detail/"},
    *    operationId = "session/detail",
    *    summary = "Регистрация устройства",
    *    description = "Регистрация устройства",
    *    produces = {"application/json"},
    *    consumes = {"application/json"},
    *    @SWG\Parameter(
    *     name="id",
    *     in="path",
    *     type="string",
    *     description="Description goes here"
    *    ),
    *    @SWG\Response(
    *        response = 200, 
    *        description = "success",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=200),
    *             @SWG\Property(property="message", type="string", example="Successfully Get Detail Session"),
    *             @SWG\Property(property="data", type="object",
    *               @SWG\Property(property="user",
    *                   type="object",
    *                   @SWG\Property(property="ID", type="integer", example=1),
    *                   @SWG\Property(property="name", type="string", example="Angga"),
    *                   @SWG\Property(property="email", type="string", example="angga@gmail.com"),
    *                   @SWG\Property(property="password", type="string", example="$2y$13$cZTgVLuME1/xYS5gUD7SC.955rI10CDdfqMsoV07hwkfFVV4WYEuS"),
    *                   @SWG\Property(property="created", type="datetime", example="2021-01-16 06:24:48"),
    *                   @SWG\Property(property="updated", type="datetime", example="2021-01-16 06:24:48"),
    *               ),
    *               @SWG\Property(property="session",
    *                   type="object",
    *                   @SWG\Property(property="ID", type="integer", example=1),
    *                   @SWG\Property(property="userID", type="integer", example=1),
    *                   @SWG\Property(property="name", type="string", example="Name Session"),
    *                   @SWG\Property(property="description", type="string", example="Desc Session"),
    *                   @SWG\Property(property="start", type="string", example="2021-01-16 06:24:48"),
    *                   @SWG\Property(property="duration", type="integer", example=60),
    *                   @SWG\Property(property="created", type="datetime", example="2021-01-16 06:24:48"),
    *                   @SWG\Property(property="updated", type="datetime", example="2021-01-16 06:24:48"),
    *               )
    *            ),
    *        )
    *    ),
    *    @SWG\Response(
    *        response = 500, 
    *        description = "Failed",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=500),
    *             @SWG\Property(property="message", type="string", example="Failed Get Detail Session"),
    *                
    *        )
    *    )
    *)
    * @throws HttpException
    */

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


    /**
    * @SWG\Post(
    *    path = "/session/create",
    *    tags = {"session/create"},
    *    operationId = "session/create",
    *    summary = "Регистрация устройства",
    *    description = "Регистрация устройства",
    *    produces = {"application/json"},
    *    consumes = {"application/json"},
    *    @SWG\Parameter(
    *          name="session",
    *          in="body",
    *          required=true,
    *          @SWG\Schema(ref="#/definitions/Session"),
    *      ),
    *    @SWG\Response(
    *        response = 200, 
    *        description = "Response after successfully save session",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=200),
    *             @SWG\Property(property="message", type="string", example="Successfully Save Session"),
    *             @SWG\Property(property="data", type="array",
    *               @SWG\Items(
    *                   type="object",
    *                   @SWG\Property(property="ID", type="integer", example=1),
    *                   @SWG\Property(property="userID", type="integer", example=1),
    *                   @SWG\Property(property="name", type="string", example="Name Session"),
    *                   @SWG\Property(property="description", type="string", example="Desc Session"),
    *                   @SWG\Property(property="start", type="string", example="2021-01-16 06:24:48"),
    *                   @SWG\Property(property="duration", type="integer", example=60),
    *                   @SWG\Property(property="created", type="datetime", example="2021-01-16 06:24:48"),
    *                   @SWG\Property(property="updated", type="datetime", example="2021-01-16 06:24:48"),
    *               )
    *            ),
    *        )
    *    ),
    *    @SWG\Response(
    *        response = 500, 
    *        description = "Failed",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=500),
    *             @SWG\Property(property="message", type="string", example="Failed Save Session"),
    *                
    *        )
    *    )
    *)
    * @throws HttpException
    */

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

    /**
    * @SWG\Post(
    *    path = "/session/update",
    *    tags = {"session/update"},
    *    operationId = "session/update",
    *    summary = "Регистрация устройства",
    *    description = "Регистрация устройства",
    *    produces = {"application/json"},
    *    consumes = {"application/json"},
    *    @SWG\Parameter(
    *          name="session",
    *          in="body",
    *          required=true,
    *          @SWG\Schema(ref="#/definitions/Session"),
    *      ),
    *    @SWG\Response(
    *        response = 200, 
    *        description = "Response after successfully save session",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=200),
    *             @SWG\Property(property="message", type="string", example="Successfully Update Session"),
    *             @SWG\Property(property="data", type="array",
    *               @SWG\Items(
    *                   type="object",
    *                   @SWG\Property(property="ID", type="integer", example=1),
    *                   @SWG\Property(property="userID", type="integer", example=1),
    *                   @SWG\Property(property="name", type="string", example="Name Session"),
    *                   @SWG\Property(property="description", type="string", example="Desc Session"),
    *                   @SWG\Property(property="start", type="string", example="2021-01-16 06:24:48"),
    *                   @SWG\Property(property="duration", type="integer", example=60),
    *                   @SWG\Property(property="created", type="datetime", example="2021-01-16 06:24:48"),
    *                   @SWG\Property(property="updated", type="datetime", example="2021-01-16 06:24:48"),
    *               )
    *            ),
    *        )
    *    ),
    *    @SWG\Response(
    *        response = 500, 
    *        description = "Failed",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=500),
    *             @SWG\Property(property="message", type="string", example="Failed Update Session"),
    *                
    *        )
    *    )
    *)
    * @throws HttpException
    */

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

    /**
    * @SWG\GET(
    *    path = "/session/delete/{id}",
    *    tags = {"session/delete/"},
    *    operationId = "session/delete",
    *    summary = "Регистрация устройства",
    *    description = "Регистрация устройства",
    *    produces = {"application/json"},
    *    consumes = {"application/json"},
    *    @SWG\Parameter(
    *     name="id",
    *     in="path",
    *     type="string",
    *     description="Description goes here"
    *    ),
    *    @SWG\Response(
    *        response = 200, 
    *        description = "success",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=200),
    *             @SWG\Property(property="message", type="string", example="Successfully Delete Session"),
    *        )
    *    ) ,   
    *    @SWG\Response(
    *        response = 500, 
    *        description = "Failed",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=500),
    *             @SWG\Property(property="message", type="string", example="Failed Delete Session"),
    *                
    *        )
    *    )
    *)
    * @throws HttpException
    */

    public function actionDeleteSession()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;

        if($session->has('hasLogin')){

            $session = Session::deleteAll(['ID' => $request->get('id')]);

            if (!$session) {
                    return $this->asJson(Yii::$app->apiFactory->createErrorResponse("Failed Delete Session")->toArray());
            
            }else{
                return $this->asJson(Yii::$app->apiFactory->createSuccessResponse([$session], "Successfully Delete Session")->toArray());
            }
        }else{
            return $this->asJson(Yii::$app->apiFactory->createErrorResponse("Failed to access, Please login")->toArray());
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
     /**
    * @SWG\GET(
    *    path = "/logout",
    *    tags = {"logout"},
    *    operationId = "logout",
    *    summary = "Регистрация устройства",
    *    description = "Регистрация устройства",
    *    produces = {"application/json"},
    *    consumes = {"application/json"},
    *    @SWG\Parameter(
    *          name="user",
    *          in="body",
    *          required=true,
    *          @SWG\Schema(ref="#/definitions/UserRegister"),
    *      ),
    *    @SWG\Response(
    *        response = 200, 
    *        description = "success",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=200),
    *             @SWG\Property(property="message", type="string", example="Successfully Logout"),
    *        )
    *    ),
    *    @SWG\Response(
    *        response = 500, 
    *        description = "Failed",
    *        @SWG\Schema(
    *             required={"status", "message", "data"},
    *             @SWG\Property(property="status", type="integer", example=500),
    *             @SWG\Property(property="message", type="string", example="Failed Logout"),
    *                
    *        )
    *    )
    *)
    * @throws HttpException
    */
    public function actionLogout()
    {
        $session = Yii::$app->session;
        if (!$session->remove('hasLogin')) {
            return $this->asJson(Yii::$app->apiFactory->createErrorResponse("Failed Logout")->toArray());
        
        }else{
            return $this->asJson(Yii::$app->apiFactory->createSuccessResponse([], "Successfully Logout")->toArray());
        }
    }
}