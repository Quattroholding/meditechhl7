<?php

namespace App\Livewire\User;

use App\Models\Client;
use App\Services\FileService;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;
    public $data;
    public $client_selected;
    public $clients;
    #[Validate('image|max:1024')] // 1MB Max
    public $avatar;

    public function mount(){
        $this->data = auth()->user();
        $this->client_selected = $this->data->clients()->pluck('client_id')->toArray();
        $this->clients = Client::pluck('name','id')->toArray();


        if(!$this->data->hasRole('admin'))
            $this->clients = $this->data->clients()->pluck('name','client_id')->toArray();
    }

    public function updatedAvatar()
    {
        $service = new FileService();
        $data['record_id'] =$this->data->id;
        $data['folder'] = 'users';
        $data['type']='avatar';
        $service->guardarArchivos([$this->avatar],$data);
        $this->data->update(['profile_picture'=>$this->data->avatar()->path]);
    }

    public function render()
    {
        return view('livewire.user.profile');
    }
}
