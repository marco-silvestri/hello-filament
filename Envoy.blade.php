@setup
    //$server = '5.189.177.205';
    $server = 'vmi640658';
    $user = 'vmi640658-060';
    $demo = "$user@$server";
    $repository = 'git@bitbucket.org:lswr-group/hello-filament.git';
    $appDir = '/var/www/vhosts/cmsquine.wdemo.it/httpdocs/hello-filament';
    $bakDir = '/var/www/vhosts/cmsquine.wdemo.it/httpdocs/hello-filament.bak';
    $tempDir = '/var/www/vhosts/cmsquine.wdemo.it/httpdocs/hello-filament.bak.temp';
@endsetup

@servers(['web' => ["$demo -p 22 -F ./ssh_config"]])

@task('test', ['on' => 'web'])
    rm -rf pippo
    ls -la
@endtask

@task('deploy', ['on' => 'web'])
    echo 'Moving current into temp'
    mv {{$appDir}} {{$tempDir}}

    echo 'Starting deployment';
    cd {{ $appDir }}

    @if ($tag)
        git fetch
        git checkout {{ $tag }}
    @else
        git pull
    @endif

    echo 'Clearing cache';
    ~/.phpenv/shims/php artisan cache:clear
    ~/.phpenv/shims/php artisan view:clear

    echo 'Updating packages';
    ~/.phpenv/shims/php /usr/local/psa/var/modules/composer/composer.phar install --no-interaction --verbose
    ~/.nodenv/shims/npm install
    ~/.nodenv/shims/npm run build

    echo 'Running migrations';
    ~/.phpenv/shims/php artisan migrate --force

    echo 'Restaring queue';
    ~/.phpenv/shims/php artisan queue:restart

    echo 'Optimize';
    ~/.phpenv/shims/php artisan optimize

    echo 'Deleting previous backup'
    rm -rf {{$bakDir}}

    echo 'Saving backup'
    mv {{$tempDir}} {{$bakDir}}

    echo 'Done!';
@endtask
