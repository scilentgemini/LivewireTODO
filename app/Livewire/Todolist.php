<?php

namespace App\Livewire;

use Exception;
use App\Models\Todo;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;

class Todolist extends Component
{
    use WithPagination;

    #[Rule('required|min:3|max:30')]
    public $name;

    public $search;


    public $editingTodoID;
    #[Rule('required|min:3|max:30')]
    public $editingTodoName;

    public function create()
    {
        $validated = $this->validateOnly('name');

        Todo::create($validated);

        $this->reset('name');

        session()->flash('success', 'Created!');
        $this->resetPage();
    }

    public function delete($todoID)
    {
        try{
            Todo::findOrfail($todoID)->delete();
        }catch(Exception $e){
            session()->flash('error', 'Failed to delete todo');
            return;
        }
    }

    public function toggle($todoID)
    {
        $todo = Todo::find($todoID);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit($todoID)
    {
        $this->editingTodoID = $todoID;
        $this->editingTodoName = Todo::find($todoID)->name;
    }

    public function cancelEdit()
    {
        $this->reset('editingTodoID', 'editingTodoName');
    }

    public function update()
    {

        $this->validateOnly('editingTodoName');
        Todo::find($this->editingTodoID)->update(
            [
                'name' => $this->editingTodoName
            ]
        );

        $this->cancelEdit();
    }

    public function render()
    {
        return view('livewire.todolist', [
            'todos' => Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(3)
        ]);
    }
}
