<?php

namespace App\Http\Controllers;

use App\Events\ThreadEvent;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ThreadPreviewResource;
use App\Http\Resources\ThreadResource;
use App\Message;
use App\MessageView;
use App\Notification;
use App\Notifications\NewMessage;
use App\Thread;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MessagesController extends Controller
{
    public function threads()
    {
        if (auth()->check()) {
            $threads = auth()->user()->threads
                ->sortByDesc('id');

            return ThreadPreviewResource::collection($threads);
        }
    }

    public function getThread(Request $request, $threadid)
    {
        //mark thread as read
        $request->user()->threads()->updateExistingPivot($threadid, ['read_at' => Carbon::now()]);

        //then get thread 
        $thread = Thread::findOrFail($threadid);
        return new ThreadResource($thread);
        // $thread = Thread::findOrFail($threadid);
        // // if($request->user()->can('view', $thread)) {
        //     return $thread;
        // // } else {
        // //     abort(403);
        // // }
    }

    public function getThreadMessages(Request $request, $threadid)
    {
        $thread = Thread::findOrFail($threadid);
        return $thread->messages()
            // ->whereHas('view', function($query) {
            //     $query->where("view", 1)->where("user_id", auth()->id());
            // })
            ->orderByDesc('created_at');
        // if($request->user()->can('view', $thread)) {
        //     return $thread->messages()->orderByDesc('created_at');
        // } else {
        //     abort(403);
        // }
    }

    public function replyToThread(Request $request, $threadid)
    {
        $request->validate([
            'body' => 'required|string'
        ]);

        $thread = Thread::findOrFail($threadid);
        $message = Message::create([
            'body' => $request->body,
            'sender_id' => auth()->id(),
            'thread_id' => $thread->id
        ]);

        //get message receiver
        $receiver = $thread->users->where('id', '!=', auth()->id())->first();
        // save message views
        $message->saveViews($receiver->id);
        //mark thread unread for receiver
        $receiver->threads()->updateExistingPivot($threadid, ['read_at' => null]);
        // broadcast(new ThreadEvent($thread->id, new MessageResource($message)));
        //send notifications to the receiver
        Notification::notifyUser($receiver, new NewMessage($message));
        return new MessageResource($message);

        // Return status????
    }

    /**
     *
     * Function to add a new thread
     *
     */
    public function newThread(Request $request)
    {
        //get existing thread or create a new one
        $thread = Thread::threadExistOrNew($request->get('sender'), $request->get('receiver'));

        $message = new Message([
            'body' => $request->get('body'),
            'thread_id' => $thread->id,
            'sender_id' => $request->get('sender')
        ]);
        //attach message to thread
        $thread->messages()->saveMany([
            $message
        ]);

        // save message views
        $message->saveViews($request->get('receiver'));

        //get message sender
        $sender = User::find($request->get('sender'));

        //get message receiver
        $receiver = User::find($request->get('receiver'));

        //mark thread unread for sender
        $sender->threads()->updateExistingPivot($thread->id, ['read_at' => now()]);

        broadcast(new ThreadEvent($thread->id, new MessageResource($message)));
        //send notifications to the receiver
        Notification::notifyUser($receiver, new NewMessage($message));
    }

    /**
     *
     * Mark thread as read so it dissappears from the messages notification dropdown
     *
     */
    public function markThreadRead(Request $request, $id)
    {
        $request->user()->threads()->updateExistingPivot($id, ['read_at' => Carbon::now()]);
    }


    public function markMessageRead($thread_id)
    {
        $thread = Thread::findOrFail($thread_id);
        $msgs = $thread->messages;
        $msgs->filter(function ($msg) {
            if ($msg->sender_id != auth()->id()) {
                return MessageView::where("message_id", $msg->id)->update(['view' => 1]);
            }
        })->values();
    }


    // public function replyToThread(Request $request, $threadid)
    // {
    //     $thread = Thread::findOrFail($threadid);
    //     if($request->user()->can('reply', $thread)) {
    //         $validated = $request->validate([
    //             'body' => 'required|string',
    //         ]);
    //         $message = new Message([
    //             'body' => $validated['body'],
    //             'user_id' => $request->user()->id,
    //             'thread_id' => $thread->id
    //         ]);
    //         $message->save();
    //         return $message;
    //     }
    // }

    function removeMessage($id = null)
    {
        $flag = Message::find($id)->delete();
        // $flag = Message::find($id)->hideMessage();

        return ['status' => 'success', 'message' => "Message remove successfully"];
    }
}
