<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Customer;
use App\Models\Message;
use App\Services\MetaMessagingService;
use Illuminate\Http\Request;

class OmnichannelController extends Controller
{
    public function index(Request $request)
    {
        $selectedConversationId = $request->query('conversation');

        // Mark as read if a conversation is selected
        if ($selectedConversationId) {
            Message::where('conversation_id', $selectedConversationId)
                ->where('direction', 'incoming')
                ->where('status', '!=', 'read')
                ->update(['status' => 'read']);
        }

        $conversations = Conversation::with(['customer', 'channel', 'latestMessage'])
            ->withCount('unreadMessages')
            ->orderBy('updated_at', 'desc')
            ->get();

        $selectedConversation = null;
        if ($selectedConversationId) {
            $selectedConversation = Conversation::with(['customer', 'channel', 'messages', 'internalNotes'])
                ->find($selectedConversationId);
        }

        if (!$selectedConversation && $conversations->isNotEmpty()) {
            $selectedConversation = Conversation::with(['customer', 'channel', 'messages', 'internalNotes'])
                ->find($conversations->first()->id);
            
            // Mark the default first conversation as read too
            if ($selectedConversation) {
                Message::where('conversation_id', $selectedConversation->id)
                    ->where('direction', 'incoming')
                    ->where('status', '!=', 'read')
                    ->update(['status' => 'read']);
                    
                // Refresh conversations to update the count for the first one
                $conversations = Conversation::with(['customer', 'channel', 'latestMessage'])
                    ->withCount('unreadMessages')
                    ->orderBy('updated_at', 'desc')
                    ->get();
            }
        }

        return view('omnichannel.index', compact('conversations', 'selectedConversation'));
    }

    public function show(Conversation $conversation)
    {
        $conversation->load(['customer', 'channel', 'messages', 'internalNotes']);

        $conversations = Conversation::with(['customer', 'channel', 'latestMessage'])
            ->orderBy('updated_at', 'desc')
            ->limit(15)
            ->get();

        return view('omnichannel.show', compact('conversation', 'conversations'));
    }

    public function syncMessages(Request $request, Conversation $conversation)
    {
        $lastId = $request->query('last_id');

        $messages = $conversation->messages()
            ->when($lastId, function ($query) use ($lastId) {
                return $query->where('id', '>', $lastId);
            })
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'direction' => $message->direction,
                    'created_at_human' => $message->created_at->format('h:i a'),
                    'sender_name' => $message->sender_type === 'customer' ? optional($message->conversation->customer)->name : 'Siz',
                ];
            }),
        ]);
    }

    public function storeMessage(Request $request, Conversation $conversation, MetaMessagingService $messagingService)
    {
        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'company_id' => $conversation->company_id,
            'sender_type' => auth()->check() ? 'user' : 'system',
            'sender_id' => auth()->id(),
            'direction' => 'outgoing',
            'body' => $request->input('body'),
            'payload' => [],
            'status' => 'pending',
        ]);

        if ($conversation->channel) {
            $result = null;

            if ($conversation->channel->provider === 'whatsapp') {
                $result = $messagingService->sendWhatsAppTextMessage(
                    $conversation->company,
                    $conversation->external_thread_id,
                    $request->input('body')
                );
            } elseif ($conversation->channel->provider === 'instagram') {
                $result = $messagingService->sendInstagramTextMessage(
                    $conversation->company,
                    $conversation->external_thread_id,
                    $request->input('body')
                );
            }

            if ($result) {
                $message->update([
                    'status' => $result['success'] ? 'sent' : 'failed',
                    'payload' => $result,
                ]);
            } else {
                $message->update(['status' => 'sent']);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'direction' => $message->direction,
                    'created_at_human' => $message->created_at->format('h:i a'),
                ],
            ]);
        }

        return back();
    }
    public function syncSidebar(Request $request)
    {
        $search = $request->query('search');
        $selectedId = $request->query('selected_id');

        $conversations = Conversation::with(['customer', 'channel', 'latestMessage'])
            ->withCount(['unreadMessages' => function($query) {
                $query->where('direction', 'incoming')->where('status', '!=', 'read');
            }])
            ->when($search, function($query) use ($search) {
                return $query->whereHas('customer', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        $selectedConversation = null;
        if ($selectedId) {
            $selectedConversation = Conversation::find($selectedId);
        }

        return view('omnichannel.partials._sidebar_list', compact('conversations', 'selectedConversation'))->render();
    }

    public function getCustomer(Customer $customer)
    {
        return response()->json([
            'success' => true,
            'customer' => $customer
        ]);
    }

    public function updateCustomer(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $customer->update($request->only(['name', 'email', 'phone', 'company', 'address']));

        return response()->json([
            'success' => true,
            'message' => 'Müşteri bilgileri güncellendi.',
            'customer' => $customer
        ]);
    }
}

