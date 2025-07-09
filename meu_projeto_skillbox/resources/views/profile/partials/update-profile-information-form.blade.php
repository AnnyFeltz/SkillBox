<section>
    <header>
        <h2>Informações do Perfil</h2>
        <p>Atualize seu nome e email.</p>
    </header>

    <form method="POST" action="{{ route('profile.update') }}" class="form-section mt-4">
        @csrf
        @method('PATCH')

        <label for="name">Nome</label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="input-field" />
        @error('name') <p class="error">{{ $message }}</p> @enderror

        <label for="email" class="mt-4">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="input-field" />
        @error('email') <p class="error">{{ $message }}</p> @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <p class="unverified-email mt-2">
            Seu email não foi verificado.
        <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="inline">
            @csrf
            <button type="submit" class="link-button">Clique aqui para reenviar o email de verificação.</button>
        </form>
        </p>
        @if (session('status') === 'verification-link-sent')
        <p class="alert-success mt-2">Novo link de verificação enviado!</p>
        @endif
        @endif

        <button type="submit" class="m-3 btn-primary mt-6">Salvar</button>

        @if (session('status') === 'profile-updated')
        <p class="success-msg" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)">Salvo.</p>
        @endif
    </form>
</section>