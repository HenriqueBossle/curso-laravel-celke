<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Mail\UserPdfMail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index(Request $request)
    {
        //$users = User::orderByDesc('id')->paginate(8);

        $users = User::when(
            $request->filled('name'),
            fn($query) => 
            $query->whereLike('name', '%' .$request->name . '%')
        )
            ->when(
                $request->filled('email'),
                fn($query) =>
                $query->whereLike('email', '%' . $request->email . '%')
            )
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
        

        return view('users.index',[
            'users' => $users,
            'name' => $request->name,
            'email' => $request->email
        ]);
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

        Mail::to($user->email)->send(new UserPdfMail($pdfPath, $user));

        if(file_exists($pdfPath)){
            unlink($pdfPath);
        }

        return redirect()->route('user.show', ['user' => $user])->with('success', 'E-mail enviado com sucesso!');


        }catch (Exception $e){
            return redirect()->route('user.show', ['user' => $user])->with('error', 'E-mail não enviado!');
        }
    }


}
