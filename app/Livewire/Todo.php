<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Todo extends Component
{
    use WithPagination, WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    // using php annotation
    #[Rule('required|min:3|max:500')]
    public $task_name = '';

    #[Rule('nullable|image|max:2048')]
    public $image;

    public $search_name = '';
    public $search_date = NULL;

    public $editTaskId = '';
    public $editTaskName = '';

    public function formSubmitHandler(){
        // validate methods validate all the public properties. So here we need to use validateonly() instead of validate(). As we have search public property
        // $this->Validate();
        $this->validateOnly('task_name');
        $this->validateOnly('image');
        // $this->validate([ 
        //     'task_name' => 'required|min:3|max:500',
        // ]);

        $file_name = NULL;
        if($this->image){
            $file_name = $this->image->store('uploads', 'public');
        }

        Task::create([
            'task_name' => $this->task_name,
            'file_name' => $file_name
        ]);

        // $this->task_name = '';
        $this->reset(['task_name', 'image']);

        $this->resetPage();

        Session::flash('success_message', 'Task created successfully.');
    }

    public function updateFormSubmitHandler(){
        $this->validate([ 
            'editTaskName' => 'required|min:3|max:500',
        ],[],[
            'editTaskName' => 'task name'
        ]);
        Task::find($this->editTaskId)->update([
            'task_name' => $this->editTaskName
        ]);

        // $this->task_name = '';
        $this->reset(['editTaskId','editTaskName']);

        Session::flash('success_message', 'Task updated successfully.');
    }

    public function markCompleteIncomplete($id){
        $task = Task::find($id);
        $task->update([
            'completed' => !$task->completed,
            'completed_at' => !$task->completed ? Carbon::now() : NULL
        ]);
    }

    public function edit($taskId){
        $this->editTaskId = $taskId;
        if($task = Task::find($taskId)){
            $this->editTaskName = $task->task_name;
        }
    }

    public function deleteTask($id){
        Task::where('id', $id)->delete();
        
        Session::flash('success_message', 'Task deleted successfully.');
    }

    public function render()
    {
        $search_date = $this->search_date;
        $tasks = Task::where('task_name', 'LIKE', "%{$this->search_name}%")
                ->when($search_date, function($query, $search_date){
                    $query->whereDate('created_at', $search_date);
                })
                ->latest()->paginate(10);

        return view('livewire.todo', compact('tasks'));
    }
}
