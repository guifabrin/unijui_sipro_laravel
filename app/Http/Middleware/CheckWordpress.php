<?php

namespace App\Http\Middleware;

use Auth;
use App\User;
use Closure;

class CheckWordpress
{
    private function userFindOrCreate($id, $email) {
        $user = User::where('wp_user_id', $id)->first();
        if ($user) return $user;
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->update(['wp_user_id' => $id]);
            return $user;
        }
        return User::create([
            'email'=> $email,
            'name'=> $email,
            'wp_user_id' => $id
        ]);
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $dotenv = \Dotenv\Dotenv::create(__DIR__ . "/../../../");
        $dotenv->load();
        $wpLoadFile = env('WORDPRESS_PATH') . 'wp-load.php';
        if (env('WORDPRESS') && file_exists($wpLoadFile)) {
            if (!defined('WP_USE_THEMES'))
                define('WP_USE_THEMES', false);
            include $wpLoadFile;
            if (!is_user_logged_in())
                return redirect(env('WORDPRESS_LOGIN_URL') . "?redirect_to=" . $request->url());
            $user = wp_get_current_user();
            $laravelUser = User::where('wp_user_id', $user->ID)->first();
            if (Auth::check()) {
                if (Auth::user()->id != $laravelUser->id) {
                    Auth::logout();
                    return redirect(env('WORDPRESS_LOGIN_URL') . "?redirect_to=" . $request->url());
                }
            } else {
                if (!$laravelUser)
                    $laravelUser = User::create([
                        'email' => $user->data->user_email,
                        'name' => $user->data->user_email,
                        'wp_user_id' => $user->ID
                    ]);
                Auth::login($laravelUser);
            }
        }
        return $next($request);
    }
}