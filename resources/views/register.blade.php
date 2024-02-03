@extends('layout')

@section('title')
    <title>Register</title>
@endsection

@section('content')
    <!-- Jumbotron principal -->

    <body>
        <div class="container mt-5">
            <div class="profile-card">
                <h2 class="text-center mb-4">Create Profile</h2>
                <form id="profile-form">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter your name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter your email">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" placeholder="Enter your password">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="passwordConfirm"
                            placeholder="Confirm your password">
                    </div>
                    <button type="button" class="btn btn-primary btn-block" id="CreateBtn">Criar Conta</button>
                </form>
            </div>
    </body>

    @push('scripts')
        <script>
            document.getElementById('CreateBtn').addEventListener('click', function() {
                // window.location.href = "/register";
                validateForm();
            });

            function validateForm() {
                var name = document.getElementById("name").value;
                var email = document.getElementById("email").value;
                var password = document.getElementById("password").value;
                var passwordConfirm = document.getElementById("passwordConfirm").value;

                var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailRegex.test(email)) {
                    alert("Por favor, insira um email válido.");
                    return;
                }

                if (password !== passwordConfirm) {
                    alert("As senha devem ser iguais");
                    return;
                }

                // Validate password
                if (password.length < 8) {
                    alert("A senha deve ter no mínimo 8 caracteres.");
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "http://localhost:8000/api/v1/users",
                    data: {
                        name: name,
                        email: email,
                        password: password
                    },
                    success: function(response) {
                        localStorage.setItem('accessToken', response.accessToken);
                        window.location.href = "/login";
                    },
                    error: function(error) {
                        alert("Falha Criar");
                    }
                });
            }
        </script>
    @endpush

    <!-- Adicionar CSS específico para esta página -->
    <!-- @push('styles')
        <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    @endpush -->

    <!-- Adicionar JS específico para esta página -->
    <!-- @push('scripts')
        <script src="{{ asset('js/home.js') }}"></script>
    @endpush -->

    <!-- Exibir os estilos e scripts adicionados -->
    @stack('styles')
    @stack('scripts')
@endsection
