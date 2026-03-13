<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Model
use App\Model\Kader;

class DashboardController extends Controller
{
    public function index()
    {

        $perguruanTinggi = Kader::select('universitas')->distinct()->get();
        $jumlahPerguruanTinggi = count($perguruanTinggi);

        $fakultas = Kader::select('fakultas')->distinct()->get();
        $jumlahFakultas = count($fakultas);

        $prodi = Kader::select('prodi')->distinct()->get();
        $jumlahProdi = count($prodi);

        $kader = Kader::count();

        if(session('role_id')===3){
            $tahunPerkaderan = Kader::with(['ref_perkaderan' => function ($query) {
                            $query->select('kader_id', 'tahun_perkaderan');
                        }])
                        ->where('deleted_at', null)
                        ->where('komisariat', Auth::user()->komisariat_id)
                        ->get()
                        ->pluck('ref_perkaderan')
                        ->flatten()
                        ->groupBy('tahun_perkaderan')
                        ->map(function ($items, $tahun_perkaderan) {
                            return strtoupper($tahun_perkaderan);
                        })
                        ->values();

            $chartKampus = Kader::with('ref_universitas')
                        ->select('universitas', DB::raw('count(*) as total'))
                        ->where('komisariat', Auth::user()->komisariat_id)
                        ->groupBy('universitas')
                        ->with('ref_universitas')
                        ->get()
                        ->transform(function ($item) {
                            $item->universitas = $item->ref_universitas[0]->kampus;
                            $item->total = (int) $item->total;
                            return $item;
                        });

            $chartFakultas = Kader::select('fakultas', DB::raw('count(*) as total'))
                    ->where('komisariat', Auth::user()->komisariat_id)
                    ->groupBy('fakultas')
                    ->with('ref_fakultas')
                    ->get()
                    ->each(function ($items) {
                        $items->fakultas = $items->ref_fakultas[0]->fakultas;
                        $items->total = (int) $items->total;
                    });

            $chartProdi = Kader::select('prodi', DB::raw('count(*) as total'))
                    ->where('komisariat', Auth::user()->komisariat_id)
                    ->groupBy('prodi')
                    ->get()
                    ->each(function ($items) {
                        $items->prodi =  $items->ref_prodi[0]->prodi;
                        $items->total = (int) $items->total;
                    });

            $chartKomisariat = Kader::select('komisariat', DB::raw('count(*) as total'))
                    ->where('komisariat', Auth::user()->komisariat_id)
                    ->groupBy('komisariat')
                    ->get()->each(function ($items) {
                        $items->komisariat = $items->ref_komisariat[0]->komisariat;
                        $items->total = (int) $items->total;
                    });

            $chartImmawan = Kader::select('jenis_kelamin', DB::raw('count(*) as total'))
                    ->where('komisariat', Auth::user()->komisariat_id)
                    ->groupBy('jenis_kelamin')
                    ->get()
                    ->map(function ($item) {
                        $item->jenis_kelamin = strtoupper($item->jenis_kelamin);
                        if ($item->jenis_kelamin === 'PRIA') {
                            $item->label = 'IMMAWAN';
                        } elseif ($item->jenis_kelamin === 'WANITA') {
                            $item->label = 'IMMAWATI';
                        }
                        $item->total = (int) $item->total;
                        unset($item->jenis_kelamin);
                        return $item;
                    });


            $chartPerkaderanUtama = Kader::select('id')
                    ->with(['ref_perkaderan' => function ($query) {
                        $query->select('kader_id', 'kegiatan_perkaderan');
                    }])
                    ->where('komisariat', Auth::user()->komisariat_id)
                    ->get()
                    ->pluck('ref_perkaderan')
                    ->flatten()
                    ->groupBy('kegiatan_perkaderan')
                    ->map(function ($items, $kegiatan_perkaderan) {
                        return [
                            'kegiatan_perkaderan' => strtoupper($kegiatan_perkaderan),
                            'total' => $items->count(),
                        ];
                    })
                    ->values();

            $chartPerkaderanKhusus = Kader::select('id')
                    ->with(['ref_pimpinan' => function ($query) {
                        $query->select('kader_id', 'kegiatan_pimpinan');
                    }])
                    ->where('komisariat', Auth::user()->komisariat_id)
                    ->get()
                    ->pluck('ref_pimpinan')
                    ->flatten()
                    ->groupBy('kegiatan_pimpinan')
                    ->map(function ($items, $kegiatan_pimpinan) {
                        return [
                            'kegiatan_pimpinan' => strtoupper($kegiatan_pimpinan),
                            'total' => $items->count(),
                        ];
                    })
                    ->values();
        }else{

            $tahunPerkaderan = Kader::with(['ref_perkaderan' => function ($query) {
                            $query->select('kader_id', 'tahun_perkaderan');
                        }])
                        ->where('deleted_at', null)
                        // ->where('komisariat', Auth::user()->komisariat_id)
                        ->get()
                        ->pluck('ref_perkaderan')
                        ->flatten()
                        ->groupBy('tahun_perkaderan')
                        ->map(function ($items, $tahun_perkaderan) {
                            return strtoupper($tahun_perkaderan);
                        })
                        ->values();

            $chartKampus = Kader::select('universitas', DB::raw('count(*) as total'))
                            ->groupBy('universitas')
                            ->with('ref_universitas')
                            ->get()
                            ->transform(function ($item) {
                                $item->universitas = $item->ref_universitas[0]->kampus;
                                $item->total = (int) $item->total;
                                return $item;
                            });


            $chartFakultas = Kader::select('fakultas', DB::raw('count(*) as total'))
                            ->groupBy('fakultas')
                            ->with('ref_fakultas')
                            ->get()
                            ->each(function ($items) {
                                $items->fakultas = $items->ref_fakultas[0]->fakultas;
                                $items->total = (int) $items->total;
                            });

            $chartProdi = Kader::select('prodi', DB::raw('count(*) as total'))
                            ->groupBy('prodi')
                            ->with('ref_prodi')
                            ->get()
                            ->each(function ($items) {
                                $items->prodi =  $items->ref_prodi[0]->prodi;
                                $items->total = (int) $items->total;
                            });

            $chartKomisariat = Kader::select('komisariat', DB::raw('count(*) as total'))
                                ->groupBy('komisariat')
                    ->get()->each(function ($items) {
                        $items->komisariat = strtoupper($items->komisariat);
                        $items->total = (int) $items->total;
                    });

            $chartImmawan = Kader::select('jenis_kelamin', DB::raw('count(*) as total'))
                                ->groupBy('jenis_kelamin')
                    ->get()
                    ->map(function ($item) {
                        $item->jenis_kelamin = strtoupper($item->jenis_kelamin);
                        if ($item->jenis_kelamin === 'PRIA') {
                            $item->label = 'IMMAWAN';
                        } elseif ($item->jenis_kelamin === 'WANITA') {
                            $item->label = 'IMMAWATI';
                        }
                        $item->total = (int) $item->total;
                        unset($item->jenis_kelamin);
                        return $item;
                    });


            $chartPerkaderanUtama = Kader::select('id')
                    ->with(['ref_perkaderan' => function ($query) {
                        $query->select('kader_id', 'kegiatan_perkaderan');
                    }])
                                        ->get()
                    ->pluck('ref_perkaderan')
                    ->flatten()
                    ->groupBy('kegiatan_perkaderan')
                    ->map(function ($items, $kegiatan_perkaderan) {
                        return [
                            'kegiatan_perkaderan' => strtoupper($kegiatan_perkaderan),
                            'total' => $items->count(),
                        ];
                    })
                    ->values();

            $chartPerkaderanKhusus = Kader::select('id')
                    ->with(['ref_pimpinan' => function ($query) {
                        $query->select('kader_id', 'kegiatan_pimpinan');
                    }])
                                        ->get()
                    ->pluck('ref_pimpinan')
                    ->flatten()
                    ->groupBy('kegiatan_pimpinan')
                    ->map(function ($items, $kegiatan_pimpinan) {
                        return [
                            'kegiatan_pimpinan' => strtoupper($kegiatan_pimpinan),
                            'total' => $items->count(),
                        ];
                    })
                    ->values();
        }

        $user = Auth::user();
        $roleId = session('role_id');
            switch ($roleId) {
                case 2:
                    $title = 'Beranda - PC IMM Kota Surakarta';
                    break;

                case 3:
                    $title = 'Beranda - ' . optional($user->ref_komisariat)->komisariat;
                    break;

                default:
                    $title = 'Beranda';
            }

        return view('contents.dashboard.home', [
            'title' => $title,
            'perguruanTinggi' => $jumlahPerguruanTinggi,
            'fakultas' => $jumlahFakultas,
            'prodi' => $jumlahProdi,
            'kader' => $kader,
            'chartKampus' => $chartKampus,
            'chartFakultas' => $chartFakultas,
            'chartProdi' => $chartProdi,
            'chartKomisariat' => $chartKomisariat,
            'chartImmawan' => $chartImmawan,
            'chartPerkaderanUtama' => $chartPerkaderanUtama,
            'chartPerkaderanKhusus' => $chartPerkaderanKhusus,
            'tahunPerkaderan' => $tahunPerkaderan,
            'tahun' => "",
        ]);
    }

