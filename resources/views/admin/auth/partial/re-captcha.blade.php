@if (isRecaptchaActive())
    <div class="mb-0 recaptcha-container mt-0">
        {!! NoCaptcha::renderJs() !!}
        {!! NoCaptcha::display() !!}
    </div>
@endif
