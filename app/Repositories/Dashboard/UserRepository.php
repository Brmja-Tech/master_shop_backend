<?php

namespace App\Repositories\Dashboard;

use App\Models\User;

class UserRepository
{

    public function getUsers()
    {
        $users = User::get();
        return $users;
    } // End getUsers method



    public function getAllUsers($search)
    {
        $user = User::when($search, function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%');
        })
            ->latest()
            ->paginate(10);
        return $user;
    } // End getAllUsers method



    public function getUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }
        return $user;
    } // End getUser method

    public function createUser(array $data)
    {
        return User::create($data);
    }

    public function updateUser($request, $country) {} // End updateUser method


    public function destroy($user)
    {
        if ($user->image && $user->image !== 'uploads/images/image.png') {
            @unlink(public_path($user->image));
        }
        return  $user->delete();
    } // End destroy method

    public function changestatus($user)
    {
        $user->status = $user->status == 1 ? 0 : 1;
        $user->save();
        return $user;
    } // End changestatus method

}
