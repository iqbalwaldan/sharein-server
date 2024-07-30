<?php

namespace App\Http\Controllers\Client\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupPostController extends Controller
{
    public function index()
    {
        return view('client.user.group-post.index', [
            'title' => 'Group Post',
            'active' => 'group-post'
        ]);
    }
}
