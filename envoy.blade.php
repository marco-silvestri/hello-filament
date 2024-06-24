@setup
    $server = env('SERVER_IP');
    $user = env('SERVER_USER');
    $repository = 'git@bitbucket.org:lswr-group/hello-filament.git';
    $appDir = '/var/www/vhosts/cmsquine.wdemo.it/httpdocs/hello-filament';
@endsetup

@servers(['demo' => "$user@$server"])

@task('deploy', ['on' => 'demo'])
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
