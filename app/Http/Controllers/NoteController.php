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
        return view('note.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        Note::saveNote($input);

        return redirect('note');
    }

    public function destroy($id)
    {
        dd($id);
    }
}

