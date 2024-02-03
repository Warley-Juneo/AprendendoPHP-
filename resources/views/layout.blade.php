<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    {{-- <script src="{{ asset('js/layout.js') }}" defer></script> --}}
    <link rel="stylesheet" href="css/layout.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="caminho/para/jquery.mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
    @vite(['resources\sass\app.scss', 'resources\js\app.js'])
</head>

<body>
    @if (!in_array(Request::path(), ['login']))
        <!-- Barra de navegação -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="/">Pixerama CRM</a>
                @if (!in_array(Request::path(), ['dashboard', 'register']))
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Login</a>
                        </li>
                    </ul>
                @endif
                {{-- @if (!in_array(Request::path(), ['register', 'home', '/']))
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="" id="logoutBtn">Logout</a>
                        <button type="button" class="btn btn-primary btn-block" id="logoutBtn">logout</button>
                    </li>
                </ul>
                 @endif  --}}
            </div>
        </nav>
    @endif

    @yield('content')

    @if (!in_array(Request::path(), ['login', 'dashboard', 'register']))
        <footer class="footer text-center">
            <p>&copy; 2024 Pixerama CRM. Todos os direitos reservados.</p>
        </footer>
    @endif

    @push('scripts')
        <script>
            document.getElementById('logoutBtn').addEventListener('click', function() {
                console.log("fui clicado")
                logout();
            });


            function logout() {
                console.log("entrei no logout");
                var accessToken = localStorage.getItem('accessToken');
                $.ajax({
                    type: "DELETE",
                    url: "http://localhost:8000/api/v1/auth",
                    headers: {
                        'Authorization': 'Bearer ' + accessToken,
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        // console.log("logout feito");
                        localStorage.removeItem('accessToken');
                        // window.location.href = "/";
                    },
                    error: function(error) {
                        alert("Falha no logout");
                    }
                });
            }
        </script>
    @endpush
</body>

</html>
