@extends('layouts/contentNavbarLayout')
@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">👤 Daftar User</h1>
        
        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="mdi mdi-plus-thick mdi-24px"></i> Tambah User Baru
        </a>
    </div>
    
    <hr>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5 class="m-0 font-weight-bold text-primary">User Management</h5>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $users->firstItem() + $loop->index }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning mb-1 px-2 py-1">
                                <i class="mdi mdi-square-edit-outline mdi-24px"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger mb-1 px-2 py-1"
                                    onclick="return confirm('Hapus user?')">
                                    <i class="mdi mdi-delete-outline mdi-24px"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Tidak ada data user
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{ $users->links() }}
        </div>
    </div>
</div>

@endsection
