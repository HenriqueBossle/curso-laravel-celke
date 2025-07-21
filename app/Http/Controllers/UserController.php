<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('id')->paginate(8);

        return view('users.index',['users' => $users]);
    }

    public function show(User $user)
    {
        return view('users.show', ['user' => $user]);
    }
    

    public function create()
    {
        //Carregar o forms
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        
    try{
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);

        return redirect()->route('user.show', ['user' => $user->id])->with('success', 'Usuário criado com sucesso!');
        }catch(Exception $e){
        return back()->withInput()->with('error', 'Erro ao criar usuário' );
        }

    }

    public function edit(User $user)
    {
        return view('users.edit', ['user' => $user]);
    }

    public function update(UserRequest $request, User $user)
    {
        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            return redirect()->route('user.show', ['user' => $user->id])->with('success', 'Usuário editado com sucesso!');

        }catch (Exception $e){
            return back()->withInput()->with('error', 'Usuário não editado!');
        }
    }

        public function editPassword(User $user)
    {

        return view('users.editPassword', ['user' => $user]);
    }

    // Editar no banco de dados a senha do usuário
    public function updatePassword(Request $request, User $user)
    {

        // Validar o formulário
        $request->validate([
            'password' => 'required|min:6',
        ], [
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos :min caracteres.',
        ]);

        try {

            // Editar as informações do registro no banco de dados
            $user->update([
                'password' => $request->password,
            ]);

            // Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('user.show', ['user' => $user->id])->with('success', 'Senha do usuário editada com sucesso!');
        } catch (Exception $e) {

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Senha do usuário não editada!');
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('user.index')->with('success', 'Usuário apagado com sucesso!');
        }catch (Exception $e){
            return redirect()->route('user.index')->with('error', 'Erro ao apagar usuário!');
        }
    }

    public function generatePdf(User $user)
    {
        try{

        $pdf = Pdf::loadView('users.generate-pdf', ['user' => $user])->setPaper('a4', 'portrait');
        $pdfPath = storage_path('app/public/view_user_{$user->id}.pdf');

        $pdf->save($pdfPath);

        

        }catch (Exception $e){
            return redirect()->route('user.show', ['user' => $user])->with('error', 'E-mail não enviado!');
        }
    }


}
