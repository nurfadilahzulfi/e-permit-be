<?php
namespace App\Http\Controllers;

// Model-model yang kita perlukan
use App\Models\GwpAlatLs;
use App\Models\GwpCek;
use App\Models\GwpCekHseLs;        // <-- [BARU] Pindah dari GwpController
use App\Models\GwpCekPemohonLs;    // <-- [BARU] Pindah dari GwpController
use App\Models\PermitCse;          // <-- [BARU] Pindah dari GwpController
use App\Models\PermitGwp;          // <-- [BARU] Pindah dari GwpController
use App\Models\PermitType;         // <-- [BARU]
use App\Models\User;               // <-- [BARU]
use App\Models\WorkPermit;         // <-- [BARU]
use App\Models\WorkPermitApproval; // <-- [BARU]

// Library lain
use Carbon\Carbon;
use Illuminate\Contracts\View\View; // <-- [BARU] Untuk return Halaman
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkPermitController extends Controller
{
    // ==============================================
    // (HSE) HALAMAN INISIASI IZIN KERJA
    // ==============================================

    /**
     * [LANGKAH 2] Menampilkan halaman (View) form inisiasi izin untuk HSE.
     */
    public function view(): View
    {
        return view('work-permit.index');
    }

    /**
     * [TETAP] FUNGSI UTAMA BARU: Inisiasi Izin Kerja oleh HSE
     * (Sesuai Langkah 3 Flow Anda)
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // 1. Validasi data utama (diisi HSE)
            $validated = $request->validate([
                'pemohon_id'            => 'required|integer|exists:user,id',
                'supervisor_id'         => 'required|integer|exists:user,id',
                'deskripsi_pekerjaan'   => 'required|string',
                'langkah_pekerjaan'     => 'required|string|min:10',
                'potensi_bahaya'        => 'required|string|min:10',
                'pengendalian_risiko'   => 'required|string|min:10',
                'lokasi'                => 'required|string|max:255',
                'shift_kerja'           => 'required|string|max:20',
                'tgl_pekerjaan_dimulai' => 'required|date',
                'tgl_pekerjaan_selesai' => 'required|date|after_or_equal:tgl_pekerjaan_dimulai',
                'permits_required'      => 'required|array|min:1',
                'permits_required.*'    => 'string|in:GWP,CSE,HWP,JSA,ELP,EWP,WhHP',
                'peralatan_pekerjaan'   => 'nullable|string|required_if:permits_required.*,GWP',
                'gas_tester_name'       => 'nullable|string|required_if:permits_required.*,CSE',
                'entry_supervisor_name' => 'nullable|string|required_if:permits_required.*,CSE',
            ]);

            $now    = Carbon::now();
            $hse_id = Auth::id(); // HSE yang sedang login

            // 2. Buat Nomor Pekerjaan
            $bulan          = $now->format('m');
            $tahun          = $now->format('Y');
            $countThisMonth = WorkPermit::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->count();
            $nomorPekerjaan = "WP-{$tahun}-{$bulan}-" . str_pad($countThisMonth + 1, 4, '0', STR_PAD_LEFT);

            // 3. Buat "Induk" Izin Kerja (WorkPermit)
            $workPermit = WorkPermit::create([
                'nomor_pekerjaan'       => $nomorPekerjaan,
                'deskripsi_pekerjaan'   => $validated['deskripsi_pekerjaan'],
                'langkah_pekerjaan'     => $validated['langkah_pekerjaan'],
                'potensi_bahaya'        => $validated['potensi_bahaya'],
                'pengendalian_risiko'   => $validated['pengendalian_risiko'],
                'lokasi'                => $validated['lokasi'],
                'shift_kerja'           => $validated['shift_kerja'],
                'pemohon_id'            => $validated['pemohon_id'],
                'supervisor_id'         => $validated['supervisor_id'],
                'hse_id'                => $hse_id,
                'status'                => 1, // 1 = Pending (Menunggu Pemohon isi checklist)
                'tgl_pekerjaan_dimulai' => $validated['tgl_pekerjaan_dimulai'],
                'tgl_pekerjaan_selesai' => $validated['tgl_pekerjaan_selesai'],
            ]);

            // 4. Buat "Anak" Sub-Permit (Looping)
            foreach ($validated['permits_required'] as $kodePermit) {

                $permitType = PermitType::where('kode', $kodePermit)->first();
                if (! $permitType) {
                    continue;
                }

                if ($kodePermit === 'GWP') {
                    $permitGwp = PermitGwp::create([
                        'work_permit_id'      => $workPermit->id,
                        'permit_type_id'      => $permitType->id,
                        'permit_type_kode'    => $kodePermit,
                        'peralatan_pekerjaan' => $validated['peralatan_pekerjaan'],
                    ]);

                    // [BARU] Langsung buat checklist kosong untuk GWP
                    $this->createEmptyChecklists($permitGwp->id, $now);
                }
                // [BARU] Logika untuk CSE
                if ($kodePermit === 'CSE') {
                    $permitCse = PermitCse::create([
                        'work_permit_id'        => $workPermit->id,
                        'permit_type_id'        => $permitType->id,
                        'permit_type_kode'      => $kodePermit,
                        'gas_tester_name'       => $validated['gas_tester_name'],
                        'entry_supervisor_name' => $validated['entry_supervisor_name'],
                    ]);
                    // [BARU] Panggil helper baru
                    $this->createEmptyCseChecklists($permitCse->id, Carbon::now());
                }

                // (NANTI)
                // if ($kodePermit === 'CSE') { ... }
            }

            // 5. Buat Alur Persetujuan (HSE -> Supervisor)
            WorkPermitApproval::create([
                'work_permit_id'     => $workPermit->id,
                'approver_id'        => $hse_id,
                'role_persetujuan'   => 'hse',
                'urutan'             => 1,
                'status_persetujuan' => 0, // Pending
            ]);
            WorkPermitApproval::create([
                'work_permit_id'     => $workPermit->id,
                'approver_id'        => $validated['supervisor_id'],
                'role_persetujuan'   => 'supervisor',
                'urutan'             => 2,
                'status_persetujuan' => 0, // Pending
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Izin Kerja berhasil dibuat.', 'data' => $workPermit], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ==============================================
    // (PEMOHON) HALAMAN LIST TUGAS SAYA
    // ==============================================

    /**
     * [LANGKAH 3] Menampilkan halaman (View) list tugas untuk Pemohon.
     */
    public function viewMyPermits(): View
    {
        return view('my-permits.index');
    }

    /**
     * [LANGKAH 3] Memberi data JSON list tugas untuk Pemohon.
     */
    public function index()
    {
        $user  = Auth::user();
        $query = WorkPermit::with(['supervisor', 'hse', 'permitGwp']); // Ambil relasi

        if ($user->role === 'pemohon') {
            $query->where('pemohon_id', $user->id);
        }
        // (Bisa ditambahkan role lain jika perlu lihat semua)

        $data = $query->latest()->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    // ==============================================
    // (PRIVATE) FUNGSI HELPER
    // ==============================================

    /**
     * [BARU] Fungsi ini dipindah dari PermitGwpController
     * Membuat checklist GWP kosong
     */
    private function createEmptyChecklists($permit_gwp_id, $now)
    {
        $dataToInsert = [];

        // 1. Ambil semua master checklist Pemohon
        $pemohonLs = GwpCekPemohonLs::all();
        foreach ($pemohonLs as $item) {
            $dataToInsert[] = [
                'permit_gwp_id' => $permit_gwp_id,
                'model'         => GwpCekPemohonLs::class,
                'ls_id'         => $item->id,
                'value'         => false,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        // 2. Ambil semua master checklist HSE
        $hseLs = GwpCekHseLs::all();
        foreach ($hseLs as $item) {
            $dataToInsert[] = [
                'permit_gwp_id' => $permit_gwp_id,
                'model'         => GwpCekHseLs::class,
                'ls_id'         => $item->id,
                'value'         => false,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        // 3. Ambil semua master checklist Alat
        $alatLs = GwpAlatLs::all();
        foreach ($alatLs as $item) {
            $dataToInsert[] = [
                'permit_gwp_id' => $permit_gwp_id,
                'model'         => GwpAlatLs::class,
                'ls_id'         => $item->id,
                'value'         => false,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        // Insert semua data sekaligus
        if (! empty($dataToInsert)) {
            GwpCek::insert($dataToInsert);
        }
    }
    /**
     * [BARU] Mengubah status Izin Kerja menjadi "Pending Approval".
     * Ini dipanggil oleh Pemohon setelah selesai mengisi checklist.
     */
    public function submitForApproval(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $workPermit = WorkPermit::findOrFail($id);

            // 1. Otorisasi: Pastikan user adalah pemohon yang ditugaskan
            if (Auth::id() !== $workPermit->pemohon_id) {
                return response()->json(['success' => false, 'error' => 'Anda tidak berhak melakukan aksi ini.'], 403);
            }

            // 2. Validasi Status: Pastikan status masih "Pending Checklist"
            if ($workPermit->status !== 1) {
                return response()->json(['success' => false, 'error' => 'Izin ini sudah dikirim atau tidak lagi valid.'], 422);
            }

            // 3. (Opsional) Validasi Checklist: Pastikan semua checklist pemohon terisi
            // ... (Logika ini bisa ditambahkan nanti jika diperlukan) ...

                                     // 4. Ubah Status Induk
            $workPermit->status = 2; // 2 = Pending Approval
            $workPermit->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checklist berhasil dikirim dan sedang menunggu persetujuan HSE.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
