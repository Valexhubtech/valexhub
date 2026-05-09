<!-- Modern 2026 Hero Section with Retro Grid -->
<section class="hero-section relative top-0 flex flex-col justify-center items-center -mt-24 w-full min-h-screen overflow-hidden">

    <!-- Retro Grid Background -->
    <style>
        @keyframes grid {
            0% { transform: translateY(-50%); }
            100% { transform: translateY(0); }
        }
        .animate-grid {
            animation: grid 20s linear infinite;
        }
        .synthwave-grid {
            background-repeat: repeat;
            background-size: 40px 40px;
            height: 300vh;
            inset: 0% 0px;
            margin-left: -50%;
            transform-origin: 100% 0 0;
            width: 600vw;
        }
        :not(.dark) .synthwave-grid-light {
            background-image: 
                linear-gradient(to right, rgba(59, 130, 246, 0.8) 2px, transparent 0),
                linear-gradient(to bottom, rgba(59, 130, 246, 0.8) 2px, transparent 0);
        }
        .dark .synthwave-grid-dark {
            background-image: 
                linear-gradient(to right, rgba(255,255,255,0.9) 2px, transparent 0),
                linear-gradient(to bottom, rgba(255,255,255,0.9) 2px, transparent 0);
        }
    </style>

    <div x-data="{ angle: 60 }" class="absolute inset-0 w-full h-full overflow-hidden opacity-60 pointer-events-none" style="perspective: 200px;">
        <div class="absolute inset-0" :style="{ transform: `rotateX(${angle}deg)` }">
            <div class="animate-grid synthwave-grid synthwave-grid-light synthwave-grid-dark"></div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-white to-transparent to-90% dark:from-black"></div>
    </div>


    <!-- Content Container -->
    <div class="hero-content flex flex-col gap-8 justify-center items-center px-8 pt-32 mx-auto w-full max-w-5xl text-center md:px-12 xl:px-20 lg:pt-32 lg:pb-16 relative z-10">

        <div class="w-full space-y-8">
            <!-- Main Headline with GSAP Animation -->
            <h1 x-data="{
                startingAnimation: { opacity: 0, y: 50, rotation: '25deg' },
                endingAnimation: { opacity: 1, y: 0, rotation: '0deg', stagger: 0.02, duration: 0.7, ease: 'back' },
                addCNDScript: false,
                splitCharactersIntoSpans(element) {
                    text = element.innerHTML;
                    modifiedHTML = [];
                    for (var i = 0; i < text.length; i++) {
                        attributes = '';
                        if(text[i].trim()){ attributes = 'class=\'inline-block\''; }
                        modifiedHTML.push('<span ' + attributes + '>' + text[i] + '</span>');
                    }
                    element.innerHTML = modifiedHTML.join('');
                },

                animateText() {
                    $el.classList.remove('invisible');
                    gsap.fromTo($el.children, this.startingAnimation, this.endingAnimation);
                }
            }"
            x-init="
                splitCharactersIntoSpans($el);
                gsapInterval = setInterval(function(){
                    if(typeof gsap !== 'undefined'){
                        animateText();
                        clearInterval(gsapInterval);
                    }
                }, 5);
            "
            class="hero-title invisible block pb-0.5 overflow-hidden text-3xl font-black tracking-tight text-center sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl text-balance leading-[0.9] mb-6 text-gray-900">
                Powerful Business Tools Built for Growth
            </h1>

            <!-- Subtitle with Typewriter Animation -->
            <div 
                x-data="{
                    text: '',
                    textArray : ['We build tailored digital solutions that help businesses recover lost revenue', 'Streamline operations and scale profitably with modern technology', 'Custom software for schools, hotels, pharmacies and SMEs across Nigeria'],
                    textIndex: 0,
                    charIndex: 0,
                    typeSpeed: 50,
                    cursorSpeed: 550,
                    pauseEnd: 2000,
                    pauseStart: 500,
                    direction: 'forward',
                }" 
                x-init="$nextTick(() => {
                    let typingInterval = setInterval(startTyping, $data.typeSpeed);
                
                    function startTyping(){
                        let current = $data.textArray[ $data.textIndex ];
                        
                        // check to see if we hit the end of the string
                        if($data.charIndex > current.length){
                                $data.direction = 'backward';
                                clearInterval(typingInterval);
                                
                                setTimeout(function(){
                                    typingInterval = setInterval(startTyping, $data.typeSpeed);
                                }, $data.pauseEnd);
                        }   
                            
                        $data.text = current.substring(0, $data.charIndex);
                        
                        if($data.direction == 'forward')
                        {
                            $data.charIndex += 1;
                        } 
                        else 
                        {
                            if($data.charIndex == 0)
                            {
                                $data.direction = 'forward';
                                clearInterval(typingInterval);
                                setTimeout(function(){
                                    $data.textIndex += 1;
                                    if($data.textIndex >= $data.textArray.length)
                                    {
                                        $data.textIndex = 0;
                                    }
                                    typingInterval = setInterval(startTyping, $data.typeSpeed);
                                }, $data.pauseStart);
                            }
                            $data.charIndex -= 1;
                        }
                    }
                                
                    setInterval(function(){
                        if($refs.cursor.classList.contains('hidden'))
                        {
                            $refs.cursor.classList.remove('hidden');
                        } 
                        else 
                        {
                            $refs.cursor.classList.add('hidden');
                        }
                    }, $data.cursorSpeed);

                })"
                class="hero-subtitle flex items-center justify-center mx-auto text-center max-w-3xl">
                <div class="relative flex items-center justify-center h-auto min-h-[2rem] md:min-h-[2.5rem] lg:min-h-[3rem] xl:min-h-[3.5rem]">
                    <p class="text-sm font-medium md:text-base lg:text-lg xl:text-xl text-gray-600 leading-relaxed" x-text="text || '.'"></p>
                    <span class="absolute right-0 w-1 -mr-1 bg-gray-600 h-5 md:h-6 lg:h-7 xl:h-8" x-ref="cursor"></span>
                </div>
            </div>

            <!-- CTA Buttons with Enhanced Design -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mx-auto mt-10">
                <a
                    href="/book-demo"
                    class="hero-cta w-full sm:w-auto bg-black hover:bg-gray-900 shadow-lg hover:shadow-xl text-white font-semibold px-8 py-4 rounded-lg transition-all duration-300 inline-flex items-center justify-center">
                    <span class="flex items-center gap-2">
                        Book a Demo
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 7l5 5-5 5"/>
                        </svg>
                    </span>
                </a>

                <a
                    href="/pricing"
                    class="hero-cta w-full sm:w-auto border-2 border-gray-900 hover:border-black bg-white hover:bg-gray-50 shadow-lg hover:shadow-xl text-gray-900 hover:text-black font-semibold px-8 py-4 rounded-lg transition-all duration-300 inline-flex items-center justify-center">
                    <span class="flex items-center gap-2">
                        Explore Products
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>
            </div>

            <!-- Trust Indicators -->
            <div class="mx-auto mt-12 max-w-2xl">
                <p class="text-sm font-semibold text-center text-gray-500 mb-4">
                    Trusted by 500+ businesses across Nigeria
                </p>
                <div class="flex justify-center space-x-8 text-center">
                    <div class="text-xs font-medium text-gray-600">Schools</div>
                    <div class="text-xs font-medium text-gray-600">Hotels</div>
                    <div class="text-xs font-medium text-gray-600">Pharmacies</div>
                    <div class="text-xs font-medium text-gray-600">SMEs</div>
                </div>
            </div>
        </div>
    </div>
    
</section>