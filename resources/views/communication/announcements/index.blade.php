@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">Announcement Board</h1>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">New Announcement</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ resolveRoute('announcements.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" name="title" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Message Content</label>
                                        <textarea name="content" class="form-control" rows="4" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Target Audience</label>
                                        <select name="target_role" class="form-control">
                                            <option value="">Broadcast to Everyone</option>
                                            <option value="Student">Students Only</option>
                                            <option value="Teacher">Teachers Only</option>
                                            <option value="Parent">Parents Only</option>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 form-group">
                                            <label>Publish Date</label>
                                            <input type="datetime-local" name="publish_at" class="form-control">
                                            <small class="text-muted">Leave blank for immediate</small>
                                        </div>
                                        <div class="col-6 form-group">
                                            <label>Expiration Date</label>
                                            <input type="datetime-local" name="expires_at" class="form-control">
                                            <small class="text-muted">Leave blank for permanent</small>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-bullhorn"></i>
                                        Publish</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title">Active Board</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="products-list product-list-in-card pl-2 pr-2">
                                    @forelse($announcements as $post)
                                        <li class="item">
                                            <div class="product-info ml-0">
                                                <span
                                                    class="product-title font-weight-bold text-lg">{{ $post->title }}</span>

                                                <span class="float-right">
                                                    @if ($post->target_role)
                                                        <span class="badge badge-warning">{{ $post->target_role }}s
                                                            Only</span>
                                                    @else
                                                        <span class="badge badge-success">Everyone</span>
                                                    @endif

                                                    <form action="{{ resolveRoute('announcements.destroy', $post->id) }}"
                                                        method="POST" class="d-inline ml-2">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-danger"
                                                            onclick="return confirm('Delete this announcement?')"><i
                                                                class="fas fa-trash"></i></button>
                                                    </form>
                                                </span>

                                                <span class="product-description mt-2 text-dark"
                                                    style="white-space: pre-wrap;">{{ $post->content }}</span>

                                                <div class="text-muted mt-2 text-sm">
                                                    <i class="fas fa-user-edit"></i> {{ $post->author->name }} &nbsp; |
                                                    &nbsp;
                                                    <i class="fas fa-clock"></i> {{ $post->publish_at->diffForHumans() }}
                                                    @if ($post->expires_at)
                                                        &nbsp; | &nbsp; <i class="fas fa-calendar-times text-danger"></i>
                                                        Expires: {{ $post->expires_at->format('M d') }}
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <div class="p-4 text-center text-muted">No active announcements.</div>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
