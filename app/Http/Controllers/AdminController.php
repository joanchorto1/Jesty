<?php
// app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('AdminUser/Login');
    }

    public function login(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Obtener las credenciales
        $credentials = $request->only('email', 'password');

        // Intentar autenticar al usuario administrador con el guard 'admin'
        if (auth()->guard('admin')->attempt($credentials)) {
            // Redirigir al dashboard si el login es exitoso
            return redirect()->route('admin.dashboard');
        }

        // Si las credenciales son incorrectas, volver atrás con un mensaje de error
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }


    public function logout()
    {
        auth()->guard('admin')->logout();
        return Inertia::location(route('admin.login'));
    }

    public function showRegisterForm()
    {
        return Inertia::render('AdminUser/Register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admin_users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        AdminUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        $companies = Company::all();
        $users = User::all();
        return Inertia::render('AdminUser/Dashboard', ['companies'=>$companies, 'users'=>$users]);
    }

    public function userAdminCreate(){
        return Inertia::render('Users/AdminCreate',[
            'companies' => Company::all(),
        ]);
    }
    public function storeAdminUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'phone' => 'required',
            'address'   => 'required',
            'admin' => 'required',
            'company_id' => 'required|exists:companies,id',
        ]);


        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'admin' => $request->admin,
            'company_id' => $request->company_id,
        ]);
      return Inertia::location(route('admin.dashboard'));
    }
    public function userRegularCreate()
    {
        return Inertia::render('Users/RegularCreate');
    }


}
