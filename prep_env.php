<?php

if (!file_exists('.env')) {
    copy('.env.example', 'storage/.env') or exit(1);
    symlink('storage/.env', '.env') or exit(2);
}

if (!file_exists('database/database.sqlite')) {
    touch('storage/database.sqlite') or exit(4);
    chdir('database') or exit(5);
    symlink('../storage/database.sqlite', 'database.sqlite') or exit(3);
}