<?php

namespace App\Http\Controllers\Backend;

use App\Pages;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['page'] = Pages::all()->sortBy('page_must');
        return view('backend.pages.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (strlen($request->page_slug) > 3) {
            $slug = Str::slug($request->page_slug);
        } else {
            $slug = Str::slug($request->page_title);

        }


        if ($request->hasFile('page_file')) {
            $request->validate([
                'page_title' => 'required',
//                'page_content' => 'required',
                'page_file' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);
            $fileName = uniqid() . '.' . $request->page_file->getClientOriginalExtension();
            $request->page_file->move(public_path('images/pages'), $fileName);
        } else {
            $fileName = null;
        }


        $page = Pages::insert([
            "page_title" => $request->page_title,
            "page_slug" => $slug,
            "page_file" => $fileName,
            "page_content" => $request->page_content,
            "page_status" => $request->page_status,

        ]);

        if ($page) {
            return redirect(route('page.index'))->with('success', 'Successfully added!');
        }

        return back()->with('error', 'Not successfully added!');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pages = Pages::where('id', $id)->first();
        return view('backend.pages.edit')->with('pages', $pages);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (strlen($request->page_slug) > 3) {
            $slug = Str::slug($request->page_slug);
        } else {
            $slug = Str::slug($request->page_title);

        }


        if ($request->hasFile('page_file')) {
            $request->validate([
                'page_title' => 'required',
//                'page_content' => 'required',
                'page_file' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);
            $fileName = uniqid() . '.' . $request->page_file->getClientOriginalExtension();
            $request->page_file->move(public_path('images/pages'), $fileName);

            $page = Pages::where('id', $id)->update([
                "page_title" => $request->page_title,
                "page_slug" => $slug,
                "page_file" => $fileName,
                "page_content" => $request->page_content,
                "page_status" => $request->page_status,

            ]);

        } else {
            $page = Pages::where('id', $id)->update([
                "page_title" => $request->page_title,
                "page_slug" => $slug,
                "page_content" => $request->page_content,
                "page_status" => $request->page_status,

            ]);
        }

        $path = 'images/pages/' . $request->oldFile;
        if (file_exists($path)) {
            @unlink(public_path($path));
        }


        if ($page) {
            return back()->with('success', 'Successfully added!');
        }

        return back()->with('error', 'Not successfully added!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $page = Pages::find(intval($id));
        if ($page->delete()) {
            echo 1;
        }
        echo 0;
    }

    public function sortable()
    {
//        print_r($_POST['item']);

        foreach ($_POST['item'] as $key => $value) {
            $pages = Pages::find(intval($value));
            $pages->page_must = intval($key);
            $pages->save();
        }

        echo true;
    }
}
