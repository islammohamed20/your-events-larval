@extends('layouts.app')

@section('title', __('contact.page_title'))

@section('content')
<!-- Page Header -->
<section class="hero-section hero-contact" style="padding: 40px 0; background: linear-gradient(135deg, var(--primary-color) 0%, #2d1a5e 50%, var(--purple-light) 100%) !important;">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold mb-3" style="color: #fff;">{{ __('contact.heading') }}</h1>
            <p class="lead" style="color: rgba(255,255,255,0.9);">{{ __('contact.description') }}</p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mb-5">
                <div class="card shadow-lg" data-aos="fade-right">
                    <div class="card-body p-5">
                        <h3 class="mb-4 text-primary">
                            <i class="fas fa-envelope me-2"></i>{{ __('contact.send_message') }}
                        </h3>
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('contact.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">{{ __('contact.full_name') }}</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">{{ __('contact.email') }}</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">{{ __('contact.phone') }}</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label d-block">{{ __('contact.select_topic') }}</label>
                                    <div class="d-flex flex-wrap gap-3" id="subject-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="subject" id="subjectBooking" value="booking" {{ old('subject') === 'booking' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="subjectBooking">{{ __('contact.booking_inquiry') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="subject" id="subjectPackages" value="packages" {{ old('subject') === 'packages' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="subjectPackages">{{ __('contact.packages_inquiry') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="subject" id="subjectServices" value="services" {{ old('subject') === 'services' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="subjectServices">{{ __('contact.services_inquiry') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="subject" id="subjectComplaint" value="complaint" {{ old('subject') === 'complaint' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="subjectComplaint">{{ __('contact.complaint') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="subject" id="subjectOther" value="other" {{ old('subject') === 'other' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="subjectOther">{{ __('contact.other') }}</label>
                                        </div>
                                    </div>
                                    @error('subject')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="message" class="form-label">{{ __('contact.message') }}</label>
                                    <div class="position-relative">
                                        <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" placeholder="{{ __('contact.message_placeholder') }}" required>{{ old('message') }}</textarea>
                                        <!-- Voice Input Buttons -->
                                        <div class="voice-controls position-absolute" style="bottom: 10px; left: 10px; display: flex; gap: 8px;">
                                            <!-- Speech to Text Button -->
                                            <button type="button" id="speechToTextBtn" class="btn btn-sm btn-outline-primary rounded-circle" title="{{ __('contact.voice_typing') }}" style="width: 38px; height: 38px; padding: 0;" data-lang="{{ app()->getLocale() === 'ar' ? 'ar-SA' : 'en-US' }}" data-allow-microphone-voice-typing="{{ __('contact.allow_microphone_voice_typing') }}" data-voice-typing-not-supported="{{ __('contact.voice_typing_not_supported') }}">
                                                <i class="fas fa-microphone"></i>
                                            </button>
                                            <!-- Voice Recording Button -->
                                            <button type="button" id="voiceRecordBtn" class="btn btn-sm btn-outline-danger rounded-circle" title="{{ __('contact.voice_record') }}" style="width: 38px; height: 38px; padding: 0;" data-allow-microphone-voice-recording="{{ __('contact.allow_microphone_voice_recording') }}">
                                                <i class="fas fa-record-vinyl"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Voice Recording Status -->
                                    <div id="recordingStatus" class="mt-2" style="display: none;">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="recording-indicator"></span>
                                            <span class="text-danger fw-bold">{{ __('contact.recording') }}</span>
                                            <span id="recordingTime" class="text-muted">00:00</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Audio Preview -->
                                    <div id="audioPreview" class="mt-2" style="display: none;">
                                        <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                                            <audio id="recordedAudio" controls class="flex-grow-1" style="height: 40px;"></audio>
                                            <button type="button" id="deleteRecording" class="btn btn-sm btn-outline-danger" title="{{ __('contact.delete_recording') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <input type="hidden" name="voice_message" id="voiceMessageInput">
                                    </div>
                                    
                                    <!-- Speech Recognition Status -->
                                    <div id="speechStatus" class="mt-2" style="display: none;">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="speech-indicator"></span>
                                            <span class="text-primary fw-bold">{{ __('contact.listening') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>{{ __('contact.send_message_btn') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Contact Information -->
                <div class="card shadow mb-4" data-aos="fade-left">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4 text-primary">
                            <i class="fas fa-info-circle me-2"></i>{{ __('contact.contact_info') }}
                        </h5>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-map-marker-alt text-primary me-3"></i>
                                <div>
                                    <strong>{{ __('contact.address') }}</strong><br>
                                    {{ \App\Models\Setting::get('contact_address', __('contact.default_address')) }}
                                </div>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-phone text-primary me-3"></i>
                                <div>
                                    <strong>{{ __('contact.phone_label') }}</strong><br>
                                    <a href="tel:{{ preg_replace('/\s+/', '', \App\Models\Setting::get('contact_phone', '+966 50 123 4567')) }}" class="phone-ltr" dir="ltr">
                                        <span>{{ \App\Models\Setting::get('contact_phone', '+966 50 123 4567') }}</span>
                                    </a>
                                </div>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-envelope text-primary me-3"></i>
                                <div>
                                    <strong>{{ __('contact.email_label') }}</strong><br>
                                    <a href="mailto:{{ \App\Models\Setting::get('contact_email', 'hello@yourevents.sa') }}">{{ \App\Models\Setting::get('contact_email', 'hello@yourevents.sa') }}</a>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-clock text-primary me-3"></i>
                                <div>
                                    <strong>{{ __('contact.working_hours') }}</strong><br>
                                    {{ \App\Models\Setting::get('working_hours', __('contact.default_working_hours')) }}
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="card shadow" data-aos="fade-left" data-aos-delay="100">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4 text-primary">
                            <i class="fas fa-share-alt me-2"></i>{{ __('contact.follow_us') }}
                        </h5>
                        <div class="social-icons">
                            @php
                                $facebookUrl = \App\Models\Setting::get('facebook_url');
                                $twitterUrl = \App\Models\Setting::get('twitter_url');
                                $instagramUrl = \App\Models\Setting::get('instagram_url');
                                $linkedinUrl = \App\Models\Setting::get('linkedin_url');
                                $snapchatUrl = \App\Models\Setting::get('snapchat_url');
                                $tiktokUrl = \App\Models\Setting::get('tiktok_url');
                            @endphp
                            
                            @if($facebookUrl)
                                <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon facebook" title="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif
                            
                            @if($instagramUrl)
                                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon instagram" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                            
                            @if($twitterUrl)
                                <a href="{{ $twitterUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon twitter" title="X (Twitter)" style="display: flex; align-items: center; justify-content: center;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                </a>
                            @endif
                            
                            @if($snapchatUrl)
                                <a href="{{ $snapchatUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon snapchat" title="Snapchat">
                                    <i class="fab fa-snapchat-ghost"></i>
                                </a>
                            @endif
                            
                            @if($linkedinUrl)
                                <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon linkedin" title="LinkedIn">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                            @endif
                            
                            @if($tiktokUrl)
                                <a href="{{ $tiktokUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon tiktok" title="TikTok">
                                    <i class="fab fa-tiktok"></i>
                                </a>
                            @endif
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', \App\Models\Setting::get('contact_phone', '+966501234567')) }}" target="_blank" class="btn btn-success w-100">
                                <i class="fab fa-whatsapp me-2"></i>{{ __('contact.whatsapp_contact') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-5 bg-secondary-custom">
    <div class="container">
        <h2 class="section-title mb-5" data-aos="fade-up" style="background: none; -webkit-text-fill-color: #000000; color: #000000;">{{ __('contact.map_title') }}</h2>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body p-0">
                        <iframe 
                            src="https://www.google.com/maps?q=24.804660797879635,46.62951321534364&hl={{ app()->getLocale() }}&z=15&output=embed" 
                            style="width: 100%; height: 400px; border: 0; border-radius: 0.375rem;" 
                            allowfullscreen 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title mb-4 text-center" data-aos="fade-up" style="background: none; -webkit-text-fill-color: #000000; color: #000000;">{{ __('contact.faq_title') }}</h2>
        <div class="text-center" data-aos="fade-up" data-aos-delay="100">
            <p class="text-muted mb-4">{{ __('contact.faq_subtitle') }}</p>
            <a href="{{ route('faq') }}" class="btn btn-primary btn-lg px-5 py-3" style="border-radius: 50px; font-size: 1.1rem; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); transition: all 0.3s ease;">
                <i class="fas fa-question-circle me-2"></i>{{ __('contact.view_all_faq') }}
            </a>
        </div>
        <div class="row justify-content-center mt-4" data-aos="fade-up" data-aos-delay="150">
            <div class="col-lg-10">
                <div class="accordion" id="contactFaqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#contactFaq1">
                                {{ __('contact.faq_q1') }}
                            </button>
                        </h2>
                        <div id="contactFaq1" class="accordion-collapse collapse show" data-bs-parent="#contactFaqAccordion">
                            <div class="accordion-body">
                                {{ __('contact.faq_a1') }}
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contactFaq2">
                                {{ __('contact.faq_q2') }}
                            </button>
                        </h2>
                        <div id="contactFaq2" class="accordion-collapse collapse" data-bs-parent="#contactFaqAccordion">
                            <div class="accordion-body">
                                {{ __('contact.faq_a2') }}
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contactFaq3">
                                {{ __('contact.faq_q3') }}
                            </button>
                        </h2>
                        <div id="contactFaq3" class="accordion-collapse collapse" data-bs-parent="#contactFaqAccordion">
                            <div class="accordion-body">
                                {{ __('contact.faq_a3') }}
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contactFaq4">
                                {{ __('contact.faq_q4') }}
                            </button>
                        </h2>
                        <div id="contactFaq4" class="accordion-collapse collapse" data-bs-parent="#contactFaqAccordion">
                            <div class="accordion-body">
                                {{ __('contact.faq_a4') }}
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contactFaq5">
                                {{ __('contact.faq_q5') }}
                            </button>
                        </h2>
                        <div id="contactFaq5" class="accordion-collapse collapse" data-bs-parent="#contactFaqAccordion">
                            <div class="accordion-body">
                                {{ __('contact.faq_a5') }}
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contactFaq6">
                                {{ __('contact.faq_q6') }}
                            </button>
                        </h2>
                        <div id="contactFaq6" class="accordion-collapse collapse" data-bs-parent="#contactFaqAccordion">
                            <div class="accordion-body">
                                {{ __('contact.faq_a6') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    .text-primary {
        color: #1f144a !important;
    }
    .social-icons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .social-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.3s ease;
        text-decoration: none;
        color: white;
    }
    
    .social-icon:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    
    .social-icon.facebook {
        background: #1877F2;
    }
    
    .social-icon.facebook:hover {
        background: #0d5dbf;
    }
    
    .social-icon.instagram {
        background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);
    }
    
    .social-icon.instagram:hover {
        background: linear-gradient(45deg, #d07823 0%,#c6582c 25%,#bc1733 50%,#ac1356 75%,#9c0878 100%);
    }
    
    .social-icon.twitter {
        background: #ffffff;
        color: #000000;
        border: 2px solid #000000;
    }
    
    .social-icon.twitter:hover {
        background: #f0f0f0;
        color: #000000;
    }
    
    .social-icon.snapchat {
        background: #FFFC00;
        color: #000000;
    }
    
    .social-icon.snapchat:hover {
        background: #e6e300;
    }
    
    .social-icon.linkedin {
        background: #0A66C2;
    }
    
    .social-icon.linkedin:hover {
        background: #084d92;
    }
    
    .social-icon.tiktok {
        background: #000000;
    }
    
    .social-icon.tiktok:hover {
        background: #ff0050;
    }
    
    /* Voice Controls Styles */
    .voice-controls .btn {
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .voice-controls .btn:hover {
        transform: scale(1.1);
    }
    
    .voice-controls .btn.recording {
        animation: pulse-red 1s infinite;
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: white !important;
    }
    
    .voice-controls .btn.listening {
        animation: pulse-blue 1s infinite;
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
        color: white !important;
    }
    
    @keyframes pulse-red {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        50% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    }
    
    @keyframes pulse-blue {
        0%, 100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7); }
        50% { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); }
    }
    
    .recording-indicator {
        width: 12px;
        height: 12px;
        background-color: #dc3545;
        border-radius: 50%;
        animation: blink 1s infinite;
    }
    
    .speech-indicator {
        width: 12px;
        height: 12px;
        background-color: #0d6efd;
        border-radius: 50%;
        animation: blink 1s infinite;
    }
    
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
    
    #message {
        padding-bottom: 55px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==========================================
    // Speech to Text (الكتابة بالصوت)
    // ==========================================
    const speechBtn = document.getElementById('speechToTextBtn');
    const messageTextarea = document.getElementById('message');
    const speechStatus = document.getElementById('speechStatus');
    
    let recognition = null;
    let isListening = false;
    
    // Check if browser supports Speech Recognition
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.continuous = true;
        recognition.interimResults = true;
        const speechLang = speechBtn ? (speechBtn.dataset.lang || 'ar-SA') : 'ar-SA';
        recognition.lang = speechLang;
        
        recognition.onstart = function() {
            isListening = true;
            speechBtn.classList.add('listening');
            speechStatus.style.display = 'block';
        };
        
        recognition.onend = function() {
            isListening = false;
            speechBtn.classList.remove('listening');
            speechStatus.style.display = 'none';
        };
        
        recognition.onresult = function(event) {
            let finalTranscript = '';
            let interimTranscript = '';
            
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const transcript = event.results[i][0].transcript;
                if (event.results[i].isFinal) {
                    finalTranscript += transcript;
                } else {
                    interimTranscript = transcript;
                }
            }
            
            if (finalTranscript) {
                // Append to existing text with space
                const currentText = messageTextarea.value;
                messageTextarea.value = currentText + (currentText ? ' ' : '') + finalTranscript;
            }
        };
        
        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
            isListening = false;
            speechBtn.classList.remove('listening');
            speechStatus.style.display = 'none';
            
            if (event.error === 'not-allowed') {
                alert(speechBtn.dataset.allowMicrophoneVoiceTyping || '');
            }
        };
        
        speechBtn.addEventListener('click', function() {
            if (isListening) {
                recognition.stop();
            } else {
                recognition.start();
            }
        });
    } else {
        speechBtn.disabled = true;
        speechBtn.title = speechBtn.dataset.voiceTypingNotSupported || '';
        speechBtn.style.opacity = '0.5';
    }
    
    // ==========================================
    // Voice Recording (تسجيل رسالة صوتية)
    // ==========================================
    const recordBtn = document.getElementById('voiceRecordBtn');
    const recordingStatus = document.getElementById('recordingStatus');
    const recordingTime = document.getElementById('recordingTime');
    const audioPreview = document.getElementById('audioPreview');
    const recordedAudio = document.getElementById('recordedAudio');
    const deleteRecordingBtn = document.getElementById('deleteRecording');
    const voiceMessageInput = document.getElementById('voiceMessageInput');
    
    let mediaRecorder = null;
    let audioChunks = [];
    let isRecording = false;
    let recordingTimer = null;
    let seconds = 0;
    
    function formatTime(totalSeconds) {
        const mins = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
        const secs = (totalSeconds % 60).toString().padStart(2, '0');
        return `${mins}:${secs}`;
    }
    
    function startRecording() {
        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(function(stream) {
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];
                
                mediaRecorder.ondataavailable = function(event) {
                    audioChunks.push(event.data);
                };
                
                mediaRecorder.onstop = function() {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    recordedAudio.src = audioUrl;
                    
                    // Convert to base64 for form submission
                    const reader = new FileReader();
                    reader.readAsDataURL(audioBlob);
                    reader.onloadend = function() {
                        voiceMessageInput.value = reader.result;
                    };
                    
                    audioPreview.style.display = 'block';
                    
                    // Stop all tracks
                    stream.getTracks().forEach(track => track.stop());
                };
                
                mediaRecorder.start();
                isRecording = true;
                recordBtn.classList.add('recording');
                recordingStatus.style.display = 'block';
                
                // Start timer
                seconds = 0;
                recordingTime.textContent = '00:00';
                recordingTimer = setInterval(function() {
                    seconds++;
                    recordingTime.textContent = formatTime(seconds);
                    
                    // Max recording time: 2 minutes
                    if (seconds >= 120) {
                        stopRecording();
                    }
                }, 1000);
            })
            .catch(function(err) {
                console.error('Error accessing microphone:', err);
                alert(recordBtn.dataset.allowMicrophoneVoiceRecording || '');
            });
    }
    
    function stopRecording() {
        if (mediaRecorder && isRecording) {
            mediaRecorder.stop();
            isRecording = false;
            recordBtn.classList.remove('recording');
            recordingStatus.style.display = 'none';
            clearInterval(recordingTimer);
        }
    }
    
    recordBtn.addEventListener('click', function() {
        if (isRecording) {
            stopRecording();
        } else {
            startRecording();
        }
    });
    
    // Delete recording
    deleteRecordingBtn.addEventListener('click', function() {
        recordedAudio.src = '';
        voiceMessageInput.value = '';
        audioPreview.style.display = 'none';
        audioChunks = [];
    });
});
</script>
@endpush

