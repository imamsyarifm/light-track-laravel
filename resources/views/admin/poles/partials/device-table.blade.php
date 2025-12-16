<div class="table-responsive text-nowrap">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Kode</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="table-border-bottom-0">
            @forelse($devices as $item)
            <tr>
                <td><span class="fw-medium">{{ $item->nomor }}</span></td>
                <td>{{ $item->kode }}</td>
                <td class="text-center">
                    {{-- ToDo --}}
                    <a href="#" class="btn btn-sm btn-outline-primary">
                        Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center py-4 text-muted small">Tidak ada data perangkat</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>