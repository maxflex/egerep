<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use App\Models\Service\Log;

class User extends Model
{
    const SUPERUSER_RIGHT = 9999;

    const ADMIN_SESSION_DURATION = 40;

    protected $connection = 'egecrm';

    protected $fillable = [
        'login',
        'password',
        'color',
        'type',
        'email',
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
            self::log(null, 'failed_login', 'неверный логин', ['login' => $data['login']]);
            return false;
        }

        # проверка пароля
        $query->where('password', static::_password($data['password']));
        if (! $query->exists()) {
            self::log($user_id, 'failed_login', 'неверный пароль');
            return false;
        }

        # забанен ли?
        $query->active();
        if (! $query->exists()) {
            self::log($user_id, 'failed_login', 'пользователь заблокирован');
        } else {
            $user = $query->first();
            # из офиса или есть доступ вне офиса
            if (User::fromOffice() || $user->allowed(\Shared\Rights::WORLDWIDE_ACCESS)) {
                # дополнительная СМС-проверка, если пользователь логинится если не из офиса
                if (! User::fromOffice() && $user->type == User::USER_TYPE && ! User::fromMaldives()) {
                    $sent_code = Redis::get("egerep:codes:{$user_id}");
                    // если уже был отправлен – проверяем
                    if (! empty($sent_code)) {
                        if (@$data['code'] != $sent_code) {
                            self::log($user_id, 'failed_login', 'неверный смс-код');
                            return false;
                        } else {
                            Redis::del("egerep:codes:{$user_id}");
                        }
                    } else {
                        // иначе отправляем код
                        self::log($user_id, 'sms_code_sent');
                        Sms::verify($user);
                        return 'sms';
                    }
                }
                self::log($user_id, 'success_login');
                $_SESSION['user'] = $user;
                return true;
            } else {
                self::log($user_id, 'failed_login', 'нет прав доступа для данного IP');
            }
        }
        return false;
    }

    /**
     * Логин из офиса
     */
    public static function fromOffice()
    {
        if (app('env') === 'local') {
            return true;
        }
        $current_ip = @$_SERVER['HTTP_X_REAL_IP'];
        foreach(['213.184.130.', '85.30.249.251', '178.140.53.201'] as $ip) {
            if (strpos($current_ip, $ip) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Из Мальдив (временно)
     */
    public static function fromMaldives()
    {
        $ips = '27.114.128.0	27.114.191.255
            43.226.220.0	43.226.223.255
            43.231.28.0	43.231.31.255
            45.42.136.0	45.42.136.255
            46.244.29.144	46.244.29.159
            57.92.192.0	57.92.207.255
            69.94.80.0	69.94.95.255
            103.31.84.0	103.31.87.255
            103.50.104.0	103.50.107.255
            103.67.26.0	103.67.26.255
            103.71.57.0	103.71.57.255
            103.76.2.0	103.76.2.255
            103.84.132.0	103.84.132.255
            103.84.134.0	103.84.134.255
            103.87.188.0	103.87.188.255
            103.103.66.0	103.103.66.255
            103.110.40.0	103.110.40.255
            103.110.109.0	103.110.111.255
            103.197.164.0	103.197.167.255
            115.84.128.0	115.84.159.255
            123.176.0.0	123.176.31.255
            124.195.192.0	124.195.223.255
            202.1.192.0	202.1.207.255
            202.21.176.0	202.21.191.255
            202.153.80.0	202.153.87.255
            202.174.131.88	202.174.131.95
            202.174.131.128	202.174.131.135
            202.174.131.144	202.174.131.151
            202.174.131.176	202.174.131.215
            202.174.131.224	202.174.131.231
            202.174.132.208	202.174.132.223
            202.174.132.240	202.174.132.247
            202.174.133.240	202.174.133.255
            203.82.2.0	203.82.3.255
            203.104.24.0	203.104.31.255
            216.183.208.0	216.183.223.255
            220.158.220.0	220.158.223.255';

        $current_ip = @$_SERVER['HTTP_X_REAL_IP'];

        foreach(explode("\n", $ips) as $line) {
            list($ip_start, $ip_end) = explode("\t", $line);
            $ip_start = ip2long(trim($ip_start));
            $ip_end = ip2long(trim($ip_end));
            if ($current_ip >= $ip_start && $current_ip <= $ip_end) {
                return true;
            }
        }
        return false;
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

    public static function id()
    {
        return User::fromSession()->id;
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
	public static function _password($password)
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


    public static function log($user_id, $type, $message = '', $data = [])
    {
        $data = array_merge($data, [
            $type => $message,
            'user_agent' => @$_SERVER['HTTP_USER_AGENT']
        ]);
        Log::custom('authorization', $user_id, $data);
    }
}
