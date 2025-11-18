<?php
namespace App\Http\Controllers;

// ===== DAFTAR MODEL YANG DIBUTUHKAN =====
use App\Models\CseCek;
use App\Models\CseCekGasLs;
use App\Models\CseCekPersiapanLs;
use App\Models\EwpCek;

// Model Permit "Anak"
use App\Models\EwpCekLs;
use App\Models\GwpAlatLs;
use App\Models\GwpCek;
use App\Models\GwpCekHseLs;
use App\Models\GwpCekPemohonLs;

// Model Checklist "Jawaban"
use App\Models\HwpCek;
use App\Models\HwpCekLs;
use App\Models\LpCek;     // <-- [BARU]
use App\Models\LpCekLs;   // <-- [BARU]
use App\Models\PermitCse; // <-- [BARU]

// Model Checklist "Master Pertanyaan"
use App\Models\PermitEwp;
use App\Models\PermitGwp;
use App\Models\PermitHwp;
use App\Models\PermitLp;
use App\Models\PermitType;
use App\Models\User;               // <-- [BARU]
use App\Models\WorkPermit;         // <-- [BARU]
use App\Models\WorkPermitApproval; // <-- [BARU]

// ===== LIBRARY =====
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkPermitController extends Controller
{
    // ==============================================
    // (HSE) HALAMAN TINJAUAN PEKERJAAN (Langkah 3)
    // ==============================================

    /**
     * Menampilkan halaman View 'work-permit/index.blade.php'
     */
    public function viewHseReview(): View
    {
        return view('work-permit.index');
    }

    /**
     * Memberi data JSON list pekerjaan (status 10) untuk HSE.
     */
    public function indexHseReview()
    {
                                                // Ambil pekerjaan yang baru diajukan pemohon (Status 10)
        $data = WorkPermit::where('status', 10) // 10 = Pending HSE Review
            ->with(['pemohon', 'supervisor'])       // Ambil relasi
            ->latest()                              // Tampilkan yang terbaru
            ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * [FLOWCHART LANGKAH 3]
     * Dipanggil HSE setelah mengisi JSA & memilih permit di modal.
     */
    public function reviewAndAssign(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // 1. Cari Izin Kerja (status 10) yang dikirim Pemohon
            $workPermit = WorkPermit::where('status', 10)->findOrFail($id);
            $now        = Carbon::now();
            $userHse    = Auth::user();

            // 2. Validasi data dari HSE (JSA, Permits, dll)
            $validated = $request->validate([
                'langkah_pekerjaan'      => 'required|string|min:10',
                'potensi_bahaya'         => 'required|string|min:10',
                'pengendalian_risiko'    => 'required|string|min:10',
                'shift_kerja'            => 'required|string|max:20',
                'tgl_pekerjaan_selesai'  => 'required|date|after_or_equal:' . $workPermit->tgl_pekerjaan_dimulai,

                'permits_required'       => 'required|array|min:1',
                'permits_required.*'     => 'string|in:GWP,CSE,HWP,EWP,LP', // Sesuaikan dengan 'kode' di tabel permit_types

                // Validasi kondisional (muncul jika permit dipilih)
                'peralatan_pekerjaan'    => 'nullable|string|required_if:permits_required.*,GWP|max:500',
                'gas_tester_name'        => 'nullable|string|required_if:permits_required.*,CSE|max:100',
                'entry_supervisor_name'  => 'nullable|string|required_if:permits_required.*,CSE|max:100',
                'equipment_tools'        => 'nullable|string|required_if:permits_required.*,HWP|max:500',
                'kedalaman_galian_meter' => 'nullable|string|required_if:permits_required.*,EWP|max:100',
                'crane_capacity'         => 'nullable|string|required_if:permits_required.*,LP|max:100',
                'load_weight'            => 'nullable|string|required_if:permits_required.*,LP|max:100',
            ]);

            // 3. UPDATE Izin Kerja "Induk" dengan data JSA & info dari HSE
            $workPermit->update([
                'langkah_pekerjaan'     => $validated['langkah_pekerjaan'],
                'potensi_bahaya'        => $validated['potensi_bahaya'],
                'pengendalian_risiko'   => $validated['pengendalian_risiko'],
                'shift_kerja'           => $validated['shift_kerja'],
                'tgl_pekerjaan_selesai' => $validated['tgl_pekerjaan_selesai'],
                'hse_id'                => $userHse->id, // [PENTING] Tetapkan HSE yang bertanggung jawab
                'status'                => 1,            // [PENTING] Ubah status ke 1 (Pending Checklist Pemohon)
            ]);

            // 4. Buat "Anak" Sub-Permit (Looping)
            foreach ($validated['permits_required'] as $kodePermit) {
                $permitType = PermitType::where('kode', $kodePermit)->first();
                if (! $permitType) {
                    continue;
                }
                // Lewati jika kode tidak ditemukan

                $dataAnak = [
                    'work_permit_id'   => $workPermit->id,
                    'permit_type_id'   => $permitType->id,
                    'permit_type_kode' => $kodePermit,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];

                switch ($kodePermit) {
                    case 'GWP':
                        $permitGwp = PermitGwp::create(array_merge($dataAnak, [
                            'peralatan_pekerjaan' => $validated['peralatan_pekerjaan'],
                        ]));
                        $this->createEmptyGwpChecklists($permitGwp->id, $now);
                        break;

                    case 'CSE':
                        $permitCse = PermitCse::create(array_merge($dataAnak, [
                            'gas_tester_name'       => $validated['gas_tester_name'],
                            'entry_supervisor_name' => $validated['entry_supervisor_name'],
                        ]));
                        $this->createEmptyCseChecklists($permitCse->id, $now);
                        break;

                    case 'HWP': // <-- [BARU]
                        $permitHwp = PermitHwp::create(array_merge($dataAnak, [
                            'equipment_tools' => $validated['equipment_tools'] ?? null,
                        ]));
                        $this->createEmptyHwpChecklists($permitHwp->id, $now); // <-- [BARU]
                        break;

                    case 'EWP': // <-- [BARU]
                        $permitEwp = PermitEwp::create(array_merge($dataAnak, [
                            'kedalaman_galian_meter' => $validated['kedalaman_galian_meter'] ?? null,
                        ]));
                        $this->createEmptyEwpChecklists($permitEwp->id, $now); // <-- [BARU]
                        break;

                    case 'LP': // <-- [BARU]
                        $permitLp = PermitLp::create(array_merge($dataAnak, [
                            'crane_capacity' => $validated['crane_capacity'],
                            'load_weight'    => $validated['load_weight'],
                            // (Tambahkan field LP lain)
                        ]));
                        $this->createEmptyLpChecklists($permitLp->id, $now); // <-- [BARU]
                        break;
                }
            }

            // 5. Buat Alur Persetujuan (HSE -> Supervisor)
            WorkPermitApproval::create([
                'work_permit_id'     => $workPermit->id,
                'approver_id'        => $userHse->id, // HSE yang me-review
                'role_persetujuan'   => 'hse',
                'urutan'             => 1,
                'status_persetujuan' => 0, // Pending
            ]);
            WorkPermitApproval::create([
                'work_permit_id'     => $workPermit->id,
                'approver_id'        => $workPermit->supervisor_id, // Supervisor dari Pemohon
                'role_persetujuan'   => 'supervisor',
                'urutan'             => 2,
                'status_persetujuan' => 0, // Pending
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Izin Kerja berhasil ditinjau dan dikirim ke Pemohon.', 'data' => $workPermit], 200);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ==============================================
    // (PEMOHON) HALAMAN LIST TUGAS SAYA (Langkah 2 & 4)
    // ==============================================

    /**
     * Menampilkan halaman View "Tugas Izin Saya" (my-permits/index.blade.php)
     */
    public function viewMyPermits(): View
    {
        return view('my-permits.index');
    }

    /**
     * [FIXED] Memberi data JSON list pekerjaan untuk Pemohon/HSE/SPV
     * Query dibuat ringan (tanpa 'permitGwp', 'permitCse', 'approvals')
     */
    public function index()
    {
        $user = Auth::user();

        $query = WorkPermit::with([
            'pemohon',
            'supervisor',
        ]);

        if ($user->role === 'pemohon') {
            $query->where('pemohon_id', $user->id);
        } else if ($user->role === 'hse') {
            $query->where('hse_id', $user->id);
            $query->with('hse');
        } else if ($user->role === 'supervisor') {
            $query->where('supervisor_id', $user->id);
        }

        $data = $query->latest()->get();

        // [PERBAIKAN] Muat relasi "anak" HANYA JIKA diperlukan (status == 1)
        $data->each(function ($permit) {
            if ($permit->status == 1) {
                // 'permitGwp', 'permitCse', 'permitHwp', 'permitEwp', 'permitLp'
                // adalah nama relasi di WorkPermit.php
                $permit->load('permitGwp', 'permitCse', 'permitHwp', 'permitEwp', 'permitLp');
            }
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * [FLOWCHART LANGKAH 2]
     * Fungsi untuk Pemohon mengajukan pekerjaan (form simpel)
     */
    public function requestJob(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'supervisor_id'         => 'required|integer|exists:user,id',
                'lokasi'                => 'required|string|max:255',
                'deskripsi_pekerjaan'   => 'required|string',
                'tgl_pekerjaan_dimulai' => 'required|date|after_or_equal:today',
            ]);

            $workPermit = WorkPermit::create([
                'deskripsi_pekerjaan'   => $validated['deskripsi_pekerjaan'],
                'lokasi'                => $validated['lokasi'],
                'supervisor_id'         => $validated['supervisor_id'],
                'tgl_pekerjaan_dimulai' => $validated['tgl_pekerjaan_dimulai'],
                'nomor_pekerjaan'       => $this->generateNomorPekerjaan(),
                'pemohon_id'            => Auth::id(),
                'status'                => 10,   // 10 = Pending HSE Review
                'hse_id'                => null, // [PENTING] Dibiarkan NULL
                'shift_kerja'           => 'Belum Ditentukan',
                'tgl_pekerjaan_selesai' => $validated['tgl_pekerjaan_dimulai'], // Default
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pengajuan pekerjaan berhasil dikirim. Menunggu peninjauan HSE.', 'data' => $workPermit], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ==============================================
    // FUNGSI HELPER (Checklist, Approval, dll)
    // ==============================================

    /**
     * [FLOWCHART LANGKAH 4]
     * Dipanggil Pemohon saat klik "Kirim Persetujuan" (setelah isi checklist)
     */
    public function submitForApproval(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $workPermit = WorkPermit::findOrFail($id);

            if (Auth::id() !== $workPermit->pemohon_id || $workPermit->status !== 1) {
                return response()->json(['success' => false, 'error' => 'Aksi tidak diizinkan.'], 403);
            }

            $workPermit->status = 2; // 2 = Pending Approval (HSE)
            $workPermit->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Checklist berhasil dikirim untuk persetujuan HSE.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper untuk membuat Nomor Pekerjaan Unik
     */
    private function generateNomorPekerjaan()
    {
        $date  = Carbon::now()->format('Ymd');
        $count = WorkPermit::whereDate('created_at', Carbon::today())->count() + 1;
        return "WP-{$date}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Helper untuk membuat baris kosong checklist GWP
     */
    private function createEmptyGwpChecklists($permit_gwp_id, $now)
    {
        $dataToInsert = [];

        $pemohonLs = GwpCekPemohonLs::all();
        foreach ($pemohonLs as $item) {
            $dataToInsert[] = ['permit_gwp_id' => $permit_gwp_id, 'model' => GwpCekPemohonLs::class, 'ls_id' => $item->id, 'value' => false, 'created_at' => $now, 'updated_at' => $now];
        }

        $hseLs = GwpCekHseLs::all();
        foreach ($hseLs as $item) {
            $dataToInsert[] = ['permit_gwp_id' => $permit_gwp_id, 'model' => GwpCekHseLs::class, 'ls_id' => $item->id, 'value' => false, 'created_at' => $now, 'updated_at' => $now];
        }

        $alatLs = GwpAlatLs::all();
        foreach ($alatLs as $item) {
            $dataToInsert[] = ['permit_gwp_id' => $permit_gwp_id, 'model' => GwpAlatLs::class, 'ls_id' => $item->id, 'value' => false, 'created_at' => $now, 'updated_at' => $now];
        }

        if (! empty($dataToInsert)) {
            GwpCek::insert($dataToInsert);
        }
    }

    /**
     * Helper untuk membuat baris kosong checklist CSE
     */
    private function createEmptyCseChecklists($permit_cse_id, $now)
    {
        $dataToInsert = [];

        $persiapanLs = CseCekPersiapanLs::all();
        foreach ($persiapanLs as $item) {
            $dataToInsert[] = ['permit_cse_id' => $permit_cse_id, 'model' => CseCekPersiapanLs::class, 'ls_id' => $item->id, 'value' => false, 'created_at' => $now, 'updated_at' => $now];
        }

        $gasLs = CseCekGasLs::all();
        foreach ($gasLs as $item) {
            $dataToInsert[] = ['permit_cse_id' => $permit_cse_id, 'model' => CseCekGasLs::class, 'ls_id' => $item->id, 'value' => false, 'created_at' => $now, 'updated_at' => $now];
        }

        if (! empty($dataToInsert)) {
            CseCek::insert($dataToInsert);
        }
    }

    /**
     * [BARU - IMPLEMENTASI] Helper untuk checklist HWP
     */
    private function createEmptyHwpChecklists($permit_hwp_id, $now)
    {
        $dataToInsert = [];
        $items        = HwpCekLs::all();
        foreach ($items as $item) {
            $dataToInsert[] = ['permit_hwp_id' => $permit_hwp_id, 'model' => HwpCekLs::class, 'ls_id' => $item->id, 'value' => false, 'created_at' => $now, 'updated_at' => $now];
        }
        if (! empty($dataToInsert)) {
            HwpCek::insert($dataToInsert);
        }
    }

    /**
     * [BARU - IMPLEMENTASI] Helper untuk checklist EWP
     */
    private function createEmptyEwpChecklists($permit_ewp_id, $now)
    {
        $dataToInsert = [];
        $items        = EwpCekLs::all();
        foreach ($items as $item) {
            $dataToInsert[] = ['permit_ewp_id' => $permit_ewp_id, 'model' => EwpCekLs::class, 'ls_id' => $item->id, 'value' => false, 'created_at' => $now, 'updated_at' => $now];
        }
        if (! empty($dataToInsert)) {
            EwpCek::insert($dataToInsert);
        }
    }

    /**
     * [BARU - IMPLEMENTASI] Helper untuk checklist LP
     */
    private function createEmptyLpChecklists($permit_lp_id, $now)
    {
        $dataToInsert = [];
        $items        = LpCekLs::all();
        foreach ($items as $item) {
            $dataToInsert[] = ['permit_lp_id' => $permit_lp_id, 'model' => LpCekLs::class, 'ls_id' => $item->id, 'value' => false, 'created_at' => $now, 'updated_at' => $now];
        }
        if (! empty($dataToInsert)) {
            LpCek::insert($dataToInsert);
        }
    }
}
