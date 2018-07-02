# Sendgrid Transactional Mails Consumer

## Purpose
Build tiny microservice, that will consume messages from RabbitMQ queue and send emails via [SendGrid API](https://sendgrid.com/docs/API_Reference/api_v3.html).

## Install
1. Create `.env` file (use `.env.dist` as template) 
2. Install composer using `composer install`
3. Run the script via `php src/script.php`