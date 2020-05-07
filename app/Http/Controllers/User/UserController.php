<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return $this->showAll($users);    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email'=> 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ];

        $this->validate($request, $rules);
        $data = $request->all();
        // $data['password'] = bycrypt($request->password);
        $data['password'] = password_hash($request->password, PASSWORD_BCRYPT);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);
        return $this->showOne($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'email'=> 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in'. User::ADMIN_USER .','. User::REGULAR_USER,
        ];

        /* validation for the name filled */
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        /* validation for the email */
        if ($request->has('email') && $user->email != $request->email) {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }

        /* validation for the password */
        if ($request->has('password')) {
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
        }

        /* validation for the admin */
        if ($request->has('admin')) {
            if (!$user->isVerified()) {
                return $this->errorResponse('Only verified users can modify the admin field', 409);
            }
            
            $user->admin = $request->admin;
        }

        /* specify if the current model intance was changed */
        if (!$user->isDirty()) {
            return $this->errorResponse('You need to specify a different value to update', 422);
        }

        $user->save();
        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->showOne($user);
    }

    // 2:17 01-30-2019 Verifying some user using its given token
    public function verify($token)
    {
        // getting if the user verification_token was listed at the user table then return it or Fail
        $user = User::where('verification_token', $token)->firstOrFail();

        $user->verified = User::VERIFIED_USER; // verifying the given user
        $user->verification_token = null; //nulling the verification_token field

        $user->save();

        // returning some message back to the client
        return $this->showMessage('The account has been verified successfully');
    }

    // 4:32 PM 01-30-2019 The resend verification token 
    public function resend(User $user)
    {
        // verifying if the current instance of the user is not verified yet
        if ($user->isVerified()) {
            return $this->errorResponse('This user is already verified', 409);
        }

        // retrying helper 5.4 >
        retry(5, function() use ($user)
        {
            // if the user is not yet verified send the User verification link to the user->email
            Mail::to($user->email)->send(new UserCreated($user));
        }, 100);

        return $this->showMessage('The verification email has been resend');
    }
}
