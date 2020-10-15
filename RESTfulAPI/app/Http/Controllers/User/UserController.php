<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController
{
    public function index()
    {
        $users = User::all();
        // return $users;
        return $this->showAll($users);
    }


    public function show(User $user)
    {
        return $this->showOne($user);
    }



    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' =>  'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ];
        $this->validate($request, $rules);

        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $newUser = User::create($data);
        return $this->showOne($newUser);
    }




    public function update(Request $request, User $user)
    {
        $rules = [
            'email' =>  'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER
        ];

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        // if user changed his email he won't be verified anymore
        if ($request->has('email') && $user->email != $request->email) {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }

        // if user changed his password we must hash it again then save it
        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        // to change 'admin' value we must check if he is verified user first
        if ($request->has('admin')) {
            if (!$user->isVerified()) {
                return $this->errorResponse('Only verified users can change the admin value', 409);
            }
            $user->admin = $request->admin;
        }

        // isDirty() means user made changes
        if (!$user->isDirty()) {
            return $this->errorResponse('You must change a value to perform an update', 422);
        }

        $user->save();
        return $this->showOne($user);
    }


    public function destroy(User $user)
    {
        $user->delete();
        // return $this->successResponse($user,200);
        return response()->json(
            ["data" => $user, 'user deleted', 200]
        );
    }

    public function verify($token)
    {
        //find the user whose token is matched with the one sent in request
        $user = User::where('verification_token', $token)->FirstOrFail();
        $user->verified = User::VERIFIED_USER;
        $user->verification_token = null;
        $user->save();
        return $this->showMessage('This account is verified sucessfuly');
    }

    public function resend(User $user)
    {
        if($user->isVerified()){
            return $this->errorResponse('This user is already verified', 409);
        }

        Mail::to($user)->send(new UserCreated($user));

        return $this->showMessage('Verification mail has been resent');
    }
}
