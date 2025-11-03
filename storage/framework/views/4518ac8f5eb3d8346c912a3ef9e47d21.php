



<div x-data="eventBookingManager(<?php echo e(json_encode($ilan->id ?? null)); ?>)" 
     x-init="init()"
     class="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 p-6">
    
    
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                📅 Rezervasyon ve Etkinlik Yönetimi
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Yazlık kiralama rezervasyonları ve bloklanan tarihleri yönetin
            </p>
        </div>
        <button 
            type="button"
            @click="showCreateModal = true"
            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
            ➕ Yeni Rezervasyon
        </button>
    </div>

    
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <button 
                @click="previousMonth()"
                class="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                ◀ Önceki
            </button>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white" x-text="currentMonthName"></h4>
            <button 
                @click="nextMonth()"
                class="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                Sonraki ▶
            </button>
        </div>

        
        <div class="grid grid-cols-7 gap-2">
            
            <template x-for="day in ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz']">
                <div class="text-center text-xs font-bold text-gray-600 dark:text-gray-400 py-2" x-text="day"></div>
            </template>

            
            <template x-for="day in calendarDays" :key="day.date">
                <div 
                    @click="selectDate(day)"
                    :class="{
                        'bg-gray-100 dark:bg-gray-900 text-gray-400 dark:text-gray-600': !day.isCurrentMonth,
                        'bg-white dark:bg-gray-800 text-gray-900 dark:text-white hover:bg-blue-50 dark:hover:bg-blue-900/30 cursor-pointer': day.isCurrentMonth && !day.isBooked && !day.isBlocked,
                        'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300': day.isBooked,
                        'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300': day.isBlocked,
                        'bg-blue-500 text-white': day.isToday,
                        'ring-2 ring-blue-500': day.isSelected
                    }"
                    class="aspect-square flex items-center justify-center text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-700 transition-all duration-200 relative">
                    <span x-text="day.dayNumber"></span>
                    <span x-show="day.isBooked" class="absolute bottom-0.5 text-xs">🔒</span>
                    <span x-show="day.isBlocked" class="absolute bottom-0.5 text-xs">⛔</span>
                </div>
            </template>
        </div>
    </div>

    
    <div class="flex flex-wrap items-center gap-4 mb-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 bg-red-100 dark:bg-red-900/30 border border-red-300 rounded"></div>
            <span class="text-sm text-gray-700 dark:text-gray-300">🔒 Rezervasyon</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-300 rounded"></div>
            <span class="text-sm text-gray-700 dark:text-gray-300">⛔ Bloke</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 bg-blue-500 rounded"></div>
            <span class="text-sm text-gray-700 dark:text-gray-300">Bugün</span>
        </div>
    </div>

    
    <div class="space-y-3 mb-6" x-show="events.length > 0">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">📋 Yaklaşan Rezervasyonlar</h4>
        <template x-for="event in upcomingEvents" :key="event.id">
            <div class="p-4 border-2 rounded-xl transition-all duration-200 hover:shadow-lg"
                 :class="event.status === 'confirmed' ? 'border-green-500 dark:border-green-400 bg-green-50 dark:bg-green-900/20' : event.status === 'pending' ? 'border-yellow-500 dark:border-yellow-400 bg-yellow-50 dark:bg-yellow-900/20' : 'border-red-500 dark:border-red-400 bg-red-50 dark:bg-red-900/20'">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-sm font-bold px-2 py-1 rounded"
                                  :class="event.status === 'confirmed' ? 'bg-green-600 text-white' : event.status === 'pending' ? 'bg-yellow-600 text-white' : 'bg-red-600 text-white'"
                                  x-text="event.status === 'confirmed' ? '✅ Onaylandı' : event.status === 'pending' ? '⏳ Beklemede' : '❌ İptal'"></span>
                            <span class="text-sm text-gray-600 dark:text-gray-400" x-text="event.event_type === 'booking' ? '🏠 Rezervasyon' : '⛔ Bloke'"></span>
                        </div>
                        <div class="space-y-1">
                            <p class="font-semibold text-gray-900 dark:text-white" x-text="event.guest_name || 'İsimsiz'"></p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                📅 <span x-text="formatDate(event.check_in)"></span> → <span x-text="formatDate(event.check_out)"></span>
                                (<span x-text="event.nights"></span> gece)
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400" x-show="event.guest_count">
                                👥 <span x-text="event.guest_count"></span> kişi
                            </p>
                            <p class="text-sm font-bold text-green-600 dark:text-green-400" x-show="event.total_price">
                                💰 <span x-text="formatPrice(event.total_price)"></span>
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <button 
                            @click="editEvent(event)"
                            class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700 transition-colors">
                            ✏️ Düzenle
                        </button>
                        <button 
                            @click="deleteEvent(event.id)"
                            class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs hover:bg-red-700 transition-colors">
                            🗑️ Sil
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    
    <div x-show="events.length === 0" class="text-center py-12 text-gray-500 dark:text-gray-400">
        <div class="text-4xl mb-3">📅</div>
        <p class="text-sm">Henüz rezervasyon veya bloke tarih yok</p>
    </div>

    
    <div x-show="showCreateModal || editingEvent" 
         @click.self="closeModal()"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
         style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl" @click.stop>
            <div class="p-6">
                
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        <span x-show="!editingEvent">➕ Yeni Rezervasyon/Bloke</span>
                        <span x-show="editingEvent">✏️ Rezervasyon Düzenle</span>
                    </h3>
                    <button @click="closeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-2xl">✖</button>
                </div>

                
                <div class="space-y-4">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tür</label>
                        <select x-model="formData.event_type" class="w-full px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="booking">🏠 Rezervasyon</option>
                            <option value="blocked">⛔ Bloke (Müsait Değil)</option>
                        </select>
                    </div>

                    
                    <div x-show="formData.event_type === 'booking'" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Misafir Adı *</label>
                            <input type="text" x-model="formData.guest_name" placeholder="Ad Soyad" class="w-full px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Telefon</label>
                                <input type="tel" x-model="formData.guest_phone" placeholder="+90 5XX XXX XX XX" class="w-full px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                <input type="email" x-model="formData.guest_email" placeholder="email@example.com" class="w-full px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kişi Sayısı</label>
                            <input type="number" x-model.number="formData.guest_count" min="1" max="20" class="w-full px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Giriş Tarihi *</label>
                            <input type="date" x-model="formData.check_in" @change="calculateNights()" class="w-full px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Çıkış Tarihi *</label>
                            <input type="date" x-model="formData.check_out" @change="calculateNights()" class="w-full px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    
                    <div x-show="formData.event_type === 'booking' && formData.nights > 0" class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Gece Sayısı</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="formData.nights"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Toplam Fiyat (₺)</label>
                            <input type="number" x-model.number="formData.total_price" step="0.01" class="w-full px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durum</label>
                        <select x-model="formData.status" class="w-full px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="pending">⏳ Beklemede</option>
                            <option value="confirmed">✅ Onaylandı</option>
                            <option value="cancelled">❌ İptal</option>
                        </select>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notlar (Opsiyonel)</label>
                        <textarea x-model="formData.notes" rows="3" placeholder="Rezervasyon notları..." class="w-full px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                
                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button 
                        @click="closeModal()"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        İptal
                    </button>
                    <button 
                        @click="saveEvent()"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg">
                        <span x-show="!editingEvent">➕ Oluştur</span>
                        <span x-show="editingEvent">💾 Kaydet</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function eventBookingManager(ilanId = null) {
    return {
        ilanId: ilanId,
        events: [],
        currentMonth: new Date(),
        selectedDate: null,
        showCreateModal: false,
        editingEvent: null,
        formData: this.getEmptyFormData(),

        get currentMonthName() {
            return this.currentMonth.toLocaleDateString('tr-TR', { month: 'long', year: 'numeric' });
        },

        get calendarDays() {
            const year = this.currentMonth.getFullYear();
            const month = this.currentMonth.getMonth();
            
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            
            const startDay = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1; // Monday = 0
            
            const days = [];
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Previous month days
            for (let i = startDay - 1; i >= 0; i--) {
                const date = new Date(year, month, -i);
                days.push(this.createDayObject(date, false));
            }

            // Current month days
            for (let i = 1; i <= lastDay.getDate(); i++) {
                const date = new Date(year, month, i);
                days.push(this.createDayObject(date, true));
            }

            // Next month days (fill to 42 = 6 weeks)
            const remaining = 42 - days.length;
            for (let i = 1; i <= remaining; i++) {
                const date = new Date(year, month + 1, i);
                days.push(this.createDayObject(date, false));
            }

            return days;
        },

        get upcomingEvents() {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            return this.events
                .filter(e => new Date(e.check_out) >= today)
                .sort((a, b) => new Date(a.check_in) - new Date(b.check_in))
                .slice(0, 5);
        },

        async init() {
            if (this.ilanId) {
                await this.loadEvents();
            }
        },

        async loadEvents() {
            try {
                const response = await fetch(`/api/admin/ilanlar/${this.ilanId}/events`);
                if (response.ok) {
                    const data = await response.json();
                    this.events = data.events || [];
                }
            } catch (error) {
                console.error('Events yüklenemedi:', error);
            }
        },

        createDayObject(date, isCurrentMonth) {
            const dateStr = date.toISOString().split('T')[0];
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            date.setHours(0, 0, 0, 0);

            const bookedEvent = this.events.find(e => 
                e.event_type === 'booking' && 
                e.status !== 'cancelled' &&
                dateStr >= e.check_in && 
                dateStr < e.check_out
            );

            const blockedEvent = this.events.find(e => 
                e.event_type === 'blocked' && 
                dateStr >= e.check_in && 
                dateStr < e.check_out
            );

            return {
                date: dateStr,
                dayNumber: date.getDate(),
                isCurrentMonth: isCurrentMonth,
                isToday: date.getTime() === today.getTime(),
                isBooked: !!bookedEvent,
                isBlocked: !!blockedEvent,
                isSelected: this.selectedDate === dateStr
            };
        },

        selectDate(day) {
            if (!day.isCurrentMonth) return;
            
            this.selectedDate = day.date;
            this.formData.check_in = day.date;
            this.showCreateModal = true;
        },

        previousMonth() {
            this.currentMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() - 1, 1);
        },

        nextMonth() {
            this.currentMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + 1, 1);
        },

        calculateNights() {
            if (this.formData.check_in && this.formData.check_out) {
                const start = new Date(this.formData.check_in);
                const end = new Date(this.formData.check_out);
                const diffTime = end - start;
                this.formData.nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            } else {
                this.formData.nights = 0;
            }
        },

        async saveEvent() {
            // Validation
            if (!this.formData.check_in || !this.formData.check_out) {
                window.toast?.('Giriş ve çıkış tarihleri gerekli', 'error');
                return;
            }

            if (this.formData.event_type === 'booking' && !this.formData.guest_name) {
                window.toast?.('Misafir adı gerekli', 'error');
                return;
            }

            const url = this.editingEvent 
                ? `/api/admin/events/${this.editingEvent.id}`
                : '/api/admin/events';
            
            const method = this.editingEvent ? 'PATCH' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ...this.formData,
                        ilan_id: this.ilanId
                    })
                });

                if (response.ok) {
                    await this.loadEvents();
                    this.closeModal();
                    window.toast?.('Rezervasyon kaydedildi', 'success');
                }
            } catch (error) {
                console.error('Save error:', error);
                window.toast?.('Kaydetme hatası', 'error');
            }
        },

        editEvent(event) {
            this.editingEvent = event;
            this.formData = { ...event };
            this.showCreateModal = true;
        },

        async deleteEvent(eventId) {
            if (!confirm('Bu rezervasyonu silmek istediğinize emin misiniz?')) return;

            try {
                const response = await fetch(`/api/admin/events/${eventId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    await this.loadEvents();
                    window.toast?.('Rezervasyon silindi', 'success');
                }
            } catch (error) {
                console.error('Delete error:', error);
            }
        },

        closeModal() {
            this.showCreateModal = false;
            this.editingEvent = null;
            this.formData = this.getEmptyFormData();
        },

        getEmptyFormData() {
            return {
                event_type: 'booking',
                guest_name: '',
                guest_phone: '',
                guest_email: '',
                guest_count: 2,
                check_in: '',
                check_out: '',
                nights: 0,
                total_price: 0,
                status: 'pending',
                notes: ''
            };
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            return new Date(dateStr).toLocaleDateString('tr-TR', { 
                day: 'numeric', 
                month: 'short', 
                year: 'numeric' 
            });
        },

        formatPrice(price) {
            return new Intl.NumberFormat('tr-TR', { 
                style: 'currency', 
                currency: 'TRY' 
            }).format(price);
        }
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/ilanlar/components/event-booking-manager.blade.php ENDPATH**/ ?>