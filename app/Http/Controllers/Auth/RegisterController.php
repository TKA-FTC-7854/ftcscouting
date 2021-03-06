<?php

namespace App\Http\Controllers\Auth;

use App\Team;
use App\TeamInvite;
use App\User;
use App\UserData;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User|\Illuminate\Database\Eloquent\Model
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        $userData = new UserData();
        $userData->user_id = $user->id;
        $userData->bio = "";
        $userData->photo_location = $data['email'];
        $userData->has_profile_photo = true;
        $userData->gravatar = true;
        $userData->save();
        if(env('AUTOJOIN_TEAM') != null){
            $team = Team::whereTeamNumber(env('AUTOJOIN_TEAM'))->first();
            if($team != null){
                $teamInvite = new TeamInvite();
                $teamInvite->accepted = true;
                $teamInvite->pending = false;
                $teamInvite->sender_id = $team->owner;
                $teamInvite->receiver_id = $user->id;
                $teamInvite->team_id = $team->id;
                $teamInvite->save();
            }

        }
        return $user;
    }
}
