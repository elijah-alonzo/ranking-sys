<x-filament-panels::page>
    <div class="ef-evaluation-card">
        <div class="ef-evaluation-header">
            <h1 class="ef-evaluation-title">{{ $this->getTitle() }}</h1>
            <p class="ef-evaluation-subheading">{{ $this->getSubheading() }}</p>
        </div>
        <div class="ef-evaluation-content">
            @if($isLocked)
                <div class="ef-locked-message">
                    <strong>Evaluation Completed:</strong> This evaluation has already been submitted and cannot be edited.
                </div>
            @endif
            <form method="POST" id="evaluation-form">
                @csrf
                @php
                    $rubric = \App\Models\EvaluationForm::getRubricStructure();
                    $questionsByDomainStrand = [];
                    foreach ($questions as $qKey => $q) {
                        $questionsByDomainStrand[$q['domain_key']][$q['strand_key']][$qKey] = $q;
                    }
                @endphp
                @foreach($rubric as $domainKey => $domain)
                    @if(isset($questionsByDomainStrand[$domainKey]))
                    <div class="ef-domain-section">
                        <div class="ef-domain-header">
                            <div class="ef-domain-title">Domain {{ substr($domainKey, -1) }}: {{ $domain['title'] }}</div>
                            @if($domain['description'])
                                <div class="ef-domain-description">{{ $domain['description'] }}</div>
                            @endif
                        </div>
                        @foreach($domain['strands'] as $strandKey => $strand)
                            @if(isset($questionsByDomainStrand[$domainKey][$strandKey]))
                            <div class="ef-strand">
                                <div class="ef-strand-title">Strand {{ substr($strandKey, -1) }}. {{ $strand['title'] }}</div>
                                <div class="ef-questions-container">
                                    @foreach($questionsByDomainStrand[$domainKey][$strandKey] as $questionKey => $question)
                                        <div class="ef-question-item">
                                            <div class="ef-question-text">{{ $question['text'] }}</div>
                                            <div class="ef-rating-scale">
                                                @foreach($question['criteria'] as $value => $criteria)
                                                    <label class="ef-rating-option {{ isset($data[$questionKey]) && $data[$questionKey] == $value ? 'selected' : '' }}">
                                                        <input type="radio"
                                                               name="answers[{{ $questionKey }}]"
                                                               value="{{ $value }}"
                                                               {{ isset($data[$questionKey]) && $data[$questionKey] == $value ? 'checked' : '' }}
                                                               {{ $isLocked ? 'disabled' : 'required' }}>
                                                        <span class="ef-rating-label">
                                                            <span class="ef-rating-value">{{ $value }}</span>
                                                            <span class="ef-rating-criteria">{{ $criteria }}</span>
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @endif
                @endforeach
            </form>
        </div>
        <div class="ef-form-actions">
            <a href="{{ \App\Filament\Resources\MyEvaluations\MyEvaluationResource::getUrl('view', ['record' => $evaluation]) }}" class="ef-btn ef-btn-secondary">
                ← Back to Evaluation
            </a>
            @unless($isLocked)
                <button type="submit" form="evaluation-form" class="ef-btn ef-btn-primary" onclick="return confirm('Are you sure you want to submit this evaluation? You will not be able to edit it afterwards.');">
                    Submit Evaluation →
                </button>
            @endunless
        </div>
    </div>

</x-filament-panels::page>
{{-- Self Evaluation Form - Shows Domain 2 strands 1-2 + Domain 3 all strands --}}


@php
    $rubric = \App\Models\EvaluationForm::getRubricStructure();
    $questionsByDomainStrand = [];
    foreach ($questions as $qKey => $q) {
        $questionsByDomainStrand[$q['domain_key']][$q['strand_key']][$qKey] = $q;
    }
@endphp

@foreach($rubric as $domainKey => $domain)
    @if(isset($questionsByDomainStrand[$domainKey]))
    <div class="ef-domain-section">
        <div class="ef-domain-header">
            <div class="ef-domain-title">Domain {{ substr($domainKey, -1) }}: {{ $domain['title'] }}</div>
            @if($domain['description'])
                <div class="ef-domain-description">{{ $domain['description'] }}</div>
            @endif
        </div>
        @foreach($domain['strands'] as $strandKey => $strand)
            @if(isset($questionsByDomainStrand[$domainKey][$strandKey]))
            <div class="ef-strand">
                <div class="ef-strand-title">Strand {{ substr($strandKey, -1) }}. {{ $strand['title'] }}</div>
                <div class="ef-questions-container">
                    @foreach($questionsByDomainStrand[$domainKey][$strandKey] as $questionKey => $question)
                        <div class="ef-question-item">
                            <div class="ef-question-text">{{ $question['text'] }}</div>
                            <div class="ef-rating-scale">
                                @foreach($question['criteria'] as $value => $criteria)
                                    <label class="ef-rating-option {{ isset($data[$questionKey]) && $data[$questionKey] == $value ? 'selected' : '' }}">
                                        <input type="radio"
                                               name="answers[{{ $questionKey }}]"
                                               value="{{ $value }}"
                                               {{ isset($data[$questionKey]) && $data[$questionKey] == $value ? 'checked' : '' }}
                                               {{ $isLocked ? 'disabled' : 'required' }}>
                                        <span class="ef-rating-label">
                                            <span class="ef-rating-value">{{ $value }}</span>
                                            <span class="ef-rating-criteria">{{ $criteria }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    </div>
    @endif
@endforeach