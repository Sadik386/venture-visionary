<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class CaseDocument extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'case_id',
        'document_type_id',
        'title',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'version',
        'uploaded_by_type',
        'uploaded_by_id',
        'status',
        'rejection_reason',
        'expiry_date',
        'is_expiry_alert_sent',
        'verified_by_id',
        'verified_at',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'verified_at' => 'datetime',
        'is_expiry_alert_sent' => 'boolean',
        'version' => 'integer',
        'file_size' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (CaseDocument $doc) {
            if (empty($doc->document_type_id)) {
                $defaultType = DocumentType::firstOrCreate(
                    ['code' => 'GENERIC'],
                    ['name' => 'General Document', 'is_active' => true]
                );
                $doc->document_type_id = $defaultType->id;
            }

            if (empty($doc->mime_type)) {
                $doc->mime_type = 'application/pdf';
            }

            if (empty($doc->file_size)) {
                $doc->file_size = 1024;
            }
        });
    }

    public function visaCase()
    {
        return $this->belongsTo(VisaCase::class, 'case_id');
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    // Section 12 & 22 Security: Temporary Signed URL (MinIO/S3 private bucket)
    public function getTemporaryDownloadUrl(int $minutes = 30): string
    {
        return Storage::disk('minio')->temporaryUrl($this->file_path, now()->addMinutes($minutes));
    }
}
