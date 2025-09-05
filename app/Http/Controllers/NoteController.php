<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index()
    {
        // Get logged-in user's notes with pagination
        $notes = Note::where('user_id', Auth::id())
                     ->latest()
                     ->paginate(10);

        return view('notes.index', compact('notes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|max:100',
            'content' => 'required',
        ]);

        $note = Note::create([
            'user_id' => Auth::id(),
            'title'   => $request->title,
            'content' => $request->content,
        ]);

        return response()->json(['success' => true, 'note' => $note]);
    }

    public function update(Request $request, Note $note)
    {
        // Ensure the note belongs to the logged-in user
        if ($note->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title'   => 'required|max:100',
            'content' => 'required',
        ]);

        $note->update($request->only('title', 'content'));

        return response()->json(['success' => true, 'note' => $note]);
    }

    public function destroy(Note $note)
    {
        // Ensure the note belongs to the logged-in user
        if ($note->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $note->delete();

        return response()->json(['success' => true]);
    }
}
