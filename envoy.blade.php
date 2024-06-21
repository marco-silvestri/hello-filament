@setup
$server = env('SERVER_IP');
$user = env('SERVER_USER');
@endsetup

@servers(['demo' => "$user@$server"])
