@setup
    //$server = '5.189.177.205';
    $server = 'vmi640658';
    $user = 'vmi640658-060';
    $demo = "$user@$server";
    $repository = 'git@bitbucket.org:lswr-group/hello-filament.git';
    $appDir = '/var/www/vhosts/cmsquine.wdemo.it/httpdocs/hello-filament';
    $bakDir = '/var/www/vhosts/cmsquine.wdemo.it/httpdocs/hello-filament.bak';
    $release = date('Ymdhis');
@endsetup

@servers(['web' => ["$demo -p 22 -F ./ssh_config"]])

@task('test', ['on' => 'web'])
    rm -rf pippo
    ls -la
@endtask

@story('restoreBackup', ['on' => 'web'])
    promoting-backup
    clear-cache
    optimize
@endstory

@task('promoting-backup')
    echo 'Removing current release';
    rm -rf {{ $appDir }}

    echo 'Promoting backup';
    cp -r {{ $bakDir }} {{ $appDir }}
@endtask

@task('clear-cache')
    echo 'Clearing cache';
    cd {{ $appDir }}
    ~/.phpenv/shims/php artisan cache:clear
    ~/.phpenv/shims/php artisan view:clear
    ~/.phpenv/shims/php artisan route:clear
@endtask

@task('updating-packages')
    echo 'Updating packages';
    cd {{ $appDir }}
    ~/.phpenv/shims/php /usr/local/psa/var/modules/composer/composer.phar install --no-interaction --verbose --no-dev --optimize-autoloader
    ~/.nodenv/shims/npm install
    ~/.nodenv/shims/npm run build
@endtask

@task('run-migrations')
    echo 'Running migrations';
    cd {{ $appDir }}
    ~/.phpenv/shims/php artisan migrate --force
@endtask

@task('optimize')
    echo 'Optimize';
    cd {{ $appDir }}
    ~/.phpenv/shims/php artisan optimize

    echo 'Restaring queue';
    ~/.phpenv/shims/php artisan queue:restart
@endtask

@task('refresh-backup')
    echo 'Deleting previous backup'
    rm -rf {{ $bakDir }}

    echo 'Saving backup'
    cp -r {{ $appDir }} {{ $bakDir }}
@endtask

@task('refresh-icons')
    cd {{ $appDir }}
    ~/.phpenv/shims/php artisan icons:clear
    ~/.phpenv/shims/php artisan icons:cache
@endtask

@task('fetch-repo')
    echo 'Starting deployment ({{ $release }})';
    cd {{ $appDir }}

    git reset --hard HEAD
    git clean -df

    @if ($tag)
        git fetch
        git checkout {{ $tag }}
    @else
        git pull
    @endif
@endtask

@story('deploy', ['on' => 'web'])
    fetch-repo
    updating-packages
    run-migrations
    clear-cache
    refresh-icons
    optimize
    refresh-backup
@endstory
