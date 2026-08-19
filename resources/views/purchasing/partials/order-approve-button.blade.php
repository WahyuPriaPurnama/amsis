@switch($order->status)
    @case('pending')
        <form action="{{ route('request-order.approve_div_head', ['id' => $order->id, 'from' => 'index']) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Kep. Divisi</button>
        </form>
    @break

    @case('approved_by_div_head')
        <form action="{{ route('request-order.approve_manager', ['id' => $order->id, 'from' => 'index']) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Manager</button>
        </form>
    @break

    @case('approved_by_manager')
        <form action="{{ route('request-order.approve_bod', ['id' => $order->id, 'from' => 'index']) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Direktur</button>
        </form>
    @break
@endswitch
