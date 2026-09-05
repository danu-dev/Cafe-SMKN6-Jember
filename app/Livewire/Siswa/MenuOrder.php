<?php

namespace App\Livewire\Siswa;

use App\Models\KategoriMenu;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SaldoTransaction;
use App\Models\StoreSetting;
use App\Models\User;
use App\Services\XenditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.siswa')]
class MenuOrder extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $kategori = '';

    public array $cart = [];

    // Modal Checkout State
    public bool $showCheckoutModal = false;
    public string $tipe_pengiriman = 'antar'; // antar / ambil
    public string $metode_pembayaran = 'saldo'; // saldo / xendit / cod
    public string $catatan = '';

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->metode_pembayaran = $user->is_verified ? 'saldo' : 'xendit';

        // Jika data lokasi siswa belum lengkap, defaultkan ke ambil sendiri
        if (empty($user->kelas) || empty($user->jurusan) || empty($user->ruangan)) {
            $this->tipe_pengiriman = 'ambil';
        }

        // Load cart from session
        $this->cart = session()->get('siswa_cart', []);
    }

    public function addToCart(int $menuId): void
    {
        $menu = Menu::where('is_available', true)->where('stok', '>', 0)->findOrFail($menuId);

        $currentQty = $this->cart[$menuId]['qty'] ?? 0;

        if ($currentQty >= $menu->stok) {
            session()->flash('error', "Stok untuk \"{$menu->nama}\" tidak mencukupi (sisa {$menu->stok}).");
            return;
        }

        $this->cart[$menuId] = [
            'id' => $menu->id,
            'nama' => $menu->nama,
            'harga' => (float) $menu->harga,
            'qty' => $currentQty + 1,
            'stok' => $menu->stok,
            'gambar' => $menu->gambar,
        ];

        $this->saveCart();
    }

    public function updateQty(int $menuId, int $qty): void
    {
        if (! isset($this->cart[$menuId])) {
            return;
        }

        if ($qty <= 0) {
            unset($this->cart[$menuId]);
        } else {
            $menu = Menu::find($menuId);
            if ($menu && $qty > $menu->stok) {
                session()->flash('error', "Stok \"{$menu->nama}\" hanya tersedia {$menu->stok}.");
                $qty = $menu->stok;
            }
            $this->cart[$menuId]['qty'] = $qty;
        }

        $this->saveCart();
    }

    public function removeFromCart(int $menuId): void
    {
        unset($this->cart[$menuId]);
        $this->saveCart();
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->saveCart();
    }

    private function saveCart(): void
    {
        session()->put('siswa_cart', $this->cart);
    }

    public function openCheckout(): void
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang belanja masih kosong.');
            return;
        }

        $this->showCheckoutModal = true;
    }

    public function closeCheckout(): void
    {
        $this->showCheckoutModal = false;
    }

    public function checkout(XenditService $xendit)
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang belanja masih kosong.');
            return;
        }

        $this->validate([
            'tipe_pengiriman' => ['required', 'in:antar,ambil'],
            'metode_pembayaran' => ['required', 'in:saldo,xendit,cod'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Cek kelengkapan data siswa jika memilih antar
        if ($this->tipe_pengiriman === 'antar') {
            if (empty($user->kelas) || empty($user->jurusan) || empty($user->ruangan)) {
                $this->addError('tipe_pengiriman', 'Data kelas, jurusan, atau ruangan Anda di profil belum lengkap. Silakan lengkapi profil Anda terlebih dahulu untuk menggunakan layanan diantar ke kelas.');
                return;
            }
        }

        // Calculate total
        $totalHarga = 0;
        $itemsPayload = [];
        foreach ($this->cart as $item) {
            $subtotal = $item['harga'] * $item['qty'];
            $totalHarga += $subtotal;
            $itemsPayload[] = [
                'name' => $item['nama'],
                'quantity' => $item['qty'],
                'price' => (int) $item['harga'],
                'category' => 'Food & Drink',
            ];
        }

        // Verification check for saldo payment
        if ($this->metode_pembayaran === 'saldo' && ! $user->is_verified) {
            $this->addError('metode_pembayaran', 'Akun Anda belum terverifikasi kartu pelajar. Silakan bayar langsung via QRIS/VA (Xendit) atau COD.');
            return;
        }

        // Saldo check
        if ($this->metode_pembayaran === 'saldo' && (float) $user->saldo < $totalHarga) {
            $this->addError('metode_pembayaran', 'Saldo tidak mencukupi (Saldo: Rp ' . number_format($user->saldo, 0, ',', '.') . ', Total: Rp ' . number_format($totalHarga, 0, ',', '.') . '). Silakan pilih QRIS/VA (Xendit) atau COD.');
            return;
        }

        // Auto accept check
        $autoAccept = StoreSetting::get('auto_accept_orders', '0') === '1';
        $initialStatus = $autoAccept ? 'diproses' : 'menunggu';
        $statusPembayaran = $this->metode_pembayaran === 'saldo' ? 'sudah_dibayar' : 'belum_dibayar';

        // Process Transaction
        $order = DB::transaction(function () use ($user, $totalHarga, $initialStatus, $statusPembayaran) {
            $lockedUser = User::lockForUpdate()->findOrFail($user->id);

            // Deduct stock
            foreach ($this->cart as $menuId => $item) {
                $menu = Menu::lockForUpdate()->findOrFail($menuId);
                if ($menu->stok < $item['qty']) {
                    throw new \Exception("Stok untuk \"{$menu->nama}\" tersisa {$menu->stok}, tidak cukup untuk pesanan Anda ({$item['qty']}).");
                }
                $menu->decrement('stok', $item['qty']);
            }

            // Deduct saldo if paid with saldo
            if ($this->metode_pembayaran === 'saldo') {
                $saldoSebelum = (float) $lockedUser->saldo;
                $saldoSesudah = $saldoSebelum - $totalHarga;
                $lockedUser->update(['saldo' => $saldoSesudah]);
            }

            // Create Order
            $kodePesanan = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            while (Order::where('kode_pesanan', $kodePesanan)->exists()) {
                $kodePesanan = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }

            $order = Order::create([
                'kode_pesanan' => $kodePesanan,
                'user_id' => $lockedUser->id,
                'tipe_pengiriman' => $this->tipe_pengiriman,
                'kelas_tujuan' => $this->tipe_pengiriman === 'antar' ? $lockedUser->kelas : null,
                'jurusan_tujuan' => $this->tipe_pengiriman === 'antar' ? $lockedUser->jurusan : null,
                'ruangan_tujuan' => $this->tipe_pengiriman === 'antar' ? $lockedUser->ruangan : null,
                'metode_pembayaran' => $this->metode_pembayaran,
                'status_pembayaran' => $statusPembayaran,
                'status' => $initialStatus,
                'total_harga' => $totalHarga,
                'catatan' => $this->catatan ?: null,
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['id'],
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $item['harga'],
                    'subtotal' => $item['harga'] * $item['qty'],
                ]);
            }

            if ($this->metode_pembayaran === 'saldo') {
                SaldoTransaction::create([
                    'user_id' => $lockedUser->id,
                    'order_id' => $order->id,
                    'tipe' => 'pembayaran',
                    'jumlah' => $totalHarga,
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $saldoSesudah,
                    'keterangan' => 'Pembayaran pesanan #' . $order->kode_pesanan,
                ]);
            }

            // Kirim notifikasi ke Admin tentang pesanan baru
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\CafeNotification(
                    title: 'Pesanan Baru Masuk',
                    message: "Pesanan #{$order->kode_pesanan} dari {$user->name} senilai Rp " . number_format($totalHarga, 0, ',', '.'),
                    type: 'order',
                    actionUrl: route('admin.orders.index')
                ));
            }

            return $order;
        });

        // If Xendit Payment -> Create Invoice & Redirect
        if ($this->metode_pembayaran === 'xendit') {
            $successUrl = route('siswa.orders.index');
            $invoice = $xendit->createInvoice(
                externalId: $order->kode_pesanan,
                amount: $totalHarga,
                payerEmail: $user->email,
                description: "Pembayaran Pesanan Cafe #{$order->kode_pesanan} ({$user->name})",
                customer: [
                    'given_names' => $user->name,
                    'email' => $user->email,
                ],
                items: $itemsPayload,
                successRedirectUrl: $successUrl
            );

            if ($invoice && isset($invoice['invoice_url'])) {
                $order->update([
                    'xendit_invoice_id' => $invoice['id'],
                    'xendit_payment_url' => $invoice['invoice_url'],
                ]);

                $this->cart = [];
                session()->forget('siswa_cart');
                $this->closeCheckout();

                return redirect()->away($invoice['invoice_url']);
            }
        }

        // Clear cart
        $this->cart = [];
        session()->forget('siswa_cart');
        $this->closeCheckout();

        session()->flash('message', "Pesanan #{$order->kode_pesanan} berhasil dibuat!");
        return $this->redirect(route('siswa.orders.index'), navigate: true);
    }

    public function render()
    {
        $categories = KategoriMenu::orderBy('nama')->get();

        $menus = Menu::with('kategori')
            ->where('is_available', true)
            ->where('stok', '>', 0)
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama', 'like', "%{$this->search}%")
                        ->orWhere('deskripsi', 'like', "%{$this->search}%");
                });
            })
            ->when($this->kategori, fn ($q) => $q->where('kategori_id', $this->kategori))
            ->latest()
            ->get();

        // Calculate Cart Totals
        $cartTotal = 0;
        $cartCount = 0;
        foreach ($this->cart as $item) {
            $cartTotal += $item['harga'] * $item['qty'];
            $cartCount += $item['qty'];
        }

        return view('livewire.siswa.menu-order', [
            'categories' => $categories,
            'menus' => $menus,
            'cartTotal' => $cartTotal,
            'cartCount' => $cartCount,
        ]);
    }
}
