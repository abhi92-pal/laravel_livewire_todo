
<div class="mt-2">
    {{-- Stop trying to control. --}}
    @if(Session::has('success_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ Session::get('success_message') }}
            {{-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button> --}}
        </div>
    @endif
    <h2>Todo Manager</h2>
    <div class="card mb-2">
        <div class="card-header">
            Add your todo here
        </div>
        <div class="card-body">
            <form action="" wire:submit.prevent="formSubmitHandler">
                <div class="form-group">
                    <label for="">Task Name</label>
                    <textarea wire:model="task_name" placeholder="Enter your task" class="form-control" cols="30" rows="5"></textarea>
                    <div class="text-danger">@error('task_name') {{ $message }} @enderror</div>
                </div>
                <div class="form-group">
                    <label for="">Upload File</label>
                    <input type="file" class="form-control" wire:model="image">
                    <div class="text-danger">@error('image') {{ $message }} @enderror</div>
                    <div wire:loading wire:target="image">Uploading...</div>
                    @if ($image) 
                        {{-- <img src="{{ $image->temporaryUrl() }}"> --}}
                    @endif
                </div>
                <button class="btn btn-success mt-3">Add Task</button>
            </form>
        </div>
    </div>
    <h3>Filter your data</h3>
    <div class="row mb-2">
        <div class="col-md-6">
            <div class="form-group">
                <input type="text" class="form-control" wire:model.live.debounce.500ms="search_name" placeholder="Search your task">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <input type="date" class="form-control" wire:model.live="search_date">
            </div>
        </div>

    </div>
    <div class="card">
        <div class="card-header">
            Your todo list logs
        </div>
        <div class="card-body">

            @forelse ($tasks as $task)
            <div class="card p-2 mb-2" wire:key="{{ $task->id }}">
                <div class="@if($task->completed) completed @endif">
                    @if($editTaskId == $task->id)
                        <form action="" wire:submit.prevent="updateFormSubmitHandler">
                            <div class="form-group">
                                <label for="">Task Name</label>
                                <textarea wire:model="editTaskName" placeholder="Enter your task" class="form-control" cols="30" rows="5"></textarea>
                                <div class="text-danger">@error('editTaskName') {{ $message }} @enderror</div>
                            </div>
                            {{-- <div class="form-group">
                                <label for="">Upload File</label>
                                <input type="file" class="form-control">
                            </div> --}}
                            <button class="btn btn-success mt-3">Update Task</button>
                            <button type="button" class="btn btn-danger mt-3" wire:click="edit('')">Cancel</button>
                        </form>
                    @else
                        <input type="checkbox" wire:change="markCompleteIncomplete({{ $task->id }})" @if($task->completed) checked @endif>
                        {{ $task->task_name }}
                    @endif
                        
                    @if($task->file_name)
                        {{-- <i class="fa fa-image"></i> --}}
                        <br>
                        <a href="{{ asset('storage/'.$task->file_name) }}" target="_blank">
                            <img src="{{ asset('storage/'.$task->file_name) }}" style="width:300px;" alt="">
                        </a>
                    @endif
                    <a href="javascript:void(0)" style="float:right;" class="text-danger" wire:click="deleteConfirm({{ $task->id }})">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                    @if(!$task->completed)
                        <a href="javascript:void(0)" style="float:right;" class="text-success mx-3" wire:click="edit({{ $task->id }})">
                            <i class="fa-solid fa-file-pen"></i>
                        </a>
                    @endif
                </div>
                <i>Created On: {{ date('d-m-Y', strtotime($task->created_at)) }}</i>
                @if($task->completed)
                <i>Completed On: {{ date('d-m-Y', strtotime($task->completed_at)) }}</i>
                @endif
            </div>    
            @empty
                <div class="text-center">
                    No task found
                </div>
            @endforelse

            {{ $tasks->links() }}
        </div>

    </div>
    
    <script>
        window.addEventListener('swal-success', (event) => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text ?? '',
                icon: 'success'
            });
        });
        
        window.addEventListener('swal-confirm', (event) => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text ?? '',
                icon: 'warning',
                showConfirmButton: true,
                confirmButtonText: 'Yes, Delete it!',
                showCancelButton: true,
            }).then(response => {
                if(response.isConfirmed){
                    console.log(event.detail.id);
                    @this.dispatch('deleteTask', [event.detail.id])

                }
            });
        });

        // document.addEventListener('livewire:initialized', () => {
        //     @this.on('post-created', (event) => {
        //         //
        //     });
        // });
    </script>
</div>

