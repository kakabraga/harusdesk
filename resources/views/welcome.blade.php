<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HarusDesk — Helpdesk SaaS Multi-tenant</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Canvas Confetti -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 font-sans antialiased selection:bg-brand-500 selection:text-white" x-data="harusApp()" x-init="initApp()" x-cloak>

    <!-- TOAST NOTIFICATIONS -->
    <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm pointer-events-none">
        <template x-for="t in toasts" :key="t.id">
            <div 
                x-show="t.visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-2xl border backdrop-blur-md"
                :class="{
                    'bg-emerald-950/90 border-emerald-500/40 text-emerald-100': t.type === 'success',
                    'bg-rose-950/90 border-rose-500/40 text-rose-100': t.type === 'error',
                    'bg-slate-900/90 border-slate-700 text-slate-200': t.type === 'info'
                }"
            >
                <div class="mt-0.5">
                    <template x-if="t.type === 'success'"><i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-400"></i></template>
                    <template x-if="t.type === 'error'"><i data-lucide="alert-triangle" class="w-5 h-5 text-rose-400"></i></template>
                    <template x-if="t.type === 'info'"><i data-lucide="info" class="w-5 h-5 text-brand-400"></i></template>
                </div>
                <div class="flex-1 text-sm leading-snug" x-text="t.message"></div>
                <button @click="removeToast(t.id)" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </template>
    </div>

    <!-- ======================================================== -->
    <!-- TELA 1: LOGIN & CADASTRO DE EMPRESA (QUANDO DESLOGADO) -->
    <!-- ======================================================== -->
    <template x-if="!token">
        <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950">
            <!-- Glow background -->
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-600/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10 text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-600 to-purple-500 shadow-xl shadow-brand-500/20 mb-4 border border-white/10">
                    <i data-lucide="layers" class="w-8 h-8 text-white"></i>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white flex items-center justify-center gap-2">
                    HarusDesk
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-brand-500/20 text-brand-300 border border-brand-500/30">SaaS v1.0</span>
                </h1>
                <p class="mt-2 text-sm text-slate-400">
                    Plataforma Helpdesk Multi-tenant com Isolamento Rigoroso e Prevenção IDOR
                </p>

                <!-- Tabs Login / Nova Empresa -->
                <div class="mt-6 p-1 bg-slate-900/80 rounded-xl border border-slate-800 flex max-w-xs mx-auto text-xs font-semibold">
                    <button 
                        @click="authTab = 'login'"
                        :class="authTab === 'login' ? 'bg-brand-600 text-white shadow-md' : 'text-slate-400 hover:text-white'"
                        class="flex-1 py-2 rounded-lg transition-all"
                    >
                        Entrar no Sistema
                    </button>
                    <button 
                        @click="authTab = 'register'; loadPlans()"
                        :class="authTab === 'register' ? 'bg-brand-600 text-white shadow-md' : 'text-slate-400 hover:text-white'"
                        class="flex-1 py-2 rounded-lg transition-all"
                    >
                        Cadastrar Empresa
                    </button>
                </div>
            </div>

            <!-- Card Formulário -->
            <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md relative z-10 px-4">
                <div class="bg-slate-900/90 border border-slate-800 shadow-2xl rounded-2xl p-6 sm:p-8 backdrop-blur-xl">

                    <!-- ABA 1: FORMULÁRIO DE LOGIN -->
                    <div x-show="authTab === 'login'" x-transition>
                        <form @submit.prevent="handleLogin()" class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-1">E-mail corporativo</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                        <i data-lucide="mail" class="w-4 h-4"></i>
                                    </div>
                                    <input 
                                        type="email" 
                                        x-model="loginForm.email" 
                                        required 
                                        placeholder="admin@empresa1.com"
                                        class="w-full bg-slate-950/80 border border-slate-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 rounded-xl py-2.5 pl-10 pr-3 text-sm text-white placeholder-slate-600 transition outline-none"
                                    >
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-1">Senha</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                        <i data-lucide="lock" class="w-4 h-4"></i>
                                    </div>
                                    <input 
                                        type="password" 
                                        x-model="loginForm.password" 
                                        required 
                                        placeholder="••••••••"
                                        class="w-full bg-slate-950/80 border border-slate-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 rounded-xl py-2.5 pl-10 pr-3 text-sm text-white placeholder-slate-600 transition outline-none"
                                    >
                                </div>
                            </div>

                            <button 
                                type="submit" 
                                :disabled="loginForm.loading"
                                class="w-full mt-2 py-3 px-4 rounded-xl font-semibold text-sm bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white shadow-lg shadow-brand-600/30 transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span x-show="!loginForm.loading">Acessar Painel</span>
                                <span x-show="loginForm.loading" class="inline-flex items-center gap-2">
                                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Autenticando...
                                </span>
                            </button>
                        </form>

                        <!-- Box de Contas de Demonstração (1 Clique) -->
                        <div class="mt-6 pt-5 border-t border-slate-800/80">
                            <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                                <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-400"></i>
                                Acesso de Demonstração (1 Clique)
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <button 
                                    @click="fillDemo('superadmin@harsudesk.com', 'password')"
                                    class="p-2.5 rounded-lg bg-slate-950/60 hover:bg-slate-800 border border-slate-800/80 hover:border-brand-500/50 text-left transition group"
                                >
                                    <div class="text-[11px] font-bold text-white group-hover:text-brand-300">Super Admin</div>
                                    <div class="text-[10px] text-slate-400 truncate">Global SaaS</div>
                                </button>
                                <button 
                                    @click="fillDemo('admin@empresa1.com', 'password')"
                                    class="p-2.5 rounded-lg bg-slate-950/60 hover:bg-slate-800 border border-slate-800/80 hover:border-brand-500/50 text-left transition group"
                                >
                                    <div class="text-[11px] font-bold text-white group-hover:text-brand-300">Admin Tenant</div>
                                    <div class="text-[10px] text-slate-400 truncate">Empresa 1</div>
                                </button>
                                <button 
                                    @click="fillDemo('user@empresa1.com', 'password')"
                                    class="p-2.5 rounded-lg bg-slate-950/60 hover:bg-slate-800 border border-slate-800/80 hover:border-brand-500/50 text-left transition group"
                                >
                                    <div class="text-[11px] font-bold text-white group-hover:text-brand-300">Solicitante</div>
                                    <div class="text-[10px] text-slate-400 truncate">Usuário comum</div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ABA 2: FORMULÁRIO DE NOVA EMPRESA (ONBOARDING SAAS) -->
                    <div x-show="authTab === 'register'" x-transition>
                        <form @submit.prevent="handleRegisterEnterprise()" class="space-y-3.5">
                            <div class="text-xs font-semibold text-brand-400 uppercase tracking-wider">Dados da Empresa</div>
                            
                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-1">Nome da Empresa</label>
                                <input 
                                    type="text" 
                                    x-model="registerForm.enterprise_name" 
                                    required 
                                    placeholder="Ex: Minha Empresa Tech"
                                    class="w-full bg-slate-950/80 border border-slate-800 focus:border-brand-500 rounded-xl py-2 px-3 text-sm text-white placeholder-slate-600 outline-none"
                                >
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1">CNPJ (14 dígitos)</label>
                                    <input 
                                        type="text" 
                                        x-model="registerForm.cnpj" 
                                        required 
                                        maxlength="14"
                                        placeholder="00000000000199"
                                        class="w-full bg-slate-950/80 border border-slate-800 focus:border-brand-500 rounded-xl py-2 px-3 text-sm text-white placeholder-slate-600 outline-none font-mono"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1">Plano Escolhido</label>
                                    <select 
                                        x-model="registerForm.plan_id" 
                                        required
                                        class="w-full bg-slate-950/80 border border-slate-800 focus:border-brand-500 rounded-xl py-2 px-2.5 text-xs text-white outline-none"
                                    >
                                        <option value="">Selecione...</option>
                                        <template x-for="p in plans" :key="p.id">
                                            <option :value="p.id" x-text="`${p.name} (R$ ${p.price})`"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-1">E-mail da Empresa</label>
                                <input 
                                    type="email" 
                                    x-model="registerForm.enterprise_email" 
                                    required 
                                    placeholder="contato@minhaempresa.com"
                                    class="w-full bg-slate-950/80 border border-slate-800 focus:border-brand-500 rounded-xl py-2 px-3 text-sm text-white placeholder-slate-600 outline-none"
                                >
                            </div>

                            <div class="text-xs font-semibold text-brand-400 uppercase tracking-wider pt-2 border-t border-slate-800">Administrador Inicial</div>

                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-1">Nome do Admin</label>
                                <input 
                                    type="text" 
                                    x-model="registerForm.admin_name" 
                                    required 
                                    placeholder="Seu Nome Completo"
                                    class="w-full bg-slate-950/80 border border-slate-800 focus:border-brand-500 rounded-xl py-2 px-3 text-sm text-white placeholder-slate-600 outline-none"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-1">E-mail do Admin</label>
                                <input 
                                    type="email" 
                                    x-model="registerForm.admin_email" 
                                    required 
                                    placeholder="admin@minhaempresa.com"
                                    class="w-full bg-slate-950/80 border border-slate-800 focus:border-brand-500 rounded-xl py-2 px-3 text-sm text-white placeholder-slate-600 outline-none"
                                >
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1">Senha (min. 8)</label>
                                    <input 
                                        type="password" 
                                        x-model="registerForm.password" 
                                        required 
                                        minlength="8"
                                        placeholder="••••••••"
                                        class="w-full bg-slate-950/80 border border-slate-800 focus:border-brand-500 rounded-xl py-2 px-3 text-sm text-white placeholder-slate-600 outline-none"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1">Confirmar Senha</label>
                                    <input 
                                        type="password" 
                                        x-model="registerForm.password_confirmation" 
                                        required 
                                        minlength="8"
                                        placeholder="••••••••"
                                        class="w-full bg-slate-950/80 border border-slate-800 focus:border-brand-500 rounded-xl py-2 px-3 text-sm text-white placeholder-slate-600 outline-none"
                                    >
                                </div>
                            </div>

                            <button 
                                type="submit" 
                                :disabled="registerForm.loading"
                                class="w-full mt-3 py-3 px-4 rounded-xl font-semibold text-sm bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                <span x-show="!registerForm.loading">Criar Empresa & Entrar</span>
                                <span x-show="registerForm.loading" class="inline-flex items-center gap-2">
                                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Registrando...
                                </span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </template>

    <!-- ======================================================== -->
    <!-- TELA 2: DASHBOARD & SISTEMA PRINCIPAL (LOGADO) -->
    <!-- ======================================================== -->
    <template x-if="token">
        <div class="h-full w-full flex flex-col relative overflow-hidden">
            <div class="h-full flex flex-col lg:flex-row overflow-hidden bg-slate-950">

            <!-- SIDEBAR -->
            <aside class="w-full lg:w-64 bg-slate-900 border-r border-slate-800 flex flex-col shrink-0">
                <!-- Brand Header -->
                <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-purple-500 flex items-center justify-center shadow-lg shadow-brand-500/20 text-white font-bold">
                            <i data-lucide="layers" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="font-bold text-white text-base leading-none">HarusDesk</div>
                            <div class="text-[10px] text-slate-400 font-medium tracking-wide">Multi-tenant Cloud</div>
                        </div>
                    </div>
                    <!-- Indicator dot -->
                    <span class="flex h-2.5 w-2.5 relative" title="API Online">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                </div>

                <!-- User Info Badge -->
                <div class="p-4 mx-3 my-3 rounded-xl bg-slate-950/80 border border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-brand-500/20 border border-brand-500/30 flex items-center justify-center text-brand-300 font-bold text-sm" x-text="user?.name ? user.name.charAt(0).toUpperCase() : 'U'"></div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-semibold text-white truncate" x-text="user?.name || 'Usuário'"></div>
                            <div class="text-[10px] text-slate-400 truncate" x-text="user?.email"></div>
                        </div>
                    </div>
                    <div class="mt-2.5 flex items-center justify-between">
                        <span 
                            class="text-[10px] font-semibold px-2 py-0.5 rounded-md uppercase tracking-wider"
                            :class="{
                                'bg-purple-500/20 text-purple-300 border border-purple-500/30': user?.role === 'super_admin',
                                'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30': user?.role === 'admin',
                                'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': user?.role === 'attendant',
                                'bg-slate-700/40 text-slate-300 border border-slate-700': user?.role === 'requester'
                            }"
                            x-text="formatRole(user?.role)"
                        ></span>
                        <span class="text-[10px] text-slate-500" x-text="`ID #${user?.id}`"></span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-3 space-y-1">
                    <button 
                        @click="activeTab = 'dashboard'"
                        :class="activeTab === 'dashboard' ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition"
                    >
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        Dashboard
                    </button>

                    <button 
                        @click="activeTab = 'tickets'; loadTickets()"
                        :class="activeTab === 'tickets' ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition"
                    >
                        <div class="flex items-center gap-3">
                            <i data-lucide="ticket" class="w-4 h-4"></i>
                            Chamados
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-950/60 text-slate-300" x-text="tickets.length"></span>
                    </button>

                    <button 
                        @click="activeTab = 'sectors'; loadSectors()"
                        :class="activeTab === 'sectors' ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition"
                    >
                        <div class="flex items-center gap-3">
                            <i data-lucide="building-2" class="w-4 h-4"></i>
                            Setores
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-950/60 text-slate-300" x-text="sectors.length"></span>
                    </button>

                    <button 
                        @click="activeTab = 'users'; loadUsers()"
                        :class="activeTab === 'users' ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition"
                    >
                        <div class="flex items-center gap-3">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            Usuários & Equipe
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-950/60 text-slate-300" x-text="users.length"></span>
                    </button>

                    <button 
                        @click="activeTab = 'plans'; loadPlans()"
                        :class="activeTab === 'plans' ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition"
                    >
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                        Planos & SaaS
                    </button>
                </nav>

                <!-- Sidebar Footer -->
                <div class="p-3 border-t border-slate-800">
                    <button 
                        @click="handleLogout()"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-rose-400 hover:bg-rose-950/30 border border-transparent hover:border-rose-900/50 transition"
                    >
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Encerrar Sessão
                    </button>
                </div>
            </aside>

            <!-- CONTEÚDO PRINCIPAL -->
            <main class="flex-1 flex flex-col overflow-hidden bg-slate-950">
                <!-- Topbar Header -->
                <header class="h-16 border-b border-slate-800 px-6 flex items-center justify-between shrink-0 bg-slate-900/40 backdrop-blur-md">
                    <div class="flex items-center gap-4">
                        <h2 class="text-lg font-bold text-white capitalize" x-text="activeTab === 'dashboard' ? 'Visão Geral' : activeTab === 'tickets' ? 'Central de Chamados' : activeTab === 'sectors' ? 'Gestão de Setores' : activeTab === 'users' ? 'Usuários & Equipe' : 'Planos & Assinaturas'"></h2>
                        <span class="hidden sm:inline-flex items-center gap-1.5 text-xs text-slate-400 bg-slate-900 px-3 py-1 rounded-full border border-slate-800">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i>
                            Tenant Isolation Ativo
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <template x-if="activeTab === 'sectors'">
                            <button 
                                @click="openNewSectorModal()"
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-brand-600 hover:bg-brand-500 text-white shadow-lg shadow-brand-600/25 transition"
                            >
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Novo Setor
                            </button>
                        </template>
                        <template x-if="activeTab === 'users'">
                            <button 
                                @click="openNewUserModal()"
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-brand-600 hover:bg-brand-500 text-white shadow-lg shadow-brand-600/25 transition"
                            >
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Novo Usuário
                            </button>
                        </template>
                        <template x-if="activeTab !== 'sectors' && activeTab !== 'users'">
                            <button 
                                @click="openNewTicketModal()"
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-brand-600 hover:bg-brand-500 text-white shadow-lg shadow-brand-600/25 transition"
                            >
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Novo Chamado
                            </button>
                        </template>
                        <button 
                            @click="refreshCurrentTab()"
                            :disabled="loading"
                            title="Atualizar dados"
                            class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 border border-slate-800 transition disabled:opacity-50"
                        >
                            <i data-lucide="refresh-cw" class="w-4 h-4" :class="loading ? 'animate-spin' : ''"></i>
                        </button>
                    </div>
                </header>

                <!-- Área Scrollável das Páginas -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6">

                    <!-- ========================================== -->
                    <!-- TAB 1: DASHBOARD -->
                    <!-- ========================================== -->
                    <div x-show="activeTab === 'dashboard'" class="space-y-6">
                        <!-- Cards de Métricas -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Card Total -->
                            <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800/90 shadow-xl relative overflow-hidden">
                                <div class="flex items-center justify-between text-slate-400 mb-2">
                                    <span class="text-xs font-medium uppercase tracking-wider">Total de Chamados</span>
                                    <div class="p-2 rounded-lg bg-slate-800 text-brand-400"><i data-lucide="folder" class="w-4 h-4"></i></div>
                                </div>
                                <div class="text-3xl font-extrabold text-white" x-text="tickets.length"></div>
                                <div class="mt-2 text-[11px] text-slate-400 flex items-center gap-1">
                                    <span class="text-emerald-400 font-semibold">100%</span> no tenant atual
                                </div>
                            </div>

                            <!-- Card Abertos -->
                            <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800/90 shadow-xl relative overflow-hidden">
                                <div class="flex items-center justify-between text-slate-400 mb-2">
                                    <span class="text-xs font-medium uppercase tracking-wider">Aguardando (Abertos)</span>
                                    <div class="p-2 rounded-lg bg-amber-500/10 text-amber-400"><i data-lucide="clock" class="w-4 h-4"></i></div>
                                </div>
                                <div class="text-3xl font-extrabold text-amber-400" x-text="tickets.filter(t => t.status === 'open').length"></div>
                                <div class="mt-2 text-[11px] text-slate-400">Requerem atendimento</div>
                            </div>

                            <!-- Card Em Progresso -->
                            <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800/90 shadow-xl relative overflow-hidden">
                                <div class="flex items-center justify-between text-slate-400 mb-2">
                                    <span class="text-xs font-medium uppercase tracking-wider">Em Andamento</span>
                                    <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-400"><i data-lucide="activity" class="w-4 h-4"></i></div>
                                </div>
                                <div class="text-3xl font-extrabold text-indigo-400" x-text="tickets.filter(t => t.status === 'in_progress').length"></div>
                                <div class="mt-2 text-[11px] text-slate-400">Em análise técnica</div>
                            </div>

                            <!-- Card Concluídos -->
                            <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800/90 shadow-xl relative overflow-hidden">
                                <div class="flex items-center justify-between text-slate-400 mb-2">
                                    <span class="text-xs font-medium uppercase tracking-wider">Concluídos</span>
                                    <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400"><i data-lucide="check-circle" class="w-4 h-4"></i></div>
                                </div>
                                <div class="text-3xl font-extrabold text-emerald-400" x-text="tickets.filter(t => t.status === 'concluded').length"></div>
                                <div class="mt-2 text-[11px] text-slate-400">Finalizados com sucesso</div>
                            </div>
                        </div>

                        <!-- Chamados Recentes -->
                        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
                            <div class="p-5 border-b border-slate-800 flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-white text-base">Chamados Recentes</h3>
                                    <p class="text-xs text-slate-400">Últimos tickets sincronizados com o banco de dados</p>
                                </div>
                                <button @click="activeTab = 'tickets'" class="text-xs font-semibold text-brand-400 hover:text-brand-300 flex items-center gap-1">
                                    Ver todos os chamados <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>

                            <div class="divide-y divide-slate-800/60">
                                <template x-for="ticket in tickets.slice(0, 5)" :key="ticket.id">
                                    <div class="p-4 hover:bg-slate-800/40 transition flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <span class="text-xs font-mono font-bold text-slate-500" x-text="`#${ticket.id}`"></span>
                                            <div class="min-w-0">
                                                <div class="text-sm font-semibold text-white truncate" x-text="ticket.title"></div>
                                                <div class="text-xs text-slate-400 flex items-center gap-2 mt-0.5">
                                                    <span x-text="getSectorName(ticket.sector_id)"></span>
                                                    <span>•</span>
                                                    <span x-text="formatDate(ticket.created_at)"></span>
                                                    <template x-if="ticket.has_attachments">
                                                        <span class="inline-flex items-center gap-1 text-[11px] text-brand-400"><i data-lucide="paperclip" class="w-3 h-3"></i> Anexos</span>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold" :class="getStatusBadgeClass(ticket.status)" x-text="formatStatus(ticket.status)"></span>
                                            <button @click="openDetailModal(ticket)" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="tickets.length === 0">
                                    <div class="p-8 text-center text-slate-400 text-sm">
                                        Nenhum chamado encontrado para este tenant.
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 2: CHAMADOS (TICKETS LIST) -->
                    <!-- ========================================== -->
                    <div x-show="activeTab === 'tickets'" class="space-y-4">
                        <!-- Filtros e Busca -->
                        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 flex flex-col sm:flex-row gap-3 items-center justify-between">
                            <div class="relative w-full sm:w-80">
                                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-3 text-slate-500"></i>
                                <input 
                                    type="text" 
                                    x-model="filters.search" 
                                    placeholder="Buscar por título ou ID..." 
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 pl-9 pr-3 text-xs text-white placeholder-slate-500 focus:border-brand-500 outline-none"
                                >
                            </div>

                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <!-- Filtro de Status -->
                                <select 
                                    x-model="filters.status" 
                                    class="bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-300 focus:border-brand-500 outline-none"
                                >
                                    <option value="">Todos os Status</option>
                                    <option value="open">Abertos</option>
                                    <option value="in_progress">Em Andamento</option>
                                    <option value="concluded">Concluídos</option>
                                    <option value="cancelled">Cancelados</option>
                                </select>

                                <!-- Filtro de Prioridade -->
                                <select 
                                    x-model="filters.priority" 
                                    class="bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-300 focus:border-brand-500 outline-none"
                                >
                                    <option value="">Todas as Prioridades</option>
                                    <option value="low">Baixa</option>
                                    <option value="medium">Média</option>
                                    <option value="high">Alta</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tabela de Chamados -->
                        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-800 bg-slate-950/40 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                                            <th class="py-3.5 px-4">#ID</th>
                                            <th class="py-3.5 px-4">Título do Chamado</th>
                                            <th class="py-3.5 px-4">Setor</th>
                                            <th class="py-3.5 px-4">Prioridade</th>
                                            <th class="py-3.5 px-4">Status</th>
                                            <th class="py-3.5 px-4">Criado em</th>
                                            <th class="py-3.5 px-4 text-right">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60 text-sm">
                                        <template x-for="t in filteredTickets" :key="t.id">
                                            <tr class="hover:bg-slate-800/30 transition">
                                                <td class="py-3.5 px-4 font-mono font-bold text-slate-400" x-text="`#${t.id}`"></td>
                                                <td class="py-3.5 px-4">
                                                    <div class="font-semibold text-white" x-text="t.title"></div>
                                                    <div class="text-xs text-slate-400 truncate max-w-xs" x-text="t.description"></div>
                                                </td>
                                                <td class="py-3.5 px-4">
                                                    <span class="text-xs text-slate-300 font-medium px-2 py-0.5 rounded-md bg-slate-800" x-text="getSectorName(t.sector_id)"></span>
                                                </td>
                                                <td class="py-3.5 px-4">
                                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full" :class="getPriorityBadgeClass(t.priority)" x-text="formatPriority(t.priority)"></span>
                                                </td>
                                                <td class="py-3.5 px-4">
                                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full" :class="getStatusBadgeClass(t.status)" x-text="formatStatus(t.status)"></span>
                                                </td>
                                                <td class="py-3.5 px-4 text-xs text-slate-400" x-text="formatDate(t.created_at)"></td>
                                                <td class="py-3.5 px-4 text-right">
                                                    <div class="inline-flex items-center gap-1">
                                                        <button 
                                                            @click="openDetailModal(t)"
                                                            title="Ver Detalhes"
                                                            class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
                                                        >
                                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                                        </button>
                                                        <button 
                                                            @click="deleteTicket(t.id)"
                                                            title="Excluir Chamado"
                                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-950/30 transition"
                                                        >
                                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="filteredTickets.length === 0">
                                            <tr>
                                                <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                                    Nenhum chamado corresponde aos filtros aplicados.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 3: SETORES (SECTORS LIST) -->
                    <!-- ========================================== -->
                    <div x-show="activeTab === 'sectors'" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-white text-base">Setores da Empresa</h3>
                                <p class="text-xs text-slate-400">Setores para direcionamento e triagem de chamados</p>
                            </div>
                            <button 
                                @click="openNewSectorModal()" 
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-brand-600 hover:bg-brand-500 text-white shadow-lg transition"
                            >
                                <i data-lucide="plus" class="w-4 h-4"></i> Novo Setor
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <template x-for="s in sectors" :key="s.id">
                                <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-mono font-bold text-slate-500" x-text="`#${s.id}`"></span>
                                            <span 
                                                class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                                :class="s.accepts_tickets ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400'"
                                                x-text="s.accepts_tickets ? 'Aceita Chamados' : 'Não Aceita'"
                                            ></span>
                                        </div>
                                        <h4 class="font-bold text-white text-base" x-text="s.name"></h4>
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
                                        <span class="text-slate-400" x-text="s.active ? 'Ativo' : 'Inativo'"></span>
                                        <button 
                                            @click="deleteSector(s.id)"
                                            class="text-slate-500 hover:text-rose-400 transition"
                                            title="Excluir Setor"
                                        >
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 4: PLANOS (PLANS SAAS) -->
                    <!-- ========================================== -->
                    <div x-show="activeTab === 'plans'" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-white text-base">Planos Disponíveis no HarusDesk</h3>
                                <p class="text-xs text-slate-400">Tiers de limites de usuários, tickets e armazenamento em nuvem</p>
                            </div>
                            <button 
                                @click="openNewPlanModal()" 
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-brand-600 hover:bg-brand-500 text-white shadow-lg transition"
                            >
                                <i data-lucide="plus" class="w-4 h-4"></i> Novo Plano
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <template x-for="p in plans" :key="p.id">
                                <div class="p-6 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between relative overflow-hidden group hover:border-brand-500/50 transition">
                                    <div>
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-brand-500/20 text-brand-300 border border-brand-500/30" x-text="`Tier #${p.id}`"></span>
                                            <span class="text-xs text-slate-400 font-mono" x-text="p.active ? 'Ativo' : 'Inativo'"></span>
                                        </div>
                                        <h4 class="text-xl font-bold text-white mb-1" x-text="p.name"></h4>
                                        <div class="text-2xl font-black text-brand-400 mb-4" x-text="`R$ ${Number(p.price).toFixed(2)} /mês`"></div>

                                        <ul class="space-y-2.5 text-xs text-slate-300">
                                            <li class="flex items-center gap-2">
                                                <i data-lucide="users" class="w-4 h-4 text-brand-400"></i>
                                                Até <strong class="text-white" x-text="p.max_users"></strong> usuários
                                            </li>
                                            <li class="flex items-center gap-2">
                                                <i data-lucide="ticket" class="w-4 h-4 text-brand-400"></i>
                                                Até <strong class="text-white" x-text="p.max_tickets_per_month"></strong> tickets/mês
                                            </li>
                                            <li class="flex items-center gap-2">
                                                <i data-lucide="hard-drive" class="w-4 h-4 text-brand-400"></i>
                                                <strong class="text-white" x-text="`${p.storage_mb} MB`"></strong> de armazenamento
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 5: USUÁRIOS & EQUIPE -->
                    <!-- ========================================== -->
                    <div x-show="activeTab === 'users'" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-white text-base">Usuários e Colaboradores</h3>
                                <p class="text-xs text-slate-400">Gerencie a equipe da empresa e seus vínculos com setores</p>
                            </div>
                            <button 
                                @click="openNewUserModal()" 
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-brand-600 hover:bg-brand-500 text-white shadow-lg transition"
                            >
                                <i data-lucide="plus" class="w-4 h-4"></i> Novo Usuário
                            </button>
                        </div>

                        <!-- Tabela de Usuários -->
                        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-800 bg-slate-950/40 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                                            <th class="py-3.5 px-4">Usuário</th>
                                            <th class="py-3.5 px-4">Perfil</th>
                                            <th class="py-3.5 px-4">Setores Vinculados</th>
                                            <th class="py-3.5 px-4">Status</th>
                                            <th class="py-3.5 px-4 text-right">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60 text-sm">
                                        <template x-for="u in users" :key="u.id">
                                            <tr class="hover:bg-slate-800/30 transition">
                                                <td class="py-3.5 px-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-brand-500/20 text-brand-300 font-bold flex items-center justify-center text-xs" x-text="u.name ? u.name.charAt(0).toUpperCase() : 'U'"></div>
                                                        <div>
                                                            <div class="font-semibold text-white flex items-center gap-2">
                                                                <span x-text="u.name"></span>
                                                                <template x-if="u.id === user?.id">
                                                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-brand-500/20 text-brand-300 border border-brand-500/30 font-medium">Você</span>
                                                                </template>
                                                            </div>
                                                            <div class="text-xs text-slate-400 font-mono" x-text="u.email"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3.5 px-4">
                                                    <span 
                                                        class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                                        :class="{
                                                            'bg-purple-500/10 text-purple-400 border border-purple-500/30': u.role === 'super_admin',
                                                            'bg-indigo-500/10 text-indigo-400 border border-indigo-500/30': u.role === 'admin',
                                                            'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30': u.role === 'attendant',
                                                            'bg-slate-800 text-slate-300 border border-slate-700': u.role === 'requester' || u.role === 'user'
                                                        }"
                                                        x-text="formatRole(u.role)"
                                                    ></span>
                                                </td>
                                                <td class="py-3.5 px-4">
                                                    <div class="flex flex-wrap items-center gap-1.5 max-w-xs">
                                                        <template x-for="sec in (u.sectors || [])" :key="sec.id">
                                                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-md bg-slate-800 text-slate-300 border border-slate-700/60" x-text="sec.name"></span>
                                                        </template>
                                                        <template x-if="!u.sectors || u.sectors.length === 0">
                                                            <span class="text-xs text-slate-500 italic">Nenhum setor</span>
                                                        </template>
                                                        <button 
                                                            @click="openEditUserSectorsModal(u)" 
                                                            class="text-xs text-brand-400 hover:text-brand-300 p-1 rounded hover:bg-slate-800 ml-1 inline-flex items-center gap-1"
                                                            title="Gerenciar setores deste usuário"
                                                        >
                                                            <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="py-3.5 px-4">
                                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium" :class="u.active !== false ? 'text-emerald-400 bg-emerald-500/10' : 'text-slate-500 bg-slate-800'" x-text="u.active !== false ? 'Ativo' : 'Inativo'"></span>
                                                </td>
                                                <td class="py-3.5 px-4 text-right">
                                                    <div class="inline-flex items-center gap-1.5">
                                                        <button 
                                                            @click="openEditUserSectorsModal(u)" 
                                                            class="p-1.5 rounded-lg text-slate-400 hover:text-brand-300 hover:bg-slate-800 transition"
                                                            title="Vincular Setores"
                                                        >
                                                            <i data-lucide="building" class="w-4 h-4"></i>
                                                        </button>
                                                        <template x-if="u.id !== user?.id && (user?.role === 'admin' || user?.role === 'super_admin')">
                                                            <button 
                                                                @click="deleteUser(u.id)" 
                                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-950/30 transition"
                                                                title="Excluir Usuário"
                                                            >
                                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="users.length === 0">
                                            <tr>
                                                <td colspan="5" class="py-12 text-center text-slate-400 text-sm">
                                                    Nenhum usuário encontrado na sua empresa.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL: NOVO CHAMADO -->
        <!-- ======================================================== -->
        <div 
            x-show="ticketModal.open" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div 
                @click.outside="ticketModal.open = false" 
                class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 overflow-hidden"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <h3 class="font-bold text-white text-base">Abrir Novo Chamado</h3>
                    <button @click="ticketModal.open = false" class="text-slate-400 hover:text-white">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form @submit.prevent="submitCreateTicket()" class="mt-4 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Título do Chamado</label>
                        <input 
                            type="text" 
                            x-model="ticketModal.title" 
                            required 
                            placeholder="Ex: Erro ao emitir relatório financeiro"
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-sm text-white focus:border-brand-500 outline-none"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Setor Destino</label>
                            <select 
                                x-model="ticketModal.sector_id" 
                                required
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-white focus:border-brand-500 outline-none"
                            >
                                <option value="">Selecione...</option>
                                <template x-for="s in sectors.filter(sec => sec.accepts_tickets)" :key="s.id">
                                    <option :value="s.id" x-text="s.name"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Prioridade</label>
                            <select 
                                x-model="ticketModal.priority" 
                                required
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-white focus:border-brand-500 outline-none"
                            >
                                <option value="low">Baixa</option>
                                <option value="medium">Média</option>
                                <option value="high">Alta</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Descrição do Problema</label>
                        <textarea 
                            rows="4" 
                            x-model="ticketModal.description" 
                            required 
                            placeholder="Explique com detalhes o que está acontecendo..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-sm text-white focus:border-brand-500 outline-none resize-none"
                        ></textarea>
                    </div>

                    <!-- Upload de Arquivos -->
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Anexar Documentos / Imagens (Opcional)</label>
                        <input 
                            type="file" 
                            multiple 
                            id="ticketFileInput"
                            @change="handleFileChange($event)"
                            class="block w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer"
                        >
                        <div class="mt-2 text-[11px] text-slate-500">Formatos aceitos: PDF, PNG, JPG, DOCX, TXT (Máx: 10MB)</div>
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2">
                        <button 
                            type="button" 
                            @click="ticketModal.open = false" 
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800 transition"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            :disabled="ticketModal.loading"
                            class="px-4 py-2 rounded-xl text-xs font-semibold bg-brand-600 hover:bg-brand-500 text-white transition disabled:opacity-50"
                        >
                            <span x-show="!ticketModal.loading">Criar Chamado</span>
                            <span x-show="ticketModal.loading">Enviando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL: DETALHES DO CHAMADO & STATUS -->
        <!-- ======================================================== -->
        <div 
            x-show="detailModal.open" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div 
                @click.outside="detailModal.open = false" 
                class="w-full max-w-xl bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 overflow-hidden max-h-[90vh] flex flex-col"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-mono font-bold text-slate-500" x-text="`#${detailModal.ticket?.id}`"></span>
                        <h3 class="font-bold text-white text-base truncate max-w-sm" x-text="detailModal.ticket?.title"></h3>
                    </div>
                    <button @click="detailModal.open = false" class="text-slate-400 hover:text-white">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto py-4 space-y-4">
                    <!-- Badges -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full" :class="getStatusBadgeClass(detailModal.ticket?.status)" x-text="formatStatus(detailModal.ticket?.status)"></span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full" :class="getPriorityBadgeClass(detailModal.ticket?.priority)" x-text="`Prioridade: ${formatPriority(detailModal.ticket?.priority)}`"></span>
                        <span class="text-xs text-slate-400 px-2 py-0.5 rounded-md bg-slate-800" x-text="`Setor: ${getSectorName(detailModal.ticket?.sector_id)}`"></span>
                    </div>

                    <!-- Descrição -->
                    <div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Descrição</div>
                        <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 text-sm text-slate-200 whitespace-pre-wrap leading-relaxed" x-text="detailModal.ticket?.description"></div>
                    </div>

                    <!-- Anexos -->
                    <div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 flex items-center justify-between">
                            <span>Anexos do Chamado</span>
                            <span class="text-slate-500" x-text="detailModal.ticket?.attachments ? `${detailModal.ticket.attachments.length} arquivo(s)` : 'Nenhum anexo'"></span>
                        </div>
                        <template x-if="detailModal.ticket?.attachments && detailModal.ticket.attachments.length > 0">
                            <div class="space-y-2">
                                <template x-for="att in detailModal.ticket.attachments" :key="att.id">
                                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between gap-3 text-xs">
                                        <div class="flex items-center gap-2 truncate">
                                            <i data-lucide="file" class="w-4 h-4 text-brand-400 shrink-0"></i>
                                            <span class="text-white truncate font-medium" x-text="att.original_name"></span>
                                            <span class="text-slate-500" x-text="`${att.size_kb} KB`"></span>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <a :href="`/api/attachments/${att.id}/preview`" target="_blank" class="p-1 rounded text-slate-400 hover:text-white hover:bg-slate-800" title="Visualizar">
                                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                            </a>
                                            <a :href="`/api/attachments/${att.id}/download`" class="p-1 rounded text-slate-400 hover:text-white hover:bg-slate-800" title="Baixar">
                                                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                            </a>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!detailModal.ticket?.attachments || detailModal.ticket.attachments.length === 0">
                            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800/60 text-xs text-slate-500 text-center">
                                Este chamado não possui arquivos anexados.
                            </div>
                        </template>
                    </div>

                    <!-- Mudar Status Rápido -->
                    <div class="pt-3 border-t border-slate-800">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Alterar Status</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <button 
                                @click="updateTicketStatus(detailModal.ticket.id, 'open')"
                                class="py-1.5 px-2.5 rounded-lg text-xs font-semibold border transition"
                                :class="detailModal.ticket?.status === 'open' ? 'bg-amber-500/20 text-amber-300 border-amber-500/50' : 'bg-slate-950 text-slate-400 border-slate-800 hover:border-slate-700'"
                            >
                                Aberto
                            </button>
                            <button 
                                @click="updateTicketStatus(detailModal.ticket.id, 'in_progress')"
                                class="py-1.5 px-2.5 rounded-lg text-xs font-semibold border transition"
                                :class="detailModal.ticket?.status === 'in_progress' ? 'bg-indigo-500/20 text-indigo-300 border-indigo-500/50' : 'bg-slate-950 text-slate-400 border-slate-800 hover:border-slate-700'"
                            >
                                Em Progresso
                            </button>
                            <button 
                                @click="updateTicketStatus(detailModal.ticket.id, 'concluded')"
                                class="py-1.5 px-2.5 rounded-lg text-xs font-semibold border transition"
                                :class="detailModal.ticket?.status === 'concluded' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/50' : 'bg-slate-950 text-slate-400 border-slate-800 hover:border-slate-700'"
                            >
                                Concluído
                            </button>
                            <button 
                                @click="updateTicketStatus(detailModal.ticket.id, 'cancelled')"
                                class="py-1.5 px-2.5 rounded-lg text-xs font-semibold border transition"
                                :class="detailModal.ticket?.status === 'cancelled' ? 'bg-rose-500/20 text-rose-300 border-rose-500/50' : 'bg-slate-950 text-slate-400 border-slate-800 hover:border-slate-700'"
                            >
                                Cancelado
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800 flex items-center justify-end">
                    <button 
                        @click="detailModal.open = false" 
                        class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-white transition"
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL: NOVO SETOR -->
        <!-- ======================================================== -->
        <div 
            x-show="sectorModal.open" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
            x-transition
        >
            <div @click.outside="sectorModal.open = false" class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="font-bold text-white text-base">Novo Setor</h3>
                    <button @click="sectorModal.open = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>

                <form @submit.prevent="submitCreateSector()" class="mt-4 space-y-3.5">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Nome do Setor</label>
                        <input type="text" x-model="sectorModal.name" required placeholder="Ex: Financeiro, TI, RH" class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-sm text-white focus:border-brand-500 outline-none">
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="accepts_tickets" x-model="sectorModal.accepts_tickets" class="rounded bg-slate-950 border-slate-800 text-brand-600 focus:ring-brand-500">
                        <label for="accepts_tickets" class="text-xs text-slate-300 cursor-pointer">Permitir recebimento de novos chamados</label>
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2">
                        <button type="button" @click="sectorModal.open = false" class="px-3.5 py-1.5 rounded-xl text-xs text-slate-300 hover:bg-slate-800">Cancelar</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-brand-600 hover:bg-brand-500 text-white">Criar Setor</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL: NOVO PLANO -->
        <!-- ======================================================== -->
        <div 
            x-show="planModal.open" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
            x-transition
        >
            <div @click.outside="planModal.open = false" class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="font-bold text-white text-base">Novo Plano de Assinatura</h3>
                    <button @click="planModal.open = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>

                <form @submit.prevent="submitCreatePlan()" class="mt-4 space-y-3.5">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Nome do Plano</label>
                        <input type="text" x-model="planModal.name" required placeholder="Ex: Plano Startup" class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-sm text-white focus:border-brand-500 outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Máx. Usuários</label>
                            <input type="number" min="1" x-model.number="planModal.max_users" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-sm text-white focus:border-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Tickets/Mês</label>
                            <input type="number" min="1" x-model.number="planModal.max_tickets_per_month" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-sm text-white focus:border-brand-500 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Espaço (MB)</label>
                            <input type="number" min="1" x-model.number="planModal.storage_mb" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-sm text-white focus:border-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Preço Mensal (R$)</label>
                            <input type="number" step="0.01" min="0" x-model.number="planModal.price" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-sm text-white focus:border-brand-500 outline-none">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2">
                        <button type="button" @click="planModal.open = false" class="px-3.5 py-1.5 rounded-xl text-xs text-slate-300 hover:bg-slate-800">Cancelar</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-brand-600 hover:bg-brand-500 text-white">Salvar Plano</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL: NOVO USUÁRIO -->
        <!-- ======================================================== -->
        <div 
            x-show="userModal.open" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
            x-transition
        >
            <div @click.outside="userModal.open = false" class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="font-bold text-white text-base">Novo Usuário</h3>
                    <button @click="userModal.open = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>

                <form @submit.prevent="submitCreateUser()" class="mt-4 space-y-3.5">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Nome Completo</label>
                        <input type="text" x-model="userModal.name" required placeholder="Ex: João Silva" class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-sm text-white focus:border-brand-500 outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">E-mail</label>
                            <input type="email" x-model="userModal.email" required placeholder="joao@empresa.com" class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-sm text-white focus:border-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Perfil de Acesso</label>
                            <select x-model="userModal.role" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-white focus:border-brand-500 outline-none">
                                <option value="requester">Solicitante (Abre chamados)</option>
                                <option value="attendant">Atendente (Responde chamados)</option>
                                <option value="admin">Administrador (Gerencia equipe)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Senha (mín. 8 caracteres)</label>
                            <input type="password" minlength="8" x-model="userModal.password" required placeholder="••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-sm text-white focus:border-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Confirmar Senha</label>
                            <input type="password" minlength="8" x-model="userModal.password_confirmation" required placeholder="••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-sm text-white focus:border-brand-500 outline-none">
                        </div>
                    </div>

                    <!-- Vínculo com Setores -->
                    <div class="pt-2 border-t border-slate-800">
                        <label class="block text-xs font-semibold text-brand-400 uppercase tracking-wider mb-2">Vincular aos Setores da Empresa</label>
                        <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto p-2 rounded-xl bg-slate-950 border border-slate-800/80">
                            <template x-for="s in sectors" :key="s.id">
                                <label class="flex items-center gap-2 text-xs text-slate-300 p-1.5 rounded hover:bg-slate-900 cursor-pointer">
                                    <input type="checkbox" :value="s.id" x-model="userModal.sector_ids" class="rounded bg-slate-900 border-slate-700 text-brand-600 focus:ring-brand-500">
                                    <span class="truncate" x-text="s.name"></span>
                                </label>
                            </template>
                            <template x-if="sectors.length === 0">
                                <div class="col-span-2 text-center text-xs text-slate-500 py-2">
                                    Nenhum setor cadastrado ainda.
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2">
                        <button type="button" @click="userModal.open = false" class="px-3.5 py-1.5 rounded-xl text-xs text-slate-300 hover:bg-slate-800">Cancelar</button>
                        <button type="submit" :disabled="userModal.loading" class="px-4 py-2 rounded-xl text-xs font-semibold bg-brand-600 hover:bg-brand-500 text-white disabled:opacity-50">
                            <span x-show="!userModal.loading">Criar Usuário</span>
                            <span x-show="userModal.loading">Salvando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL: GERENCIAR VÍNCULOS DE SETORES -->
        <!-- ======================================================== -->
        <div 
            x-show="userSectorsModal.open" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
            x-transition
        >
            <div @click.outside="userSectorsModal.open = false" class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h3 class="font-bold text-white text-base">Vincular Setores</h3>
                        <p class="text-xs text-slate-400" x-text="`Colaborador: ${userSectorsModal.user?.name}`"></p>
                    </div>
                    <button @click="userSectorsModal.open = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>

                <form @submit.prevent="submitUserSectors()" class="mt-4 space-y-4">
                    <div class="space-y-2 max-h-60 overflow-y-auto p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <template x-for="s in sectors" :key="s.id">
                            <label class="flex items-center justify-between text-xs text-slate-300 p-2 rounded-lg hover:bg-slate-900 cursor-pointer">
                                <span class="font-medium text-white" x-text="s.name"></span>
                                <input type="checkbox" :value="s.id" x-model="userSectorsModal.sector_ids" class="rounded bg-slate-900 border-slate-700 text-brand-600 focus:ring-brand-500">
                            </label>
                        </template>
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2">
                        <button type="button" @click="userSectorsModal.open = false" class="px-3.5 py-1.5 rounded-xl text-xs text-slate-300 hover:bg-slate-800">Cancelar</button>
                        <button type="submit" :disabled="userSectorsModal.loading" class="px-4 py-2 rounded-xl text-xs font-semibold bg-brand-600 hover:bg-brand-500 text-white disabled:opacity-50">
                            <span x-show="!userSectorsModal.loading">Salvar Vínculos</span>
                            <span x-show="userSectorsModal.loading">Atualizando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        </div>
    </template>

    <!-- ======================================================== -->
    <!-- SCRIPT DE LOGICA DO CLIENTE (ALPINE STORE & CONTROLLER) -->
    <!-- ======================================================== -->
    <script>
        function harusApp() {
            return {
                token: localStorage.getItem('harus_token') || null,
                user: JSON.parse(localStorage.getItem('harus_user') || 'null'),
                activeTab: 'dashboard',
                authTab: 'login',
                loading: false,
                tickets: [],
                sectors: [],
                users: [],
                plans: [],
                toasts: [],

                filters: {
                    status: '',
                    priority: '',
                    search: ''
                },

                loginForm: {
                    email: '',
                    password: '',
                    loading: false
                },

                registerForm: {
                    enterprise_name: '',
                    cnpj: '',
                    enterprise_email: '',
                    plan_id: '',
                    admin_name: '',
                    admin_email: '',
                    password: '',
                    password_confirmation: '',
                    loading: false
                },

                ticketModal: {
                    open: false,
                    title: '',
                    sector_id: '',
                    priority: 'medium',
                    description: '',
                    files: [],
                    loading: false
                },

                detailModal: {
                    open: false,
                    ticket: null
                },

                sectorModal: {
                    open: false,
                    name: '',
                    accepts_tickets: true
                },

                planModal: {
                    open: false,
                    name: '',
                    max_users: 10,
                    max_tickets_per_month: 500,
                    storage_mb: 5120,
                    price: 99.90
                },

                userModal: {
                    open: false,
                    name: '',
                    email: '',
                    role: 'requester',
                    password: '',
                    password_confirmation: '',
                    sector_ids: [],
                    loading: false
                },

                userSectorsModal: {
                    open: false,
                    user: null,
                    sector_ids: [],
                    loading: false
                },

                initApp() {
                    this.$nextTick(() => {
                        lucide.createIcons();
                    });

                    if (this.token) {
                        this.loadSectors();
                        this.loadTickets();
                        this.loadPlans();
                        this.loadUsers();
                    } else {
                        this.loadPlans();
                    }
                },

                // API Helper
                async api(endpoint, options = {}) {
                    const headers = {
                        'Accept': 'application/json',
                        ...(options.headers || {})
                    };

                    if (this.token) {
                        headers['Authorization'] = `Bearer ${this.token}`;
                    }

                    if (!(options.body instanceof FormData)) {
                        headers['Content-Type'] = 'application/json';
                    }

                    const response = await fetch(endpoint, {
                        ...options,
                        headers
                    });

                    if (response.status === 401) {
                        this.token = null;
                        this.user = null;
                        localStorage.removeItem('harus_token');
                        localStorage.removeItem('harus_user');
                        this.showToast('Sessão expirada. Faça login novamente.', 'error');
                        return null;
                    }

                    if (response.status === 204) {
                        return { success: true };
                    }

                    const data = await response.json();
                    if (!response.ok) {
                        throw new Error(data.message || 'Ocorreu um erro na requisição');
                    }
                    return data;
                },

                // Auth
                async handleLogin() {
                    this.loginForm.loading = true;
                    try {
                        const res = await this.api('/api/auth/login', {
                            method: 'POST',
                            body: JSON.stringify({
                                email: this.loginForm.email,
                                password: this.loginForm.password
                            })
                        });

                        this.token = res.data.token;
                        this.user = res.data.user;
                        localStorage.setItem('harus_token', this.token);
                        localStorage.setItem('harus_user', JSON.stringify(this.user));

                        this.showToast(`Bem-vindo, ${this.user.name}!`, 'success');
                        confetti({ particleCount: 80, spread: 60, origin: { y: 0.6 } });

                        this.initApp();
                    } catch (err) {
                        this.showToast(err.message, 'error');
                    } finally {
                        this.loginForm.loading = false;
                        this.$nextTick(() => lucide.createIcons());
                    }
                },

                fillDemo(email, pass) {
                    this.loginForm.email = email;
                    this.loginForm.password = pass;
                    this.handleLogin();
                },

                async handleLogout() {
                    try {
                        await this.api('/api/auth/logout', { method: 'POST' });
                    } catch (e) {}
                    this.token = null;
                    this.user = null;
                    this.tickets = [];
                    this.sectors = [];
                    localStorage.removeItem('harus_token');
                    localStorage.removeItem('harus_user');
                    this.showToast('Sessão encerrada com sucesso.', 'info');
                    this.$nextTick(() => lucide.createIcons());
                },

                async handleRegisterEnterprise() {
                    this.registerForm.loading = true;
                    try {
                        const payload = {
                            enterprise: {
                                name: this.registerForm.enterprise_name,
                                cnpj: this.registerForm.cnpj,
                                email: this.registerForm.enterprise_email,
                                plan_id: parseInt(this.registerForm.plan_id)
                            },
                            userAdmin: {
                                name: this.registerForm.admin_name,
                                email: this.registerForm.admin_email,
                                password: this.registerForm.password,
                                password_confirmation: this.registerForm.password_confirmation
                            }
                        };

                        await this.api('/api/enterprises', {
                            method: 'POST',
                            body: JSON.stringify(payload)
                        });

                        this.showToast('Empresa cadastrada com sucesso! Fazendo login...', 'success');
                        confetti({ particleCount: 120, spread: 80, origin: { y: 0.6 } });

                        // Loga com o admin criado
                        this.loginForm.email = this.registerForm.admin_email;
                        this.loginForm.password = this.registerForm.password;
                        await this.handleLogin();
                    } catch (err) {
                        this.showToast(err.message, 'error');
                    } finally {
                        this.registerForm.loading = false;
                    }
                },

                // Tickets
                async loadTickets() {
                    this.loading = true;
                    try {
                        let url = '/api/tickets?per_page=100';
                        if (this.filters.status) url += `&status=${this.filters.status}`;
                        if (this.filters.priority) url += `&priority=${this.filters.priority}`;

                        const res = await this.api(url);
                        this.tickets = res.data || [];
                    } catch (err) {
                        this.showToast(err.message, 'error');
                    } finally {
                        this.loading = false;
                        this.$nextTick(() => lucide.createIcons());
                    }
                },

                get filteredTickets() {
                    return this.tickets.filter(t => {
                        const matchesSearch = !this.filters.search || 
                            t.title.toLowerCase().includes(this.filters.search.toLowerCase()) ||
                            String(t.id).includes(this.filters.search);
                        const matchesStatus = !this.filters.status || t.status === this.filters.status;
                        const matchesPriority = !this.filters.priority || t.priority === this.filters.priority;
                        return matchesSearch && matchesStatus && matchesPriority;
                    });
                },

                openNewTicketModal() {
                    this.ticketModal.open = true;
                    this.ticketModal.title = '';
                    this.ticketModal.description = '';
                    this.ticketModal.priority = 'medium';
                    this.ticketModal.sector_id = this.sectors.find(s => s.accepts_tickets)?.id || '';
                    this.ticketModal.files = [];
                    this.$nextTick(() => lucide.createIcons());
                },

                handleFileChange(event) {
                    this.ticketModal.files = Array.from(event.target.files);
                },

                async submitCreateTicket() {
                    this.ticketModal.loading = true;
                    try {
                        const fd = new FormData();
                        fd.append('title', this.ticketModal.title);
                        fd.append('description', this.ticketModal.description);
                        fd.append('sector_id', this.ticketModal.sector_id);
                        fd.append('priority', this.ticketModal.priority);

                        if (this.user?.role === 'super_admin') {
                            fd.append('enterprise_id', this.sectors.find(s => s.id == this.ticketModal.sector_id)?.enterprise_id || 1);
                        }

                        this.ticketModal.files.forEach(f => {
                            fd.append('attachments[]', f);
                        });

                        await this.api('/api/tickets', {
                            method: 'POST',
                            body: fd
                        });

                        this.showToast('Chamado aberto com sucesso!', 'success');
                        this.ticketModal.open = false;
                        await this.loadTickets();
                    } catch (err) {
                        this.showToast(err.message, 'error');
                    } finally {
                        this.ticketModal.loading = false;
                        this.$nextTick(() => lucide.createIcons());
                    }
                },

                async openDetailModal(ticket) {
                    try {
                        const res = await this.api(`/api/tickets/${ticket.id}`);
                        this.detailModal.ticket = res.data;
                        this.detailModal.open = true;
                    } catch (e) {
                        this.detailModal.ticket = ticket;
                        this.detailModal.open = true;
                    }
                    this.$nextTick(() => lucide.createIcons());
                },

                async updateTicketStatus(ticketId, newStatus) {
                    try {
                        await this.api(`/api/tickets/${ticketId}`, {
                            method: 'PUT',
                            body: JSON.stringify({ status: newStatus })
                        });

                        this.showToast(`Status alterado para ${this.formatStatus(newStatus)}`, 'success');
                        if (this.detailModal.ticket) {
                            this.detailModal.ticket.status = newStatus;
                        }
                        await this.loadTickets();
                    } catch (err) {
                        this.showToast(err.message, 'error');
                    }
                },

                async deleteTicket(id) {
                    if (!confirm(`Deseja realmente excluir o chamado #${id}?`)) return;
                    try {
                        await this.api(`/api/tickets/${id}`, { method: 'DELETE' });
                        this.showToast(`Chamado #${id} excluído com sucesso.`, 'info');
                        await this.loadTickets();
                    } catch (err) {
                        this.showToast(err.message, 'error');
                    }
                },

                // Sectors
                async loadSectors() {
                    try {
                        const res = await this.api('/api/sectors');
                        this.sectors = res.data || [];
                    } catch (err) {
                        // ignore if unauthorized
                    }
                    this.$nextTick(() => lucide.createIcons());
                },

                openNewSectorModal() {
                    if (this.user?.role === 'requester') {
                        this.showToast('Solicitantes não têm permissão para criar setores. Acesse como Admin ou Super Admin.', 'error');
                        return;
                    }
                    this.sectorModal.name = '';
                    this.sectorModal.accepts_tickets = true;
                    this.sectorModal.open = true;
                    this.$nextTick(() => lucide.createIcons());
                },

                openNewPlanModal() {
                    if (this.user?.role !== 'super_admin') {
                        this.showToast('Apenas Super Administradores podem cadastrar novos planos SaaS.', 'error');
                        return;
                    }
                    this.planModal.open = true;
                    this.$nextTick(() => lucide.createIcons());
                },

                async submitCreateSector() {
                    try {
                        const payload = {
                            name: this.sectorModal.name,
                            accepts_tickets: Boolean(this.sectorModal.accepts_tickets),
                            active: true
                        };

                        if (this.user?.role === 'super_admin') {
                            payload.enterprise_id = this.user.enterprise_id || (this.sectors[0]?.enterprise_id || 1);
                        }

                        await this.api('/api/sectors', {
                            method: 'POST',
                            body: JSON.stringify(payload)
                        });

                        this.showToast('Setor criado com sucesso!', 'success');
                        this.sectorModal.open = false;
                        this.sectorModal.name = '';
                        await this.loadSectors();
                    } catch (err) {
                        this.showToast(err.message, 'error');
                    }
                },

                async deleteSector(id) {
                    if (!confirm(`Deseja realmente excluir o setor #${id}?`)) return;
                    try {
                        await this.api(`/api/sectors/${id}`, { method: 'DELETE' });
                        this.showToast('Setor excluído com sucesso.', 'info');
                        await this.loadSectors();
                    } catch (err) {
                        this.showToast(err.message, 'error');
                    }
                },

                // Plans
                async loadPlans() {
                    try {
                        const res = await this.api('/api/plans');
                        this.plans = res.data || [];
                    } catch (err) {}
                    this.$nextTick(() => lucide.createIcons());
                },

                async submitCreatePlan() {
                    try {
                        await this.api('/api/plans', {
                            method: 'POST',
                            body: JSON.stringify(this.planModal)
                        });

                        this.showToast('Plano cadastrado com sucesso!', 'success');
                        this.planModal.open = false;
                        await this.loadPlans();
                    } catch (err) {
                        this.showToast(err.message, 'error');
                    }
                },

                // Users
                async loadUsers() {
                    try {
                        const res = await this.api('/api/users');
                        this.users = res.data || [];
                    } catch (err) {}
                    this.$nextTick(() => lucide.createIcons());
                },

                openNewUserModal() {
                    if (this.user?.role === 'requester') {
                        this.showToast('Solicitantes não têm permissão para criar usuários. Acesse como Administrador.', 'error');
                        return;
                    }
                    this.userModal.name = '';
                    this.userModal.email = '';
                    this.userModal.role = 'requester';
                    this.userModal.password = '';
                    this.userModal.password_confirmation = '';
                    this.userModal.sector_ids = [];
                    this.userModal.open = true;
                    this.$nextTick(() => lucide.createIcons());
                },

                async submitCreateUser() {
                    this.userModal.loading = true;
                    try {
                        const payload = {
                            name: this.userModal.name,
                            email: this.userModal.email,
                            role: this.userModal.role,
                            password: this.userModal.password,
                            password_confirmation: this.userModal.password_confirmation,
                            sector_ids: this.userModal.sector_ids.map(Number)
                        };

                        if (this.user?.role === 'super_admin') {
                            payload.enterprise_id = this.user.enterprise_id || (this.sectors[0]?.enterprise_id || 1);
                        }

                        await this.api('/api/users', {
                            method: 'POST',
                            body: JSON.stringify(payload)
                        });

                        this.showToast('Usuário cadastrado com sucesso!', 'success');
                        this.userModal.open = false;
                        await this.loadUsers();
                    } catch (err) {
                        this.showToast(err.message, 'error');
                    } finally {
                        this.userModal.loading = false;
                        this.$nextTick(() => lucide.createIcons());
                    }
                },

                openEditUserSectorsModal(u) {
                    if (this.user?.role === 'requester') {
                        this.showToast('Solicitantes não têm permissão para gerenciar setores.', 'error');
                        return;
                    }
                    this.userSectorsModal.user = u;
                    this.userSectorsModal.sector_ids = (u.sectors || []).map(s => s.id);
                    this.userSectorsModal.open = true;
                    this.$nextTick(() => lucide.createIcons());
                },

                async submitUserSectors() {
                    this.userSectorsModal.loading = true;
                    try {
                        await this.api(`/api/users/${this.userSectorsModal.user.id}/sectors`, {
                            method: 'POST',
                            body: JSON.stringify({
                                sector_ids: this.userSectorsModal.sector_ids.map(Number)
                            })
                        });

                        this.showToast('Vínculos de setores atualizados com sucesso!', 'success');
                        this.userSectorsModal.open = false;
                        await this.loadUsers();
                    } catch (err) {
                        this.showToast(err.message, 'error');
                    } finally {
                        this.userSectorsModal.loading = false;
                        this.$nextTick(() => lucide.createIcons());
                    }
                },

                async deleteUser(id) {
                    if (!confirm(`Deseja realmente remover o usuário #${id}?`)) return;
                    try {
                        await this.api(`/api/users/${id}`, { method: 'DELETE' });
                        this.showToast(`Usuário #${id} excluído com sucesso.`, 'info');
                        await this.loadUsers();
                    } catch (err) {
                        this.showToast(err.message, 'error');
                    }
                },

                refreshCurrentTab() {
                    if (this.activeTab === 'tickets' || this.activeTab === 'dashboard') {
                        this.loadTickets();
                        this.loadSectors();
                        this.loadUsers();
                    } else if (this.activeTab === 'sectors') {
                        this.loadSectors();
                    } else if (this.activeTab === 'users') {
                        this.loadUsers();
                        this.loadSectors();
                    } else if (this.activeTab === 'plans') {
                        this.loadPlans();
                    }
                },

                // Helpers
                getSectorName(id) {
                    const s = this.sectors.find(sec => sec.id === id);
                    return s ? s.name : `Setor #${id}`;
                },

                formatRole(role) {
                    const roles = {
                        'super_admin': 'Super Admin',
                        'admin': 'Administrador',
                        'attendant': 'Atendente',
                        'requester': 'Solicitante'
                    };
                    return roles[role] || role;
                },

                formatStatus(status) {
                    const map = {
                        'open': 'Aberto',
                        'in_progress': 'Em Andamento',
                        'concluded': 'Concluído',
                        'cancelled': 'Cancelado',
                        'reopened': 'Reaberto'
                    };
                    return map[status] || status;
                },

                formatPriority(priority) {
                    const map = {
                        'low': 'Baixa',
                        'medium': 'Média',
                        'high': 'Alta'
                    };
                    return map[priority] || priority;
                },

                getStatusBadgeClass(status) {
                    const classes = {
                        'open': 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
                        'in_progress': 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/30',
                        'concluded': 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30',
                        'cancelled': 'bg-slate-800 text-slate-400 border border-slate-700',
                        'reopened': 'bg-purple-500/10 text-purple-400 border border-purple-500/30'
                    };
                    return classes[status] || 'bg-slate-800 text-slate-300';
                },

                getPriorityBadgeClass(priority) {
                    const classes = {
                        'low': 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                        'medium': 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                        'high': 'bg-rose-500/10 text-rose-400 border border-rose-500/20'
                    };
                    return classes[priority] || 'bg-slate-800 text-slate-300';
                },

                formatDate(dateStr) {
                    if (!dateStr) return '';
                    try {
                        const d = new Date(dateStr);
                        return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    } catch (e) {
                        return dateStr;
                    }
                },

                showToast(message, type = 'info') {
                    const id = Date.now();
                    this.toasts.push({ id, message, type, visible: true });
                    this.$nextTick(() => lucide.createIcons());
                    setTimeout(() => this.removeToast(id), 4500);
                },

                removeToast(id) {
                    const index = this.toasts.findIndex(t => t.id === id);
                    if (index !== -1) {
                        this.toasts[index].visible = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(t => t.id !== id);
                        }, 250);
                    }
                }
            }
        }
    </script>
</body>
</html>
