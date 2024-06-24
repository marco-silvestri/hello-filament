@setup
    $server = '5.189.177.205';
    $user = 'vmi640658-060';
    $demo = "$user@$server";
    $repository = 'git@bitbucket.org:lswr-group/hello-filament.git';
    $appDir = '/var/www/vhosts/cmsquine.wdemo.it/httpdocs/hello-filament';
@endsetup

@servers(['web' => [$demo]])

@task('deploy', ['on' => 'web'])
    echo 'Starting deployment ({{ $release }})';

    cd {{ $appDir }}

    @if ($tag)
        git fetch
        git checkout {{ $tag }}
    @else
        git pull
    @endif

    echo 'Clearing cache';
    php artisan clear:cache

    echo 'Updating packages';
    composer install --no-interaction --prefer-dist --optimize-autoloader
    npm run build

    echo 'Running migrations';
    php artisan migrate --force

    echo 'Restaring queue';
    php artisan queue:restart

    echo 'Optimize';
    php artisan optimize

    echo 'Done!';
@endtask
