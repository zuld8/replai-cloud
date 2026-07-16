<template>
  <div class="mb-builder">
    <!-- ══════════════ HEADER ══════════════ -->
    <div class="mb-header">
      <div class="mb-header-left">
        <div class="mb-breadcrumb">
          <a href="/app/auto-reply/menu-otomatis" class="mb-bc-link">Menu Otomatis</a>
          <span class="mb-bc-sep">/</span>
          <span>{{ flowId ? 'Edit' : 'Buat Baru' }}</span>
        </div>
        <input v-model="flow.name" class="mb-title-input" placeholder="Nama menu (wajib)…" maxlength="120" />
        <span class="badge ms-2" :class="flow.status === 'active' ? 'bg-success' : 'bg-secondary'">
          {{ flow.status === 'active' ? 'aktif' : 'nonaktif' }}
        </span>
      </div>
      <div class="mb-header-right">
        <button class="btn btn-sm btn-outline-secondary me-2" @click="toggleStatus">
          {{ flow.status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
        </button>
        <button class="btn btn-primary" :disabled="saving" @click="save">
          <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
          <i v-else class="bx bx-save me-1"></i>
          Simpan
        </button>
      </div>
    </div>

    <!-- ══════════════ SETTINGS PANEL ══════════════ -->
    <div class="mb-settings">
      <div class="mb-settings-grid">
        <!-- Pemicu -->
        <div class="mb-setting-item">
          <label class="mb-label">Pemicu</label>
          <select v-model="triggerMode" class="form-select form-select-sm" @change="onTriggerChange">
            <option value="default">Pesan apa saja ⚠️</option>
            <option value="contains">Mengandung kata</option>
            <option value="exact">Sama persis (rekomendasi)</option>
          </select>
        </div>

        <!-- Kata kunci (muncul jika keyword mode) -->
        <div class="mb-setting-item" v-if="triggerMode !== 'default'">
          <label class="mb-label">Kata Kunci <small class="text-muted">(Enter untuk tambah)</small></label>
          <div class="mb-tag-input" @click="$refs.kwInput.focus()">
            <span v-for="(kw, i) in flow.trigger_keywords" :key="i" class="mb-tag">
              {{ kw }} <button @click.stop="removeKeyword(i)">×</button>
            </span>
            <input ref="kwInput" v-model="kwDraft" @keydown.enter.prevent="addKeyword"
              @keydown.backspace="kwDraft === '' && removeKeyword(flow.trigger_keywords.length - 1)"
              placeholder="ketik + Enter…" class="mb-tag-input-field" />
          </div>
        </div>

        <!-- Channel -->
        <div class="mb-setting-item">
          <label class="mb-label">Channel</label>
          <select v-model="flow.channels" class="form-select form-select-sm" multiple>
            <option value="">Semua channel</option>
            <option v-for="d in devices" :key="d.id" :value="d.id">{{ d.phone }}</option>
          </select>
          <small class="text-muted">Kosong = semua channel</small>
        </div>

        <!-- Kalau tak cocok -->
        <div class="mb-setting-item">
          <label class="mb-label">Kalau tak cocok</label>
          <select v-model="flow.fallback_action" class="form-select form-select-sm">
            <option value="repeat_menu">Ulangi menu</option>
            <option value="ai_agent">AI Agent</option>
            <option value="manual_reply">Balasan manual</option>
          </select>
        </div>

        <!-- Sesi berakhir -->
        <div class="mb-setting-item">
          <label class="mb-label">Sesi berakhir</label>
          <div class="input-group input-group-sm">
            <input v-model.number="flow.session_timeout_min" type="number" min="1" max="1440"
              class="form-control" style="max-width:80px" />
            <span class="input-group-text">menit</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ══════════════ MAIN CONTENT ══════════════ -->
    <div class="mb-content">
      <!-- LEFT: Node Cards -->
      <div class="mb-steps">
        <div class="mb-steps-header">
          <h6 class="mb-0">Langkah-langkah</h6>
        </div>
        <div class="mb-steps-list">
          <div v-for="(node, ni) in nodes" :key="node.temp_id"
            class="mb-node-card" :class="{ 'mb-node-selected': selectedTempId === node.temp_id, 'mb-node-start': startTempId === node.temp_id }"
            @click="selectedTempId = node.temp_id">

            <!-- Card Header -->
            <div class="mb-node-header">
              <div class="mb-node-badges">
                <span v-if="startTempId === node.temp_id" class="mb-badge-start">Awal</span>
                <span class="mb-badge-type" :class="'mb-type-'+node.type">
                  {{ typeLabel(node.type) }}
                </span>
                <span class="mb-node-num">Langkah {{ ni + 1 }}</span>
              </div>
              <div class="mb-node-actions">
                <button class="mb-icon-btn" title="Jadikan langkah awal" @click.stop="startTempId = node.temp_id" v-if="startTempId !== node.temp_id">
                  <i class="bx bx-home-alt"></i>
                </button>
                <button class="mb-icon-btn mb-icon-danger" title="Hapus langkah" @click.stop="removeNode(ni)">
                  <i class="bx bx-trash"></i>
                </button>
              </div>
            </div>

            <!-- Body text -->
            <textarea v-model="node.body_text" class="mb-node-body"
              :placeholder="node.type === 'handoff' ? 'Pesan handoff ke CS… (opsional)' : 'Teks pesan…'"
              rows="2" @click.stop></textarea>

            <!-- Header/Footer (buttons/list) -->
            <div v-if="node.type === 'buttons' || node.type === 'list'" class="mb-node-extras">
              <input v-model="node.header" class="mb-extra-input" placeholder="Header (opsional, max 60)" maxlength="60" @click.stop />
              <input v-model="node.footer" class="mb-extra-input" placeholder="Footer (opsional, max 60)" maxlength="60" @click.stop />
              <input v-if="node.type === 'list'" v-model="node.list_button_label" class="mb-extra-input"
                placeholder="Label tombol list (max 20, mis. 'Pilih')" maxlength="20" @click.stop />
            </div>

            <!-- Options -->
            <div class="mb-options" v-if="node.type === 'buttons' || node.type === 'list'">
              <div v-for="(opt, oi) in node.options" :key="oi" class="mb-option-row" @click.stop>
                <i class="bx me-1 text-muted" :class="opt.kind === 'button' ? 'bx-radio-circle' : 'bx-menu'"></i>
                <div class="mb-option-inputs">
                  <input v-model="opt.label" class="mb-opt-label"
                    :placeholder="opt.kind === 'button' ? 'Label tombol (max 20)' : 'Judul baris (max 24)'"
                    :maxlength="opt.kind === 'button' ? 20 : 24" />
                  <span class="mb-char-count" :class="{ 'text-danger': opt.label.length >= (opt.kind === 'button' ? 20 : 24) }">
                    {{ opt.label.length }}/{{ opt.kind === 'button' ? 20 : 24 }}
                  </span>
                  <input v-if="opt.kind === 'list_row'" v-model="opt.description"
                    class="mb-opt-desc" placeholder="Deskripsi (opsional, max 72)" maxlength="72" />
                </div>
                <div class="mb-option-target">
                  <select v-model="opt.target_action" class="form-select form-select-sm mb-target-select"
                    @change="opt.target_temp_id = ''">
                    <option value="goto_node">→ Ke Langkah</option>
                    <option value="back_to_start">↩ Menu awal</option>
                    <option value="handoff">👤 Serahkan ke CS</option>
                    <option value="end">✋ Akhiri</option>
                  </select>
                  <select v-if="opt.target_action === 'goto_node'" v-model="opt.target_temp_id"
                    class="form-select form-select-sm mb-target-node">
                    <option value="">Pilih langkah…</option>
                    <option v-for="(n2, n2i) in nodes" :key="n2.temp_id"
                      v-if="n2.temp_id !== node.temp_id"
                      :value="n2.temp_id">
                      Langkah {{ n2i + 1 }} — {{ n2.body_text ? n2.body_text.slice(0,30) : typeLabel(n2.type) }}
                    </option>
                  </select>
                </div>
                <button class="mb-icon-btn mb-icon-danger ms-1" @click="removeOption(node, oi)">×</button>
              </div>

              <!-- Tambah option -->
              <button class="mb-add-opt"
                :disabled="(node.type === 'buttons' && node.options.length >= 3) || (node.type === 'list' && node.options.length >= 10)"
                @click.stop="addOption(node)">
                <i class="bx bx-plus"></i>
                {{ node.type === 'buttons' ? 'Tambah tombol' : 'Tambah pilihan' }}
                ({{ node.options.length }}/{{ node.type === 'buttons' ? 3 : 10 }})
              </button>
            </div>
          </div>

          <!-- Tambah Langkah -->
          <div class="mb-add-node-area">
            <div v-if="!addingNode" class="mb-add-node-btn" @click="addingNode = true">
              <i class="bx bx-plus"></i> Tambah langkah
            </div>
            <div v-else class="mb-type-picker">
              <div class="mb-type-btn" @click="addNode('message')">
                <i class="bx bx-message-detail"></i><span>Pesan</span>
              </div>
              <div class="mb-type-btn" @click="addNode('buttons')">
                <i class="bx bx-layout"></i><span>Tombol</span>
              </div>
              <div class="mb-type-btn" @click="addNode('list')">
                <i class="bx bx-list-ul"></i><span>Daftar</span>
              </div>
              <div class="mb-type-btn mb-type-btn-handoff" @click="addNode('handoff')">
                <i class="bx bx-user-voice"></i><span>Serah ke CS</span>
              </div>
              <button class="mb-icon-btn ms-2" @click="addingNode = false"><i class="bx bx-x"></i></button>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT: WA Preview -->
      <div class="mb-preview">
        <div class="mb-preview-header">Pratinjau WhatsApp</div>
        <div class="mb-preview-phone">
          <template v-if="previewNode">
            <div class="mb-wa-bubble">
              <div class="mb-wa-header" v-if="previewNode.header">{{ previewNode.header }}</div>
              <div class="mb-wa-body">{{ previewNode.body_text || '…' }}</div>
              <div class="mb-wa-footer" v-if="previewNode.footer">{{ previewNode.footer }}</div>
              <div class="mb-wa-time">{{ now }}</div>
            </div>
            <!-- Buttons preview -->
            <div v-if="previewNode.type === 'buttons'" class="mb-wa-btns">
              <div v-for="opt in previewNode.options" :key="opt.label" class="mb-wa-btn">
                <i class="bx bx-reply me-1"></i>{{ opt.label || '…' }}
              </div>
            </div>
            <!-- List preview -->
            <div v-if="previewNode.type === 'list'" class="mb-wa-list-btn">
              <i class="bx bx-list-ul me-1"></i>{{ previewNode.list_button_label || 'Pilih' }}
            </div>
            <div v-if="previewNode.type === 'list'" class="mb-wa-list-rows">
              <div v-for="opt in previewNode.options" :key="opt.label" class="mb-wa-list-row">
                <div class="mb-wa-list-title">{{ opt.label || '…' }}</div>
                <div class="mb-wa-list-desc" v-if="opt.description">{{ opt.description }}</div>
              </div>
            </div>
            <!-- Handoff preview -->
            <div v-if="previewNode.type === 'handoff'" class="mb-wa-handoff">
              <i class="bx bx-user-voice me-1"></i> Menghubungkan ke CS…
            </div>
          </template>
          <div v-else class="mb-preview-empty">
            <i class="bx bx-select-multiple fs-2 text-muted mb-2 d-block"></i>
            Klik langkah untuk pratinjau
          </div>
        </div>
      </div>
    </div>

    <!-- Error list -->
    <div v-if="errors.length" class="alert alert-danger mt-3 mx-3">
      <ul class="mb-0">
        <li v-for="e in errors" :key="e">{{ e }}</li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MenuBuilder',
  data() {
    return {
      flowId: null,
      flow: {
        name: '',
        trigger_type: 'keyword',
        keyword_match: 'exact',
        trigger_keywords: [],
        channels: [],
        fallback_action: 'repeat_menu',
        session_timeout_min: 30,
        status: 'active',
      },
      nodes: [],
      devices: [],
      startTempId: null,
      selectedTempId: null,
      seq: 0,
      kwDraft: '',
      addingNode: false,
      saving: false,
      errors: [],
      now: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
    };
  },
  computed: {
    triggerMode: {
      get() {
        if (this.flow.trigger_type === 'default') return 'default';
        return this.flow.keyword_match === 'contains' ? 'contains' : 'exact';
      },
      set(val) {
        this.flow.trigger_type = val === 'default' ? 'default' : 'keyword';
        this.flow.keyword_match = val === 'contains' ? 'contains' : 'exact';
      },
    },
    previewNode() {
      return this.nodes.find(n => n.temp_id === this.selectedTempId) || null;
    },
  },
  methods: {
    newTemp() { return 'n' + (++this.seq); },
    typeLabel(t) {
      return { message: 'Pesan', buttons: 'Tombol', list: 'Daftar pilihan', handoff: 'Serah ke CS' }[t] || t;
    },
    onTriggerChange() {},

    addKeyword() {
      const kw = this.kwDraft.trim();
      if (kw && !this.flow.trigger_keywords.includes(kw)) {
        this.flow.trigger_keywords.push(kw);
      }
      this.kwDraft = '';
    },
    removeKeyword(i) {
      this.flow.trigger_keywords.splice(i, 1);
    },

    addNode(type) {
      const tid = this.newTemp();
      const node = {
        temp_id: tid, type,
        body_text: '', header: '', footer: '', list_button_label: '',
        position: this.nodes.length + 1,
        options: [],
      };
      this.nodes.push(node);
      if (!this.startTempId) this.startTempId = tid;
      this.selectedTempId = tid;
      this.addingNode = false;
    },
    removeNode(i) {
      if (!confirm('Hapus langkah ini? Opsi yang mengarah ke langkah ini akan kehilangan target.')) return;
      const tid = this.nodes[i].temp_id;
      this.nodes.splice(i, 1);
      if (this.startTempId === tid) this.startTempId = this.nodes[0]?.temp_id || null;
      if (this.selectedTempId === tid) this.selectedTempId = this.nodes[0]?.temp_id || null;
      // Hapus referensi target ke node yang dihapus
      this.nodes.forEach(n => n.options.forEach(o => {
        if (o.target_temp_id === tid) { o.target_action = 'end'; o.target_temp_id = ''; }
      }));
      this.reindex();
    },
    reindex() { this.nodes.forEach((n, i) => n.position = i + 1); },

    addOption(node) {
      const kind = node.type === 'buttons' ? 'button' : 'list_row';
      node.options.push({ kind, label: '', description: '', order: node.options.length + 1, target_action: 'end', target_temp_id: '' });
    },
    removeOption(node, i) { node.options.splice(i, 1); node.options.forEach((o, j) => o.order = j + 1); },

    toggleStatus() { this.flow.status = this.flow.status === 'active' ? 'inactive' : 'active'; },

    validate() {
      const errs = [];
      if (!this.flow.name.trim()) errs.push('Nama menu wajib diisi.');
      if (this.nodes.length === 0) errs.push('Minimal 1 langkah.');
      if (!this.startTempId) errs.push('Langkah awal belum ditentukan.');
      if (this.flow.trigger_type === 'keyword' && this.flow.trigger_keywords.length === 0) {
        errs.push('Mode keyword butuh minimal 1 kata kunci.');
      }
      this.nodes.forEach((n, i) => {
        if ((n.type === 'buttons' || n.type === 'list') && n.options.length === 0) {
          errs.push(`Langkah ${i+1} (${this.typeLabel(n.type)}) butuh minimal 1 opsi.`);
        }
        n.options.forEach((o, oi) => {
          if (o.target_action === 'goto_node' && !o.target_temp_id) {
            errs.push(`Langkah ${i+1}, opsi ${oi+1}: pilih langkah tujuan.`);
          }
        });
      });
      return errs;
    },

    async save() {
      this.errors = this.validate();
      if (this.errors.length) return;
      this.saving = true;
      try {
        const payload = {
          ...this.flow,
          id: this.flowId,
          start_temp_id: this.startTempId,
          nodes: this.nodes.map((n, i) => ({
            ...n,
            position: i + 1,
            body_text: n.body_text || null,
            header: n.header || null,
            footer: n.footer || null,
            list_button_label: n.list_button_label || null,
          })),
        };
        const { data } = await this.$axios.post('auto-reply/menu-otomatis/save', payload);
        this.flowId = data.id;
        this.$showToast('Menu otomatis tersimpan!', 'success');
        window.history.replaceState({}, '', `/app/auto-reply/menu-otomatis/${data.id}/edit`);
      } catch (e) {
        const msg = e.response?.data?.message || e.response?.data?.errors
          ? Object.values(e.response.data.errors || {}).flat().join(', ')
          : 'Gagal menyimpan';
        this.$showToast(msg, 'error');
      } finally {
        this.saving = false;
      }
    },

    async loadData() {
      if (!this.flowId) {
        // Create mode — ambil devices dari data attribute
        const el = document.getElementById('app');
        try { this.devices = JSON.parse(el.dataset.devices || '[]'); } catch {}
        return;
      }
      try {
        const { data } = await this.$axios.get(`auto-reply/menu-otomatis/${this.flowId}/data`);
        const f = data.flow;
        this.flow = {
          name: f.name, trigger_type: f.trigger_type,
          keyword_match: f.keyword_match || 'exact',
          trigger_keywords: f.trigger_keywords || [],
          channels: f.channels || [],
          fallback_action: f.fallback_action || 'repeat_menu',
          session_timeout_min: f.session_timeout_min || 30,
          status: f.status,
        };
        this.devices = data.devices || [];

        // Pass 1: build tempId map (plain objects, belum reaktif)
        const rawNodes = Array.isArray(f.nodes) ? f.nodes : [];
        const idToTemp = {};
        const pass1 = rawNodes.map(n => {
          if (!n) return null;
          const tid = this.newTemp();
          idToTemp[n.id] = tid;
          return { _rawNode: n, temp_id: tid };
        }).filter(Boolean);

        // Pass 2: resolve options → assign sekali ke this.nodes (tanpa mutasi reactive)
        this.nodes = pass1.map(({ _rawNode: n, temp_id: tid }) => ({
          temp_id: tid,
          type: n.type || 'message',
          body_text: n.body_text || '',
          header: n.header || '',
          footer: n.footer || '',
          list_button_label: n.list_button_label || '',
          position: n.position || 0,
          options: (Array.isArray(n.options) ? n.options : []).map(o => ({
            kind: o.kind || 'button',
            label: o.label || '',
            description: o.description || '',
            order: o.order || 0,
            target_action: o.target_action || 'end',
            target_temp_id: o.target_node_id ? (idToTemp[o.target_node_id] || '') : '',
          })),
        }));

        this.startTempId = f.start_node_id ? (idToTemp[f.start_node_id] || null) : null;
        this.selectedTempId = this.nodes[0]?.temp_id || null;
      } catch (e) {
        console.error('[MenuBuilder] loadData error:', e);
        this.$showToast('Gagal memuat data: ' + (e.message || 'unknown'), 'error');
      }
    },
  },
  mounted() {
    const el = document.getElementById('app');
    this.flowId = el.dataset.flowId || null;
    this.loadData();
    setInterval(() => { this.now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }); }, 10000);
  },
};
</script>

