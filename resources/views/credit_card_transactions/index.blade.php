<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Transações de Cartão</title>
</head>
<body>
    <h1>Transações de Cartão de Crédito</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="{{ route('credit_card_transactions.store') }}" method="post">
        @csrf
        <div>
            <label>Nome do Cartão:</label>
            <input type="text" name="credit_card_name" required>
        </div>
        <div>
            <label>Data:</label>
            <input type="date" name="transaction_date" required>
        </div>
        <div>
            <label>Descrição:</label>
            <input type="text" name="description" required>
        </div>
        <div>
            <label>Valor:</label>
            <input type="number" step="0.01" name="amount" required>
        </div>
        <div>
            <label>Total de Parcelas:</label>
            <input type="number" name="installments_total">
        </div>
        <div>
            <label>Nº da Parcela:</label>
            <input type="number" name="installment_number">
        </div>
        <div>
            <label>Data de Vencimento:</label>
            <input type="date" name="due_date">
        </div>
        <div>
            <label>Pago?</label>
            <select name="paid">
                <option value="0">Não</option>
                <option value="1">Sim</option>
            </select>
        </div>
        <button type="submit">Cadastrar Transação de Cartão</button>
    </form>

    <h2>Lista de Transações de Cartão</h2>
    <ul>
        @foreach($transactions as $transaction)
            <li>{{ $transaction->transaction_date }} - {{ $transaction->description }} - R$ {{ $transaction->amount }}</li>
        @endforeach
    </ul>
</body>
</html>
