<?php
namespace app\models;

use yii\db\ActiveRecord;

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