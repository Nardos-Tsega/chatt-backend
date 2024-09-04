<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
    public function sendMessages(Request $request) {
        $validated = $request->validate([
            'receiver_id' => 'required|integer',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
        ]);

        // Broadcast the message to the receiver

        return response()->json($message, 201);
    }

    public function getMessages($userId) {
        $message = Message::where(function($query) use ($userId) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($userId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', auth()->id());
        })->get();

        return response()->json($message, 200);
    }
}
