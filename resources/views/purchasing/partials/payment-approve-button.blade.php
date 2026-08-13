@switch($payment->status)
    @case('pending')
        <form action="{{ route('request-payment.approve_manager', ['id' => $payment->id, 'from' => 'index']) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Acc Direktur</button>
        </form>
    @break

    @case('approved_by_manager')
        <form action="{{ route('request-payment.approve_bod', ['id' => $payment->id, 'from' => 'index']) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Acc Direktur OP</button>
        </form>
    @break
@endswitch
