<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_account".
 *
 * @property integer $id
 * @property integer $role
 * @property integer $mentor_id
 * @property integer $adviser_type
 * @property integer $discovery
 * @property string $reminder
 * @property integer $priority
 * @property string $definition
 * @property integer $country_id
 * @property integer $city_id
 * @property string $language_id
 * @property string $email
 * @property integer $page_id
 * @property string $phone
 * @property string $skype
 * @property string $name
 * @property string $surname
 * @property string $name_local
 * @property string $surname_local
 * @property string $birth_date
 * @property integer $birth_city
 * @property integer $gender
 * @property integer $university
 * @property string $password
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $auth_key
 * @property string $verified_at
 * @property string $remark
 * @property string $telephony
 * @property string $geo
 * @property string $navigator
 * @property integer $expectation
 * @property integer $timezone
 * @property string $ref
 * @property string $source
 * @property integer $pid
 * @property string $desc
 * @property string $desc_1
 * @property string $desc_2
 * @property string $desc_3
 * @property string $desc_4
 * @property string $desc_5
 * @property string $desc_6
 * @property string $desc_7
 * @property string $desc_8
 * @property string $desc_9
 * @property string $desc_10
 * @property string $desc_11
 * @property string $desc_12
 * @property string $nevus
 * @property string $acted_at
 * @property integer $author_id
 * @property integer $editor_id
 * @property string $updated_at
 * @property string $created_at
 * @property integer $mark
 */
class User extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role', 'mentor_id', 'adviser_type', 'discovery', 'priority', 'country_id', 'city_id', 'page_id', 'birth_city', 'gender', 'university', 'expectation', 'timezone', 'pid', 'author_id', 'editor_id', 'mark'], 'integer'],
            [['reminder', 'birth_date', 'verified_at', 'acted_at', 'updated_at', 'created_at'], 'safe'],
            [['definition', 'telephony', 'geo', 'navigator', 'ref', 'source', 'desc', 'desc_1', 'desc_2', 'desc_3', 'desc_4', 'desc_5', 'desc_6', 'desc_7', 'desc_8', 'desc_9', 'desc_10', 'desc_11', 'desc_12'], 'string'],
            [['language_id'], 'string', 'max' => 8],
            [['email', 'phone', 'skype', 'name', 'surname', 'name_local', 'surname_local'], 'string', 'max' => 55],
            [['password', 'password_hash', 'auth_key'], 'string', 'max' => 70],
            [['password_reset_token'], 'string', 'max' => 255],
            [['remark'], 'string', 'max' => 300],
            [['nevus'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role' => 'Role',
            'mentor_id' => 'Mentor ID',
            'adviser_type' => 'Adviser Type',
            'discovery' => 'Discovery',
            'reminder' => 'Reminder',
            'priority' => 'Priority',
            'definition' => 'Definition',
            'country_id' => 'Country ID',
            'city_id' => 'City ID',
            'language_id' => 'Language ID',
            'email' => 'Email',
            'page_id' => 'Page ID',
            'phone' => 'Phone',
            'skype' => 'Skype',
            'name' => 'Name',
            'surname' => 'Surname',
            'name_local' => 'Name Local',
            'surname_local' => 'Surname Local',
            'birth_date' => 'Birth Date',
            'birth_city' => 'Birth City',
            'gender' => 'Gender',
            'university' => 'University',
            'password' => 'Password',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'auth_key' => 'Auth Key',
            'verified_at' => 'Verified At',
            'remark' => 'Remark',
            'telephony' => 'Telephony',
            'geo' => 'Geo',
            'navigator' => 'Navigator',
            'expectation' => 'Expectation',
            'timezone' => 'Timezone',
            'ref' => 'Ref',
            'source' => 'Source',
            'pid' => 'Pid',
            'desc' => 'Desc',
            'desc_1' => 'Desc 1',
            'desc_2' => 'Desc 2',
            'desc_3' => 'Desc 3',
            'desc_4' => 'Desc 4',
            'desc_5' => 'Desc 5',
            'desc_6' => 'Desc 6',
            'desc_7' => 'Desc 7',
            'desc_8' => 'Desc 8',
            'desc_9' => 'Desc 9',
            'desc_10' => 'Desc 10',
            'desc_11' => 'Desc 11',
            'desc_12' => 'Desc 12',
            'nevus' => 'Nevus',
            'acted_at' => 'Acted At',
            'author_id' => 'Author ID',
            'editor_id' => 'Editor ID',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'mark' => 'Mark',
        ];
    }
}
