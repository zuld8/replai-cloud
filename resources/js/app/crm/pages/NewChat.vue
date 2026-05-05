<template>
    <div class="new-chat-loader">
        <div class="loader-content">
            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
            <h4>{{ statusMessage }}</h4>
            <p class="text-muted">{{ statusDetail }}</p>

            <!-- Auto-selected device info -->
            <div v-if="deviceAutoSelected" class="alert alert-info mt-3">
                <i class="bx bx-info-circle me-2"></i>
                Device otomatis dipilih: <strong>{{ deviceName }}</strong>
            </div>

            <!-- Error state -->
            <div v-if="error" class="alert alert-danger mt-3">
                <i class="bx bx-error-circle me-2"></i>
                {{ error }}

                <!-- Specific error for NO_DEVICE_ACCESS -->
                <div v-if="errorCode === 'NO_DEVICE_ACCESS'" class="mt-2">
                    <small class="d-block">
                        Anda tidak memiliki akses ke perangkat {{ platformName }}.
                        Silakan hubungi administrator untuk mendapatkan akses.
                    </small>
                </div>

                <button class="btn btn-sm btn-outline-danger mt-2" @click="retry">
                    <i class="bx bx-refresh me-1"></i>
                    Coba Lagi
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import NProgress from "nprogress";

export default {
    name: "NewChat",

    data() {
        return {
            statusMessage: "Memproses permintaan...",
            statusDetail: "Mohon tunggu sebentar",
            error: null,
            errorCode: null,
            deviceAutoSelected: false,
            deviceName: null,
            params: {
                type: null,
                to: null,
                to_name: null,
                device_id: null,
                text: null,
            }
        };
    },

    computed: {
        platformName() {
            const names = {
                'whatsapp': 'WhatsApp',
                'waba': 'WhatsApp Business API',
                'livechat': 'LiveChat',
                'telegram': 'Telegram',
                'instagram': 'Instagram',
                'messanger': 'Messenger',
            };
            return names[this.params.type] || this.params.type;
        }
    },

    mounted() {
        this.handleNewChat();
    },

    methods: {
        async handleNewChat() {
            try {
                NProgress.start();

                // 1. Extract & validate query params
                this.extractParams();
                this.validateParams();

                // 2. Update status
                if (!this.params.device_id) {
                    this.statusMessage = "Mencari perangkat tersedia...";
                    this.statusDetail = `Memilih perangkat ${this.platformName} otomatis`;
                } else {
                    this.statusMessage = "Menyiapkan chat...";
                    this.statusDetail = `Menghubungi ${this.params.to_name || this.params.to}`;
                }

                // 3. Call API to create/get chat
                const result = await this.createOrGetChat();

                // 4. Check if device was auto-selected
                if (result.device_auto_selected) {
                    this.deviceAutoSelected = true;
                    this.deviceName = result.device_name;
                }

                // 5. Redirect to chat with pre-filled text
                this.statusMessage = "Berhasil!";
                this.statusDetail = "Mengalihkan ke chat...";

                await this.$nextTick();

                // Small delay untuk UX
                setTimeout(() => {
                    this.redirectToChat(result.contact.id);
                }, 800);

            } catch (error) {
                console.error("❌ Error handling new chat:", error);
                this.error = error.message || "Gagal membuat chat. Silakan coba lagi.";
                this.errorCode = error.code || null;
                this.statusMessage = "Terjadi kesalahan";
                this.statusDetail = "";
            } finally {
                NProgress.done();
            }
        },

        extractParams() {
            const query = this.$route.query;

            this.params = {
                type: query.type || 'whatsapp',
                to: query.to,
                to_name: query.to_name || query.to,
                device_id: query.device_id || null, // ✅ Nullable
                text: query.text ? decodeURIComponent(query.text) : null,
            };

            console.log("📋 Extracted params:", this.params);
        },

        validateParams() {
            const errors = [];

            if (!this.params.to) {
                errors.push("Parameter 'to' (nomor tujuan) wajib diisi");
            }

            // ✅ device_id tidak wajib lagi
            // Hanya validate type
            const validTypes = ['whatsapp', 'waba', 'livechat', 'telegram', 'instagram', 'messanger'];
            if (!validTypes.includes(this.params.type)) {
                errors.push(`Type '${this.params.type}' tidak valid. Gunakan: ${validTypes.join(', ')}`);
            }

            if (errors.length > 0) {
                throw new Error(errors.join('. '));
            }
        },

        async createOrGetChat() {
            try {
                const payload = {
                    name: this.params.to_name,
                    phone: this.params.to,
                    type: this.params.type,
                };

                // ✅ Hanya kirim device_id kalau ada
                if (this.params.device_id) {
                    payload.device_id = this.params.device_id;
                }

                const response = await this.$axios.post('/crm/contacts', payload);

                console.log("✅ Chat created/retrieved:", response.data);

                // Return full response data
                return response.data;

            } catch (error) {
                console.error("❌ API Error:", error.response?.data || error);

                // Handle specific error cases
                if (error.response?.status === 403) {
                    // No device access
                    const customError = new Error(error.response.data.message);
                    customError.code = error.response.data.error || 'NO_DEVICE_ACCESS';
                    throw customError;

                } else if (error.response?.status === 404) {
                    throw new Error("Resource tidak ditemukan. Periksa parameter Anda.");

                } else if (error.response?.status === 422) {
                    const messages = error.response.data.errors;
                    const errorMsg = Object.values(messages).flat().join(', ');
                    throw new Error(errorMsg);

                } else {
                    throw new Error(error.response?.data?.message || "Gagal membuat chat");
                }
            }
        },

        redirectToChat(chatId) {
            const query = {};

            // Include text param if exists
            if (this.params.text) {
                query.text = encodeURIComponent(this.params.text);
            }

            // Navigate to chat room
            this.$router.push({
                name: 'chat_room',
                params: { chatid: chatId },
                query: query
            });
        },

        retry() {
            this.error = null;
            this.errorCode = null;
            this.deviceAutoSelected = false;
            this.deviceName = null;
            this.statusMessage = "Memproses permintaan...";
            this.statusDetail = "Mohon tunggu sebentar";
            this.handleNewChat();
        }
    }
};
</script>

<style scoped>
.new-chat-loader {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
    padding: 20px;
}

.loader-content {
    text-align: center;
    max-width: 500px;
}

.loader-content h4 {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 8px;
}

.loader-content p {
    font-size: 14px;
}

.alert {
    text-align: left;
    border-radius: 8px;
}

.alert-info {
    background-color: #dbeafe;
    border-color: #93c5fd;
    color: #1e40af;
}

.alert small {
    display: block;
    margin-top: 4px;
    opacity: 0.9;
}

.spinner-border {
    animation: spinner-border 0.75s linear infinite;
}

@keyframes spinner-border {
    to {
        transform: rotate(360deg);
    }
}
</style>
