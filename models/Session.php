<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
* @SWG\Definition(
*      definition="Session",
*      required={"user_id", "name", "description", "start", "duration"},
*      @SWG\Property(
*          property="user_id",
*          type="integer",
*          description="ID User",
*          example="1"
*      ),
*      @SWG\Property(
*          property="name",
*          type="string",
*          description="Name Session",
*          example="Session Name"
*      ),
*      @SWG\Property(
*          property="description",
*          type="string",
*          description="Description Session",
*          example="Session Desc"
*      ),
*      @SWG\Property(
*          property="start",
*          type="datetime",
*          description="Start (Date Time) Session",
*          example="2020-12-31 13:00:00"
*      ),
*      @SWG\Property(
*          property="duration",
*          type="integer",
*          description="Duration Session",
*          example=60
*      )
* )
*/
class Session extends ActiveRecord
{

	public $userss;

    public static function tableName()
    {
        return 'session';
    }

    public function rules()
    {
        return [
            ['userID', 'required'],
            ['name', 'required'],
            ['description', 'required'],
            ['start', 'required'],
            ['duration', 'required'],
            ['duration', 'integer'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['ID' => 'userID']);
    }
}