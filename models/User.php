<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
* @SWG\Definition(
*      definition="UserRegister",
*      required={"name", "email", "password"},
*      @SWG\Property(
*          property="name",
*          type="string",
*          description="Name User",
*          example="Angga"
*      ),
*      @SWG\Property(
*          property="email",
*          type="string",
*          description="Email User",
*          example="angganurprasetya4@gmail.com"
*      ),
*      @SWG\Property(
*          property="password",
*          type="string",
*          description="Password User",
*          example="rahasia"
*      )
* )

* @SWG\Definition(
*      definition="UserLogin",
*      required={"email", "password"},
*      @SWG\Property(
*          property="email",
*          type="string",
*          description="Email User",
*          example="angganurprasetya4@gmail.com"
*      ),
*      @SWG\Property(
*          property="password",
*          type="string",
*          description="Password User",
*          example="rahasia"
*      )
* )
*/

class User extends ActiveRecord
{

    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            ['password', 'required'],
            ['name', 'required'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'message' => 'This email address has already been taken'],
        ];
    }

    public function beforeSave($insert)
    {

        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->created = date("Y-m-d H:i:s", time());
                $this->updated = date("Y-m-d H:i:s", time());

            } else {

                $this->updated = date("Y-m-d H:i:s", time());
            }
            return true;
        } else {
            return false;
        }


    }

    public static function findIdentity($id)
    {
        return static::find()->where(
            'id = :id', [
                ':id' => $id
            ])->one();
    }

    public static function findByEmail($email)
    {
        return static::find()->where(
            'email = :email', [
                ':email' => $email
            ])->one();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }
}
