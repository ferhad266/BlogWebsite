<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Pages;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function detail($slug)
    {
        $pageList = Pages::all()->sortBy('page_must');
        $page = Pages::where('page_slug',$slug)->first();
        return view('frontend.blog.detail',compact('page','pageList'));
    }
}
