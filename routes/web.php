<?php

use App\Http\Controllers\Auth\LoginController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth');

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/users/create', function () {
    return Inertia::render('Users/Create');
})->can('create', 'App\Models\User');
//->middleware('can:create.App\Models\User'); or ->can('create', 'App\Models\User');
Route::post('/users', function () {
    // validate the request
    $attributes = Request::validate([
        'name' => 'required',
        'email' => ['required', 'email'],
        'password' => 'required',
    ]);
    // create the user
    User::create($attributes);
    // redirect
    return redirect('/users');
});

Route::middleware('auth')->group(function () {
    Route::get('/users', function () {
        return Inertia::render('Users/Index', [
            'users' => User::query()
                ->when(Request::input('search'), function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->withQueryString()
                ->through(fn($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'can' => [
                        'edit' => Auth::user()->can('edit', $user)
                    ]
                ]),

            'filters' => Request::only(['search']),
            'can' => [
                'createUser' => Auth::user()->can('create', User::class),
            ]
        ]);
    });


    Route::get('/settings', function () {
        return Inertia::render('Settings');
    });

    Route::get('/users/{user:id}/edit', function ($id) {
        $user = Auth::user();
        $model = User::query()->where('id', '=', $id)->first();
        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'can' => [
                    'edit' => Auth::user()->can('edit', $user, $model),
                ]
            ],
            'model' => [
                'id' => $model->id,
                'name' => $model->name,
                'email' => $model->email,
            ]
        ]);
    });
//    })->can('edit', 'App\Models\User');

    Route::post('/users/{user:id}/edit', function ($id) {
        $model = User::query()->where('id', '=', $id)->first();
        $attributes = Request::validate([
            'id' => 'required',
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($model)],
            'password' => 'nullable',
        ]);
        // update the user
        $model->update($attributes);
        // redirect
        return redirect('/users');
    });
//    })->can('edit', 'App\Models\User');

});
