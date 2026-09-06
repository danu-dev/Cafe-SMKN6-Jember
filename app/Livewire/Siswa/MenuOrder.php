<?php

namespace App\Livewire\Siswa;

use App\Actions\Order\CreateOrderAction;
use App\Enums\PaymentMethod;
use App\Exceptions\InsufficientBalanceException;
use App\Models\KategoriMenu;
use App\Models\Menu;
use App\Models\User;
use App\Services\XenditService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
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

    #[Validate(['required', 'in:antar,ambil'])]
    public string $tipe_pengiriman = 'antar';

    #[Validate(['required', 'in:saldo,xendit,cod'])]
    public string $metode_pembayaran = 'saldo';

    #[Validate(['nullable', 'string', 'max:500'])]
    public string $catatan = '';

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->metode_pembayaran = $user->is_verified ? PaymentMethod::SALDO->value : PaymentMethod::XENDIT->value;

        // Jika data lokasi siswa belum lengkap, defaultkan ke ambil sendiri
        if (empty($user->kelas) || empty($user->jurusan) || empty($user->ruangan)) {
            $this->tipe_pengiriman = 'ambil';
        }

        // Load cart from session
        $this->cart = session()->get('siswa_cart', []);
    }

    public function addToCart(int $menuId): void
    {
        $menu = Menu::where('is_available', true)->findOrFail($menuId);

        $currentQty = $this->cart[$menuId]['qty'] ?? 0;

        $this->cart[$menuId] = [
            'id' => $menu->id,
            'nama' => $menu->nama,
            'harga' => (float) $menu->harga,
            'qty' => $currentQty + 1,
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

    public function checkout(XenditService $xendit, CreateOrderAction $createOrderAction)
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang belanja masih kosong.');
            return;
        }

        $this->validate();

        /** @var User $user */
        $user = Auth::user();

        // Cek kelengkapan data siswa jika memilih antar
        if ($this->tipe_pengiriman === 'antar') {
            if (empty($user->kelas) || empty($user->jurusan) || empty($user->ruangan)) {
                $this->addError('tipe_pengiriman', 'Data kelas, jurusan, atau ruangan Anda di profil belum lengkap. Silakan lengkapi profil Anda terlebih dahulu untuk menggunakan layanan diantar ke kelas.');
                return;
            }
        }

        // Fetch fresh menu data from DB
        $menuIds = array_keys($this->cart);
        $dbMenus = Menu::whereIn('id', $menuIds)->get()->keyBy('id');

        $totalHarga = 0;
        $itemsPayload = [];
        $validCart = [];

        foreach ($this->cart as $menuId => $item) {
            $dbMenu = $dbMenus->get($menuId);
            if (! $dbMenu || ! $dbMenu->is_available) {
                $menuName = $dbMenu ? $dbMenu->nama : 'Salah satu menu';
                $this->addError('metode_pembayaran', "Menu \"{$menuName}\" sedang tidak tersedia (Not Ready).");
                return;
            }

            $freshPrice = (float) $dbMenu->harga;
            $qty = (int) $item['qty'];
            $subtotal = $freshPrice * $qty;
            $totalHarga += $subtotal;

            $validCart[$menuId] = [
                'id' => $dbMenu->id,
                'nama' => $dbMenu->nama,
                'harga' => $freshPrice,
                'qty' => $qty,
                'subtotal' => $subtotal,
            ];

            $itemsPayload[] = [
                'name' => $dbMenu->nama,
                'quantity' => $qty,
                'price' => (int) $freshPrice,
                'category' => 'Food & Drink',
            ];
        }

        // Verification check for saldo payment
        if ($this->metode_pembayaran === PaymentMethod::SALDO->value && ! $user->is_verified) {
            $this->addError('metode_pembayaran', 'Akun Anda belum terverifikasi kartu pelajar. Silakan bayar langsung via QRIS/VA (Xendit) atau COD.');
            return;
        }

        // Saldo check
        if ($this->metode_pembayaran === PaymentMethod::SALDO->value && (float) $user->saldo < $totalHarga) {
            $this->addError('metode_pembayaran', 'Saldo tidak mencukupi (Saldo: Rp ' . number_format($user->saldo, 0, ',', '.') . ', Total: Rp ' . number_format($totalHarga, 0, ',', '.') . '). Silakan pilih QRIS/VA (Xendit) atau COD.');
            return;
        }

        try {
            $order = $createOrderAction->execute(
                user: $user,
                tipePengiriman: $this->tipe_pengiriman,
                metodePembayaran: $this->metode_pembayaran,
                validCart: $validCart,
                totalHarga: $totalHarga,
                catatan: $this->catatan
            );
        } catch (InsufficientBalanceException $e) {
            $this->addError('metode_pembayaran', $e->getMessage());
            return;
        }

        // If Xendit Payment -> Create Invoice & Redirect
        if ($this->metode_pembayaran === PaymentMethod::XENDIT->value) {
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

            $this->addError('metode_pembayaran', 'Gagal membuat invoice Xendit. Silakan pilih metode bayar lain atau hubungi admin.');
            return;
        }

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
