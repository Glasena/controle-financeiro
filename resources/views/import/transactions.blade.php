@extends('layouts.app')
@section('title', 'Importar Transações')
@section('content')
    <div class="card shadow">
        <div class="card-body">
            <h1>Importar Transações</h1>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="importForm" action="{{ route('transactions.import') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="bank" class="form-label">Banco:</label>
                    <select name="bank" id="bank" class="form-select" required>
                        <option value="bancodobrasil">Banco do Brasil</option>
                        <option value="xp">XP</option>
                        <option value="nubank">Nubank</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="competence" class="form-label">Competência (Mês/Ano):</label>
                    <input type="month" name="competence" id="competence" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="file" class="form-label">Arquivo:</label>
                    <input type="file" name="file" id="file" class="form-control" accept=".csv, .txt, .pdf" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.getElementById('importForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const bank = document.getElementById('bank').value;
        const competence = document.getElementById('competence').value;
        const url = "{{ route('transactions.exist') }}?bank=" + encodeURIComponent(bank) + "&competence=" + encodeURIComponent(competence);
        
        try {
            const response = await fetch(url);
            const data = await response.json();
            if(data.exists) {
                if(confirm("Já existem registros para essa competência e banco. Deseja sobrescrever?")) {
                    // Cria um input hidden para sobrescrever
                    const hiddenOverwrite = document.createElement('input');
                    hiddenOverwrite.type = 'hidden';
                    hiddenOverwrite.name = 'overwrite';
                    hiddenOverwrite.value = 'true';
                    form.appendChild(hiddenOverwrite);
                    form.submit();
                } else {
                    return;
                }
            } else {
                form.submit();
            }
        } catch (error) {
            console.error("Erro na verificação:", error);
            form.submit();
        }
    });
    </script>
@endsection
