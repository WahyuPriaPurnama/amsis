@switch($order->status)
    @case('pending')
        <form action="{{ route('request-order.approve_div_head', $order->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">Approve</button>
        </form>
    @break

    @case('approved_by_div_head')
        <form action="{{ route('request-order.approve_manager', $order->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Approve</button>
        </form>
    @break

    @case('approved_by_manager')
        @if ($order->subsidiary->id == 2)
            @can('request-order.export')
                <x-buttons.pdf href="{{ route('request-order.pdf', $order->id) }}"></x-buttons.pdf>
            @endcan
        @else
            <form action="{{ route('request-order.approve_bod', $order->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">Approve</button>
            </form>
        @endif
    @break

@endswitch
