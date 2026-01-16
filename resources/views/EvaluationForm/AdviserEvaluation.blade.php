{{-- Adviser Evaluation Form - Shows all questions --}}

@php
    $groupedQuestions = [];
    foreach ($questions as $key => $question) {
        $domain = $question['domain'];
        $groupedQuestions[$domain][$key] = $question;
    }
@endphp

@foreach($groupedQuestions as $domain => $domainQuestions)
    <div class="domain-section">
        <div class="domain-header">
            <h3 class="domain-title">{{ $domain }}</h3>
            <p class="domain-description">
                Please rate each statement based on your supervision and guidance experience with this student.
            </p>
        </div>
        
        <div class="questions-container">
            @foreach($domainQuestions as $questionKey => $question)
                <div class="question-item">
                    <div class="question-text">
                        {{ $question['text'] }}
                    </div>
                    
                    <div class="rating-scale">
                        @foreach([0 => 'Never/Poor', 1 => 'Sometimes/Fair', 2 => 'Often/Good', 3 => 'Always/Excellent'] as $value => $label)
                            <label class="rating-option {{ isset($data[$questionKey]) && $data[$questionKey] == $value ? 'selected' : '' }}">
                                <input type="radio" 
                                       name="answers[{{ $questionKey }}]" 
                                       value="{{ $value }}"
                                       {{ isset($data[$questionKey]) && $data[$questionKey] == $value ? 'checked' : '' }}
                                       {{ $isLocked ? 'disabled' : 'required' }}>
                                <span class="rating-label">{{ $value }} - {{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endforeach