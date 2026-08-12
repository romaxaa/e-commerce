<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo " [*] Воркер отправки почты запущен и ждет подтверждения заказов...\n";

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'user', 'root');
$channel = $connection->channel();

// 1. Указываем имя обменника (ДОЛЖНО БЫТЬ ТАКИМ ЖЕ, как в RabbitService)
$exchange = 'shop.events'; // Поменяй на имя своего exchange!

// 2. Объявляем СВОЮ СОБСТВЕННУЮ очередь для чеков
$queueName = 'worker_mail_queue';
$channel->queue_declare($queueName, false, true, false, false, false, new AMQPTable(['x-queue-type' => 'quorum']));

// 3. САМЫЙ ВАЖНЫЙ ШАГ: Связываем нашу очередь с обменником по новому ключу!
// Мы говорим Кролику: "Если в $exchange прилетит ключ 'order.confirmed', дай копию в эту очередь"
$channel->queue_bind($queueName, $exchange, 'order.confirmed');


// 4. Пишем логику отправки почты
$callback = function (AMQPMessage $msg) 
{   
    // Получаем те самые данные, которые отправил worker_db!
    $data = json_decode($msg->getBody(), true);
    
    print_r($data); // Посмотри в консоли, тут будет order_id, total_sum и т.д.
    
    //доразбираться в запросах
    $orderdata = R::getRow('
        SELECT 
        u.name as user_name, u.email as user_email, 
        ords.status, ords.payment_type, ords.delivery_type, 
        ua.street as adress, ua.office as office, ords.created_at
        FROM orderitem o 
        INNER JOIN orders ords ON o.order_id = ords.id 
        INNER JOIN users u ON ords.user_id = u.id 
        INNER JOIN user_adresses ua ON ords.adress_id WHERE order_id = ?
    ', [$data['order_id']]);

    $productdata = R::findAll('SELECT p.name, o.quantity, p.price FROM orderitem o INNER JOIN products p ON o.product_id = p.id WHERE o.order_id = ?', [$data['order_id']]);

    foreach($productdata as $row)
    {
        $totalprice += $row['price'] * $row[quantity];
    }
    
    $mail = new PHPMailer(true);

    $html_v2 = <<<END
        <!DOCTYPE html>
        <html lang="ru">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GlassShop — Детали заказа</title>
        <style>
            /* Базовые стили для email-клиентов */
            * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            }
            body {
            background-color: #e9eef3;
            line-height: 1.5;
            padding: 20px;
            }
            .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            }
            .email-header {
            background: #0a0a0f;
            padding: 24px 30px;
            text-align: center;
            border-bottom: 3px solid #c084fc;
            }
            .email-header h1 {
            color: #ffffff;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin: 0 0 6px;
            }
            .email-header p {
            color: #a1a1aa;
            font-size: 14px;
            margin: 0;
            }
            .email-body {
            padding: 32px 30px;
            }
            .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #18181b;
            margin-bottom: 8px;
            }
            .order-id {
            background: #f4f4f6;
            display: inline-block;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 500;
            color: #27272a;
            margin: 16px 0 12px;
            }
            .order-status {
            background: #e6f7ec;
            color: #1e7b3c;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 40px;
            display: inline-block;
            margin-left: 10px;
            }
            .info-grid {
            background: #f8fafc;
            border-radius: 16px;
            padding: 18px 20px;
            margin: 24px 0;
            border: 1px solid #e2e8f0;
            }
            .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
            }
            .info-row:last-child {
            margin-bottom: 0;
            }
            .info-label {
            color: #4b5563;
            font-weight: 500;
            }
            .info-value {
            color: #1f2937;
            font-weight: 500;
            }
            table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0 20px;
            }
            th {
            text-align: left;
            padding: 12px 6px 8px 0;
            font-size: 13px;
            color: #6c757d;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            }
            td {
            padding: 12px 6px 12px 0;
            border-bottom: 1px solid #f0f2f5;
            font-size: 14px;
            color: #212529;
            }
            .total-line {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 15px;
            border-top: 1px solid #e2e8f0;
            margin-top: 12px;
            }
            .grand-total {
            font-size: 20px;
            font-weight: 800;
            color: #0a0a0f;
            border-top: 2px solid #cbd5e1;
            padding-top: 16px;
            margin-top: 8px;
            }
            .warranty-block {
            background: #fefce8;
            border-left: 4px solid #eab308;
            padding: 16px 20px;
            border-radius: 14px;
            margin: 28px 0 24px;
            }
            .warranty-block p {
            margin: 5px 0;
            font-size: 13px;
            color: #854d0e;
            }
            .button {
            display: inline-block;
            background-color: #0a0a0f;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 15px;
            margin: 10px 0 8px;
            }
            .footer {
            background-color: #f9fafb;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #eef2f6;
            font-size: 12px;
            color: #6c757d;
            }
            .footer a {
            color: #3b82f6;
            text-decoration: none;
            }
            @media (max-width: 500px) {
            .email-body {
                padding: 24px 20px;
            }
            .info-row {
                flex-direction: column;
                gap: 4px;
            }
            td, th {
                font-size: 12px;
            }
            }
        </style>
        </head>
        <body>
        <div class="email-wrapper">
            <!-- Шапка письма -->
            <div class="email-header">
            <h1>GlassShop</h1>
            <p>Премиальная электроника</p>
            </div>

            <!-- Основной контент -->
            <div class="email-body">
            <div class="greeting">Здравствуйте, {$orderdata['user_name']} </div>
            <p style="margin: 8px 0 0px; color: #3b3b45;">Спасибо за покупку! Ваш заказ успешно оплачен и поступил в обработку.</p>
            
            <div>
                <span class="order-id">Заказ № GL-2405-892</span> 
        END;
                if($orderdata['status'] == 'paid')
                {
                    $html_v2 .= <<<END
                        <span class="order-status">✅ {$orderdata['status']}</span>
                    </div>
                END;
                }
                else
                {
                    $html_v2 .= <<<END
                        <span class="order-status">⚠️ {$orderdata['status']}</span>
                    </div>
                END;
                }

        <<<END
            <!-- Детали -->
            <div class="info-grid">
                <div class="info-row">
                <span class="info-label">Дата заказа:</span>
                <span class="info-value">{$orderdata['created_at']}</span>
                </div>
                <div class="info-row">
                <span class="info-label">Способ оплаты:</span>
                <span class="info-value">{$orderdata['payment_type']}</span>
                </div>
                <div class="info-row">
                <span class="info-label">Доставка:</span>
                <span class="info-value">{$orderdata['delivery_type']}</span>
                </div>
                <div class="info-row">
                <span class="info-label">Адрес получения:</span>
                <span class="info-value">{$orderdata['adress']} {$orderdata['office']}</span>
                </div>
            </div>

            <!-- Список товаров -->
            <h3 style="font-size: 18px; margin-bottom: 8px;">🛍️ Состав заказа</h3>
            <table>
                <thead>
                    <tr><th>Товар</th><th>Кол-во</th><th>Цена</th></tr>
                </thead>
                <tbody>
        END;
        //сделать отдельную выборку для товаров
                foreach($productdata as $item) 
                {
                    $html_v2 .= <<<END
                                <tr>
                                    <td>{$item['product_name']}</td>
                                    <td>{$item['product_quantity']} шт</td>
                                    <td>{$item['product_price']} ₽</td>
                                </tr>
                    END;
                }

                $html_v2 .= <<<END
                                </tbody>
                            </table>
                END;
        <<<END
            
            <!-- Итоговая сумма -->
            <div class="total-line">
                <span>Стоимость товаров:</span>
                <span>186 960 ₽</span>
            </div>
            <div class="total-line">
                <span>Скидка (WELCOME10):</span>
                <span>- 18 696 ₽</span>
            </div>
            <div class="total-line">
                <span>Доставка:</span>
                <span>0 ₽</span>
            </div>
            <div class="grand-total">
                Итого к оплате: 168 264 ₽
            </div>

            <!-- Блок гарантии -->
            <div class="warranty-block">
                <p>🛡️ <strong>Гарантия 12 месяцев</strong> — официальная гарантия на всю технику.</p>
                <p>🔄 Если товар не подошел — вы можете вернуть его в течение 14 дней.</p>
                <p>🔧 Сервисная поддержка доступна по телефону 8 (800) 555-35-35.</p>
            </div>

            <!-- Кнопка отслеживания -->
            <div style="text-align: center;">
                <a href="#" class="button">📦 Отследить заказ</a>
                <p style="font-size: 12px; color: #7f8c8d; margin-top: 12px;">Статус заказа также доступен в личном кабинете.</p>
            </div>
            </div>

            <!-- Footer -->
            <div class="footer">
            <p>© 2025 GlassShop — Техника, которой доверяют.<br>
            <a href="#">Политика конфиденциальности</a> | <a href="#">Помощь</a></p>
            <p style="margin-top: 12px;">Это письмо было отправлено автоматически, пожалуйста, не отвечайте на него.<br>
            Если у вас есть вопросы, свяжитесь с поддержкой: support@glassshop.ru</p>
            </div>
            </div>
            </body>
            </html> 
        END;
    
    try 
    {
        if(!empty($orderdata))
        {
            $mail->isSMTP();

            $mail->Host = 'mailpit';
            $mail->Port = 1025;

            $mail->SMTPAuth = false;

            $mail->CharSet = 'UTF-8';

            $mail->setFrom('shop@test.local', 'GlassShop');

            $mail->addAddress('client@test.local');

            $mail->isHTML(true);

            $mail->Subject = 'Детали заказа №123123';

            $mail->Body = $html;

            $mail->send();

            echo 'Сообщение отправлено';
            $msg->ack();
        }

    } catch (Exception $e) 
    {
        echo "Ошибка: {$mail->ErrorInfo}";
        $msg->nack(false, false, true);
    }
};

// 5. Запускаем прослушивание
$channel->basic_qos(null, 1, false);
$channel->basic_consume($queueName, '', false, false, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo "Воркер чеков остановлен: " . $exception->getMessage() . "\n";
}

$channel->close();
$connection->close();