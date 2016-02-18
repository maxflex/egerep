<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'egecrm';

    protected $fillable = ['login', 'password'];
    public $timestamps = false;

    # ID of the last real user
    const LAST_REAL_ID = 112;
    const USER_TYPE = 'USER';

    /**
     * Вход пользователя
     */
    public static function login($data)
    {
        $User = User::where([
            'login'     => $data['login'],
            'password'  => static::_password($data['password']),
        ]);

        if ($User->exists()) {
            $_SESSION['user'] = $User->first();
            return true;
        } else {
            return false;
        }
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
     */
    public static function getReal($only_working = false)
    {
        $query = User::where('id', '<=', self::LAST_REAL_ID);

        if ($only_working) {
            $query = $query->where('worktime', 1);
        }

        return $query->get();
    }
}
