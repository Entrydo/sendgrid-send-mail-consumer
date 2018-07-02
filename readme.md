# Sendgrid Transactional Mails Consumer

## Purpose
Build tiny microservice, that will consume messages from RabbitMQ queue and send emails via [SendGrid API](https://sendgrid.com/docs/API_Reference/api_v3.html).

## Install
1. Create `.env` file (use `.env.dist` as template) 
2. Install composer using `composer install`
3. Run the script via `php src/script.php`

## How it works
The script connects to RabbitMQ queue and consumes messages. Consumer expects messages to be in JSON format.


```json
{
  "from": {
    "name": "John Doe",
    "mail": "john@doe.com" 
  },
  "to": {
    "name": "John Doe",
    "mail": "john@doe.com" 
  },
  "template_id": "7011212b-09f7-4806-8b3a-79bc16682674"
}
```

## TODO
- Dynamic queue name via parameter
- Support for messages without template (adds subject and content properties to message's json)