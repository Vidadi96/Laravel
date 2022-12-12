<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Universal_model;
use App\MyCustom\SmsMessage;
use App\Notifications\EmailNotification;
use Illuminate\Validation\Rule;
use Notification;

class LoginController extends Controller
{
    private $umodel;

    function __construct()
    {
      $this->umodel = new Universal_model();
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
          return response(auth()->user(), 200);
        }

        abort(401);
    }

    public function registration(Request $request)
    {
      $request->validate([
        'name' => ['required', 'min:3'],
        'email' => ['required', 'email', Rule::unique('users', 'email')],
        'phone' => 'required',
        'country' => 'required'
      ]);

      $vars = array(
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt('')
      );

      $user = User::create($vars);
      auth()->login($user);

      $vars = array(
        'user_id' => auth()->id(),
        'name' => $request->country
      );
      $this->umodel->add_item($vars, 'user_countries');

      $vars = array(
        'user_id' => auth()->id(),
        'phone' => $request->phone
      );
      $this->umodel->add_item($vars, 'phone_book');

      $sms = new SmsMessage;
      $sms->to($request->phone)->line('Congratulations, you are registered in our system!')->send();

      $project = [
          'greeting' => 'Hi '.$user->name.',',
          'body' => 'Congratulations.',
          'thanks' => 'Thank you for registration in our system',
          'actionText' => 'Vue Project',
          'actionURL' => url('/')
      ];

      Notification::send($user, new EmailNotification($project));
    }

    public function logout()
    {
        Auth::logout();
        return response(null, 200);
    }
}
