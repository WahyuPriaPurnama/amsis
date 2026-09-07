<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="fw-bold">Portal Vendor: {{ $vendor->company_name ?? 'Vendor' }}</h2>
                    <p class="text-muted">Selamat datang di portal resmi mitra vendor perusahaan. Anda dapat memantau Purchase Order (PO) dan mengunggah tagihan/invoice melalui halaman ini.</p>
                </div>
            </div>

            <!-- Bagian Akses PO & Upload Invoice -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-primary text-white">Daftar Purchase Order (PO)</div>
                        <div class="card-body">
                            <p class="text-muted">Belum ada Purchase Order masuk saat ini.</p>
                            <!-- Tabel PO bisa disematkan di sini nanti -->
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-secondary text-white">Upload Invoice / Surat Jalan</div>
                        <div class="card-body">
                            <p class="text-muted">Pilih PO yang bersangkutan untuk mengunggah dokumen penagihan.</p>
                            <!-- Form upload invoice bisa disematkan di sini nanti -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>