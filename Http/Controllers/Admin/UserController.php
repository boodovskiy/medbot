<?php

namespace App\Http\Controllers\Admin;

use App\Avatar;
use App\Http\Requests\Admin\UserAddRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name', 'id')->sortBy('id')->all();
        return view('admin.user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserAddRequest $request)
    {
        $input = $request->all();
        $input['remember_token'] = $input['_token'];
        $user = User::create($input);

        if ($file = $request->file('avatar')) {
            $filename = 'avatar_user_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move('uploads', $filename);
            $avatar = $user->avatar()->create(['filename' => $filename]);
            $user->avatar()->save($avatar);
        }
        Session::flash('add', 'Пользователь '.$user->email.' добавлен');
        return redirect()->route('user.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::pluck('name', 'id')->sortBy('id')->all();
        return view('admin.user.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);
        if (trim($request->password) == '') {
            $input = $request->except('password');
        } else {
            $input = $request->all();
        }
        /*Проверка загрузки автарки и удаление старой*/
        if ($file = $request->file('avatar')) {
            $filename = 'avatar_user_' . $user->id . '.' . $file->getClientOriginalExtension();
            if (count($user->avatar) != 0) {
                if (file_exists(public_path() . $user->avatar->filename)) {
                    unlink(public_path() . $user->avatar->filename);
                }
                $avatar = Avatar::findOrFail($user->avatar->id);
                $avatar->update(['filename' => $filename]);
                $user->avatar()->save($avatar);
            } else {
                $avatar = $user->avatar()->create(['filename' => $filename]);
                $user->avatar()->save($avatar);
            }
            $file->move('uploads', $filename);
        }
        $input['remember_token'] = $input['_token'];
        $user->update($input);
        Session::flash('edit', 'Пользователь '.$user->email.' обновлен');
        return redirect()->route('user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        Session::flash('del', 'Пользователь '.$user->email.' удален');
        return redirect()->route('user.index');
    }
}
