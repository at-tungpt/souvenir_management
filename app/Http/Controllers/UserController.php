<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repositories;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Session;

class UserController extends Controller
{
    protected $userRepository;

    /**
     * Function__construct description
     *
     * @param UserRepository $userRepository void
     */
    public function __construct(UserRepository $userRepository)
    {
        return $this->userRepository = $userRepository;
    }

    /**
     * List information a user
     *
     * @return array information a user
     */
    public function index()
    {
        return view('user/profile.index');
    }

    /**
     * Edit profile a user
     *
     * @param int $id id of user
     *
     * @return array     a user
     */
    public function edit($id)
    {
        $user = $this->userRepository->find($id);
        return view('user/profile.edit', compact('user'));
    }

    /**
     * Update insert new information a user
     *
     * @param UserRequest $request descriptio request
     * @param type        $id      description request
     *
     * @return array               a user
     */
    public function update(UserRequest $request, $id)
    {
        $user = $this->userRepository->find($id);
        if (empty($user)) {
            Session::flash('msg', trans('label_trans.not_found'));
            return view('admin.index');
        }
        $input = $request->only('name', 'password', 'gender', 'birthday', 'number_phone');
        if ($request->hasFile('avatar')) {
            $input['avatar'] = $this->userRepository->saveFile($request->file('avatar'));
            $oldImage = $user->avatar;
            if (file_exists(config('path.path_avatar').$oldImage) == true) {
                unlink(config('path.path_avatar').$oldImage);
            }
        }

        if (!empty($request['old-password'])) {
            if (!Hash::check($request['old-password'], Auth::user()->password)) {
                return redirect()->back()->withErrors(trans('lable_trans.old_password_does_not_match'));
            }
        }
        $user = $this->userRepository->update($input, $id);
        Session::flash('msg', trans('label_trans.update_successfully'));
        return view('admin.index');
    }

    /**
     * Delete a user
     *
     * @param int $id id of user
     *
     * @return array     delete a user in list view
     */
    public function destroy($id)
    {
        $avatar = $this->userRepository->find($id)->avatar;
        if (file_exists(config('path.path_avatar').$avatar) == true && $avatar != config('define.AVATAR_DEFAULT')) {
            unlink(config('path.path_avatar').$avatar);
        }
        $user = $this->userRepository->delete($id);
        if (empty($user)) {
            Session::flash('msg', trans('label_trans.not_found'));
            return view('admin.index');
        }
        Session::flash('msg', trans('label_trans.delete_successfully'));
        return back()->withInput();
    }
}
