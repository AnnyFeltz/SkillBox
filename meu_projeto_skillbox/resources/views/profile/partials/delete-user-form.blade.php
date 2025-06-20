<section>
    <header>
        <h2>Deletar Conta</h2>
        <p class="text-red-600">Ao deletar sua conta, seus dados serão apagados para sempre.</p>
    </header>

    <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Você tem certeza? Essa ação é irreversível!');" class="mt-4">
        @csrf
        @method('DELETE')

        <button type="submit" class="btn-danger">Deletar Conta</button>
    </form>
</section>
