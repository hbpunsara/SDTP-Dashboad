@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Task List</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add New Task
        </a>
    </div>
</div>

<div class="row">
    @if(count($tasks) > 0)
        @foreach($tasks as $task)
            <div class="col-md-6 mb-3">
                <div class="card task-priority-{{ $task->priority }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">{{ $task->title }}</h5>
                            <span class="badge status-badge-{{ $task->status }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>
                        <p class="card-text text-muted">
                            {{ Str::limit($task->description, 100) }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}
                            </span>
                            <div>
                                <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" title="View details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-warning me-1" data-bs-toggle="tooltip" title="Edit task">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this task?')" data-bs-toggle="tooltip" title="Delete task">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="col-12 mt-3">
            {{ $tasks->links() }}
        </div>
    @else
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> No tasks found. <a href="{{ route('tasks.create') }}">Create a new task</a> to get started.
            </div>
        </div>
    @endif
</div>
@endsection
