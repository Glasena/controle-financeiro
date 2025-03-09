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

            <form action="{{ route('import.credit_card') }}" method="post" enctype="multipart/form-data">
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
@endsection
