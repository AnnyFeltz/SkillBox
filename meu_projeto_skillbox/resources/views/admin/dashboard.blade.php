@extends('layouts.skillboxApp')

@section('title', 'Painel Administrativo')

@section('titulo')
Painel do Administrador
@endsection

@section('content')
<div class="container mt-4">

    {{-- Visão Geral --}}
    <h3>📊 Visão Geral</h3>
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white" style="background-color: #4f46e5;">
                <div class="card-body">
                    <h5>Usuários</h5>
                    <p class="fs-4">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white" style="background-color: #6366f1;">
                <div class="card-body">
                    <h5>Projetos Públicos</h5>
                    <p class="fs-4">{{ $totalProjetosPublicos }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white" style="background-color: #3730a3;">
                <div class="card-body">
                    <h5>Todos os Projetos</h5>
                    <p class="fs-4">{{ $totalProjetos }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white" style="background-color: #4c51bf;">
                <div class="card-body">
                    <h5>Ferramentas</h5>
                    <p class="fs-4">{{ $totalFerramentas }}</p>
                </div>
            </div>
        </div>
    </div>

    <hr>

    {{-- Lista de Usuários --}}
    <h3>👥 Lista de Usuários</h3>
    @if ($users->isEmpty())
        <p class="text-muted">Nenhum usuário cadastrado.</p>
    @else
        <div class="table-responsive mb-4">
            <table class="table align-middle table-hover table-bordered">
                <thead style="background-color: #4f46e5; color: white;">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Função</th>
                        <th>Criado em</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php $isAdmin = (bool) $user->is_admin; @endphp
                            <span class="badge" style="background-color: {{ $isAdmin ? '#dc2626' : '#6b7280' }}">
                                {{ $isAdmin ? 'Admin' : 'Usuário' }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    @endif

    <hr>

    {{-- Trabalhos Publicados --}}
    <h3>🌐 Trabalhos Publicados</h3>
    @if ($publicados->isEmpty())
        <p class="text-muted">Nenhum projeto publicado ainda.</p>
    @else
        <div class="d-flex align-items-center overflow-auto gap-2 mb-4">
            @foreach ($publicados as $canvas)
            <div class="card" style="min-width: 180px; max-width: 180px; flex-shrink: 0;">
                <div class="card-header text-center p-1">
                    <small><strong>{{ $canvas->user->name }}</strong></small>
                </div>
                <div class="card-body p-2">
                    @if ($canvas->preview_url)
                    <div class="db-miniatura-container mb-1">
                        <img src="{{ $canvas->preview_url }}" alt="Miniatura de {{ $canvas->titulo }}">
                    </div>
                    @else
                    <div class="db-miniatura-container db-mini-preview mb-1">
                        <span class="text-muted small">Sem preview</span>
                    </div>
                    @endif

                    <h6 class="card-title text-truncate" title="{{ $canvas->titulo }}">{{ $canvas->titulo }}</h6>
                    <a href="{{ route('canvas.visualizar', $canvas->id) }}" class="btn btn-sm btn-outline-primary w-100">Visualizar</a>
                </div>
            </div>
            @endforeach
        </div>

        @if ($publicados->count() > 6)
        <div class="text-end mt-2 mb-4">
            <a href="{{ route('canvas.publicados') }}" class="btn btn-secondary">Ver Todos</a>
        </div>
        @endif
    @endif

    <hr>

    {{-- Ferramentas --}}
    <h3>🛠️ Ferramentas</h3>
    <div class="d-flex align-items-center overflow-auto gap-2 mb-4">
        <a href="{{ route('tools.create') }}" class="btn btn-primary btn-circle flex-shrink-0" title="Criar Nova Ferramenta">+</a>

        @forelse ($ferramentas as $tool)
        <div class="card px-2 py-1" style="min-width: 140px; max-width: 140px; flex-shrink: 0; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <span class="text-truncate w-100 text-center" title="{{ $tool->nome }}">{{ $tool->nome }}</span>
        </div>
        @empty
        <span class="text-muted ms-2">Nenhuma ferramenta ainda.</span>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/konva@9/konva.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const previews = document.querySelectorAll('.db-mini-preview');

        previews.forEach(async (preview) => {
            const binId = preview.dataset.binId;
            const width = parseInt(preview.dataset.width) || 1000;
            const height = parseInt(preview.dataset.height) || 600;

            if (!binId) return;

            try {
                const res = await fetch(`https://api.jsonbin.io/v3/b/${binId}`, {
                    headers: {
                        'X-Master-Key': window.JSONBIN_API_KEY
                    }
                });
                const data = await res.json();
                const json = data.record;

                const container = document.createElement('div');
                container.style.position = 'absolute';
                container.style.left = '-9999px';
                container.style.width = width + 'px';
                container.style.height = height + 'px';
                document.body.appendChild(container);

                const stage = Konva.Node.create(json, container);
                await Konva.Util.requestAnimFrame(() => {});

                const dataURL = stage.toDataURL({ pixelRatio: 0.2 });

                preview.innerHTML = '';
                preview.style.backgroundImage = `url('${dataURL}')`;
                preview.style.backgroundSize = 'cover';
                preview.style.backgroundPosition = 'center';

                stage.destroy();
                container.remove();
            } catch (err) {
                preview.innerHTML = '<small class="text-danger">Erro no preview</small>';
                console.error('Erro ao carregar JSON para preview:', err);
            }
        });
    });
</script>
@endpush


@section('menu-items')
<li><a href="/dashboard"><i class="icon-dashboard material-symbols-outlined">dashboard</i> <span class="label">Dashboard</span></a></li>
<li><a href="/profile"><i class="icon-user material-symbols-outlined">account_circle</i> <span class="label">Perfil</span></a></li>
<li><a href="/canvas/publicados"><i class="icon-user material-symbols-outlined">gallery_thumbnail</i> <span class="label">Galeria</span></a></li>
<li><a href="/canvas"><i class="icon-user material-symbols-outlined">wall_art</i> <span class="label">Canvas</span></a></li>
<li><a href="/tools"><i class="icon-user material-symbols-outlined">construction</i> <span class="label">Ferramentas</span></a></li>
@endsection