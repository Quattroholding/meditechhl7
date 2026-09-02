<?php

namespace App\Livewire;

use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ContactForm extends Component
{
    public $name = '';

    public $email = '';

    public $phone = '';

    public $company = '';

    public $message = '';

    public $successMessage = '';

    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'message' => 'required|string|min:10|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'Debe ingresar un correo electrónico válido',
            'message.required' => 'El mensaje es requerido',
            'message.min' => 'El mensaje debe tener al menos 10 caracteres',
            'message.max' => 'El mensaje no puede exceder 1000 caracteres',
        ];
    }

    public function submit()
    {
        $this->validate();

        try {
            Mail::to('business@meditecpty.com')->send(new ContactFormMail([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'company' => $this->company,
                'message' => $this->message,
            ]));

            $this->successMessage = '¡Gracias por contactarnos! Hemos recibido tu mensaje y te responderemos pronto.';

            // Reset form
            $this->reset(['name', 'email', 'phone', 'company', 'message']);
        } catch (\Exception $e) {
            $this->addError('form', 'Hubo un error al enviar el mensaje. Por favor, intenta nuevamente.');
            logger()->error('Error sending contact form', ['error' => $e->getMessage()]);
        }
    }

    public function updated($property, $value)
    {
        // Ensure all string properties are always cast to string
        if (in_array($property, ['name', 'email', 'phone', 'company', 'message', 'successMessage'])) {
            $this->{$property} = is_array($value) ? '' : (string) $value;
        }
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
