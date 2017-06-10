<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use Auth;
use Markdown;

class NoteController extends Controller
{
    /**
     * 记事本
     * @return \Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $notes = Note::getNotes(Auth::id());
        foreach ($notes as $note) {
            $note->content = Markdown::convertToHtml($note->content);
        }

        return view('note.index', compact('notes'));
    }

    public function show($id)
    {
        $note = Note::findOrFail($id);
        $this->authorize('own', $note);
        $note->content = Markdown::convertToHtml($note->content);

        return view('note.show', compact('note'));
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
        $this->authorize('own', $note);

        return view('note.edit', compact('note'));
    }

    public function update(Request $request, $id)
    {
        $note = Note::findOrFail($id);
        $this->authorize('own', $note);

        $input = $request->only(['title', 'content']);
        Note::updateNote($id, $input);

        return redirect(action('NoteController@index'));
    }

    public function destroy($id)
    {
        $note = Note::findOrFail($id);
        $this->authorize('own', $note);
        Note::destroy($id);
    }
}
