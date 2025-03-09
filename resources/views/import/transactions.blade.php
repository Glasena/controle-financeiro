<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Importar Transações</title>
</head>
<body>
    <h1>Importar Transações</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <div style="color: red;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transactions.import') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="bank">Banco:</label>
            <select name="bank" id="bank" required>
                <option value="bancodobrasil">Banco do Brasil</option>
                <option value="xp">XP</option>
                <option value="nubank">Nubank</option>
            </select>
        </div>
        <div>
            <label for="competence">Competência (Mês/Ano):</label>
            <input type="month" name="competence" id="competence" required>
        </div>
        <div>
            <label for="file">Arquivo:</label>
            <input type="file" name="file" id="file" accept=".csv, .txt, .pdf" required>
        </div>
        <button type="submit">Importar</button>
    </form>
</body>
</html>
