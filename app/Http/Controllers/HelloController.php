<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hello;
class HelloController extends Controller
{
    public function index(){
        $Hellos=Hello::all();
        return view('home',['Hellos'=>$Hellos]);

    }
    public function create(){
        return view('AjouteHello');
    }
    public function store (Request $request){
        $validated =$request->validate([
            "nom"=>'required|string|max:250',
            "age"=>'required|integer|max:100'
        ]);

        Hello::create($validated);
        return redirect()->route('home')->with('sucess','ajouter avec sucese');
    }
}
