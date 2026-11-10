<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fluxo — tarefas em dia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('tasks.index') }}"><span class="brand-mark">f.</span><span>fluxo<span class="brand-dot">.</span></span></a>
        <div class="workspace-label">ESPAÇO DE TRABALHO</div>
        <div class="workspace"><span class="workspace-icon">✳</span><span><strong>Meu espaço</strong><small>Organização pessoal</small></span><span class="chevron">⌄</span></div>
        <div class="nav-label">MENU</div>
        <nav class="nav-list">
            <a class="nav-link {{ $filter === 'all' ? 'active' : '' }}" href="{{ route('tasks.index', ['filter' => 'all']) }}"><span class="nav-icon">▦</span> Todas as tarefas <span class="nav-count">{{ $counts['all'] }}</span></a>
            <a class="nav-link {{ $filter === 'today' ? 'active' : '' }}" href="{{ route('tasks.index', ['filter' => 'today']) }}"><span class="nav-icon">◷</span> Para hoje <span class="nav-count">{{ $counts['today'] }}</span></a>
            <a class="nav-link {{ $filter === 'upcoming' ? 'active' : '' }}" href="{{ route('tasks.index', ['filter' => 'upcoming']) }}"><span class="nav-icon">▤</span> Próximas <span class="nav-count">{{ $counts['upcoming'] }}</span></a>
            <a class="nav-link {{ $filter === 'completed' ? 'active' : '' }}" href="{{ route('tasks.index', ['filter' => 'completed']) }}"><span class="nav-icon">✓</span> Concluídas <span class="nav-count">{{ $counts['completed'] }}</span></a>
        </nav>
        <div class="sidebar-bottom"><div class="tip-card"><span class="tip-spark">✦</span><strong>Um passo de cada vez.</strong><p>Pequenas tarefas feitas hoje viram grandes conquistas amanhã.</p></div><div class="profile"><span class="avatar">M</span><span><strong>Meu perfil</strong><small>Plano pessoal</small></span><span class="more">•••</span></div></div>
    </aside>

    <main class="main-area">
        <header class="topbar"><div class="breadcrumb">Meu espaço <span>/</span> <strong>{{ ['all' => 'Todas as tarefas', 'today' => 'Para hoje', 'upcoming' => 'Próximas', 'completed' => 'Concluídas'][$filter] ?? 'Todas as tarefas' }}</strong></div><div class="top-actions"><span class="date-chip">◷ &nbsp; {{ now()->translatedFormat('l, d \d\e F') }}</span><span class="top-avatar">M</span></div></header>
        <div class="page-content">
            <section class="welcome-row"><div><div class="eyebrow"><span class="eyebrow-dot"></span> SEU DIA, NO SEU RITMO</div><h1>{{ $filter === 'completed' ? 'Olha só o quanto você já fez.' : 'Vamos tirar isso da cabeça?' }}</h1><p class="subheading">{{ $filter === 'completed' ? 'Cada tarefa concluída é um passo a menos na lista.' : 'Organize suas tarefas e deixe o trabalho fluir.' }}</p></div><div class="progress-card"><div class="progress-copy"><span>PROGRESSO DE HOJE</span><strong>{{ $counts['all'] > 0 ? (int) round(($counts['completed'] / $counts['all']) * 100) : 0 }}<small>%</small></strong></div><div class="progress-track"><span style="width: {{ $counts['all'] > 0 ? (int) round(($counts['completed'] / $counts['all']) * 100) : 0 }}%"></span></div><small>{{ $counts['completed'] }} de {{ $counts['all'] }} tarefas concluídas</small></div></section>

            @if (session('success'))<div class="flash success">✓ &nbsp; {{ session('success') }}</div>@endif
            @if (session('error'))<div class="flash error">{{ session('error') }}</div>@endif
            @if ($errors->any())<div class="flash error">Confira os campos do formulário: {{ $errors->first() }}</div>@endif

            <section class="task-panel">
                <div class="panel-heading"><div><h2>{{ ['all' => 'Sua lista', 'today' => 'Para hoje', 'upcoming' => 'Próximas tarefas', 'completed' => 'Tarefas concluídas'][$filter] ?? 'Sua lista' }} <span class="heading-count">{{ $tasks->count() }}</span></h2><p>Um lugar simples para manter tudo em movimento.</p></div><div class="panel-controls"><span class="sort-label">↕ &nbsp; Mais próximas</span><button class="button-primary" type="button" onclick="document.getElementById('task-title').focus()"><span>＋</span> Nova tarefa</button></div></div>

                <form class="quick-add" action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <div class="quick-icon">＋</div>
                    <div class="quick-fields"><label class="sr-only" for="task-title">Nome da tarefa</label><input id="task-title" name="title" value="{{ old('title') }}" placeholder="O que precisa ser feito?" required maxlength="120"><div class="quick-extras"><label class="description-input"><span>↳</span><input name="description" value="{{ old('description') }}" placeholder="Adicionar uma descrição..." maxlength="500"></label><label class="date-input"><span>▦</span><input type="date" name="due_date" value="{{ old('due_date') }}" aria-label="Data de entrega"></label><label class="priority-input"><span>⚑</span><select name="priority" aria-label="Prioridade"><option value="low">Baixa</option><option value="medium" selected>Média</option><option value="high">Alta</option></select></label><button class="add-submit" type="submit">Adicionar <span>↗</span></button></div></div>
                </form>

                @if ($tasks->isEmpty())
                    <div class="empty-state"><div class="empty-art"><span>✓</span><i>✦</i></div><h3>{{ $filter === 'completed' ? 'Ainda não há tarefas concluídas' : 'Sua lista está começando agora' }}</h3><p>{{ $filter === 'completed' ? 'Quando concluir uma tarefa, ela aparece por aqui.' : 'Adicione uma tarefa acima e dê o primeiro passo.' }}</p><button type="button" class="empty-link" onclick="document.getElementById('task-title').focus()">＋ Criar minha primeira tarefa</button></div>
                @else
                    <div class="task-list">
                        @foreach ($tasks as $task)
                            <article class="task-row {{ $task['completed'] ? 'is-complete' : '' }}">
                                <form action="{{ route('tasks.update', $task['id']) }}" method="POST" class="check-form">@csrf @method('PATCH')<input type="hidden" name="toggle" value="1"><button class="task-check" type="submit" aria-label="{{ $task['completed'] ? 'Marcar como pendente' : 'Concluir tarefa' }}">{{ $task['completed'] ? '✓' : '' }}</button></form>
                                <div class="task-main"><div class="task-title">{{ $task['title'] }}</div>@if ($task['description'])<div class="task-description">{{ $task['description'] }}</div>@endif<div class="task-meta"><span class="priority priority-{{ $task['priority'] }}"><i></i>{{ ['low' => 'Baixa', 'medium' => 'Média', 'high' => 'Alta'][$task['priority']] }}</span>@if ($task['due_date'])<span class="due-date {{ ! $task['completed'] && $task['due_date'] < now()->toDateString() ? 'overdue' : '' }}">▦ &nbsp; {{ \Carbon\Carbon::parse($task['due_date'])->translatedFormat('d M') }}</span>@else<span class="due-date">▦ &nbsp; Sem prazo</span>@endif</div>
                                    <details class="edit-details"><summary>Editar</summary><form action="{{ route('tasks.update', $task['id']) }}" method="POST" class="edit-form">@csrf @method('PATCH')<input name="title" value="{{ $task['title'] }}" maxlength="120" required aria-label="Nome da tarefa"><input name="description" value="{{ $task['description'] }}" maxlength="500" placeholder="Descrição" aria-label="Descrição"><input type="date" name="due_date" value="{{ $task['due_date'] }}" aria-label="Data"><select name="priority" aria-label="Prioridade"><option value="low" @selected($task['priority'] === 'low')>Baixa</option><option value="medium" @selected($task['priority'] === 'medium')>Média</option><option value="high" @selected($task['priority'] === 'high')>Alta</option></select><button type="submit">Salvar</button></form></details>
                                </div>
                                <div class="task-actions"><span class="task-date-label">{{ $task['due_date'] ? \Carbon\Carbon::parse($task['due_date'])->translatedFormat('d/m/Y') : 'Sem prazo' }}</span><form action="{{ route('tasks.destroy', $task['id']) }}" method="POST" onsubmit="return confirm('Remover esta tarefa?')">@csrf @method('DELETE')<button class="delete-button" type="submit" aria-label="Remover tarefa">×</button></form></div>
                            </article>
                        @endforeach
                    </div>
                    <div class="list-footer"><span>Mostrando {{ $tasks->count() }} {{ $tasks->count() === 1 ? 'tarefa' : 'tarefas' }}</span><a href="{{ route('tasks.index', ['filter' => 'all']) }}">Ver todas <span>→</span></a></div>
                @endif
            </section>
            <footer class="page-footer"><span>Feito para deixar o dia mais leve <span class="footer-heart">♥</span></span><span>FLUXO &nbsp;·&nbsp; ORGANIZE. RESPIRE. SIGA.</span></footer>
        </div>
    </main>
</div>
</body>
</html>
