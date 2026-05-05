<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller; 

class ProfileController extends Controller
{ 
    public function index()
    {
        return view('admin.profile', ['page'   => __('page.my_profile'),'breadcumb' => false]);
    }
}
