<?php

namespace App\Http\Controllers;


class IndexController extends Controller
{


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect('/admin/alipayopen');
        //return view('index.index');
    }


}
