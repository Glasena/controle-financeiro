<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Transações Bancárias</title>
</head>
<body>
    <h1>Transações Bancárias</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="{{ route('transactions.store') }}" method="post">
        @csrf
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
            <label>Banco:</label>
            <input type="text" name="bank" required>
        </div>
        <div>
            <label>Tipo:</label>
            <select name="type" required>
                <option value="normal">Normal</option>
                <option value="credit_card_payment">Pagamento de Cartão</option>
                <option value="other">Outro</option>
            </select>
        </div>
        <button type="submit">Cadastrar Transação</button>
    </form>

    <h2>Lista de Transações</h2>
    <ul>
        @foreach($transactions as $transaction)
            <li>{{ $transaction->transaction_date }} - {{ $transaction->description }} - R$ {{ $transaction->amount }}</li>
        @endforeach
    </ul>
</body>
</html>
