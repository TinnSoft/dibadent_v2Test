<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordGenerated;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
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

    public function reset_password(Request $request){
        
        $emailData['email'] = $request->email;
        $emailData['password'] =Str::random(6);
        $this->sendEmail($emailData);

          return response()
            ->json([
                'sent' => true,
                'em'=> $request->email
            ]);
    }

    public function sendEmail($data)
    {    

        $body='Cordial saludo, <br/><br/>';
        $body .= 'A continuación puedes visualizar sus credenciales de acceso a dibadent:  <br/><br/>';
        $body .= 'Usuario: <strong>'.$data['email'].' </strong> <br/>';
        $body .= 'Contraseña: <strong>'.$data['password'].'</strong><br/><br/>';
        $body .= 'Para iniciar sesion ingrese a la siguiente ruta: https://dibadent.com/login <br/><br/>';
        $body .= 'Cordialmente,';
        $body .= '<br/><br/><br/>';
        $body .= 'Equipo Dibadent<br/>';

      
        Mail::to($data['email'])
            ->send(new PasswordGenerated($body));
    }

    protected function sendResetResponse(Request $request, $response)
    {
        return ['status' => trans($response)];
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return response()->json(['email' => trans($response)], 400);
    }
}
