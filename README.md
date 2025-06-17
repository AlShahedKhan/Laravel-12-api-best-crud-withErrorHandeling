1)To setup laravel nightwatch run 
command composer require laravel/nightwatch

2) In .env add those
LOG_CHANNEL=nightwatch
NIGHTWATCH_TOKEN=VEBqwN3o73cwbOosgy1rQmxJktYuhhN556MhAV8d7hKO
NIGHTWATCH_REQUEST_SAMPLE_RATE=1.0
NIGHTWATCH_INGEST_URI=127.0.0.1:2407
NIGHTWATCH_IGNORE_OUTGOING_REQUESTS=false  # This is for outgoing requests

3)To check its connected or not use those command 

php artisan nightwatch:agent --listen-on=127.0.0.1:2407
php artisan nightwatch:status
