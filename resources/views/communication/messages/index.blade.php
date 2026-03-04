@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">Messages</h1>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-4">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Inbox</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="nav nav-pills flex-column">
                                    @forelse($threads as $thread)
                                        @php
                                            $otherUser = $thread->otherUser;
                                            $latestMsg = $thread->messages->first();
                                            $unreadCount = $thread->messages
                                                ->where('sender_id', '!=', auth()->id())
                                                ->whereNull('read_at')
                                                ->count();
                                        @endphp
                                        <li class="nav-item border-bottom">
                                            <a href="{{ route('messages.show', $thread->id) }}"
                                                class="nav-link {{ isset($activeThread) && $activeThread->id == $thread->id ? 'bg-light' : '' }}">
                                                <div class="d-flex justify-content-between">
                                                    <span class="font-weight-bold text-dark">{{ $otherUser->name }}</span>
                                                    @if ($unreadCount > 0)
                                                        <span class="badge badge-danger">{{ $unreadCount }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-muted text-sm text-truncate mt-1">
                                                    {{ $latestMsg ? $latestMsg->body : 'No messages yet.' }}
                                                </div>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="nav-item p-3 text-muted text-center">No conversations found.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        @if (isset($activeThread))
                            <div class="card direct-chat direct-chat-primary card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Chat with <strong>{{ $activeThread->otherUser->name }}</strong>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <div class="direct-chat-messages" style="height: 500px;">
                                        @foreach ($activeThread->messages as $msg)
                                            @php $isMe = $msg->sender_id === auth()->id(); @endphp

                                            <div class="direct-chat-msg {{ $isMe ? 'right' : '' }}">
                                                <div class="direct-chat-infos clearfix">
                                                    <span
                                                        class="direct-chat-name {{ $isMe ? 'float-right' : 'float-left' }}">{{ $msg->sender->name }}</span>
                                                    <span
                                                        class="direct-chat-timestamp {{ $isMe ? 'float-left' : 'float-right' }}">{{ $msg->created_at->format('M d, g:i A') }}</span>
                                                </div>
                                                <img class="direct-chat-img"
                                                    src="{{ asset('dist/img/user2-160x160.jpg') }}" alt="User">

                                                <div class="direct-chat-text">
                                                    {{ $msg->body }}

                                                    @if ($msg->attachment_path)
                                                        <div class="mt-2">
                                                            <a href="{{ asset('storage/' . $msg->attachment_path) }}"
                                                                target="_blank" class="text-white text-underline">
                                                                <i class="fas fa-paperclip"></i> View Attachment
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <form action="{{ route('messages.store', $activeThread->id) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text" name="body" placeholder="Type Message ..."
                                                class="form-control" autocomplete="off">
                                            <span class="input-group-append">
                                                <label class="btn btn-default mb-0" style="cursor: pointer;">
                                                    <i class="fas fa-paperclip"></i>
                                                    <input type="file" name="attachment" class="d-none">
                                                </label>
                                                <button type="submit" class="btn btn-primary"><i
                                                        class="fas fa-paper-plane"></i> Send</button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="card card-outline card-info text-center p-5">
                                <h4 class="text-muted"><i class="fas fa-comments fa-3x mb-3"></i><br>Select a conversation
                                    from your inbox to start messaging.</h4>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
