<div class=" mx-auto md:flex md:flex-col">
    <div>
        @section('menu')
        @show
    </div>

    <div class="flex flex-col justify-between px-4 mx-auto w-full md:w-[80rem] sm:px-6 lg:px-8">
        @section('deck')
        @show
    </div>

    <div class="hidden md:flex md:flex-col justify-between px-4 mx-auto w-full md:w-[80rem] sm:px-6 lg:px-8">
        @section('prev-next')
        @show
    </div>

    <div class="mt-8">
        @section('comments-deck')
        @show
    </div>

    <div class="mt-8">
        @section('related')
        @show
    </div>
    <x-footer :hasSitemap="false"/>
</div>
