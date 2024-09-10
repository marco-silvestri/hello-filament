@props([
    'hasSitemap' => true,
])
<footer class="pb-4">
    <button id="to-top-button" onclick="goToTop()" title="Go To Top" class="absolute hidden ">
    <x-elements.up/>
        <span class="sr-only">Go to top</span>
    </button>
    @if ($hasSitemap)
        <div class="hidden md:flex md:flex-col bg-shade-400">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <x-sitemap />
            </div>
        </div>
    @endif

    <div class=" bg-shade-300 md:pb-6">
        {{ config('blog.footer.name') }}
        <div class="w-full space-between">

            <div class="flex flex-col px-4 py-2 mx-auto text-left text-gray-700 footer-info max-w-7xl sm:px-6 lg:px-8">
                <div>
                    <img class="mt-3 mb-5" src="{{asset('company.png')}}" alt="{{ config('blog.footer.company_name') }}">
                </div>
                <div class="text-left text-gray-700 footer-info">
                    <p class="mt-3">{{ __('company.address') }}</p>
                    <p>{{ config('blog.footer.company_address') }}</p>
                    <p>{{ config('blog.footer.company_city') }}, {{ config('blog.footer.company_cap') }},
                        {{ config('blog.footer.company_prov') }}, {{ config('blog.footer.company_country') }}</p>
                    <p class="mt-2">{{ __('company.phone') }} {{ config('blog.footer.company_phone') }}</p>
                    <p>{{ __('company.fax') }} {{ config('blog.footer.company_fax') }}</p>
                    <p class="mt-2">{{ __('company.piva') }} {{ config('blog.footer.company_piva') }}</p>
                    <p>{{ __('company.editorial_email') }}: {{ config('blog.footer.company_editorial_email') }}</p>
                    <br>
                    {{ __('company.copyright') }} - {{ config('blog.footer.company_name') }} -
                    {{ __('company.piva') }} {{ config('blog.footer.company_piva') }} -
                    {{ config('blog.footer.company_email') }} | {{ __('company.data_protection_officer') }}
                    {{ config('blog.footer.data_protection_officer_name') }} - {{ __('company.contact') }}:
                    {{ config('blog.footer.data_protection_officer_email') }}
                </div>
            </div>
            @if (config('cms.sharing.follow_footer_btn',true))
            <x-cms.social-button />
            @endif
            <div class="md:hidden">
                <x-sitemap />
            </div>
</footer>
<script>
    // Get the 'to top' button element by ID
    let toTopButton = document.getElementById("to-top-button");

    // Check if the button exists
    if (toTopButton) {

        // On scroll event, toggle button visibility based on scroll position
        window.onscroll = function() {
            if (document.body.scrollTop > screen.height || document.documentElement.scrollTop > screen.height) {
                toTopButton.classList.remove("hidden");
            } else {
                toTopButton.classList.add("hidden");
            }
        };

        // Function to scroll to the top of the page smoothly
        window.goToTop = function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        };
    }
</script>
