<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

//
use Mail;
use Hash;
use Carbon\Carbon;
use App\Mail\SendInvitationLink;
use App\Mail\VerificationCode;

//Libraries
use App\Http\Controllers\Api\BaseController;

class AuthController extends BaseController
{
    //

    public function sendInvitation(Request $request)
    {
        
        Mail::to($request['email'])->send(new SendInvitationLink($request));
        
        return $this->sendResponse(true, __('Invitation link sent successfully.'));
    }

    /**
     * AuthController Register.
     *
     * @param LoginRequest $request
     */
    public function register(RegisterRequest $request)
    {
        
        $password = bcrypt($request->password);
        $userCreated = User::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => $password,
            'registered_at' => Carbon::now(),
        ]);

        $request['code'] = $this->generatePIN(6);

        Mail::to($request['email'])->send(new VerificationCode($request));
        
        if ($userCreated) {
            $userCreated->update(['code' => $request->code]);
            return $this->sendResponse(true, __('Thank you kindly check the email you provided for complete the registeration.'));
        }
        return $this->sendError(__('Error while regisering.'), false);
    }


    public function confirmRegistration(Request $request)
    {
        
        $userCreated = User::whereCode($request->code)->first();

        
        
        if ($userCreated) {
            $userCreated->update(['code' => null]);
            return $this->sendResponse(true, __('Registeration successfull.'));
        }
        return $this->sendError(__('Please provide the correct code.'), false);
    }

    /**
     * AuthController Login.
     *
     * @param LoginRequest $request
     */

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {

            if (Hash::check($request->password, $user->password)) {
                // Indicating user has logged In from temp pass after login
                $token = $user->createToken(env('APP_NAME'))->accessToken;

                $user['token'] = $token;
                if (!is_null($user) && $user) {
                return $this->sendResponse($user, __('Logged in successfully.'));
                }
            }

            return $this->sendError(__('Password incorrect.'), false);
        }

        return $this->sendError(__('No user exists.'), false);
    }

    public function userProfile(Request $request)
    {

        if($request->file('avatar')) {
            $image = $request->avatar;
            $image = $this->fileUpload($image[0], 'user'); 
            User::where('id', auth()->user()->id)->update([
                'avatar' => $image,
            ]);
        }

        $profileUpdated = User::where('id', auth()->user()->id)->update([
            'name' => $request->name.' '.$request->last_name,
            'user_name' => $request->user_name,
        ]);

        if ($profileUpdated) {
            return $this->sendResponse(true, __('Profile updated successfully.'));
        }
        return $this->sendError(__('responseMessages.errorEditingProfile'), false);
    }

    public function logoutUser(Request $request)
    {
        if (true) {
            $user = $request->user();

            $user->token()->revoke();

            return $this->sendResponse(true, __('Logged out successfully.'));
        }

        return $this->sendError(__('responseMessages.errorLogout'), false);
    }


    /**
     * AuthController getProfile.
     *
     * @param Request $request
     */
    public function getProfile($id = null)
    {
        if ($id != null) {
            return User::whereId($id)->first();
        }
        return $this->sendResponse(auth()->user(), __('responseMessages.passwordUpdated'));
    }

    /**
     * AuthController CoinBuySell.
     *
     * @param CoinBuySellRequest $request
     */
    public function coinBuyAndSell(Request $request)
    {
        $user = $request->user();
        if ($user) {
         
            $wallet = WalletDetail::create([
                'user_id' => $user->id,
                'type' => $request->type,
                'amount' => $request->amount,
            ]);

            if ($request->type == 'buy') {
                $user->increment('wallet', $request->amount);
            } elseif ($request->type == 'sell') {
                $user->decrement('wallet', $request->amount);
            }

            return $this->sendResponse(auth()->user(), __('Processed successfully.'));

        }
        
        return $this->sendError(__('You need to login first.'), false);
    }


}
