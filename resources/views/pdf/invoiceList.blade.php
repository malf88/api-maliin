<h3>Cartão {{ $billList['credit_card']['name'] }}</h3>
<div style="clear: both; height: 30px;">
    <div style="float: left; margin: 5px;"><strong>Início da fatura:</strong> {{ \Carbon\Carbon::create($billList['start_date'])->format('d/m/Y') }}</div>
    <div style="float: left; margin: 5px;"><strong>Fim da fatura:</strong> {{ \Carbon\Carbon::create($billList['end_date'])->format('d/m/Y')  }}</div>
    <div style="float: left; margin: 5px;"><strong>Vencimento:</strong> {{ \Carbon\Carbon::create($billList['due_date'])->format('d/m/Y')  }}</div>
    <div style="float: left; margin: 5px;"><strong>Total:</strong> R$ {{ number_format($billList['total_balance'],2,',','.') }}</div>
</div>
<table style="border: 1px solid;width: 100%">
    <thead>
    <tr>
        <th style="width: 5%">ID</th>
        <th style="width: 40%">Descrição</th>
        <th style="width: 10%">Data</th>
        <th style="width: 10%">Pagamento</th>
        <th style="width: 15%">Categoria</th>
        <th style="width: 15%">Valor</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($billList['bills'] as $bill)
    <tr style="color: @if($bill->amount < 0) #FF0000 @else #00F @endif">
        <td>{{ $bill->id }}</td>
        <td>{{ $bill->description }}</td>
        <td>{{ $bill->date->format('d/m/Y') }}</td>
        <td>{{ ($bill->pay_day != null) ? $bill->pay_day->format('d/m/Y'):'' }}</td>
        <td>{{ ($bill->category != null) ? $bill->category->name:'' }}</td>
        <td>R$ {{ $bill->amount }}</td>
        </tr>
    @endforeach
    </tbody>
    </table>
