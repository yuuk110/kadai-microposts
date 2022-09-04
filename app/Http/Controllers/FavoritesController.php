<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class FavoritesController extends Controller
{
    public function store(Request $request, $micropostId){
        \Auth::user()->favorite($micropostId);
        return back();
    }

    public function destroy($id){
        \Auth::user()->unfavorite($id);
        return back();
    }
}
