@servers(['web' => 'root@188.226.142.147'])

@task('deploy')
    cd /var/www/html/repetitors/htdocs/egerep
    git pull github master
@endtask

@task('laroute')
    cd /var/www/html/repetitors/htdocs/egerep
    php artisan laroute:generate
@endtask

@task('migrate')
    cd /var/www/html/repetitors/htdocs/egerep
    php artisan migrate --force
@endtask
