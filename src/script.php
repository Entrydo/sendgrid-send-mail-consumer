<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use SendGrid\Mail\Mail;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv(__DIR__ . '/../');
$dotenv->load();
$dotenv->required(['SENDGRID_API_KEY', 'RABBITMQ_HOST', 'RABBITMQ_VHOST', 'RABBITMQ_PORT', 'RABBITMQ_USERNAME', 'RABBITMQ_PASSWORD']);

$connection = new AMQPStreamConnection(
	getenv('RABBITMQ_HOST'),
	getenv('RABBITMQ_PORT'),
	getenv('RABBITMQ_USERNAME'),
	getenv('RABBITMQ_PASSWORD'),
	getenv('RABBITMQ_VHOST')
);
$channel = $connection->channel();

$queueName = $exchangeName = 'send-sendgrid-transactional-mail';

$channel->queue_bind($queueName, $exchangeName);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function (AMQPMessage $msg) {
	echo ' [x] Received ', $msg->body, "\n";


	try {
		$message = json_decode($msg->body);

		$mail = new Mail();
		$mail->setFrom($message->from->mail, $message->from->name);
		$mail->addTo($message->to->mail, $message->to->name);
		$mail->setTemplateId($message->template_id);

		// Example of how to send an attachment
		/*
		$fileEncoded = base64_encode(file_get_contents(__DIR__ . '/Vstupenka.pdf'));
		$mail->addAttachment(
			$fileEncoded,
			'application/pdf',
			'Vstupenka.pdf',
			'attachment'
		);
		*/

		$sendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));

		$response = $sendgrid->send($mail);
		print $response->statusCode() . "\n";
		print $response->body() . "\n";

	} catch (Throwable $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
};

$channel->basic_consume($queueName, '', false, true, false, false, $callback);

while (count($channel->callbacks)) {
	$channel->wait();
}
