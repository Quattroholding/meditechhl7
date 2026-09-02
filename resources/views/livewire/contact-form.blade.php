<div class="contact-form-wrapper">
    @if($successMessage)
        <div class="success-message">
            <svg class="success-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <p>{{ (string) $successMessage }}</p>
        </div>
    @endif
    {{--}}
    @error('form')
        <div class="error-message">
            <p>{{ is_array($message) ? reset($message) : $message }}</p>
        </div>
    @enderror
    {{--}}

    <form wire:submit.prevent="submit" class="contact-form">
        <div class="form-row">
            <div class="form-group">
                <label for="name">Nombre Completo <span class="required">*</span></label>
                <input
                    type="text"
                    id="name"
                    wire:model="name"
                    placeholder="Tu nombre"
                    class="form-input @error('name') error @enderror"
                >
                @error('name')
                    <span class="error-text">{{ is_array($message) ? reset($message) : $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico <span class="required">*</span></label>
                <input
                    type="email"
                    id="email"
                    wire:model="email"
                    placeholder="tu@email.com"
                    class="form-input @error('email') error @enderror"
                >
                @error('email')
                    <span class="error-text">{{ is_array($message) ? reset($message) : $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="phone">Teléfono</label>
                <input
                    type="tel"
                    id="phone"
                    wire:model="phone"
                    placeholder="+507 1234-5678"
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label for="company">Empresa</label>
                <input
                    type="text"
                    id="company"
                    wire:model="company"
                    placeholder="Nombre de tu empresa"
                    class="form-input"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="message">Mensaje <span class="required">*</span></label>
            <textarea
                id="message"
                wire:model="message"
                rows="5"
                placeholder="Cuéntanos cómo podemos ayudarte..."
                class="form-textarea @error('message') error @enderror"
            ></textarea>
            @error('message')
                <span class="error-text">{{ is_array($message) ? reset($message) : $message }}</span>
            @enderror
            <div class="char-count">
                {{ strlen((string) (is_array($message) ? '' : $message)) }}/1000 caracteres
            </div>
        </div>

        <button type="submit" class="submit-btn" wire:loading.attr="disabled">
            <span wire:loading.remove>
                Enviar Mensaje
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span wire:loading>
                <svg class="spinner" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.25"/>
                    <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="4" fill="none" stroke-linecap="round"/>
                </svg>
                Enviando...
            </span>
        </button>
    </form>
</div>
