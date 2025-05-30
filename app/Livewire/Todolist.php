<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;

class Todolist extends Component
{
    use WithPagination;

    #[Rule('required|min:3|max:30')]
    public $name;

    public $search;

    public function create(){
        $validated = $this->validateOnly('name');

        Todo::create($validated);

        $this -> reset('name');

        session()->flash('success', 'Created!');
    }

public function delete($todoID){
    Todo::find($todoID)->delete();
}

    public function render()
    {
        return view('livewire.todolist',[
            'todos' => Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(3)
        ]);
    }
}
