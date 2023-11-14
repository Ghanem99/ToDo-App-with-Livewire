<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination; 
use App\Models\Todo;

class TodoList extends Component
{
    use WithPagination;

    public $title;
    public $search;

    public $editingTodoID;
    public $editingTodoTitle;

    protected $rules = [
        'title' => 'required|min:6',
        'editingTodoTitle' => 'required|min:6'           
    ];

    public function create() 
    {
        $this->validateOnly('title');

        Todo::create([
            'title' => $this->title,
        ]);

        $this->reset('title');

        session()->flash('success', 'Todo Created Successfully.');
        
        $this->resetPage();
    }

    public function delete($id)
    {
        Todo::find($id)->delete();
    }

    public function toggle($id)
    {
        $todo = Todo::find($id);
        $todo->completed = !$todo->completed;
        $todo->save();

        // need to know, from where this attribute 'completed' is called
    }

    public function edit($id)
    {
        $this->editingTodoID = $id;
        $this->editingTodoTitle = Todo::find($id)->title; 
    }

    public function update() 
    {
        $this->validateOnly('editingTodoTitle');

        Todo::find($this->editingTodoID)->update([
            'title' => $this->editingTodoTitle,
        ]);

        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        // $this->editingTodoID = null;
        // $this->editingTodoTitle = null;

        $this->reset(['editingTodoID', 'editingTodoTitle']);
    }

    public function render()
    {
        return view('livewire.todo-list', [
            'todos' => Todo::latest()
                            ->where('title', 'like', "%{$this->search}%")
                            ->paginate(5),
        ]);
        
    }
}
