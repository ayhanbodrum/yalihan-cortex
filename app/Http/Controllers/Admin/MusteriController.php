<?php

namespace App\Http\Controllers\Admin;

/**
 * @deprecated Bu controller artık kullanılmıyor. 
 * Lütfen KisiController kullanın (admin.kisiler.* route'ları).
 * Geriye dönük uyumluluk için redirect route'ları mevcuttur.
 * 
 * Context7 Standard: C7-DEPRECATED-MUSTERI-2025-11-05
 * Bu controller'ın tüm metodları KisiController'a taşınmıştır.
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MusteriController extends AdminController
{
    /**
     * Display a listing of customers.
     * Context7: Müşteri listesi ve filtreleme
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 20);
            $search = $request->get('search', '');
            $status = $request->get('status', 'all');
            $type = $request->get('type', 'all');
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $customers = $this->getCustomers($search, $status, $type, $sortBy, $sortOrder, $perPage);
            $stats = $this->getCustomerStats();
            $filters = $this->getCustomerFilters();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'customers' => $customers,
                        'stats' => $stats,
                        'filters' => $filters
                    ]
                ]);
            }

            return view('admin.customers.index', compact('customers', 'stats', 'filters'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Müşteri listesi yüklenirken hata: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Müşteri listesi yüklenirken hata: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new customer.
     * Context7: Yeni müşteri oluşturma formu
     */
    public function create()
    {
        try {
            $customerTypes = $this->getCustomerTypes();
            $cities = $this->getCities();
            $sources = $this->getCustomerSources();

            return view('admin.customers.create', compact('customerTypes', 'cities', 'sources'));
        } catch (\Exception $e) {
            return back()->with('error', 'Form yüklenirken hata: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created customer.
     * Context7: Yeni müşteri kaydetme
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ad' => 'required|string|max:50',
                'soyad' => 'required|string|max:50',
                'email' => 'nullable|email|unique:customers,email',
                'telefon' => 'required|string|max:20',
                'telefon2' => 'nullable|string|max:20',
                'tc_no' => 'nullable|string|size:11|unique:customers,tc_no',
                'dogum_tarihi' => 'nullable|date|before:today',
                'cinsiyet' => 'nullable|in:erkek,kadın',
                'medeni_durum' => 'nullable|in:bekar,evli,dul,boşanmış',
                'meslek' => 'nullable|string|max:100',
                'gelir_durumu' => 'nullable|numeric|min:0',
                'il_id' => 'required|integer',
                'ilce_id' => 'nullable|integer',
                'mahalle_id' => 'nullable|integer',
                'adres' => 'nullable|string|max:500',
                'musteri_tipi' => 'required|in:alici,satici,kiralayan,kiralayacak',
                'kaynak' => 'nullable|string|max:50',
                'notlar' => 'nullable|string|max:1000',
                'aktif_mi' => 'boolean'
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation hatası',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return back()->withErrors($validator)->withInput();
            }

            $customerData = [
                'id' => time(), // Mock ID
                'ad' => $request->ad,
                'soyad' => $request->soyad,
                'email' => $request->email,
                'telefon' => $request->telefon,
                'telefon2' => $request->telefon2,
                'tc_no' => $request->tc_no,
                'dogum_tarihi' => $request->dogum_tarihi,
                'cinsiyet' => $request->cinsiyet,
                'medeni_durum' => $request->medeni_durum,
                'meslek' => $request->meslek,
                'gelir_durumu' => $request->gelir_durumu,
                'il_id' => $request->il_id,
                'ilce_id' => $request->ilce_id,
                'mahalle_id' => $request->mahalle_id,
                'adres' => $request->adres,
                'musteri_tipi' => $request->musteri_tipi,
                'kaynak' => $request->kaynak,
                'notlar' => $request->notlar,
                'aktif_mi' => $request->boolean('aktif_mi', true),
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ];

            // TODO: Customer model ile kaydetme
            // Customer::create($customerData);

            // Cache temizle
            Cache::forget('customer_stats');
            Cache::forget('recent_customers');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Müşteri başarıyla oluşturuldu',
                    'data' => $customerData
                ], 201);
            }

            return redirect()->route('admin.customers.index')->with('success', 'Müşteri başarıyla oluşturuldu');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Müşteri oluşturulurken hata: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Müşteri oluşturulurken hata: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified customer.
     * Context7: Müşteri detayları ve ilanları
     */
    public function show($id)
    {
        try {
            $customer = $this->getSampleCustomer($id);

            if (!$customer) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Müşteri bulunamadı'
                    ], 404);
                }

                return redirect()->route('admin.customers.index')->with('error', 'Müşteri bulunamadı');
            }

            $customerProperties = $this->getCustomerProperties($id);
            $customerActivities = $this->getCustomerActivities($id);
            $relatedCustomers = $this->getRelatedCustomers($customer);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'customer' => $customer,
                        'properties' => $customerProperties,
                        'activities' => $customerActivities,
                        'related' => $relatedCustomers
                    ]
                ]);
            }

            return view('admin.customers.show', compact('customer', 'customerProperties', 'customerActivities', 'relatedCustomers'));
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Müşteri detayları alınırken hata: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Müşteri detayları alınırken hata: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified customer.
     * Context7: Müşteri düzenleme formu
     */
    public function edit($id)
    {
        try {
            $customer = $this->getSampleCustomer($id);

            if (!$customer) {
                return redirect()->route('admin.customers.index')->with('error', 'Müşteri bulunamadı');
            }

            $customerTypes = $this->getCustomerTypes();
            $cities = $this->getCities();
            $sources = $this->getCustomerSources();

            return view('admin.customers.edit', compact('customer', 'customerTypes', 'cities', 'sources'));
        } catch (\Exception $e) {
            return back()->with('error', 'Form yüklenirken hata: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified customer.
     * Context7: Müşteri bilgileri güncelleme
     */
    public function update(Request $request, $id)
    {
        try {
            $customer = $this->getSampleCustomer($id);

            if (!$customer) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Müşteri bulunamadı'
                    ], 404);
                }

                return back()->with('error', 'Müşteri bulunamadı');
            }

            $request->validate([
                'ad' => 'required|string|max:50',
                'soyad' => 'required|string|max:50',
                'email' => 'nullable|email|unique:customers,email,' . $id,
                'telefon' => 'required|string|max:20',
                'telefon2' => 'nullable|string|max:20',
                'tc_no' => 'nullable|string|size:11|unique:customers,tc_no,' . $id,
                'dogum_tarihi' => 'nullable|date|before:today',
                'cinsiyet' => 'nullable|in:erkek,kadın',
                'medeni_durum' => 'nullable|in:bekar,evli,dul,boşanmış',
                'meslek' => 'nullable|string|max:100',
                'gelir_durumu' => 'nullable|numeric|min:0',
                'il_id' => 'required|integer',
                'ilce_id' => 'nullable|integer',
                'mahalle_id' => 'nullable|integer',
                'adres' => 'nullable|string|max:500',
                'musteri_tipi' => 'required|in:alici,satici,kiralayan,kiralayacak',
                'kaynak' => 'nullable|string|max:50',
                'notlar' => 'nullable|string|max:1000',
                'aktif_mi' => 'boolean'
            ]);

            $updateData = [
                'ad' => $request->ad,
                'soyad' => $request->soyad,
                'email' => $request->email,
                'telefon' => $request->telefon,
                'telefon2' => $request->telefon2,
                'tc_no' => $request->tc_no,
                'dogum_tarihi' => $request->dogum_tarihi,
                'cinsiyet' => $request->cinsiyet,
                'medeni_durum' => $request->medeni_durum,
                'meslek' => $request->meslek,
                'gelir_durumu' => $request->gelir_durumu,
                'il_id' => $request->il_id,
                'ilce_id' => $request->ilce_id,
                'mahalle_id' => $request->mahalle_id,
                'adres' => $request->adres,
                'musteri_tipi' => $request->musteri_tipi,
                'kaynak' => $request->kaynak,
                'notlar' => $request->notlar,
                'aktif_mi' => $request->boolean('aktif_mi', true),
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ];

            // TODO: Customer model ile güncelleme
            // Customer::where('id', $id)->update($updateData);

            // Cache temizle
            Cache::forget('customer_stats');
            Cache::forget('customer_' . $id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Müşteri bilgileri başarıyla güncellendi',
                    'data' => array_merge($customer, $updateData)
                ]);
            }

            return redirect()->route('admin.customers.show', $id)->with('success', 'Müşteri bilgileri başarıyla güncellendi');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Müşteri güncellenirken hata: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Müşteri güncellenirken hata: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified customer.
     * Context7: Müşteri silme (soft delete)
     */
    public function destroy($id)
    {
        try {
            $customer = $this->getSampleCustomer($id);

            if (!$customer) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Müşteri bulunamadı'
                    ], 404);
                }

                return back()->with('error', 'Müşteri bulunamadı');
            }

            // TODO: Customer model ile soft delete
            // Customer::findOrFail($id)->delete();

            // Cache temizle
            Cache::forget('customer_stats');
            Cache::forget('customer_' . $id);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Müşteri başarıyla silindi'
                ]);
            }

            return redirect()->route('admin.customers.index')->with('success', 'Müşteri başarıyla silindi');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Müşteri silinirken hata: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Müşteri silinirken hata: ' . $e->getMessage());
        }
    }

    /**
     * Context7: Müşteri arama
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $type = $request->get('type', 'all');
            $limit = $request->get('limit', 10);

            if (empty($query)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $customers = $this->searchCustomers($query, $type, $limit);

            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Arama sırasında hata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Context7: Müşteri istatistikleri API
     */
    public function stats()
    {
        try {
            $stats = Cache::remember('customer_stats', 3600, function () {
                return $this->getCustomerStats();
            });

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İstatistikler alınırken hata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Context7: Müşteri aktiviteleri
     */
    public function activities($id)
    {
        try {
            $activities = $this->getCustomerActivities($id);

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aktiviteler alınırken hata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Context7: Örnek müşteri verileri
     */
    private function getCustomers($search = '', $status = 'all', $type = 'all', $sortBy = 'created_at', $sortOrder = 'desc', $perPage = 20)
    {
        // Mock data - gerçek implementasyonda veritabanından gelecek
        $allCustomers = [
            [
                'id' => 1,
                'ad' => 'Ahmet',
                'soyad' => 'Yılmaz',
                'email' => 'ahmet@email.com',
                'telefon' => '0532 123 45 67',
                'musteri_tipi' => 'alici',
                'il' => 'İstanbul',
                'aktif_mi' => true,
                'created_at' => now()->subDays(10),
                'last_activity' => now()->subHours(2)
            ],
            [
                'id' => 2,
                'ad' => 'Fatma',
                'soyad' => 'Demir',
                'email' => 'fatma@email.com',
                'telefon' => '0533 987 65 43',
                'musteri_tipi' => 'satici',
                'il' => 'Ankara',
                'aktif_mi' => true,
                'created_at' => now()->subDays(5),
                'last_activity' => now()->subDays(1)
            ]
        ];

        // Filtreleme ve sıralama logic'i
        return array_slice($allCustomers, 0, $perPage);
    }

    /**
     * Context7: Müşteri istatistikleri
     */
    private function getCustomerStats()
    {
        return [
            'total_customers' => 1234,
            'active_customers' => 987,
            'new_this_month' => 45,
            'by_type' => [
                'alici' => 456,
                'satici' => 321,
                'kiralayan' => 234,
                'kiralayacak' => 223
            ],
            'by_city' => [
                'İstanbul' => 456,
                'Ankara' => 234,
                'İzmir' => 187,
                'Diğer' => 357
            ]
        ];
    }

    /**
     * Context7: Müşteri filtreleri
     */
    private function getCustomerFilters()
    {
        return [
            'status' => [
                'all' => 'Tümü',
                'active' => 'Aktif',
                'inactive' => 'Pasif'
            ],
            'type' => $this->getCustomerTypes(),
            'sort_options' => [
                'created_at' => 'Kayıt Tarihi',
                'ad' => 'Ad',
                'last_activity' => 'Son Aktivite'
            ]
        ];
    }

    /**
     * Context7: Müşteri tipleri
     */
    private function getCustomerTypes()
    {
        return [
            'all' => 'Tümü',
            'alici' => 'Alıcı',
            'satici' => 'Satıcı',
            'kiralayan' => 'Kiralayan',
            'kiralayacak' => 'Kiralayacak'
        ];
    }

    /**
     * Context7: Şehir listesi
     */
    private function getCities()
    {
        return [
            1 => 'İstanbul',
            2 => 'Ankara',
            3 => 'İzmir',
            4 => 'Bursa',
            5 => 'Antalya'
        ];
    }

    /**
     * Context7: Müşteri kaynakları
     */
    private function getCustomerSources()
    {
        return [
            'website' => 'Web Sitesi',
            'referral' => 'Referans',
            'advertising' => 'Reklam',
            'social_media' => 'Sosyal Medya',
            'direct' => 'Doğrudan Başvuru'
        ];
    }

    /**
     * Context7: Örnek müşteri detayı
     */
    private function getSampleCustomer($id)
    {
        $customers = $this->getCustomers();
        return collect($customers)->firstWhere('id', (int)$id);
    }

    /**
     * Context7: Müşteri ilanları
     */
    private function getCustomerProperties($customerId)
    {
        return [
            [
                'id' => 1,
                'title' => 'Satılık Villa',
                'price' => 2500000,
                'status' => 'active',
                'created_at' => now()->subDays(15)
            ]
        ];
    }

    /**
     * Context7: Müşteri aktiviteleri
     */
    private function getCustomerActivities($customerId)
    {
        return [
            [
                'type' => 'property_view',
                'description' => 'İlan görüntüledi: Satılık Villa',
                'created_at' => now()->subHours(2)
            ],
            [
                'type' => 'contact',
                'description' => 'Telefon görüşmesi yapıldı',
                'created_at' => now()->subDays(1)
            ]
        ];
    }

    /**
     * Context7: İlgili müşteriler
     */
    private function getRelatedCustomers($customer)
    {
        return [
            [
                'id' => 3,
                'ad' => 'Mehmet',
                'soyad' => 'Kaya',
                'similarity_reason' => 'Aynı bölgede arıyor'
            ]
        ];
    }

    /**
     * Context7: Müşteri arama
     */
    private function searchCustomers($query, $type, $limit)
    {
        $customers = $this->getCustomers();

        return array_slice(array_filter($customers, function($customer) use ($query) {
            return str_contains(strtolower($customer['ad'] . ' ' . $customer['soyad']), strtolower($query)) ||
                   str_contains(strtolower($customer['telefon']), strtolower($query));
        }), 0, $limit);
    }
}
