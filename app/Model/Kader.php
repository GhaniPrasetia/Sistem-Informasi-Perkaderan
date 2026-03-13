<?php

namespace App\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

// Model
use App\Model\Ref_Riwayat_Sekolah;
use App\Model\Ref_Pendidikan_Terakhir;
use App\Model\Ref_Pengalaman_Organisasi_IMM;
use App\Model\Ref_Pengalaman_Organisasi_Lainnya;
use App\Model\Ref_Perkaderan;
use App\Model\Ref_Pimpinan;
use App\Model\Kampus;
use App\Model\Fakultas;
use App\Model\Prodi;
use App\Model\Komisariat;

class Kader extends Model
{
    use SoftDeletes;
    protected $table = 'kader';

    protected $fillable = [
        'nama_lengkap',
        'no_hp',
        'email',
        'sosial_media',
        'jenis_kelamin',
        'agama',
        'kebangsaan',
        'status_menikah',
        'tanggal_lahir',
        'tempat_lahir',
        'domisili',
        'foto',
        'pc',
        'komisariat',
        'universitas',
        'fakultas',
        'prodi','user_id'
    ];

    public function ref_perkaderan()
    {
        return $this->hasMany(Ref_Perkaderan::class, 'kader_id', 'id');
    }

    public function ref_universitas()
    {
        return $this->hasMany(Kampus::class, 'id', 'universitas');
    }

    public function ref_fakultas()
    {
        return $this->hasMany(Fakultas::class, 'id', 'fakultas');
    }

    public function ref_prodi()
    {
        return $this->hasMany(Prodi::class, 'id', 'prodi');
    }

    public function ref_pimpinan()
    {
        return $this->hasMany(Ref_Pimpinan::class, 'kader_id', 'id');
    }

    public function ref_komisariat()
    {
        return $this->hasMany(Komisariat::class, 'id', 'komisariat');
    }
}
