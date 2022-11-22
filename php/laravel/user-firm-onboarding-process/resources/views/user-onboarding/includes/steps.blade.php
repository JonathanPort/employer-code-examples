<div class="process-steps">

    @for ($step = 1; $step <= $stepCount; $step++)

        <div class="
            process-steps__step
            {{ $currentStep === $step ? 'active' : '' }}
            {{ $step < $currentStep ? 'complete' : '' }}
        ">
            {{ $step }}
        </div>

        @if ($step != $stepCount)
            <div class="process-steps__step-bar"></div>
        @endif

    @endfor

</div>
