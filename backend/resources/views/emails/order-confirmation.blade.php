<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #FFB6C1 0%, #ADD8E6 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .order-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .order-info h2 {
            margin-top: 0;
            color: #333;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #555;
        }
        .value {
            color: #333;
        }
        .total {
            font-size: 20px;
            font-weight: bold;
            color: #FFB6C1;
        }
        .products {
            margin: 20px 0;
        }
        .product-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background-color: #f8f9fa;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #FFB6C1;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            margin: 10px 0;
        }
        .whatsapp {
            background-color: #25D366;
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛒 PET CUTE CLOTHES</h1>
            <p>Confirmación de Pedido</p>
        </div>
        
        <div class="content">
            <h2>¡Gracias por tu compra!</h2>
            <p>Hola <strong>{{ $order->user->name }}</strong>,</p>
            <p>Tu pedido ha sido procesado correctamente. A continuación te compartimos los detalles:</p>
            
            <div class="order-info">
                <h2>📋 Pedido #{{ $order->id }}</h2>
                
                <div class="info-row">
                    <span class="label">Estado:</span>
                    <span class="value">⏳ Pendiente de pago</span>
                </div>
                
                <div class="info-row">
                    <span class="label">Fecha:</span>
                    <span class="value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="info-row">
                    <span class="label">Método de envío:</span>
                    <span class="value">
                        @if(strpos($order->notes, 'Retiro en sucursal') !== false)
                            🏪 Retiro en sucursal
                        @else
                            📦 Envío a domicilio
                        @endif
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="label">Método de pago:</span>
                    <span class="value">
                        @switch($order->payment_method)
                            @case('cash')
                                💵 Efectivo (5% OFF)
                                @break
                            @case('transfer')
                                🏦 Transferencia bancaria
                                @break
                            @case('whatsapp')
                                📱 Acordar por WhatsApp
                                @break
                            @default
                                No especificado
                        @endswitch
                    </span>
                </div>
            </div>
            
            <h3>📦 Productos</h3>
            <div class="products">
                @foreach($order->items as $item)
                <div class="product-item">
                    <div>
                        <strong>{{ $item->product->name }}</strong>
                        <br>
                        <small class="text-muted">Cantidad: {{ $item->quantity }}</small>
                    </div>
                    <div>${{ number_format($item->price * $item->quantity, 0, ',', '.') }}</div>
                </div>
                @endforeach
            </div>
            
            <div class="order-info">
                <div class="info-row">
                    <span class="label">Subtotal:</span>
                    <span class="value">${{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
                @if($order->payment_method === 'cash')
                <div class="info-row">
                    <span class="label">Descuento (5% OFF):</span>
                    <span class="value text-success">-${{ number_format($order->total * 0.05, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="label total">Total:</span>
                    <span class="value total">${{ number_format($order->payment_method === 'cash' ? $order->total * 0.95 : $order->total, 0, ',', '.') }}</span>
                </div>
            </div>
            
            @if($order->payment_method === 'transfer')
            <div class="order-info">
                <h3>🏦 Datos para Transferencia</h3>
                <p><strong>Importante:</strong> Consultar los datos bancarios antes de abonar.</p>
                <a href="https://wa.me/543815152840" class="whatsapp">
                    📱 Consultar datos por WhatsApp
                </a>
            </div>
            @endif
            
            @if($order->payment_method === 'whatsapp')
            <div class="order-info">
                <h3>📱 Acuerdo de Pago</h3>
                <p>Para coordinar el pago y envío, comunicate con nosotros:</p>
                <a href="https://wa.me/543815152840" class="whatsapp">
                    💬 Escribir por WhatsApp
                </a>
            </div>
            @endif
            
            <div style="text-align: center; margin-top: 30px;">
                <p>¿Tenés alguna pregunta?</p>
                <a href="https://wa.me/543815152840" class="btn">
                    📱 Contactar por WhatsApp
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p>🐾 PET CUTE CLOTHES - Ropa adorable para tus mascotas</p>
            <p>San Miguel de Tucumán, Argentina</p>
        </div>
    </div>
</body>
</html>