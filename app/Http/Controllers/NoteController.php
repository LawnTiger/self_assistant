<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * 记事本
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $notes = Note::getNotes(Auth::id());

        return view('note.index', compact('notes'));
    }

    public function create()
    {
        return view('note.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
        ]);

        $input = $request->only(['title', 'content']);
        $input['user_id'] = Auth::id();
        Note::saveNote($input);

        return redirect(action('NoteController@index'));
    }

    public function edit($id)
    {
        $note = Note::findOrFail($id);
        $this->authorize('update', $note);

        return view('note.edit', compact('note'));
    }

    public function update(Request $request, $id)
    {
        $note = Note::findOrFail($id);
        $this->authorize('update', $note);

        $input = $request->only(['title', 'content']);
        Note::updateNote($id, $input);

        return redirect(action('NoteController@index'));
    }

    public function destroy($id)
    {
        $note = Note::findOrFail($id);
        $this->authorize('destroy', $note);
        Note::destroy($id);

        return redirect(action('NoteController@index'));
    }
}
