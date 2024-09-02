@props([
'section' => [],
'skip' => 1,
'isHighlighted'=>false
])
@if(!$isHighlighted)
<div class="flex">
<div class="flex flex-col w-full ">
@endif
<div x-data="{
        skip: {{$skip}},
        atBeginning: false,
        atEnd: false,
        next() {
            this.to((current, offset) => current + (offset * this.skip))
        },
        prev() {
            this.to((current, offset) => current - (offset * this.skip))
        },
        to(strategy) {
            let slider = this.$refs.slider
            let current = slider.scrollLeft
            let offset = slider.firstElementChild.getBoundingClientRect().width
            slider.scrollTo({ left: strategy(current, offset), behavior: 'smooth' })
        },
        focusableWhenVisible: {
            'x-intersect:enter'() {
                this.$el.removeAttribute('tabindex')
            },
            'x-intersect:leave'() {
                this.$el.setAttribute('tabindex', '-1')
            },
        },
        disableNextAndPreviousButtons: {
            'x-intersect:enter.threshold.05'() {
                let slideEls = this.$el.parentElement.children

                // If this is the first slide.
                if (slideEls[0] === this.$el) {
                    this.atBeginning = true
                // If this is the last slide.
                } else if (slideEls[slideEls.length-1] === this.$el) {
                    this.atEnd = true
                }
            },
            'x-intersect:leave.threshold.05'() {
                let slideEls = this.$el.parentElement.children

                // If this is the first slide.
                if (slideEls[0] === this.$el) {
                    this.atBeginning = false
                // If this is the last slide.
                } else if (slideEls[slideEls.length-1] === this.$el) {
                    this.atEnd = false
                }
            },
        },
    }" class="flex w-full flex-col relative">
    @if (count($section)>3)
    <div class="flex flex-row absolute container-arrows">
        <!-- Prev Button -->
        <div class="">
            <button x-on:click="prev" class="text-6xl arrow-carousel" :aria-disabled="atBeginning" :tabindex="atEnd ? -1 : 0" :class="{ 'opacity-50 cursor-not-allowed': atBeginning }">
                <span aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>

                </span>
                <span class="sr-only">Skip to previous slide page</span>
            </button>
        </div>
        <div>
            <!-- Next Button -->
            <button x-on:click="next" class="text-6xl arrow-carousel" :aria-disabled="atEnd" :tabindex="atEnd ? -1 : 0" :class="{ 'opacity-50 cursor-not-allowed': atEnd }">
                <span aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>

                </span>
                <span class="sr-only">Skip to next slide page</span>
            </button>
        </div>
    </div>
    @endif
    <div x-on:keydown.right="next" x-on:keydown.left="prev" tabindex="0" role="region" aria-labelledby="carousel-label" class="flex space-x-6">
        <h2 id="carousel-label" class="sr-only" hidden>Carousel</h2>



        <span id="carousel-content-label" class="sr-only" hidden>Carousel</span>

        <ul x-ref="slider" tabindex="0" role="listbox" aria-labelledby="carousel-content-label" class="flex w-full snap-x snap-mandatory overflow-hidden">
            @foreach ($section as $post)
            <li x-bind="disableNextAndPreviousButtons" class="flex w-full md:w-auto shrink-0 snap-start flex-col items-center  p-2" role="option">
                <x-cards.simple-card :post="$post" />
            </li>
            @endforeach

        </ul>


    </div>
</div>
@if(!$isHighlighted)
</div>

</div>
@endif
