@servers(['web' => 'root@188.226.142.147'])

@task('deploy')
    cd /var/www/html/repetitors/htdocs/egerep
    git pull github master
    php artisan config:cache
    php artisan route:cache
@endtask

@task('laroute')
    cd /var/www/html/repetitors/htdocs/egerep
    php artisan laroute:generate
@endtask

@task('gulp')
    cd /var/www/html/repetitors/htdocs/egerep
    gulp --production
@endtask

@task('cache')
    cd /var/www/html/repetitors/htdocs/egerep
    php artisan config:cache
    php artisan route:cache
@endtask

@task('migrate')
    cd /var/www/html/repetitors/htdocs/egerep
    php artisan migrate --force
@endtask
