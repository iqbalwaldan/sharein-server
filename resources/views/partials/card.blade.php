<!-- resources/views/partials/card.blade.php -->
@php
    $subscriptionCardItems = [
        [
            'id' => 'basic',
            'title' => 'Basic',
            'desc' => 'Lorem ipsum dolor sit amet consectetur. Fusce nisl',
            'normal_price' => '190.000',
            'disc' => '60',
            'disc_bg_color' => 'bg-[#2652FF]',
            'disc_text_color' => 'text-primary-base',
            'disc_price' => '60.000',
            'btn_color' => 'bg-primary-base',
            'btn_text_color' => 'text-primary-base',
            'btn_bottom_color' => '#2652FF',
            'icons' => [
                ['icon' => '<i class="text-red-500"><svg>...</svg></i>', 'text' => 'Random Delay Group'],
                ['icon' => '<i class="text-red-500"><svg>...</svg></i>', 'text' => 'Random Delay Marketplace'],
                ['icon' => '<i class="text-red-500"><svg>...</svg></i>', 'text' => 'Auto Generate Photo Frame'],
                ['icon' => '<i class="text-red-500"><svg>...</svg></i>', 'text' => 'Massal Post to Marketplace'],
                ['icon' => '<i class="text-red-500"><svg>...</svg></i>', 'text' => 'Massal Post to Group'],
                ['icon' => '<i class="text-red-500"><svg>...</svg></i>', 'text' => 'Auto Inbox'],
                [
                    'icon' => '<i class="text-red-500"><svg>...</svg></i>',
                    'text' => 'Auto Post to Marketplace by Schedule',
                ],
                ['icon' => '<i class="text-red-500"><svg>...</svg></i>', 'text' => 'Auto Post to Group by Schedule'],
            ],
        ],
        [
            'id' => 'premium',
            'title' => 'Premium',
            'desc' => 'Lorem ipsum dolor sit amet consectetur. Fusce nisl',
            'normal_price' => '190.000',
            'disc' => '60',
            'disc_bg_color' => 'bg-[#F7B217]',
            'disc_text_color' => 'text-button-base',
            'disc_price' => '60.000',
            'btn_color' => 'bg-button-base',
            'btn_text_color' => 'text-button-base',
            'btn_bottom_color' => '#F7B217',
            'icons' => [
                ['icon' => '<i class="text-red-500"><svg>...</svg></i>', 'text' => 'Random Delay Group'],
                ['icon' => '<i class="text-red-500"><svg>...</svg></i>', 'text' => 'Random Delay Marketplace'],
                ['icon' => '<i class="text-red-500"><svg>...</svg></i>', 'text' => 'Auto Generate Photo Frame'],
                ['icon' => '<i class="text-red-500"><svg>...</svg></i>', 'text' => 'Massal Post to Marketplace'],
                ['icon' => '<i class="text-red-500"><svg>...</svg></i>', 'text' => 'Massal Post to Group'],
                ['icon' => '<i class="text-red-500"><svg>...</svg></i>', 'text' => 'Auto Inbox'],
                [
                    'icon' => '<i class="text-red-500"><svg>...</svg></i>',
                    'text' => 'Auto Post to Marketplace by Schedule',
                ],
                ['icon' => '<i class="text-red-500"><svg>...</svg></i>', 'text' => 'Auto Post to Group by Schedule'],
            ],
        ],
        // More subscription card items...
    ];
@endphp

<div class="flex flex-row gap-5 2xl:gap-12">
    @foreach ($subscriptionCardItems as $index => $item)
        <div class="{{ $index === 0 ? '' : 'ml-4' }}">
            <div class="p-6 border border-neutral-30 rounded-[20px]">
                <div class="flex flex-col items-center justify-center">
                    <div class="text-center">
                        <h1 class="text-3xl font-semibold text-neutral-80 mb-2">{{ $item['title'] }} Plan</h1>
                        <p class="px-[5%] 2xl:px-[20%] text-base 2xl:text-lg font-normal text-neutral-80 mb-4">
                            {{ $item['desc'] }}</p>
                        <!-- Render other subscription card content -->
                        <div class="flex justify-evenly items-center mb-4">
                            <p class="text-base font-normal text-neutral-80 line-through">Rp.
                                {{ $item['normal_price'] }},</p>
                            <div
                                class="p-2 {{ $item['disc_bg_color'] }} bg-opacity-30 {{ $item['disc_text_color'] }} rounded-[10px] text-sm font-semibold">
                                <p>Diskon {{ $item['disc'] }}%</p>
                            </div>
                        </div>
                        <div class="flex justify-center items-center mb-4">
                            <p class="font-light text-lg text-neutral-80">RP</p>
                            <h1 class="text-5xl font-bold text-neutral-80">{{ $item['disc_price'] }}</h1>
                            <p class="font-light text-lg text-neutral-80">/Bln</p>
                        </div>
                        <a href="#" class="inline-block">
                            <button
                                class="py-3 px-8 2xl:px-10 {{ $item['btn_color'] }} text-base 2xl:text-xl font-semibold text-white rounded-lg mb-2">
                                Choose a Package
                            </button>
                        </a>
                        <p class="font-normal text-xs text-neutral-50">Rp. {{ $item['normal_price'] }}/month at the time
                            of service renewal</p>
                    </div>
                </div>
                <div class="flex flex-col items-start mt-14">
                    <h2 class="text-lg font-semibold text-neutral-80 mb-4">Top Features</h2>
                    @foreach ($item['icons'] as $iconItem)
                        <div class="flex justify-between w-full mb-4">
                            <div class="flex">
                                {!! $iconItem['icon'] !!}
                                <p class="ml-2 text-sm font-normal text-neutral-80">{{ $iconItem['text'] }}</p>
                            </div>
                            <i class="text-gray-500">@svg('heroicon-o-question-mark-circle', ['class' => 'h-6 w-6'])</i>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-center mt-16">
                    <p class="text-base font-semibold {{ $item['btn_text_color'] }} mr-4">See more</p>
                    <i class="text-[#F7B217]">@svg('heroicon-o-chevron-down', ['class' => 'h-6 w-6'])</i>
                </div>
            </div>
        </div>
    @endforeach
</div>
