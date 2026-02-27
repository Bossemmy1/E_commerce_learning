<?php
// Edit DB values or use environment variables
return [
    'db_host' => getenv('DB_HOST') ?: 'db',
    'db_name' => getenv('DB_NAME') ?: 'ecommerce',
    'db_user' => getenv('DB_USER') ?: 'ecommerce',
    'db_pass' => getenv('DB_PASS') ?: 'secret',
];