<nav class="fixed w-full h-24 z-50 transition duration-300">
    <div class="flex justify-between items-center h-full w-full px-24 2xl:px-16">
        <a href="{{ url('/') }}">
            <div class="flex flex-row items-center justify-center gap-2">
                <div
                    class="flex items-center justify-center h-9 bg-gradient-to-b from-primary-base to-secondary-base rounded-lg w-9 cursor-pointer">
                    <img src="{{ asset('assets/images/logo-white.png') }}" width="21" height="23" />
                </div>
                <p class="font-bold text-[32px] text-primary-base">Sharein</p>
            </div>
        </a>
        <div id="guest" class="flex">
            <ul class="flex items-center justify-center text-white">
                <a href="#hero">
                    <li class="ml-10 text-xl font-semibold">
                        Why us
                    </li>
                </a>
                <a href="#features">
                    <li class="ml-10 text-xl font-semibold">
                        Features
                    </li>
                </a>
                <a href="{{ url('/pricing') }}">
                    <li class="ml-10 text-xl font-semibold">
                        Pricing
                    </li>
                </a>
                <a href="{{ url('/resources') }}">
                    <li class="ml-10 text-xl font-semibold">
                        Resources
                    </li>
                </a>
                <a href="{{ route('user.register') }}">
                    <li>
                        <button id="register"
                            class="px-6 py-1 ml-10 rounded-xl text-xl font-semibold border-2 border-neutral-10">
                            Register
                        </button>
                    </li>
                </a>
                <a href="{{ route('user.login') }}">
                    <li>
                        <button id="login"
                            class="px-6 py-2 ml-10 rounded-xl text-xl font-semibold bg-neutral-10 text-primary-base">
                            Login
                        </button>
                    </li>
                </a>
            </ul>
        </div>
        <div id="user" class="hidden flex">
            <ul class="flex gap-10 items-center justify-center text-white">
                <a href="#hero">
                    <li class="text-xl font-semibold">
                        Why us
                    </li>
                </a>
                <a href="#features">
                    <li class="text-xl font-semibold">
                        Features
                    </li>
                </a>
                <a href="{{ url('/pricing') }}">
                    <li class="text-xl font-semibold">
                        Pricing
                    </li>
                </a>
                <a href="{{ url('/resources') }}">
                    <li class="text-xl font-semibold">
                        Resources
                    </li>
                </a>
                <a href="{{ route('user.dashboard') }}">
                    <li>
                        <p class="text-lg font-normal">
                            Welcome back, <strong>{{ auth()->user()->first_name ?? 'Guest' }}</strong>
                        </p>
                    </li>
                </a>
            </ul>
        </div>
    </div>
</nav>
<script>
    $(document).ready(function() {
        const navbar = document.querySelector('nav');
        const navLinks = navbar.querySelectorAll('a');
        // const logoText = navbar.querySelector('p');x
        const registerButton = navbar.querySelector('#register');
        const loginButton = navbar.querySelector('#login');

        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('bg-white', 'shadow');
                navLinks.forEach(link => link.classList.add('text-primary-base'));
                // logoText.classList.add('text-primary-base');
                registerButton.classList.add('border-primary-base');
                loginButton.classList.add('bg-primary-base', 'text-white');
            } else {
                navbar.classList.remove('bg-white', 'shadow');
                navLinks.forEach(link => link.classList.remove('text-primary-base'));
                // logoText.classList.remove('text-primary-base');
                registerButton.classList.remove('border-primary-base');
                loginButton.classList.remove('bg-primary-base', 'text-white');
            }
        });

        if ('{{ auth()->user()->id ?? 0 }}' == 0) {
            document.querySelector('#guest').classList.remove('hidden');
            document.querySelector('#user').classList.add('hidden');
        } else {
            document.querySelector('#guest').classList.add('hidden');
            document.querySelector('#user').classList.remove('hidden');
        }
    });
</script>
