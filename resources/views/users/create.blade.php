@extends('layouts.admin')
@section('content')

        <div class="content">
            <div class="content-title">
                <h1 class="page-title">Cadastrar Usuário</h1>
                <a href="{{ route('user.index') }}" class="btn-primary">Listar</a></div>

            <x-alert />

            <form action="{{ route("user.store") }}" method="POST" class="form-container">
                @csrf

                <div class="mb-4">
                   <label for="name" class="form-label">Nome: </label>
                   <input type="text" name="name" id="name" placeholder="Nome completo" class="form-input" value="{{ old('name') }}">
                </div>
                
                <div class="mb-4">
                   <label for="email" class="form-label">E-mail: </label>
                   <input type="email" name="email" id="email" placeholder="Melhor e-mail" class="form-input" value="{{ old('email') }}">
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Senha: </label>
                    <input type="password" name="password" id="password" placeholder="Senha com no minimo 6 caracteres" class="form-input" value="{{ old('password') }}" >
                </div>

                <button type="submit" class="btn-success">Cadastrar</button>

            </form>
        </div>
         
    


@endsection