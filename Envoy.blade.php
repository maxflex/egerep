@servers(['web' => 'root@188.226.142.147'])

@task('deploy')
    cd /var/www/html/repetitors/htdocs/egerep
    git pull github master
@endtask
