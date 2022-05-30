<div style="clear: both; height: 30px;">
    <div style="float: left; margin: 5px;"><strong>Receitas:</strong> R$ {{ number_format($billList['total']['total_cash_in'],2,',', '.') }}</div>
    <div style="float: left; margin: 5px;"><strong>Despesas:</strong> R$ {{ number_format($billList['total']['total_cash_out'],2,',', '.') }}</div>
    <div style="float: left; margin: 5px;"><strong>Orçamento executado:</strong> R$ {{ number_format($billList['total']['total_paid'],2,',', '.') }}</div>
    <div style="float: left; margin: 5px;"><strong>Data:</strong> R$ {{ \Carbon\Carbon::now()->format('d/m/Y h:i:s') }}</div>

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