    public function filter_tahun(Request $request)
    {
        $perguruanTinggi = Kader::with('ref_perkaderan')
                        ->whereHas('ref_perkaderan', function($query) use ($request) {
                            $query->where('tahun_perkaderan', $request->tahun);
                        })
                        ->select('universitas')
                        ->count();

        $fakultas = Kader::with('ref_perkaderan')
                    ->whereHas('ref_perkaderan', function($query) use ($request) {
                        $query->where('tahun_perkaderan', $request->tahun);
                    })->select('fakultas')->count();
        $prodi = Kader::with('ref_perkaderan')
                ->whereHas('ref_perkaderan', function($query) use ($request) {
                    $query->where('tahun_perkaderan', $request->tahun);
                })->select('prodi')->count();
        $kader = Kader::with('ref_perkaderan')
                ->whereHas('ref_perkaderan', function($query) use ($request) {
                    $query->where('tahun_perkaderan', $request->tahun);
                })->count();

        if(session('role_id')===3){

            $tahunPerkaderan = Kader::select('id')
                    ->with(['ref_perkaderan' => function ($query) {
                        $query->select('kader_id', 'tahun_perkaderan');
                    }])
                    ->where('deleted_at', null)
                    ->get()
                    ->pluck('ref_perkaderan')
                    ->flatten()
                    ->groupBy('tahun_perkaderan')
                    ->map(function ($items, $tahun_perkaderan) {
                        return strtoupper($tahun_perkaderan);
                    })
                    ->values();

            $chartKampus = Kader::with('ref_perkaderan')
                        ->whereHas('ref_perkaderan', function($query) use ($request) {
                            $query->where('tahun_perkaderan', $request->tahun);
                        })->select('universitas', DB::raw('count(*) as total'))
                        // ->where('user_id', Auth::user()->id)
                        ->groupBy('universitas')
                        ->get()
                        ->transform(function ($item) {
                            $item->universitas = $item->ref_universitas[0]->kampus;
                            // $item->universitas = strtoupper($item->universitas);
                            $item->total = (int) $item->total;
                            return $item;
                        });

            $chartFakultas = Kader::with('ref_perkaderan')
            ->whereHas('ref_perkaderan', function($query) use ($request) {
                $query->where('tahun_perkaderan', $request->tahun);
            })->select('fakultas', DB::raw('count(*) as total'))
            // ->where('user_id', Auth::user()->id)
                    ->groupBy('fakultas')
                    ->get()->each(function ($items) {
                        $items->fakultas = $items->ref_fakultas[0]->fakultas;
                        // $items->fakultas = strtoupper($items->fakultas);
                        $items->total = (int) $items->total;
                    });

            $chartProdi = Kader::with('ref_perkaderan')
            ->whereHas('ref_perkaderan', function($query) use ($request) {
                $query->where('tahun_perkaderan', $request->tahun);
            })->select('prodi', DB::raw('count(*) as total'))
            // ->where('user_id', Auth::user()->id)
                    ->groupBy('prodi')
                    ->get()->each(function ($items) {
                        $items->prodi =  $items->ref_prodi[0]->prodi;
                        // $items->prodi = strtoupper($items->prodi);
                        $items->total = (int) $items->total;
                    });

            $chartKomisariat = Kader::with('ref_perkaderan')
            ->whereHas('ref_perkaderan', function($query) use ($request) {
                $query->where('tahun_perkaderan', $request->tahun);
            })->select('komisariat', DB::raw('count(*) as total'))
            // ->where('user_id', Auth::user()->id)
                    ->groupBy('komisariat')
                    ->get()->each(function ($items) {
                        $items->komisariat = strtoupper($items->ref_komisariat[0]->komisariat);
                        $items->total = (int) $items->total;
                    });

            $chartImmawan = Kader::with('ref_perkaderan')
            ->whereHas('ref_perkaderan', function($query) use ($request) {
                $query->where('tahun_perkaderan', $request->tahun);
            })->select('jenis_kelamin', DB::raw('count(*) as total'))
            // ->where('user_id', Auth::user()->id)
                    ->groupBy('jenis_kelamin')
                    ->get()
                    ->map(function ($item) {
                        // Convert jenis_kelamin to uppercase
                        $item->jenis_kelamin = strtoupper($item->jenis_kelamin);

                        // Determine the label based on jenis_kelamin
                        if ($item->jenis_kelamin === 'PRIA') {
                            $item->label = 'IMMAWAN';
                        } elseif ($item->jenis_kelamin === 'WANITA') {
                            $item->label = 'IMMAWATI';
                        }

                        // Convert total to integer
                        $item->total = (int) $item->total;

                        // Unset jenis_kelamin as it's not needed anymore
                        unset($item->jenis_kelamin);

                        return $item;
                    });


            $chartPerkaderanUtama = Kader::with('ref_perkaderan')
            ->whereHas('ref_perkaderan', function($query) use ($request) {
                $query->where('tahun_perkaderan', $request->tahun);
            })->select('id')
                    ->with(['ref_perkaderan' => function ($query) {
                        $query->select('kader_id', 'kegiatan_perkaderan');
                    }])
                    // ->where('user_id', Auth::user()->id)
                    ->get()
                    ->pluck('ref_perkaderan')
                    ->flatten()
                    ->groupBy('kegiatan_perkaderan')
                    ->map(function ($items, $kegiatan_perkaderan) {
                        return [
                            'kegiatan_perkaderan' => strtoupper($kegiatan_perkaderan),
                            'total' => $items->count(),
                        ];
                    })
                    ->values();

            $chartPerkaderanKhusus = Kader::with('ref_perkaderan')
            ->whereHas('ref_perkaderan', function($query) use ($request) {
                $query->where('tahun_perkaderan', $request->tahun);
            })->select('id')
                    ->with(['ref_pimpinan' => function ($query) {
                        $query->select('kader_id', 'kegiatan_pimpinan');
                    }])
                    // ->where('user_id', Auth::user()->id)
                    ->get()
                    ->pluck('ref_pimpinan')
                    ->flatten()
                    ->groupBy('kegiatan_pimpinan')
                    ->map(function ($items, $kegiatan_pimpinan) {
                        return [
                            'kegiatan_pimpinan' => strtoupper($kegiatan_pimpinan),
                            'total' => $items->count(),
                        ];
                    })
                    ->values();
        }else{

            $tahunPerkaderan = Kader::select('id')
                    ->with(['ref_perkaderan' => function ($query) {
                        $query->select('kader_id', 'tahun_perkaderan');
                    }])
                    ->where('deleted_at', null)
                    ->get()
                    ->pluck('ref_perkaderan')
                    ->flatten()
                    ->groupBy('tahun_perkaderan')
                    ->map(function ($items, $tahun_perkaderan) {
                        return strtoupper($tahun_perkaderan);
                    })
                    ->values();

            $chartKampus = Kader::with('ref_perkaderan')
                        ->whereHas('ref_perkaderan', function($query) use ($request) {
                            $query->where('tahun_perkaderan', $request->tahun);
                        })->select('universitas', DB::raw('count(*) as total'))
                        ->groupBy('universitas')
                        ->get()
                        ->transform(function ($item) {
                            $item->universitas = $item->ref_universitas[0]->kampus;
                            // $item->universitas = strtoupper($item->universitas);
                            $item->total = (int) $item->total;
                            return $item;
                        });


            $chartFakultas = Kader::with('ref_perkaderan')
            ->whereHas('ref_perkaderan', function($query) use ($request) {
                $query->where('tahun_perkaderan', $request->tahun);
            })->select('fakultas', DB::raw('count(*) as total'))
                                ->groupBy('fakultas')
                    ->get()->each(function ($items) {
                        $items->fakultas = $items->ref_fakultas[0]->fakultas;

                        // $items->fakultas = strtoupper($items->fakultas);
                        $items->total = (int) $items->total;
                    });

            $chartProdi = Kader::with('ref_perkaderan')
            ->whereHas('ref_perkaderan', function($query) use ($request) {
                $query->where('tahun_perkaderan', $request->tahun);
            })->select('prodi', DB::raw('count(*) as total'))
                                ->groupBy('prodi')
                    ->get()->each(function ($items) {
                        $items->prodi =  $items->ref_prodi[0]->prodi;

                        // $items->prodi = strtoupper($items->prodi);
                        $items->total = (int) $items->total;
                    });

            $chartKomisariat = Kader::with('ref_perkaderan')
            ->whereHas('ref_perkaderan', function($query) use ($request) {
                $query->where('tahun_perkaderan', $request->tahun);
            })->select('komisariat', DB::raw('count(*) as total'))
                                ->groupBy('komisariat')
                    ->get()->each(function ($items) {
                        $items->komisariat = strtoupper($items->ref_komisariat[0]->komisariat);
                        $items->total = (int) $items->total;
                    });

            $chartImmawan = Kader::with('ref_perkaderan')
            ->whereHas('ref_perkaderan', function($query) use ($request) {
                $query->where('tahun_perkaderan', $request->tahun);
            })->select('jenis_kelamin', DB::raw('count(*) as total'))
                                ->groupBy('jenis_kelamin')
                    ->get()
                    ->map(function ($item) {
                        // Convert jenis_kelamin to uppercase
                        $item->jenis_kelamin = strtoupper($item->jenis_kelamin);

                        // Determine the label based on jenis_kelamin
                        if ($item->jenis_kelamin === 'PRIA') {
                            $item->label = 'IMMAWAN';
                        } elseif ($item->jenis_kelamin === 'WANITA') {
                            $item->label = 'IMMAWATI';
                        }

                        // Convert total to integer
                        $item->total = (int) $item->total;

                        // Unset jenis_kelamin as it's not needed anymore
                        unset($item->jenis_kelamin);

                        return $item;
                    });


            $chartPerkaderanUtama = Kader::with('ref_perkaderan')
            ->whereHas('ref_perkaderan', function($query) use ($request) {
                $query->where('tahun_perkaderan', $request->tahun);
            })->select('id')
                    ->with(['ref_perkaderan' => function ($query) {
                        $query->select('kader_id', 'kegiatan_perkaderan');
                    }])
                                        ->get()
                    ->pluck('ref_perkaderan')
                    ->flatten()
                    ->groupBy('kegiatan_perkaderan')
                    ->map(function ($items, $kegiatan_perkaderan) {
                        return [
                            'kegiatan_perkaderan' => strtoupper($kegiatan_perkaderan),
                            'total' => $items->count(),
                        ];
                    })
                    ->values();

            $chartPerkaderanKhusus = Kader::with('ref_perkaderan')
            ->whereHas('ref_perkaderan', function($query) use ($request) {
                $query->where('tahun_perkaderan', $request->tahun);
            })->select('id')
                    ->with(['ref_pimpinan' => function ($query) {
                        $query->select('kader_id', 'kegiatan_pimpinan');
                    }])
                                        ->get()
                    ->pluck('ref_pimpinan')
                    ->flatten()
                    ->groupBy('kegiatan_pimpinan')
                    ->map(function ($items, $kegiatan_pimpinan) {
                        return [
                            'kegiatan_pimpinan' => strtoupper($kegiatan_pimpinan),
                            'total' => $items->count(),
                        ];
                    })
                    ->values();
        }

        // return $chartKomisariat;

        return
        view('contents.dashboard.home',[
            'title' => 'Beranda',
            'perguruanTinggi' => $perguruanTinggi,
            'fakultas' => $fakultas,
            'prodi' => $prodi,
            'kader' => $kader,
            'chartKampus' => $chartKampus,
            'chartFakultas' => $chartFakultas,
            'chartProdi' => $chartProdi,
            'chartKomisariat' => $chartKomisariat,
            'chartImmawan' => $chartImmawan,
            'chartPerkaderanUtama' => $chartPerkaderanUtama,
            'chartPerkaderanKhusus' => $chartPerkaderanKhusus,
            'tahunPerkaderan' => $tahunPerkaderan,
            'tahun' => $request->tahun,
        ]);

    }
}
