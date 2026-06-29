<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bioimplant — Smart Surgery Management</title>
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
       
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white text-slate-900 antialiased overflow-x-hidden">
        
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 z-50 bg-white/70 backdrop-blur-xl border-b border-slate-50 px-8 lg:px-16">
            <div class="max-w-[1600px] mx-auto h-24 flex items-center justify-between">
                <img src="{{ asset('images/logo.png') }}" alt="Bioimplant" class="h-8 w-auto">
                <div class="flex items-center gap-10">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-[11px] font-bold uppercase tracking-widest text-slate-950 hover:opacity-60 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-[11px] font-bold uppercase tracking-widest text-slate-950 hover:opacity-60 transition">Acceso</a>
                        <a href="{{ route('login') }}" class="bg-slate-950 text-white px-8 py-3.5 rounded-full text-[11px] font-bold uppercase tracking-widest hover:bg-brand-600 transition shadow-elegant">Entrar</a>
                    @endauth
                </div>
            </div>
        </header>

        <main>
            <!-- Hero -->
            <section class="min-h-screen flex flex-col justify-center px-8 lg:px-16 pt-24 relative overflow-hidden">
                <!-- Abstract Gradient -->
                <div class="absolute -top-40 -right-40 w-[800px] h-[800px] bg-brand-100/30 rounded-full blur-[120px] -z-10 animate-pulse"></div>
                <div class="absolute -bottom-40 -left-40 w-[600px] h-[600px] bg-brand-50 rounded-full blur-[100px] -z-10"></div>

                <div class="max-w-6xl mx-auto text-center space-y-12">
                    <span class="inline-block text-[11px] font-bold uppercase tracking-[0.4em] text-brand-600">Surgical Logic v2.0</span>
                    
                    <h1 class="text-7xl lg:text-9xl font-serif leading-[0.9] tracking-tight text-slate-950">
                        Smart <span class="italic text-brand-500">Traceability</span> <br> for Surgeons.
                    </h1>
                    
                    <p class="max-w-2xl mx-auto text-xl lg:text-2xl text-slate-400 font-light leading-relaxed">
                        Sistema integral para la gestión, esterilización y control en tiempo real de cajas quirúrgicas. Eficiencia absoluta para Bioimplant.
                    </p>

                    <div class="pt-8 flex flex-col sm:flex-row items-center justify-center gap-6">
                        <a href="{{ route('login') }}" class="w-full sm:w-auto bg-slate-950 text-white px-12 py-5 rounded-full text-[13px] font-bold uppercase tracking-widest hover:bg-brand-950 transition-all shadow-elegant hover:scale-105 active:scale-95">
                            Comenzar ahora
                        </a>
                        <a href="#features" class="text-[13px] font-bold uppercase tracking-widest text-slate-400 hover:text-slate-950 transition border-b border-transparent hover:border-slate-950 pb-1">
                            Ver características
                        </a>
                    </div>
                </div>
            </section>

            <!-- Features Grid -->
            <section id="features" class="py-40 px-8 lg:px-16 bg-slate-50/50">
                <div class="max-w-[1400px] mx-auto grid grid-cols-1 md:grid-cols-3 gap-16">
                    <div class="space-y-6">
                        <div class="h-[1px] w-12 bg-slate-200"></div>
                        <h3 class="text-3xl font-serif text-slate-950 italic">Esterilización</h3>
                        <p class="text-slate-400 font-light leading-loose">Control riguroso de cada ciclo previo al ingreso en quirófano. Seguridad garantizada.</p>
                    </div>
                    <div class="space-y-6">
                        <div class="h-[1px] w-12 bg-brand-300"></div>
                        <h3 class="text-3xl font-serif text-slate-950 italic">Tiempo Real</h3>
                        <p class="text-slate-400 font-light leading-loose">Dashboard inteligente para monitorear el estado exacto de cada caja y cirugía.</p>
                    </div>
                    <div class="space-y-6">
                        <div class="h-[1px] w-12 bg-slate-200"></div>
                        <h3 class="text-3xl font-serif text-slate-950 italic">Consumos</h3>
                        <p class="text-slate-400 font-light leading-loose">Reportes de materiales simplificados para técnicos, integrados directamente en el sistema.</p>
                    </div>
                </div>
            </section>
        </main>

        <footer class="py-32 px-8 lg:px-16 border-t border-slate-50">
            <div class="max-w-[1400px] mx-auto flex flex-col md:flex-row justify-between items-center gap-12">
                <img src="{{ asset('images/logo.png') }}" alt="Bioimplant" class="h-7 w-auto grayscale opacity-30">
                <div class="flex gap-10 text-[11px] font-bold uppercase tracking-widest text-slate-300">
                    <a href="#" class="hover:text-slate-950 transition">Privacidad</a>
                    <a href="#" class="hover:text-slate-950 transition">Términos</a>
                    <a href="#" class="hover:text-slate-950 transition">Soporte</a>
                </div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-300 leading-none">
                    &copy; {{ date('Y') }} Bioimplant SRL.
                </p>
            </div>
        </footer>
    </body>
</html>
