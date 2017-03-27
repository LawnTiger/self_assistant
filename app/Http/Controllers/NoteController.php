<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $notes = Note::getNotes($request->user()->id);

        return view('note.index', compact('notes'));
    }

    public function create()
    {

    }

    public function destroy()
    {

    }
}

