<?php

namespace App\Http\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UserIndex extends Component
{
    public $search;
    public $username,
        $firstName,
        $lastName,
        $email,
        $password;
    public $userId;
    public $editMode = false;
    protected $rules = [
        'username' => 'required',
        'email' => 'required|email',
        'firstName' => 'required',
        'lastName' => 'required',
        'password' => 'required',
    ];
    public function storeUser()
    {
        $this->validate();
        User::create([
            'username' => $this->username,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'password' => Hash::make($this->password),

        ]);
        $this->reset();
        $this->dispatchBrowserEvent('closeModal');
        session()->flash('user_message','User Successfully Created');

    }
    public function mount()
    {
        $this->reset();
    }
    public function render()
    {

        $users = User::all();
        if (strlen($this->search) > 2) {
            $search = '%' . $this->search . '%';
            $this->users =   User::where('username', 'LIKE', $search);
        }



        return view('livewire.users.user-index', ['users' => $users])
            ->layout('layouts.main');
    }
    public function showEditModal($id)
    {
        $this->reset();
        $this->editMode = true;
        //find UserId
        $this->userId = $id;
        //load UserData
        $this->loadUser();
        //showModal
        $this->dispatchBrowserEvent('showModal');
    }
    public function closeModal()
    {
        $this->dispatchBrowserEvent('closeModal');
        $this->reset();
    }
    public function updateUser()
    {
        $validated = $this->validate([
            'username' => 'required',
            'email' => 'required|email',
            'firstName' => 'required',
            'lastName' => 'required',
        ]);
        $user = User::find($this->userId);
        $user->update($validated);
        $this->reset();
        $this->dispatchBrowserEvent('closeModal');
        session()->flash('user_message', 'User Successfully Updated');
    }
    public function deleteUser($id)
    {
        $user = User::find($id);
        $user->delete();
        session()->flash('user_message', 'User Successfully Deleted');
    }
    public function loadUser()
    {

        $user = User::find($this->userId);
        $this->username = $user->username;
        $this->firstName = $user->first_name;
        $this->lastName = $user->last_name;
        $this->email = $user->email;
    }
}
