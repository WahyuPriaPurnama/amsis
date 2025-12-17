@switch($order->status)
    @case('pending')
        <form action="{{ route('request-order.approve_div_head', ['id' => $order->id, 'from' => 'index']) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">Approve Kadiv</button>
        </form>
    @break

    @case('approved_by_div_head')
        <form action="{{ route('request-order.approve_manager', ['id' => $order->id, 'from' => 'index']) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Approve Mgr</button>
        </form>
    @break

    @case('approved_by_manager')
        @if ($order->subsidiary->id == 2)
            @can('request-order.export')
                <x-buttons.pdf href="{{ route('request-order.pdf', $order->id) }}"></x-buttons.pdf>
            @endcan
        @else
            <form action="{{ route('request-order.approve_bod', ['id' => $order->id, 'from' => 'index']) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">Approve BOD</button>
            </form>
        @endif
    @break

@endswitch
