<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'egecrm';

    protected $fillable = [
        'login',
        'password',
        'color',
        'type',
        'banned_egerep',
        'can_approve_tutors',
        'id_entity',
    ];

    public $timestamps = false;

    const USER_TYPE    = 'USER';
    const BANNED_COLOR = 'black';

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
        if ($this->banned_egerep) {
            return static::BANNED_COLOR;
        } else {
            return $this->attributes['color'];
        }
    }

    /**
     * Вход пользователя
     */
    public static function login($data)
    {
        $User = User::where([
            'login'         => $data['login'],
            'password'      => static::_password($data['password']),
            'banned_egerep' => 0,
            'type'          => static::USER_TYPE
        ]);

        if ($User->exists()) {
            $user = $User->first();
            if ($user->worldwide_access || strpos($_SERVER['HTTP_X_REAL_IP'], '213.184.130.') === 0) {
                $_SESSION['user'] = $user;
                return true;
            }
        }
        return false;
    }

    public static function logout()
    {
        unset($_SESSION['user']);
    }

    public static function loggedIn()
    {
        return isset($_SESSION['user']);
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
        return $query->where('type', static::USER_TYPE)->where('banned_egerep', 0);
    }
}
