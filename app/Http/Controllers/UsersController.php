<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    /**
     * Show the user's profile.
     *
     * @param User $user
     * @return View
     */
    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the user's profile.
     *
     * @param User $user
     * @return View
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user):RedirectResponse
    {
        $data = $request->all();
        if ($request->avatar){
            $result = $uploader->save($request->avatar, 'avatars', $user->id);
            if ($result === false) {
                return redirect()->back()->withErrors('Image upload failed. Please try again.');
            }
            $data['avatar'] = $result['path'];
        }
        $user->update($data);
        return redirect()->route('users.show', $user)->with('success', 'Profile updated successfully.');
    }
}
