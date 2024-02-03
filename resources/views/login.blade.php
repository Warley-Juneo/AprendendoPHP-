@extends('layout')

@section('title')
    <title>Login</title>
@endsection



@section('content')

    <body>
        <div class="container">
            <div class="login-container">
                <h2 class="text-center mb-4">Login</h2>
                <form id="loginForm">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuário:</label>
                        <input type="text" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="button" class="btn btn-primary btn-block" id="loginBtn">Entrar</button>
                    <button type="button" class="btn btn-primary btn-block" id="CreateBtn">Cadastre-se</button>

                </form>
            </div>
        </div>
    </body>

    @push('scripts')
        <script>
            document.getElementById('CreateBtn').addEventListener('click', function() {
                window.location.href = "/register";
            });


            document.getElementById('loginBtn').addEventListener('click', function() {
                validateForm();
            });

            function validateForm() {
                var email = document.getElementById("email").value;
                var password = document.getElementById("password").value;

                // console.log("email: " + email);
                // console.log("password: " + password);

                // Validate email
                var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailRegex.test(email)) {
                    alert("Por favor, insira um email válido.");
                    return;
                }

                // Validate password
                if (password.length < 8) {
                    alert("A senha deve ter no mínimo 8 caracteres.");
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "http://localhost:8000/api/v1/auth",
                    data: {
                        email: email,
                        password: password
                    },
                    success: function(response) {
                        alert("login feito")
                        localStorage.setItem('accessToken', response.accessToken);
                        window.location.href = "/dashboard";
                    },
                    error: function(error) {
                        alert("Falha no login. Verifique suas credenciais e tente novamente.");
                    }
                });
            }
        </script>
    @endpush


    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('js/login.js') }}"></script>
    @endpush

    @stack('styles')
    @stack('scripts')
@endsection
