@extends('layout')

@section('title')
    <title>Dashboard</title>
@endsection



@section('content')

    <body>
        <div class="container mt-5">
            <h2>Listagem de Leads</h2>
            <div class="container mt-3">
                <h3>Adicionar Novo Lead</h3>
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="newLeadName" placeholder="Nome Completo">
                    </div>
                    <div class="col-md-4">
                        <input type="email" class="form-control" id="newLeadEmail" placeholder="Email">
                    </div>
                    <div class="col-md-3">
                        <input type="tel" class="form-control" id="newLeadPhone" placeholder="Telefone">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-success" onclick="addNewLead()">Salvar</button>
                    </div>
                </div>
            </div>

            <hr>
            <div class="table-responsive">
                <table class="table" id="leadTable">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="leadList">
                        <!-- Os leads serão adicionados dinamicamente aqui -->
                    </tbody>
                </table>
            </div>


        </div>

        <!-- Modal de exclusão -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
            aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Tem certeza que deseja excluir este lead?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary cancel-modal-btn"
                            data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Excluir</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de edição -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar Lead</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editLeadId">
                        <input type="text" class="form-control mt-3" id="editLeadName" placeholder="Nome Completo">
                        <input type="email" class="form-control mt-3" id="editLeadEmail" placeholder="Email">
                        <input type="tel" class="form-control mt-3" id="editLeadPhoneNumber" placeholder="Telefone">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary cancel-modal-btn"
                            data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="saveEditLead()"
                            id="saveEditBtn">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </body>




    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endpush

    @push('scripts')
        <script>
            function loadData() {
                var accessToken = localStorage.getItem('accessToken');
                $.ajax({
                    type: "GET",
                    url: "http://localhost:8000/api/v1/leads",
                    headers: {
                        'Authorization': 'Bearer ' + accessToken,
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        leads = response;
                        fillTable();
                    },
                    error: function(xhr, status, error) {
                        console.log("Erro na requisição AJAX:", xhr.responseText);
                        alert("Falha no login. Verifique suas credenciais e tente novamente.");
                    }
                });
            }

            function formatPhone(phone) {
                return phone.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
            }

            var leads = [];

            function fillTable() {
                var table = $('#leadList');
                table.empty();

                leads.forEach(function(lead, index) {
                    var line = $('<tr>');
                    line.append('<td>' + lead.id + '</td>');
                    line.append('<td>' + lead.name + '</td>');
                    line.append('<td>' + lead.email + '</td>');
                    line.append('<td>' + formatPhone(lead.phone_number) + '</td>');
                    line.append('<td><button class="btn btn-warning btn-sm" onclick="editLead(' + index +
                        ')">Editar</button> ' +
                        '<button class="btn btn-danger btn-sm" onclick="confirmDeletion(' + lead.id +
                        ')">Excluir</button></td>');
                    table.append(line);
                });
            }

            $(document).ready(function() {
                var accessToken = localStorage.getItem('accessToken');
                if (!accessToken) {
                    window.location.href = "/login";
                }


                loadData();

                $('#newLeadPhone').inputmask('(99) 99999-9999', {
                    removeMaskOnSubmit: false
                });

                $('#editLeadPhoneNumber').inputmask('(99) 99999-9999', {
                    removeMaskOnSubmit: false
                });
            });

            function addNewLead() {
                var name = $('#newLeadName').val();
                var email = $('#newLeadEmail').val();
                var phone_number = $('#newLeadPhone').val();

                // console.log("name: " + name);
                // console.log("email: " + email);
                // console.log("phone_number: " + phone_number);

                // Validate name
                if (name.trim().length === 0) {
                    alert("Nome não pode ser vazio");
                    return;
                }

                // Validate email
                var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailRegex.test(email)) {
                    alert("Por favor, insira um email válido.");
                    return;
                }

                // Validate password
                const numericPhoneNumber = phone_number.replace(/\D/g, '');
                if (numericPhoneNumber.length !== 11) {
                    alert("O número de telefone deve ter 11 dígitos.");
                    return;
                }

                var accessToken = localStorage.getItem('accessToken');
                $.ajax({
                    type: "POST",
                    url: "http://localhost:8000/api/v1/leads",
                    data: {
                        name: name.trim(),
                        email: email.trim(),
                        phone_number: numericPhoneNumber.trim()
                    },
                    headers: {
                        'Authorization': 'Bearer ' + accessToken,
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        $('#newLeadName').val('');
                        $('#newLeadEmail').val('');
                        $('#newLeadPhone').val('');

                        loadData();
                    },
                    error: function(error) {
                        alert(
                            "Falha na inserção da Lead, faca login novamente, e-mail ou telfone pode, ja está em uso"
                        );
                    }
                });
            }

            function editLead(index) {
                // console.log("index: " + JSON.stringify(leads[index]));
                var lead = leads[index];
                $('#editLeadId').val(lead.id);
                $('#editLeadName').val(lead.name);
                $('#editLeadEmail').val(lead.email);
                $('#editLeadPhoneNumber').val(lead.phone_number);
                $('#editModal').modal('show');
            }

            function confirmDeletion(index) {
                $('#confirmDeleteModal').modal('show');
                $('#confirmDeleteBtn').off('click').on('click', function() {
                    deleteLead(index);
                    $('#confirmDeleteModal').modal('hide');
                });
            }

            function deleteLead(index) {
                var accessToken = localStorage.getItem('accessToken');
                $.ajax({
                    type: "DELETE",
                    url: "http://localhost:8000/api/v1/leads/" + index,
                    headers: {
                        'Authorization': 'Bearer ' + accessToken,
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        loadData();
                    },
                    error: function(error) {
                        alert("Falha Excluir. Verifique suas credenciais e tente novamente.");
                    }
                });
            }

            function saveEditLead() {

                var id = $('#editLeadId').val();
                var name = $('#editLeadName').val();
                var email = $('#editLeadEmail').val();
                var phone_number = $('#editLeadPhoneNumber').val();

                // Validate name
                if (name.trim().length === 0) {
                    alert("Nome não pode ser vazio");
                    return;
                }

                // Validate email
                var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailRegex.test(email)) {
                    alert("Por favor, insira um email válido.");
                    return;
                }

                // Validate password
                const numericPhoneNumber = phone_number.replace(/\D/g, '');
                if (numericPhoneNumber.length !== 11) {
                    alert("O número de telefone deve ter 11 dígitos.");
                    return;
                }

                var accessToken = localStorage.getItem('accessToken');
                $.ajax({
                    type: "PUT",
                    url: "http://localhost:8000/api/v1/leads/" + id,
                    data: {
                        name: name.trim(),
                        email: email.trim(),
                        phone_number: numericPhoneNumber.trim()
                    },
                    headers: {
                        'Authorization': 'Bearer ' + accessToken,
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        $('#editModal').modal('hide');
                        loadData();
                    },
                    error: function(error) {
                        alert("Falha na edição. Verifique suas credenciais e tente novamente.");
                    }
                });
            }

            // document.getElementById('cadastreSeBtn').addEventListener('click', function() {
            //     window.location.href = "/register";
            // });
        </script>
    @endpush

    @stack('styles')
    @stack('scripts')
@endsection
