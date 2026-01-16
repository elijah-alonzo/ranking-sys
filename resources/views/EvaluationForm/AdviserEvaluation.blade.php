<x-filament-panels::page>
    <style>
        @media (max-width: 600px) {
            .ef-rating-scale {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }
    /* ...existing code... */
        .ef-evaluation-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            border: 1px solid #f1f1f1;
            margin: 32px auto;
            max-width: 900px;
            width: 100%;
            padding: 32px 32px 24px 32px;
            box-sizing: border-box;
        }
                /* Make the table fill the card width */
                .ef-evaluation-card table {
                    width: 100%;
                    margin: 0;
                    border-collapse: collapse;
                    table-layout: fixed;
                }
                .ef-evaluation-card th,
                .ef-evaluation-card td {
                    padding: 12px 8px;
                    word-break: break-word;
                }
                .ef-evaluation-card thead th {
                    padding-top: 16px;
                    padding-bottom: 16px;
                }
                .ef-evaluation-card tbody td {
                    padding-top: 12px;
                    padding-bottom: 12px;
                }
        .ef-evaluation-header {
            margin-bottom: 24px;
        }
        .ef-evaluation-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .ef-evaluation-subheading {
            color: #555;
            font-size: 1.1rem;
            margin-bottom: 0;
        }
        .ef-domain-section {
            margin-bottom: 32px;
            border-radius: 10px;
            border: 1px solid #f1f1f1;
            background: #fff;
        }
        .ef-domain-header {
            border-bottom: 1px solid #f1f1f1;
            padding: 20px 24px 10px 24px;
        }
        .ef-domain-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #222;
        }
        .ef-domain-description {
            font-size: 1rem;
            color: #444;
            margin-top: 4px;
        }
        .ef-strand {
            padding: 16px 24px 0 24px;
        }
        .ef-strand-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 8px;
        }
        .ef-questions-container {
            margin-bottom: 12px;
        }
        .ef-question-item {
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f1f1;
        }
        .ef-question-text {
            font-size: 1.08rem;
            color: #222;
            margin-bottom: 10px;
            font-weight: 500;
        }
        .ef-rating-scale {
            display: flex;
            gap: 32px;
            margin-left: 0;
            margin-top: 4px;
        }
        .ef-rating-option {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 1rem;
        }
        .ef-rating-option input[type="radio"] {
            accent-color: #22C55E;
            width: 18px;
            height: 18px;
            margin-right: 6px;
        }
        .ef-rating-label {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .ef-rating-value {
            font-weight: 600;
            color: #22C55E;
            margin-right: 2px;
        }
        .ef-rating-criteria {
            color: #444;
            font-size: 0.97rem;
        }
        .ef-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
        }
        .ef-btn {
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .ef-btn-primary {
            background: #22C55E;
            color: #fff;
        }
        .ef-btn-primary:hover {
            background: #16a34a;
        }
        .ef-btn-secondary {
            background: #f1f1f1;
            color: #222;
        }
        .ef-btn-secondary:hover {
            background: #e1e1e1;
        }
        .ef-locked-message {
            background: #f8fafc;
            color: #22C55E;
            border-left: 4px solid #22C55E;
            padding: 12px 18px;
            margin-bottom: 18px;
            border-radius: 6px;
            font-weight: 500;
        }
    </style>
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
            @unless($isLocked)
                <button type="submit" form="evaluation-form" class="ef-btn ef-btn-primary" onclick="return confirm('Are you sure you want to submit this evaluation? You will not be able to edit it afterwards.');">
                    Submit Evaluation
                </button>
            @endunless
        </div>
    </div>

</x-filament-panels::page>