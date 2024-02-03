{{-- <!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="css/home.css">
    <script src="{{ asset('js/home.js') }}" defer></script>
</head>

<body>
    <h1>test</h1>
    <button onclick="testar()">teste funcao</button>

</body>

</html> --}}

@extends('layout')

@section('title')
    <title>Pixerama CRM</title>
@endsection

@section('content')
    <!-- Jumbotron principal -->
    <div class="jumbotron">
        <div class="container">
            <h1 class="display-4">Transforme seus negócios com Pixerama CRM</h1>
            <p class="lead">Gerencie clientes, automize processos e impulsione suas vendas com a nossa plataforma CRM
                fácil de usar.</p>
            <a class="btn btn-dark btn-lg" href="/register" role="button">Criar Conta</a>

        </div>
    </div>

    <!-- Recursos principais -->
    <div class="container">
        <div class="row">
            <div class="col-md-4 feature">
                <h2>Gerenciamento de Clientes</h2>
                <p>Centralize as informações dos seus clientes em um só lugar para facilitar o relacionamento e as
                    vendas.</p>
            </div>
            <div class="col-md-4 feature">
                <h2>Automatização de Processos</h2>
                <p>Automize tarefas repetitivas e melhore a eficiência operacional da sua equipe.</p>
            </div>
            <div class="col-md-4 feature">
                <h2>Análise de Vendas</h2>
                <p>Obtenha insights valiosos com análises de vendas para impulsionar o crescimento do seu negócio.</p>
            </div>
        </div>
    </div>

    <!-- Adicionar CSS específico para esta página -->
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    @endpush

    <!-- Adicionar JS específico para esta página -->
    @push('scripts')
        <script src="{{ asset('js/home.js') }}"></script>
    @endpush

    <!-- Exibir os estilos e scripts adicionados -->
    @stack('styles')
    @stack('scripts')
@endsection
