<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use App\Models\Service\Log;

class User extends Model
{
    protected $connection = 'egecrm';

    protected $fillable = [
        'login',
        'password',
        'color',
        'type',
        'id_entity',
    ];

    protected static $commaSeparated = ['rights'];

    public $timestamps = false;

    const USER_TYPE    = 'USER';
    const DEFAULT_COLOR = 'black';

    # Fake system user
    const SYSTEM_USER = [
        'id'    => 0,
        'login' => 'system',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = static::_password($value);
    }

    /**
     * Если пользователь заблокирован,то его цвет должен быть черным
     */
    public function getColorAttribute()
    {
        if ($this->allowed(\Shared\Rights::ER_BANNED)) {
            return static::DEFAULT_COLOR;
        } else {
            return $this->attributes['color'];
        }
    }

    /**
     * Вход пользователя
     */
    public static function login($data)
    {
        $query = User::where('login', $data['login']);

         # проверка логина
        if ($query->exists()) {
            $user_id = $query->value('id');
        } else {
            self::log('wrong_login', null, ['login' => $data['login']]);
            return false;
        }

        # проверка пароля
        $query->where('password', static::_password($data['password']));
        if (! $query->exists()) {
            self::log('wrong_password', $user_id);
            return false;
        }

        # забанен ли?
        $query->active();
        if (! $query->exists()) {
            self::log('banned', $user_id);
        } else {
            $user = $query->first();
            # из офиса или есть доступ вне офиса
            if (User::fromOffice() || $user->allowed(\Shared\Rights::WORLDWIDE_ACCESS)) {
                # дополнительная СМС-проверка, если пользователь логинится если не из офиса
                if (! User::fromOffice() && $user->type == User::USER_TYPE) {
                    $sent_code = Redis::get("egerep:codes:{$user_id}");
                    // если уже был отправлен – проверяем
                    if (! empty($sent_code)) {
                        if (@$data['code'] != $sent_code) {
                            self::log('wrong_sms_code', $user_id);
                            return false;
                        } else {
                            Redis::del("egerep:codes:{$user_id}");
                        }
                    } else {
                        // иначе отправляем код
                        self::log('sms_code_sent', $user_id);
                        Sms::verify($user);
                        return 'sms';
                    }
                }
                self::log('login', $user_id);
                $_SESSION['user'] = $user;
                return true;
            } else {
                self::log('outside_office', $user_id);
            }
        }
        return false;
    }

    /**
     * Логин из офиса
     */
    public static function fromOffice()
    {
        return app('env') === 'local' || strpos($_SERVER['HTTP_X_REAL_IP'], '213.184.130.') === 0;
    }

    /**
     * Вход из офиса или включена настройка «доступ отовсюду»
     */
    public static function worldwideAccess()
    {
        return User::fromOffice() || User::whereId(User::fromSession()->id)
                ->whereRaw('FIND_IN_SET(' . \Shared\Rights::WORLDWIDE_ACCESS . ', rights)')
                ->exists();
    }

    public static function logout()
    {
        unset($_SESSION['user']);
    }

    /*
	 * Проверяем, залогинен ли пользователь
	 */
	public static function loggedIn()
	{
		return isset($_SESSION["user"]) // пользователь залогинен
            && ! User::isBlocked()      // и не заблокирован
            && User::worldwideAccess()  // и можно входить
            && User::notChanged();      // и данные не изменились
	}

    /*
	 * Пользователь из сессии
	 * @boolean $init – инициализировать ли соединение с БД пользователя
	 * @boolean $update – обновлять данные из БД
	 */
	public static function fromSession($upadte = false)
	{
		// Если обновить данные из БД, то загружаем пользователя
		if ($upadte) {
			$User = User::find($_SESSION["user"]->id);
			$User->toSession();
		} else {
			// Получаем пользователя из СЕССИИ
			$User = $_SESSION['user'];
		}

		// Возвращаем пользователя
		return $User;
	}

    /**
     * Текущего пользователя в сессию
     */
    public function toSession()
    {
        $_SESSION['user'] = $this;
    }

    /**
     * Вернуть системного пользователя
     */
    public static function getSystem()
    {
        return (object)static::SYSTEM_USER;
    }

    /**
	 * Вернуть пароль, как в репетиторах
	 *
	 */
	private static function _password($password)
	{
		$password = md5($password."_rM");
        $password = md5($password."Mr");

		return $password;
	}

    /**
     * Get real users
     *
     */
    public static function scopeReal($query)
    {
        return $query->where('type', static::USER_TYPE);
    }

    /**
     * Get real users
     *
     */
    public static function scopeActive($query)
    {
        return $query->real()->whereRaw('NOT FIND_IN_SET(' . \Shared\Rights::ER_BANNED . ', rights)');
    }

    public static function isBlocked()
    {
        return User::whereId(User::fromSession()->id)
                ->whereRaw('FIND_IN_SET(' . \Shared\Rights::ER_BANNED . ', rights)')
                ->exists();
    }

    /**
     * Данные по пользователю не изменились
     * если поменяли в настройках хоть что-то, сразу выкидывает, чтобы перезайти
     */
    public static function notChanged()
    {
        return User::fromSession()->updated_at == dbEgecrm('users')->whereId(User::fromSession()->id)->value('updated_at');
    }

    /**
     * User has rights to perform the action
     */
    public function allowed($right)
    {
        return in_array($right, $this->rights);
    }


    public static function log($type, $user_id, $data = [])
    {
        $data = array_merge($data, ['user_agent' => @$_SERVER['USER_AGENT']]);
        Log::custom($type, $user_id, $data);
    }
}
