@extends('layouts.app')
@section('title', 'Importar Transações de Cartão')
@section('content')
    <div class="card shadow">
        <div class="card-body">
            <h1 class="card-title text-center mb-4">Importar Transações de Cartão de Crédito</h1>

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

            <form id="importForm" action="{{ route('import.credit_card') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="bank" class="form-label">Banco:</label>
                    <select name="bank" id="bank" class="form-select" required>
                        <option value="xp">XP</option>
                        <option value="bancodobrasil">Banco do Brasil</option>
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

            <div class="text-center mt-3">
                <a href="{{ route('import.index') }}" class="btn btn-link">Voltar</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('importForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;
            const bank = document.getElementById('bank').value;
            const competence = document.getElementById('competence').value;
            const url = "{{ route('credit_card_transactions.exist') }}" + "?bank=" + encodeURIComponent(bank) + "&competence=" + encodeURIComponent(competence);

            try {
                const response = await fetch(url);
                const data = await response.json();
                if(data.exists) {
                    if(confirm("Já existem registros para essa competência e banco. Deseja sobrescrever?")) {
                        // Adiciona um input hidden para sobrescrever
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'overwrite';
                        hidden.value = 'true';
                        form.appendChild(hidden);
                        form.submit();
                    } else {
                        return;
                    }
                } else {
                    form.submit();
                }
            } catch(error) {
                console.error("Erro na verificação:", error);
                form.submit(); // Em caso de erro, envia o formulário normalmente
            }
        });
    </script>
@endsection
