@extends('layouts.app')
@section('title', 'Importação de CSV - Controle Financeiro')
@section('content')
    <div class="card shadow">
        <div class="card-body">
            <h1 class="card-title text-center mb-4">Importação de CSV</h1>
            <p class="text-center">Escolha o tipo de importação:</p>
            <ul class="list-inline text-center">
                <li class="list-inline-item m-2">
                    <a href="{{ route('import.transactions.form') }}" class="btn btn-primary">
                        Importar Transações Bancárias
                    </a>
                </li>
                <li class="list-inline-item m-2">
                    <a href="{{ route('import.credit_card.form') }}" class="btn btn-secondary">
                        Importar Transações de Cartão de Crédito
                    </a>
                </li>
            </ul>
        </div>
    </div>
@endsection
