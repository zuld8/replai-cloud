<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="@{{ csrf_token() }}">
    <title>Replai {{ __('track_ticket.page_title') }} - Support System</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Bootstrap CSS for dropdown -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        [v-cloak] {
            display: none;
        }
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fade-in 0.5s ease-out;
        }
        .language-dropdown {
            position: relative;
        }
        .language-dropdown .dropdown-menu {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div id="app" v-cloak></div>

    <!-- Vue 3 from CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    
    <script>
        const { createApp } = Vue;
        
        // Translations from Laravel
        const translations = {
            pageTitle: '{{ __('track_ticket.page_title') }}',
            signIn: '{{ __('track_ticket.sign_in') }}',
            trackYourTicket: '{{ __('track_ticket.track_your_ticket') }}',
            enterTicketSubtitle: '{{ __('track_ticket.enter_ticket_id_subtitle') }}',
            enterTicketPlaceholder: '{{ __('track_ticket.enter_ticket_placeholder') }}',
            trackButton: '{{ __('track_ticket.track_button') }}',
            searching: '{{ __('track_ticket.searching') }}',
            errorEnterTicketId: '{{ __('track_ticket.error_enter_ticket_id') }}',
            errorTicketNotFound: '{{ __('track_ticket.error_ticket_not_found') }}',
            errorFailedFetch: '{{ __('track_ticket.error_failed_fetch') }}',
            ticketId: '{{ __('track_ticket.ticket_id') }}',
            category: '{{ __('track_ticket.category') }}',
            ticketLevel: '{{ __('track_ticket.ticket_level') }}',
            created: '{{ __('track_ticket.created') }}',
            description: '{{ __('track_ticket.description') }}',
            na: '{{ __('track_ticket.na') }}',
            progressTimeline: '{{ __('track_ticket.progress_timeline') }}',
            assignedTo: '{{ __('track_ticket.assigned_to') }}',
            pending: '{{ __('track_ticket.pending') }}',
            awaitingStage: '{{ __('track_ticket.awaiting_stage') }}',
            assignedTeam: '{{ __('track_ticket.assigned_team') }}',
            agent: '{{ __('track_ticket.agent') }}',
            updatesNotes: '{{ __('track_ticket.updates_notes') }}',
            supportTeam: '{{ __('track_ticket.support_team') }}',
            needMoreHelp: '{{ __('track_ticket.need_more_help') }}',
            supportAssist: '{{ __('track_ticket.support_assist') }}',
            signInToReply: '{{ __('track_ticket.sign_in_to_reply') }}',
            emailSupport: '{{ __('track_ticket.email_support') }}',
            howToTrack: '{{ __('track_ticket.how_to_track') }}',
            step1Title: '{{ __('track_ticket.step_1_title') }}',
            step1Desc: '{{ __('track_ticket.step_1_desc') }}',
            step2Title: '{{ __('track_ticket.step_2_title') }}',
            step2Desc: '{{ __('track_ticket.step_2_desc') }}',
            step3Title: '{{ __('track_ticket.step_3_title') }}',
            step3Desc: '{{ __('track_ticket.step_3_desc') }}',
            allRightsReserved: '{{ __('track_ticket.all_rights_reserved') }}',
            privacyPolicy: '{{ __('track_ticket.privacy_policy') }}',
            termsOfService: '{{ __('track_ticket.terms_of_service') }}',
            contactUs: '{{ __('track_ticket.contact_us') }}'
        };
        
        createApp({
            data() {
                return {
                    searchQuery: '',
                    ticket: null,
                    loading: false,
                    error: null,
                    t: translations
                };
            },
            computed: {
                statusTimeline() {
                    if (!this.ticket || !this.ticket.label_process) return [];
                    
                    return this.ticket.label_process.map(item => ({
                        id: item.label.id,
                        label: item.label.name,
                        color: item.label.color,
                        position: item.label.position,
                        agents: item.log && item.log.agents ? item.log.agents : [],
                        timestamp: item.log ? item.log.log_time : null,
                        created_at: item.log ? item.log.created_at : null,
                        hasLog: !!item.log,
                        active: !!item.log
                    }));
                },
            },
            mounted() {
                const urlParams = new URLSearchParams(window.location.search);
                const ticketId = urlParams.get('ticket_id');
                if (ticketId) {
                    this.searchQuery = ticketId;
                    this.searchTicket();
                }
            },
            methods: {
                async searchTicket() {
                    if (!this.searchQuery.trim()) {
                        this.error = this.t.errorEnterTicketId;
                        return;
                    }
                    
                    this.loading = true;
                    this.error = null;
                    this.ticket = null;
                    
                    try {
                        const response = await fetch(`/api-app/public/tickets/${this.searchQuery}`);
                        
                        if (!response.ok) {
                            if (response.status === 404) {
                                throw new Error(this.t.errorTicketNotFound);
                            }
                            throw new Error(this.t.errorFailedFetch);
                        }
                        
                        const data = await response.json();
                        this.ticket = data.data;
                        
                        const url = new URL(window.location);
                        url.searchParams.set('ticket_id', this.searchQuery);
                        window.history.pushState({}, '', url);
                        
                    } catch (err) {
                        this.error = err.message;
                    } finally {
                        this.loading = false;
                    }
                },
                
                getStatusBadgeClass(status) {
                    const classes = {
                        'Open': 'bg-blue-100 text-blue-800',
                        'In Progress': 'bg-yellow-100 text-yellow-800',
                        'Waiting': 'bg-orange-100 text-orange-800',
                        'Resolved': 'bg-green-100 text-green-800',
                        'Closed': 'bg-gray-100 text-gray-800',
                    };
                    return classes[status] || 'bg-gray-100 text-gray-800';
                },
                
                getPriorityClass(priority) {
                    const classes = {
                        'Low': 'bg-blue-100 text-blue-800',
                        'Medium': 'bg-yellow-100 text-yellow-800',
                        'High': 'bg-orange-100 text-orange-800',
                        'Critical': 'bg-red-100 text-red-800',
                    };
                    return classes[priority] || 'bg-gray-100 text-gray-800';
                },
                
                formatDate(dateString) {
                    if (!dateString) return 'N/A';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },
                
                getInitials(name) {
                    if (!name) return '?';
                    return name
                        .split(' ')
                        .map(n => n[0])
                        .join('')
                        .toUpperCase()
                        .slice(0, 2);
                },
            },
            template: `
                <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-emerald-50">
                    <!-- Header -->
                    <header class="bg-white shadow-sm sticky top-0 z-50">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img src="/assets/img/color-logo.png" alt="Replai Logo" class="h-8 object-contain">
                                </div>
                                <div class="flex items-center gap-4">
                                    <!-- Language Selector -->
                                    <div class="language-dropdown">
                                        <button class="btn btn-sm btn-light border-0 p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: transparent;">
                                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() == 'id' ? 'active' : '' }}" href="{{ route('setlang','id') }}">
                                                    <img src="/assets/img/flags/indonesia.png" alt="Indonesia" class="rounded" style="width: 20px; height: 15px;">
                                                    <span>{{__('sidebar.indonesia')}}</span>
                                                    @if(app()->getLocale() == 'id')
                                                        <svg class="w-4 h-4 ms-auto text-success" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('setlang','en') }}">
                                                    <img src="/assets/img/flags/english.png" alt="English" class="rounded" style="width: 20px; height: 15px;">
                                                    <span>{{__('sidebar.english')}}</span>
                                                    @if(app()->getLocale() == 'en')
                                                        <svg class="w-4 h-4 ms-auto text-success" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() == 'hi' ? 'active' : '' }}" href="{{ route('setlang','hi') }}">
                                                    <img src="/assets/img/flags/india.png" alt="India" class="rounded" style="width: 20px; height: 15px;">
                                                    <span>{{__('sidebar.india')}}</span>
                                                    @if(app()->getLocale() == 'hi')
                                                        <svg class="w-4 h-4 ms-auto text-success" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() == 'pt' ? 'active' : '' }}" href="{{ route('setlang','pt') }}">
                                                    <img src="/assets/img/flags/portugal.png" alt="Portugal" class="rounded" style="width: 20px; height: 15px;">
                                                    <span>{{__('sidebar.portugal')}}</span>
                                                    @if(app()->getLocale() == 'pt')
                                                        <svg class="w-4 h-4 ms-auto text-success" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() == 'es' ? 'active' : '' }}" href="{{ route('setlang','es') }}">
                                                    <img src="/assets/img/flags/spanyol.png" alt="Spanish" class="rounded" style="width: 20px; height: 15px;">
                                                    <span>{{__('sidebar.spanish')}}</span>
                                                    @if(app()->getLocale() == 'es')
                                                        <svg class="w-4 h-4 ms-auto text-success" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() == 'de' ? 'active' : '' }}" href="{{ route('setlang','de') }}">
                                                    <img src="/assets/img/flags/de.svg" alt="German" class="rounded" style="width: 20px; height: 15px;">
                                                    <span>{{__('sidebar.german')}}</span>
                                                    @if(app()->getLocale() == 'de')
                                                        <svg class="w-4 h-4 ms-auto text-success" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() == 'ar' ? 'active' : '' }}" href="{{ route('setlang','ar') }}">
                                                    <img src="/assets/img/flags/arab.png" alt="Arabic" class="rounded" style="width: 20px; height: 15px;">
                                                    <span>{{__('sidebar.arab')}}</span>
                                                    @if(app()->getLocale() == 'ar')
                                                        <svg class="w-4 h-4 ms-auto text-success" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() == 'ja' ? 'active' : '' }}" href="{{ route('setlang','ja') }}">
                                                    <img src="/assets/img/flags/jp.svg" alt="Japan" class="rounded" style="width: 20px; height: 15px;">
                                                    <span>{{__('sidebar.japan')}}</span>
                                                    @if(app()->getLocale() == 'ja')
                                                        <svg class="w-4 h-4 ms-auto text-success" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() == 'nl' ? 'active' : '' }}" href="{{ route('setlang','nl') }}">
                                                    <img src="/assets/img/flags/nl.svg" alt="Dutch" class="rounded" style="width: 20px; height: 15px;">
                                                    <span>{{__('sidebar.dutch')}}</span>
                                                    @if(app()->getLocale() == 'nl')
                                                        <svg class="w-4 h-4 ms-auto text-success" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="/login" class="px-4 py-2 text-sm font-medium text-green-600 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors">
                                        @{{ t.signIn }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </header>

                    <!-- Main Content -->
                    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                        <!-- Search Section -->
                        <div class="text-center mb-12">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-600 to-emerald-600 rounded-full mb-6 shadow-lg">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-3">@{{ t.trackYourTicket }}</h2>
                            <p class="text-lg text-gray-600 mb-8">@{{ t.enterTicketSubtitle }}</p>
                            
                            <!-- Search Form -->
                            <div class="max-w-2xl mx-auto">
                                <form @submit.prevent="searchTicket" class="relative">
                                    <div class="flex gap-3">
                                        <div class="flex-1 relative">
                                            <input
                                                v-model="searchQuery"
                                                type="text"
                                                :placeholder="t.enterTicketPlaceholder"
                                                class="w-full px-6 py-4 text-lg border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                                                :class="{ 'border-red-500': error }"
                                            />
                                        </div>
                                        <button
                                            type="submit"
                                            :disabled="loading || !searchQuery"
                                            class="px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:scale-105"
                                        >
                                            <span v-if="!loading">@{{ t.trackButton }}</span>
                                            <span v-else>@{{ t.searching }}</span>
                                        </button>
                                    </div>
                                </form>
                                
                                <!-- Error Message -->
                                <div v-if="error" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3 text-red-700">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                    <span>@{{ error }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Details -->
                        <div v-if="ticket && !loading" class="space-y-6 animate-fade-in">
                            <!-- Ticket Header Card -->
                            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-green-100 text-sm font-medium mb-1">@{{ t.ticketId }}</p>
                                            <h3 class="text-2xl font-bold text-white">@{{ ticket.ticket_id }}</h3>
                                        </div>
                                        <div class="text-right">
                                            <span 
                                                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold"
                                                :class="getStatusBadgeClass(ticket.status)"
                                            >
                                                @{{ ticket.status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="px-8 py-6">
                                    <h4 class="text-xl font-bold text-gray-900 mb-4">@{{ ticket.subject }}</h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500 mb-1">@{{ t.category }}</p>
                                                <p class="font-semibold text-gray-900">@{{ ticket.category ? ticket.category.name : t.na }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500 mb-1">@{{ t.ticketLevel }}</p>
                                                <span 
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold"
                                                    :class="getPriorityClass(ticket.ticket_level)"
                                                >
                                                    @{{ ticket.ticket_level }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500 mb-1">@{{ t.created }}</p>
                                                <p class="font-semibold text-gray-900">@{{ formatDate(ticket.created_at) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div v-if="ticket.description" class="mt-6 pt-6 border-t border-gray-200">
                                        <p class="text-sm text-gray-500 mb-2">@{{ t.description }}</p>
                                        <p class="text-gray-700">@{{ ticket.description }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Timeline -->
                            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                                <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    @{{ t.progressTimeline }}
                                </h4>
                                
                                <div class="space-y-4">
                                    <div 
                                        v-for="(status, index) in statusTimeline" 
                                        :key="status.id"
                                        class="flex gap-4"
                                        :class="{ 'opacity-40': !status.hasLog }"
                                    >
                                        <div class="flex flex-col items-center">
                                            <div 
                                                class="w-12 h-12 rounded-full flex items-center justify-center border-4"
                                                :class="status.hasLog ? 'bg-green-600 border-green-200' : 'bg-gray-300 border-gray-200'"
                                                :style="status.hasLog ? { backgroundColor: status.color, borderColor: status.color + '33' } : {}"
                                            >
                                                <svg v-if="status.hasLog" class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                <svg v-else class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div 
                                                v-if="index < statusTimeline.length - 1"
                                                class="w-1 h-16 mt-2"
                                                :class="status.hasLog ? 'bg-green-600' : 'bg-gray-300'"
                                                :style="status.hasLog ? { backgroundColor: status.color } : {}"
                                            ></div>
                                        </div>
                                        
                                        <div class="flex-1 pb-8">
                                            <div class="flex items-center justify-between mb-2">
                                                <h5 class="font-semibold text-lg" :class="status.hasLog ? 'text-gray-900' : 'text-gray-500'">
                                                    @{{ status.label }}
                                                </h5>
                                                <span v-if="status.hasLog" class="text-sm text-gray-600">
                                                    @{{ formatDate(status.timestamp) }}
                                                </span>
                                                <span v-else class="text-xs px-2 py-1 bg-gray-100 text-gray-500 rounded-full">
                                                    @{{ t.pending }}
                                                </span>
                                            </div>
                                            <div v-if="status.hasLog && status.agents.length > 0">
                                                <p class="text-sm text-gray-600 mb-2">@{{ t.assignedTo }}</p>
                                                <div class="flex flex-wrap gap-2">
                                                    <span 
                                                        v-for="agent in status.agents" 
                                                        :key="agent.id"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full text-sm"
                                                    >
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                        </svg>
                                                        @{{ agent.name }}
                                                    </span>
                                                </div>
                                            </div>
                                            <p v-else class="text-sm text-gray-400 italic">
                                                @{{ t.awaitingStage }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Assigned Agents -->
                            <div v-if="ticket.agents && ticket.agents.length > 0" class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                                <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    @{{ t.assignedTeam }}
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div 
                                        v-for="agent in ticket.agents" 
                                        :key="agent.id"
                                        class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl"
                                    >
                                        <div class="w-12 h-12 bg-gradient-to-br from-green-600 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                            @{{ getInitials(agent.name) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">@{{ agent.name }}</p>
                                            <p class="text-sm text-gray-500">@{{ agent.pivot ? agent.pivot.role : t.agent }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes Section -->
                            <div v-if="ticket.notes && ticket.notes.length > 0" class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                                <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                    @{{ t.updatesNotes }}
                                </h4>
                                
                                <div class="space-y-4">
                                    <div 
                                        v-for="note in ticket.notes" 
                                        :key="note.id"
                                        class="border-l-4 border-green-500 pl-4 py-2">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="font-semibold text-gray-900">@{{ note.user ? note.user.name : t.supportTeam }}</p>
                                            <span class="text-sm text-gray-500">@{{ formatDate(note.created_at) }}</span>
                                        </div>
                                        <p class="text-gray-700">@{{ note.note }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Support -->
                            <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl shadow-xl p-8 text-white text-center">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <h4 class="text-2xl font-bold mb-2">@{{ t.needMoreHelp }}</h4>
                                <p class="text-green-100 mb-6">@{{ t.supportAssist }}</p>
                                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                    <a href="/login" class="px-6 py-3 bg-white text-green-600 font-semibold rounded-lg hover:bg-green-50 transition-colors">
                                        @{{ t.signInToReply }}
                                    </a>
                                    <a href="mailto:support@example.com" class="px-6 py-3 bg-green-700 text-white font-semibold rounded-lg hover:bg-green-800 transition-colors">
                                        @{{ t.emailSupport }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-if="!ticket && !loading && !error" class="mt-12">
                            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                                <h3 class="text-xl font-bold text-gray-900 mb-6 text-center">@{{ t.howToTrack }}</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <span class="text-2xl font-bold text-green-600">1</span>
                                        </div>
                                        <h4 class="font-semibold text-gray-900 mb-2">@{{ t.step1Title }}</h4>
                                        <p class="text-sm text-gray-600">@{{ t.step1Desc }}</p>
                                    </div>
                                    
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <span class="text-2xl font-bold text-emerald-600">2</span>
                                        </div>
                                        <h4 class="font-semibold text-gray-900 mb-2">@{{ t.step2Title }}</h4>
                                        <p class="text-sm text-gray-600">@{{ t.step2Desc }}</p>
                                    </div>
                                    
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <span class="text-2xl font-bold text-green-600">3</span>
                                        </div>
                                        <h4 class="font-semibold text-gray-900 mb-2">@{{ t.step3Title }}</h4>
                                        <p class="text-sm text-gray-600">@{{ t.step3Desc }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>

                    <!-- Footer -->
                    <footer class="bg-gray-900 text-white mt-20">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                                <p class="text-gray-400">&copy; 2025 Manufactur Digital Hub. @{{ t.allRightsReserved }}</p>
                                <div class="flex gap-6">
                                    <a href="#" class="text-gray-400 hover:text-white transition-colors">@{{ t.privacyPolicy }}</a>
                                    <a href="#" class="text-gray-400 hover:text-white transition-colors">@{{ t.termsOfService }}</a>
                                    <a href="#" class="text-gray-400 hover:text-white transition-colors">@{{ t.contactUs }}</a>
                                </div>
                            </div>
                        </div>
                    </footer>
                </div>
            `
        }).mount('#app');
    </script>
    
    <!-- Bootstrap JS for dropdown functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

