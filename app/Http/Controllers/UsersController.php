<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Microposts;

class UsersController extends Controller
{
    public function index(){
        $users = User::orderBy('id','desc')->paginate(10);

        return view('users.index',['users' => $users,]);
    }

    public function show($id){
        $user = User::find($id);
        $microposts = $user->microposts()->orderBy('created_at', 'desc')->paginate(10);

        $data = [
            'user' => $user,
            'microposts' => $microposts,
        ];

        $data += $this->counts($user);

        return view('users.show', $data);
    }

    public function edit($id){
        $user = User::find($id);

        if (\Auth::id() === $user->id) {
            return view('users.edit', ['user' => $user,]);
        }
        // 編集画面に入れなかった場合はトップページへ
        return redirect('/');
    }

    public function update(Request $request, $id){
        $user = User::find($id);

        if (\Auth::id() == $user->id) {
            $user->name    = $request->name;
            $user->profile = $request->profile;
            $user->save();
        }

            return back();
    }

    public function followings($id){
        $user = User::find($id);
        $followings = $user->followings()->paginate(10);

        $data = [
            'user' => $user,
            'users' => $followings,
            ];

        $data += $this->counts($user);

        return view('users.followings', $data);
    }

    public function followers($id){
        $user = User::find($id);
        $followers = $user->followers()->paginate(10);

        $data = [
            'user' => $user,
            'users' => $followers,
        ];

        $data += $this->counts($user);

        return view('users.followers', $data);
    }

    public function favorites($id){
        $user = User::find($id);
        $favorites = $user->favorites()->paginate(50);

        $data = [
            'user' => $user,
            'microposts' => $favorites,
            ];

        $data += $this->counts($user);

        return view('users.favorites', $data);
    }

}
