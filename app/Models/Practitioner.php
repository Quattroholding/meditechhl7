<?php

namespace App\Models;

use App\Models\Scopes\PractitionerScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Practitioner extends BaseModel
{
    use HasFactory,Notifiable;

    protected $fillable = [
        'fhir_id', 'identifier', 'name', 'given_name', 'family_name', 'user_id',
        'gender', 'birth_date', 'address', 'phone', 'email', 'active', 'registry', 'licence_code', 'identifier_type',
        'prescription_authorization', 'prescription_authorization_date', 'prescription_authorization_ip', 'prescription_authorization_terms',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'active' => 'boolean',
        'prescription_authorization' => 'boolean',
        'prescription_authorization_date' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new PractitionerScope);
    }

    public function routeNotificationForMail($notification = null)
    {
        // Si estamos en testing, usar correo específico
        if (config('app.env') === 'testing' || config('mail.testing_mode', false)) {
            return config('mail.testing_practitioner_email', 'doctor.test@example.com');
        }

        return $this->email;
    }

    // Relaciones
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function encounters(): HasMany
    {
        return $this->hasMany(Encounter::class);
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(PractitionerQualification::class);
    }

    public function specialties()
    {
        return $this->belongsToMany(MedicalSpeciality::class, 'practitioner_qualifications');
    }

    public function medicationRequests(): HasMany
    {
        return $this->hasMany(MedicationRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function insuranceCompanies()
    {
        return $this->belongsToMany(InsuranceCompany::class, 'practitioner_insurance_company')
            ->withPivot('accepts', 'custom_coverage_percentage', 'custom_copay_amount', 'notes')
            ->withTimestamps();
    }

    public function acceptedInsurances()
    {
        return $this->belongsToMany(InsuranceCompany::class, 'practitioner_insurance_company')
            ->wherePivot('accepts', true)
            ->withPivot('accepts', 'custom_coverage_percentage', 'custom_copay_amount', 'notes')
            ->withTimestamps();
    }

    public function rejectedInsurances()
    {
        return $this->belongsToMany(InsuranceCompany::class, 'practitioner_insurance_company')
            ->wherePivot('accepts', false)
            ->withPivot('accepts', 'custom_coverage_percentage', 'custom_copay_amount', 'notes')
            ->withTimestamps();
    }

    public function surveyResponses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function avatar()
    {
        return $this->files()->whereType('avatar')->latest()->first();
    }

    public function signature()
    {
        return $this->files()->whereType('signature')->latest()->first();
    }

    public function seal()
    {
        return $this->files()->whereType('seal')->latest()->first();
    }

    public function getSignaturePath()
    {
        $signature = $this->signature();
        if ($signature && \Storage::disk('local')->exists($signature->path)) {
            return \Storage::disk('local')->path($signature->path);
        }

        return null;
    }

    public function getSealPath()
    {
        $seal = $this->seal();
        if ($seal && \Storage::disk('local')->exists($seal->path)) {
            return \Storage::disk('local')->path($seal->path);
        }

        return null;
    }

    public function getProfileNameAttribute()
    {

        $path = url('assets/img/profiles/avatar-02.jpg');
        if ($this->avatar()) {
            $path = url('storage/'.$this->avatar()->path);
        }

        return '<div class="profile-image m-0">
                  <a href="'.url('practitioners/'.$this->id.'/profile').'"  class= "text-base">
                                        <img width="28" height="28" src="'.$path.'" class="rounded-circle m-r-5" alt="" style="display:inline-block;">
                                        '.$this->name.'
                                    </a>
                    </div>';
    }

    public function getBirthDateAttribute($attr)
    {
        return Carbon::parse($attr)->format('d/m/Y');
    }

    // Scope para obtener solo practicantes activos
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeUserActive($query)
    {
        return $query->whereHas('user', function ($query) {
            $query->where('users.active', true);
        });
    }

    public function scopeActiveAgent($query)
    {
        return $query->whereHas('user', function ($query) {
            $query->ActiveAgent();
        });
    }

    /**
     * Scope para filtrar solo practitioners cuyo cliente tiene suscripción activa o en periodo de gracia
     * Incluye suscripciones: active, trial, y past_due dentro del periodo de gracia
     */
    public function scopeWithActiveSubscription($query)
    {
        return $query->whereHas('user.clients.subscriptions', function ($q) {
            $q->activeOrInGracePeriod();
        });
    }

    /**
     * Get completed survey responses for this practitioner
     */
    public function completedSurveyResponses()
    {
        return $this->surveyResponses()
            ->where('status', 'completed')
            ->whereNotNull('submitted_at');
    }

    /**
     * Calculate the average rating for practitioner-specific questions
     * Questions 5, 6, 7, and 11 from the Patient Satisfaction Survey
     */
    public function calculatePractitionerRating(): float
    {
        $completedResponses = $this->completedSurveyResponses()->get();

        if ($completedResponses->isEmpty()) {
            return 0.0;
        }

        // Get the IDs of the practitioner-related questions
        // These are questions with order 5, 6, 7, and 11
        $practitionerQuestionOrders = [5, 6, 7, 11];

        $totalRating = 0;
        $totalQuestions = 0;

        foreach ($completedResponses as $surveyResponse) {
            // Get all question responses for this survey response
            $questionResponses = $surveyResponse->questionResponses()
                ->whereHas('question', function ($query) use ($practitionerQuestionOrders) {
                    $query->whereIn('order', $practitionerQuestionOrders)
                        ->where('question_type', 'rating');
                })
                ->with('question')
                ->get();

            foreach ($questionResponses as $questionResponse) {
                // The answer_text contains the numeric rating (1-5)
                $rating = (float) $questionResponse->answer_text;
                if ($rating > 0) {
                    $totalRating += $rating;
                    $totalQuestions++;
                }
            }
        }

        if ($totalQuestions === 0) {
            return 0.0;
        }

        return round($totalRating / $totalQuestions, 2);
    }

    /**
     * Get the total number of completed patient surveys
     */
    public function getTotalPatientSurveysAttribute(): int
    {
        return $this->completedSurveyResponses()->count();
    }

    /**
     * Get the practitioner rating (cached attribute)
     */
    public function getPractitionerRatingAttribute(): float
    {
        return $this->calculatePractitionerRating();
    }

    /**
     * Scope to order practitioners by their rating (highest first)
     */
    public function scopeOrderByRating($query, $direction = 'desc')
    {
        return $query->withCount(['surveyResponses as practitioner_rating' => function ($query) {
            $practitionerQuestionOrders = [5, 6, 7, 11];

            $query->select(\DB::raw('AVG(CAST(survey_question_responses.answer_text AS DECIMAL(3,2)))'))
                ->join('survey_question_responses', 'survey_responses.id', '=', 'survey_question_responses.survey_response_id')
                ->join('survey_questions', 'survey_question_responses.survey_question_id', '=', 'survey_questions.id')
                ->where('survey_responses.status', 'completed')
                ->whereNotNull('survey_responses.submitted_at')
                ->whereIn('survey_questions.order', $practitionerQuestionOrders)
                ->where('survey_questions.question_type', 'rating');
        }])->orderBy('practitioner_rating', $direction);
    }

    /**
     * Scope to filter practitioners with minimum rating
     */
    public function scopeWithMinimumRating($query, float $minRating)
    {
        return $query->whereHas('surveyResponses', function ($query) use ($minRating) {
            $practitionerQuestionOrders = [5, 6, 7, 11];

            $query->select('practitioner_id')
                ->join('survey_question_responses', 'survey_responses.id', '=', 'survey_question_responses.survey_response_id')
                ->join('survey_questions', 'survey_question_responses.survey_question_id', '=', 'survey_questions.id')
                ->where('survey_responses.status', 'completed')
                ->whereNotNull('survey_responses.submitted_at')
                ->whereIn('survey_questions.order', $practitionerQuestionOrders)
                ->where('survey_questions.question_type', 'rating')
                ->groupBy('practitioner_id')
                ->havingRaw('AVG(CAST(survey_question_responses.answer_text AS DECIMAL(3,2))) >= ?', [$minRating]);
        });
    }

    /**
     * Get detailed rating breakdown for practitioner
     * Returns array with average rating for each practitioner-related question
     */
    public function getRatingBreakdown(): array
    {
        $completedResponses = $this->completedSurveyResponses()->get();

        if ($completedResponses->isEmpty()) {
            return [
                'overall_rating' => 0.0,
                'medical_attention' => 0.0,  // Question 5
                'communication' => 0.0,       // Question 6
                'empathy' => 0.0,            // Question 7
                'recommendation' => 0.0,      // Question 11
                'total_surveys' => 0,
            ];
        }

        $ratings = [
            5 => [],  // Medical attention
            6 => [],  // Communication
            7 => [],  // Empathy
            11 => [], // Recommendation
        ];

        foreach ($completedResponses as $surveyResponse) {
            $questionResponses = $surveyResponse->questionResponses()
                ->whereHas('question', function ($query) {
                    $query->whereIn('order', [5, 6, 7, 11])
                        ->where('question_type', 'rating');
                })
                ->with('question')
                ->get();

            foreach ($questionResponses as $questionResponse) {
                $order = $questionResponse->question->order;
                $rating = (float) $questionResponse->answer_text;
                if ($rating > 0 && isset($ratings[$order])) {
                    $ratings[$order][] = $rating;
                }
            }
        }

        return [
            'overall_rating' => $this->calculatePractitionerRating(),
            'medical_attention' => count($ratings[5]) > 0 ? round(array_sum($ratings[5]) / count($ratings[5]), 2) : 0.0,
            'communication' => count($ratings[6]) > 0 ? round(array_sum($ratings[6]) / count($ratings[6]), 2) : 0.0,
            'empathy' => count($ratings[7]) > 0 ? round(array_sum($ratings[7]) / count($ratings[7]), 2) : 0.0,
            'recommendation' => count($ratings[11]) > 0 ? round(array_sum($ratings[11]) / count($ratings[11]), 2) : 0.0,
            'total_surveys' => $completedResponses->count(),
        ];
    }
}
