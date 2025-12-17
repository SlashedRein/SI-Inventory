@if($isCorporate)
    @include('penjualan.struk-corporate')
@else
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Struk Penjualan #{{ $penjualan->id_penjualan }}</title>
    </head>
    <body>
        @include('penjualan.struk-personal')
    </body>
    </html>
@endif
