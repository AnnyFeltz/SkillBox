<section>
    <header>
        <h2>Atualizar Senha</h2>
        <p>Use uma senha segura para proteger sua conta.</p>
    </header>

    <form method="POST" action="{{ route('password.update') }}" class="form-section mt-4">
        @csrf
        @method('PUT')

        <label for="current_password">Senha Atual</label>
        <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="input-field" />
        @error('current_password') <p class="error">{{ $message }}</p> @enderror

        <label for="password" class="mt-4">Nova Senha</label>
        <input id="password" name="password" type="password" required autocomplete="new-password" class="input-field" />
        @error('password') <p class="error">{{ $message }}</p> @enderror

        <label for="password_confirmation" class="mt-4">Confirmar Nova Senha</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="input-field" />
        @error('password_confirmation') <p class="error">{{ $message }}</p> @enderror

        <button type="submit" class="btn-primary mt-6">Salvar</button>

        @if (session('status') === 'password-updated')
            <p class="success-msg" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)">Salvo.</p>
        @endif
    </form>
</section>
