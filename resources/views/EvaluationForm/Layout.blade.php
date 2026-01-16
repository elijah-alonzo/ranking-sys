<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $this->getTitle() }}</title>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        
        .evaluation-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .evaluation-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .evaluation-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .evaluation-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
        }
        
        .evaluation-header p {
            font-size: 1rem;
            opacity: 0.9;
            margin: 0;
        }
        
        .evaluation-content {
            padding: 2rem;
        }
        
        .domain-section {
            margin-bottom: 2.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .domain-header {
            background: #f8fafc;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .domain-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 0.5rem 0;
        }
        
        .domain-description {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
        }
        
        .questions-container {
            padding: 1.5rem;
        }
        
        .question-item {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .question-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .question-text {
            font-size: 1rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        
        .rating-scale {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .rating-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 140px;
        }
        
        .rating-option:hover {
            border-color: #d1d5db;
            background: #f9fafb;
        }
        
        .rating-option input[type="radio"] {
            margin: 0;
        }
        
        .rating-option.selected {
            border-color: #4f46e5;
            background: #eef2ff;
            color: #4f46e5;
        }
        
        .rating-label {
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .form-actions {
            background: #f8fafc;
            padding: 1.5rem 2rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: #4f46e5;
            color: white;
        }
        
        .btn-primary:hover {
            background: #4338ca;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #374151;
        }
        
        .locked-message {
            background: #fef3cd;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            color: #92400e;
            font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
            .evaluation-container {
                padding: 1rem;
            }
            
            .evaluation-header {
                padding: 1.5rem 1rem;
            }
            
            .evaluation-content {
                padding: 1.5rem 1rem;
            }
            
            .rating-scale {
                flex-direction: column;
            }
            
            .rating-option {
                min-width: auto;
                justify-content: center;
            }
            
            .form-actions {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="evaluation-container">
        <div class="evaluation-card">
            <!-- Header -->
            <div class="evaluation-header">
                <h1>{{ $this->getTitle() }}</h1>
                <p>{{ $this->getSubheading() }}</p>
            </div>
            
            <!-- Content -->
            <div class="evaluation-content">
                @if($isLocked)
                    <div class="locked-message">
                        <strong>Evaluation Completed:</strong> This evaluation has already been submitted and cannot be edited.
                    </div>
                @endif
                
                <form method="POST" id="evaluation-form">
                    @csrf
                    
                    @if($evaluationType === 'adviser')
                        @include('EvaluationForm.AdviserEvaluation')
                    @elseif($evaluationType === 'peer')
                        @include('EvaluationForm.PeerEvaluation')
                    @elseif($evaluationType === 'self')
                        @include('EvaluationForm.SelfEvaluation')
                    @endif
                    
                </form>
            </div>
            
            <!-- Actions -->
            <div class="form-actions">
                <a href="{{ \App\Filament\Resources\MyEvaluations\MyEvaluationResource::getUrl('view', ['record' => $evaluation]) }}" class="btn btn-secondary">
                    ← Back to Evaluation
                </a>
                
                @unless($isLocked)
                    <button type="submit" form="evaluation-form" class="btn btn-primary" onclick="return confirmSubmission()">
                        Submit Evaluation →
                    </button>
                @endunless
            </div>
        </div>
    </div>
    
    <script>
        // Handle radio button selection styling
        document.addEventListener('DOMContentLoaded', function() {
            const radioButtons = document.querySelectorAll('input[type="radio"]');
            
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Remove selected class from all options in this group
                    const groupName = this.name;
                    const groupOptions = document.querySelectorAll(`input[name="${groupName}"]`);
                    
                    groupOptions.forEach(option => {
                        const parent = option.closest('.rating-option');
                        if (parent) {
                            parent.classList.remove('selected');
                        }
                    });
                    
                    // Add selected class to current option
                    const parent = this.closest('.rating-option');
                    if (parent) {
                        parent.classList.add('selected');
                    }
                });
                
                // Initialize selected state for pre-filled values
                if (radio.checked) {
                    const parent = radio.closest('.rating-option');
                    if (parent) {
                        parent.classList.add('selected');
                    }
                }
            });
        });
        
        // Confirmation dialog
        function confirmSubmission() {
            if ({{ $isLocked ? 'true' : 'false' }}) {
                alert('This evaluation has already been submitted.');
                return false;
            }
            
            return confirm('Are you sure you want to submit this evaluation? You will not be able to edit it afterwards.');
        }
    </script>
</body>
</html>