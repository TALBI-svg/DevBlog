<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index()
    {
        $message = "Hello";
        return view('user.profile', ['message' => $message]);
    }
}