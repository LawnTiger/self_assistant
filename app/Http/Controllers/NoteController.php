<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;

class NoteController extends Controller
{
    /**
     * 记事本
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory
     */
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

        $input = $request->only(['title', 'content']);
        $input['user_id'] = $request->user()->id;
        Note::saveNote($input);

        return redirect(action('NoteController@index'));
    }

    public function edit(Request $request, $id)
    {
        $note = Note::getNotes($request->user()->id, $id)[0];

        return view('note.edit', compact('note'));
    }

    public function update(Request $request, $id)
    {
        $input = $request->only(['title', 'content']);
        Note::updateNote($id, $input);

        return redirect(action('NoteController@index'));
    }

    public function destroy($id)
    {
        dd($id);
    }
}