<style scoped>
/* ── Layout ──────────────────────────────────────────── */
.mb-builder { display:flex; flex-direction:column; min-height:100vh; background:#F5F8FC; }

/* ── Header ──────────────────────────────────────────── */
.mb-header {
  display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;
  padding:12px 20px; background:#fff; border-bottom:1px solid #E4EAF2; position:sticky; top:0; z-index:20;
}
.mb-header-left { display:flex; align-items:center; gap:10px; flex:1; min-width:0; }
.mb-breadcrumb { font-size:12px; color:#64748B; white-space:nowrap; }
.mb-bc-link { color:#2E8DE1; text-decoration:none; }
.mb-bc-sep { margin:0 4px; }
.mb-title-input {
  border:none; outline:none; font-size:17px; font-weight:600; color:#1E2A4A;
  min-width:200px; max-width:340px; background:transparent;
  border-bottom:2px solid transparent; transition:border-color .15s;
}
.mb-title-input:focus { border-bottom-color:#2E8DE1; }
.mb-header-right { display:flex; align-items:center; gap:8px; flex-shrink:0; }

/* ── Settings ─────────────────────────────────────────── */
.mb-settings { background:#fff; border-bottom:1px solid #E4EAF2; padding:14px 20px; }
.mb-settings-grid { display:flex; gap:20px; flex-wrap:wrap; }
.mb-setting-item { flex:1; min-width:160px; max-width:260px; }
.mb-label { font-size:11px; font-weight:600; text-transform:uppercase; color:#64748B; letter-spacing:.05em; display:block; margin-bottom:4px; }

/* Tag input */
.mb-tag-input {
  display:flex; flex-wrap:wrap; gap:4px; align-items:center;
  border:1px solid #E4EAF2; border-radius:6px; padding:4px 8px; cursor:text;
  background:#F5F8FC; min-height:32px;
}
.mb-tag { background:#EAF3FC; color:#2E8DE1; font-size:12px; padding:2px 6px; border-radius:4px; display:flex; align-items:center; gap:4px; }
.mb-tag button { border:none; background:none; color:#2E8DE1; cursor:pointer; font-size:14px; line-height:1; padding:0; }
.mb-tag-input-field { border:none; outline:none; background:transparent; font-size:13px; min-width:80px; flex:1; }

/* ── Main ─────────────────────────────────────────────── */
.mb-content { display:flex; gap:0; flex:1; overflow:hidden; min-height:0; }

/* ── Steps ────────────────────────────────────────────── */
.mb-steps { flex:1; min-width:0; overflow-y:auto; max-width:600px; }
.mb-steps-header { padding:14px 20px 8px; font-size:13px; font-weight:600; color:#1E2A4A; border-bottom:1px solid #E4EAF2; }
.mb-steps-list { padding:16px 20px; display:flex; flex-direction:column; gap:12px; }

/* Node card */
.mb-node-card {
  background:#fff; border:1.5px solid #E4EAF2; border-radius:12px; padding:14px 16px;
  cursor:pointer; transition:border-color .15s, box-shadow .15s;
}
.mb-node-card:hover { border-color:#2E8DE1; }
.mb-node-selected { border-color:#2E8DE1; box-shadow:0 0 0 3px rgba(46,141,225,.1); }
.mb-node-start { border-color:#16A34A; }
.mb-node-start.mb-node-selected { box-shadow:0 0 0 3px rgba(22,163,74,.12); }

.mb-node-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
.mb-node-badges { display:flex; align-items:center; gap:6px; }
.mb-node-num { font-size:11px; color:#94A3B8; }
.mb-badge-start { background:#DCFCE7; color:#16A34A; font-size:11px; font-weight:600; padding:2px 7px; border-radius:20px; }
.mb-badge-type { font-size:11px; font-weight:600; padding:2px 8px; border-radius:20px; }
.mb-type-message { background:#EAF3FC; color:#2E8DE1; }
.mb-type-buttons { background:#F1ECFE; color:#5B3FB0; }
.mb-type-list    { background:#FEF9EC; color:#D97706; }
.mb-type-handoff { background:#E0F2FE; color:#0369A1; }
.mb-node-actions { display:flex; gap:4px; }
.mb-icon-btn { background:none; border:none; cursor:pointer; color:#64748B; padding:3px 5px; border-radius:5px; font-size:14px; transition:background .15s; }
.mb-icon-btn:hover { background:#F1F5F9; color:#1E2A4A; }
.mb-icon-danger { color:#DC2626 !important; }

.mb-node-body {
  width:100%; border:1px solid #E4EAF2; border-radius:8px; padding:8px 10px;
  font-size:13px; resize:vertical; background:#F5F8FC; transition:border-color .15s;
}
.mb-node-body:focus { border-color:#2E8DE1; outline:none; background:#fff; }

.mb-node-extras { margin-top:8px; display:flex; flex-direction:column; gap:5px; }
.mb-extra-input { border:1px solid #E4EAF2; border-radius:6px; padding:5px 8px; font-size:12px; width:100%; background:#F5F8FC; }
.mb-extra-input:focus { border-color:#2E8DE1; outline:none; background:#fff; }

/* Options */
.mb-options { margin-top:10px; border-top:1px solid #F1F5F9; padding-top:10px; display:flex; flex-direction:column; gap:7px; }
.mb-option-row { display:flex; align-items:flex-start; gap:6px; }
.mb-option-inputs { display:flex; flex-direction:column; gap:3px; flex:1; min-width:0; }
.mb-opt-label { border:1px solid #E4EAF2; border-radius:5px; padding:4px 7px; font-size:12px; width:100%; }
.mb-opt-label:focus { border-color:#2E8DE1; outline:none; }
.mb-opt-desc { border:1px solid #E4EAF2; border-radius:5px; padding:4px 7px; font-size:12px; width:100%; color:#64748B; }
.mb-opt-desc:focus { border-color:#2E8DE1; outline:none; }
.mb-char-count { font-size:10px; color:#94A3B8; text-align:right; }
.mb-option-target { display:flex; flex-direction:column; gap:3px; min-width:130px; }
.mb-target-select, .mb-target-node { font-size:12px !important; }
.mb-add-opt { background:none; border:1px dashed #CBD5E1; border-radius:6px; padding:5px 10px; font-size:12px; color:#64748B; cursor:pointer; width:100%; text-align:center; transition:all .15s; }
.mb-add-opt:hover:not(:disabled) { border-color:#2E8DE1; color:#2E8DE1; background:#EAF3FC; }
.mb-add-opt:disabled { opacity:.45; cursor:not-allowed; }

/* Add node */
.mb-add-node-area { margin-top:4px; }
.mb-add-node-btn { border:1.5px dashed #CBD5E1; border-radius:10px; padding:12px; text-align:center; color:#64748B; cursor:pointer; font-size:13px; transition:all .15s; }
.mb-add-node-btn:hover { border-color:#2E8DE1; color:#2E8DE1; background:#EAF3FC; }
.mb-type-picker { display:flex; align-items:center; gap:8px; flex-wrap:wrap; border:1.5px dashed #CBD5E1; border-radius:10px; padding:10px 14px; }
.mb-type-btn { display:flex; flex-direction:column; align-items:center; gap:4px; cursor:pointer; padding:8px 14px; border-radius:8px; font-size:12px; color:#1E2A4A; transition:background .15s; }
.mb-type-btn:hover { background:#F1F5F9; }
.mb-type-btn i { font-size:20px; color:#2E8DE1; }
.mb-type-btn-handoff i { color:#0369A1; }

/* ── Preview ──────────────────────────────────────────── */
.mb-preview { width:300px; flex-shrink:0; border-left:1px solid #E4EAF2; background:#ECE5DD; display:flex; flex-direction:column; }
.mb-preview-header { padding:12px 16px; font-size:12px; font-weight:600; color:#64748B; text-transform:uppercase; letter-spacing:.05em; background:#fff; border-bottom:1px solid #E4EAF2; }
.mb-preview-phone { flex:1; overflow-y:auto; padding:16px 12px; display:flex; flex-direction:column; align-items:flex-end; gap:6px; }
.mb-preview-empty { text-align:center; color:#94A3B8; font-size:13px; margin:auto; }

.mb-wa-bubble { background:#fff; border-radius:12px 2px 12px 12px; padding:10px 12px; max-width:240px; box-shadow:0 1px 2px rgba(0,0,0,.12); }
.mb-wa-header { font-weight:700; font-size:13px; color:#1E2A4A; margin-bottom:4px; }
.mb-wa-body { font-size:13px; color:#333; white-space:pre-line; }
.mb-wa-footer { font-size:11px; color:#94A3B8; margin-top:4px; }
.mb-wa-time { font-size:10px; color:#94A3B8; text-align:right; margin-top:4px; }

.mb-wa-btns { display:flex; flex-direction:column; gap:4px; max-width:240px; }
.mb-wa-btn { background:#fff; border-radius:8px; padding:9px 12px; font-size:13px; color:#2E8DE1; font-weight:500; text-align:center; box-shadow:0 1px 2px rgba(0,0,0,.1); }

.mb-wa-list-btn { background:#fff; border-radius:8px; padding:9px 14px; font-size:13px; color:#2E8DE1; font-weight:500; text-align:center; max-width:240px; box-shadow:0 1px 2px rgba(0,0,0,.1); }
.mb-wa-list-rows { background:#fff; border-radius:8px; padding:6px 0; max-width:240px; box-shadow:0 1px 2px rgba(0,0,0,.1); }
.mb-wa-list-row { padding:8px 14px; border-bottom:1px solid #F1F5F9; }
.mb-wa-list-row:last-child { border-bottom:none; }
.mb-wa-list-title { font-size:13px; font-weight:500; color:#1E2A4A; }
.mb-wa-list-desc { font-size:11px; color:#94A3B8; }

.mb-wa-handoff { background:#fff; border-radius:8px; padding:10px 14px; font-size:13px; color:#0369A1; max-width:240px; box-shadow:0 1px 2px rgba(0,0,0,.1); }

@media (max-width: 900px) {
  .mb-preview { display:none; }
  .mb-steps { max-width:100%; }
}
</style>
