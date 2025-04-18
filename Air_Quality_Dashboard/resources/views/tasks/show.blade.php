@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Task Details</h4>
                <div>
                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning btn-sm me-2">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this task?')">
                            <i class="fas fa-trash me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h2 class="mb-2">{{ $task->title }}</h2>
                        <div class="d-flex mb-3">
                            <span class="badge status-badge-{{ $task->status }} me-2 px-3 py-2">
                                {{ ucfirst($task->status) }}
                            </span>
                            <span class="badge bg-secondary px-3 py-2">
                                Priority: {{ $task->priority }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3">
                        <h5>Due Date</h5>
                        <p>{{ $task->due_date ? date('F d, Y', strtotime($task->due_date)) : 'No due date' }}</p>
                    </div>
                    <div class="col-md-3">
                        <h5>Created</h5>
                        <p>{{ $task->created_at->format('F d, Y') }}</p>
                    </div>
                    <div class="col-md-3">
                        <h5>Last Updated</h5>
                        <p>{{ $task->updated_at->format('F d, Y') }}</p>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5>Description</h5>
                        <div class="p-3 bg-light rounded">
                            {{ $task->description ?: 'No description provided.' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Tasks
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
