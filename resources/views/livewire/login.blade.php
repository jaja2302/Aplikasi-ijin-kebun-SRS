@section('title', 'Sign in')

<div class="bg-white w-full h-screen scrollbar-none overflow-y-auto">
    <!-- component -->
    <div class="h-screen md:flex">
        <div
            class="relative overflow-hidden md:flex w-1/2 bg-gradient-to-tr from-emerald-800 to-amber-700 i justify-around items-center hidden">
            <div>
                <h1 class="text-white font-bold text-4xl font-sans">SRS - Izin Kebun Apps</h1>
                <p class="text-white mt-1">We are an Agronomy and Laboratory Services Provider in Kalimantan</p>
                <a href="https://srs-ssms.com" target="_blank"
                    class="flex justify-center w-28 shadow-md bg-white text-emerald-800 mt-4 py-2 rounded-2xl font-bold mb-2 hover:bg-slate-200 hover:shadow-md duration-300">
                    Read
                    More</a>
            </div>
            <div class="absolute -bottom-32 -left-40 w-80 h-80 border-4 rounded-full border-opacity-30 border-t-8">
            </div>
            <div class="absolute -bottom-40 -left-20 w-80 h-80 border-4 rounded-full border-opacity-30 border-t-8">
            </div>
            <div class="absolute -top-40 -right-0 w-80 h-80 border-4 rounded-full border-opacity-30 border-t-8"></div>
            <div class="absolute -top-20 -right-20 w-80 h-80 border-4 rounded-full border-opacity-30 border-t-8"></div>
        </div>
        <div class="flex md:w-1/2 justify-center py-10 items-center bg-white">
            <div class="w-1/2">
                <div class="flex flex-col items-center">
                    <img src="{{ asset('images/icons/logo.svg') }}" class="hidden lg:block w-24 mb-5" alt="SRS Logo">
                    <h1 class="text-slate-800 font-bold text-3xl mb-1">Welcome Back</h1>
                    <p class="text-sm font-semibold text-slate-400 mb-7">Please enter your details</p>
                </div>
                <form wire:submit.prevent="authUser">
                    {{ $this->form }}
                    <div class="w-full flex flex-row mt-5 mb-6">
                        <div class="w-1/2 flex flex-row justify-start">
                            <input wire:model="remember" type="checkbox"
                                class="checkbox checkbox-sm checkbox-success border-slate-300 [--chkfg:white]" />
                            <span class="text-slate-500 text-sm font-semibold mx-2">Remember me</span>
                        </div>
                        <div class="w-1/2 flex flex-row justify-end">
                            <a href="#" class="text-slate-400 text-sm font-semibold">Forgot password?</a>
                        </div>
                    </div>
                    <button type="submit"
                        class="block w-full bg-emerald-600 mt-4 py-2.5 rounded-lg text-white font-semibold mb-2 hover:bg-emerald-700 duration-300">Sign
                        in<i
                            class="fas fa-arrow-right ms-2 text-sm mt-0.5 text-slate-100 hover:scale-150 duration-500"></i></button>
                    <div class="w-full flex flex-row justify-center mt-5">
                        <h1 class="text-slate-400 text-sm font-semibold">Don't have an account?</h1>
                        <a href="#" class="mx-1 font-bold text-emerald-600 text-sm">Sign
                            up</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
