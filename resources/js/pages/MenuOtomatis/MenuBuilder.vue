<template>
  <div class="mb-builder">

    <!-- ══════════════ HEADER ══════════════ -->
    <div class="mb-header">
      <div class="mb-header-left">
        <div class="mb-breadcrumb">
          <a href="/app/auto-reply/menu-otomatis" class="mb-bc-link">Menu Otomatis</a>
          <span class="mb-bc-sep">/</span>
          <span>{{ flowId ? 'Edit' : 'Buat baru' }}</span>
        </div>
        <input v-model="flow.name" class="mb-title-input" placeholder="Nama menu (wajib)…" maxlength="120" />
        <span class="badge ms-2" :class="flow.status === 'active' ? 'bg-success' : 'bg-secondary'">
          {{ flow.status === 'active' ? 'aktif' : 'nonaktif' }}
        </span>
      </div>
      <div class="mb-header-right">
        <!-- Buntu summary near Simpan -->
        <span v-if="buntuNodes.length" class="mb-buntu-hint">
          ⚠ {{ buntuNodes.length }} buntu
        </span>
        <button class="btn btn-sm btn-outline-secondary me-2" @click="toggleStatus">
          {{ flow.status === 'active' ? '■ Nonaktifkan' : '▶ Aktifkan' }}
        </button>
        <button class="btn btn-primary" :disabled="saving" @click="save">
          <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
          <i v-else class="bx bx-save me-1"></i>Simpan
        </button>
      </div>
    </div>

    <!-- ══════════════ SETTINGS BAR ══════════════ -->
    <div class="mb-settings-bar">
      <div class="mb-settings-summary" @click="settingsOpen = !settingsOpen">
        <i class="bx bx-equalizer me-1"></i>
        <strong class="me-1">Pengaturan</strong>
        <span class="mb-settings-divider">—</span>
        <span class="mb-settings-info">{{ settingsSummary }}</span>
        <span class="mb-settings-toggle ms-auto">{{ settingsOpen ? '✕ tutup' : 'buka ↓' }}</span>
      </div>
      <transition name="mb-slide">
        <div v-if="settingsOpen" class="mb-settings-panel">
          <div class="mb-settings-grid">
            <!-- Pemicu -->
            <div class="mb-setting-item">
              <label class="mb-label">Pemicu</label>
              <select v-model="triggerMode" class="form-select form-select-sm">
                <option value="default">Pesan apa saja ⚠️</option>
                <option value="contains">Mengandung kata</option>
                <option value="exact">Sama persis (rekomendasi)</option>
              </select>
            </div>

            <!-- Kata kunci -->
            <div class="mb-setting-item" v-if="triggerMode !== 'default'">
              <label class="mb-label">Kata Kunci <small class="text-muted">(Enter untuk tambah)</small></label>
              <div class="mb-tag-input" @click="$refs.kwInput.focus()">
                <span v-for="(kw, i) in flow.trigger_keywords" :key="i" class="mb-tag">
                  {{ kw }} <button @click.stop="removeKeyword(i)">×</button>
                </span>
                <input ref="kwInput" v-model="kwDraft"
                  @keydown.enter.prevent="addKeyword"
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

            <!-- Fallback — radio dengan deskripsi (1E) -->
            <div class="mb-setting-item mb-setting-fallback">
              <label class="mb-label">Kalau pelanggan ketik bebas (bukan tap tombol)</label>
              <div class="mb-fallback-radios">
                <label class="mb-fallback-radio">
                  <input type="radio" v-model="flow.fallback_action" value="ai_agent" />
                  <div class="mb-fr-content">
                    <span class="mb-fr-title">🤖 Jawab pakai AI Chatbot</span>
                    <span class="mb-fr-desc">Rekomendasi kalau AI aktif</span>
                  </div>
                </label>
                <label class="mb-fallback-radio">
                  <input type="radio" v-model="flow.fallback_action" value="repeat_menu" />
                  <div class="mb-fr-content">
                    <span class="mb-fr-title">↩ Ulangi menu ini</span>
                    <span class="mb-fr-desc">Kirim ulang langkah pertama</span>
                  </div>
                </label>
                <label class="mb-fallback-radio">
                  <input type="radio" v-model="flow.fallback_action" value="manual_reply" />
                  <div class="mb-fr-content">
                    <span class="mb-fr-title">👤 Teruskan ke agen (CS)</span>
                    <span class="mb-fr-desc">Mode handoff, CS yang balas</span>
                  </div>
                </label>
              </div>
            </div>

            <!-- Sesi -->
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
      </transition>
    </div>

    <!-- ══════════════ TABS ══════════════ -->
    <div class="mb-tabs">
      <button :class="['mb-tab', {active: activeTab === 'steps'}]" @click="activeTab = 'steps'">
        <i class="bx bx-list-ul me-1"></i>Langkah-langkah
      </button>
      <button :class="['mb-tab', {active: activeTab === 'map'}]" @click="activeTab = 'map'">
        <i class="bx bx-sitemap me-1"></i>Peta alur
      </button>
    </div>

    <!-- ══════════════ MAIN CONTENT ══════════════ -->
    <div class="mb-content">

      <!-- ── LEFT: Steps ── -->
      <div class="mb-steps">

        <!-- TAB: Langkah-langkah -->
        <div v-show="activeTab === 'steps'" class="mb-steps-list">

          <!-- Buntu summary -->
          <div v-if="buntuNodes.length" class="mb-buntu-summary">
            <i class="bx bx-error-circle me-1"></i>
            <strong>{{ buntuNodes.length }} langkah buntu</strong> — pelanggan mentok di situ. Klik badge ⚠ di kartu untuk perbaiki.
          </div>

          <!-- Node cards -->
          <div v-for="(node, ni) in nodes" :key="node.temp_id"
            class="mb-node-card"
            :class="{
              'mb-node-selected': selectedTempId === node.temp_id,
              'mb-node-start':    startTempId === node.temp_id,
              'mb-node-buntu':    isBuntu(node),
            }"
            :ref="'node_' + node.temp_id"
            @click="selectedTempId = node.temp_id">

            <!-- ── Card Header (1B) ── -->
            <div class="mb-node-header">
              <div class="mb-node-badges">
                <span class="mb-node-num">L{{ ni + 1 }}</span>
                <span v-if="nodeTitle(node)" class="mb-node-title-txt">· {{ nodeTitle(node) }}</span>
                <span v-if="startTempId === node.temp_id" class="mb-badge-start">🏠 mulai</span>
                <!-- Buntu badge (1D) -->
                <button v-if="isBuntu(node)" class="mb-badge-buntu" @click.stop="quickFixBuntu(node)"
                  title="Klik: tambah tombol ↩ Menu otomatis">
                  ⚠ Buntu — klik perbaiki
                </button>
              </div>
              <div class="mb-node-actions">
                <button class="mb-icon-btn" title="Jadikan langkah awal"
                  v-if="startTempId !== node.temp_id"
                  @click.stop="startTempId = node.temp_id; previewStart()">
                  <i class="bx bx-home-alt"></i>
                </button>
                <button class="mb-icon-btn mb-icon-danger" title="Hapus langkah" @click.stop="removeNode(ni)">
                  <i class="bx bx-trash"></i>
                </button>
              </div>
            </div>

            <!-- ── Body text ── -->
            <textarea v-model="node.body_text" class="mb-node-body"
              :placeholder="node.type === 'handoff' ? 'Pesan handoff ke CS… (opsional)' : 'Teks pesan…'"
              @click.stop @input="onBodyInput($event, node)"></textarea>

            <!-- ── Cara lanjut chips (1A) ── -->
            <div class="mb-cara-lanjut" @click.stop>
              <span class="mb-cl-label">Cara lanjut:</span>
              <div class="mb-cl-chips">
                <button :class="['mb-cl-chip', { active: node.type === 'buttons' }]"
                  @click.stop="setNodeType(node, 'buttons')">
                  <i class="bx bx-layout"></i> Tombol
                </button>
                <button :class="['mb-cl-chip', { active: node.type === 'list' }]"
                  @click.stop="setNodeType(node, 'list')">
                  <i class="bx bx-list-ul"></i> Daftar pilihan
                  <span v-if="node.type === 'list'" class="mb-cl-count">({{ node.options.length }} item → daftar)</span>
                </button>
                <button :class="['mb-cl-chip', { active: node.type === 'handoff' }]"
                  @click.stop="setNodeType(node, 'handoff')">
                  <i class="bx bx-user-voice"></i> Sambung agen
                </button>
                <button :class="['mb-cl-chip', { active: node.type === 'message' }]"
                  @click.stop="setNodeType(node, 'message')">
                  <i class="bx bx-message-detail"></i> Tanpa pilihan
                </button>
                <button class="mb-cl-chip mb-cl-disabled" disabled title="Segera hadir">
                  <i class="bx bx-link-external"></i> Tombol website
                  <span class="mb-cl-soon">soon</span>
                </button>
              </div>
            </div>

            <!-- ── Header/Footer toggle (collapsible) ── -->
            <div v-if="node.type === 'buttons' || node.type === 'list'" @click.stop>
              <button class="mb-extras-toggle" @click.stop="toggleExtras(node)">
                {{ node._showExtras ? '▲ Sembunyikan header / footer' : '+ Tambah header / footer' }}
              </button>
              <div v-if="node._showExtras" class="mb-node-extras">
                <input v-model="node.header" class="mb-extra-input" placeholder="Header (opsional, max 60)" maxlength="60" @click.stop />
                <input v-model="node.footer" class="mb-extra-input" placeholder="Footer (opsional, max 60)" maxlength="60" @click.stop />
                <template v-if="node.type === 'list'">
                  <label class="mb-extra-label">Teks tombol buka daftar</label>
                  <input v-model="node.list_button_label" class="mb-extra-input"
                    placeholder="mis. Lihat pilihan" maxlength="20" @click.stop />
                </template>
              </div>
            </div>

            <!-- ── Options editor (buttons/list) ── -->
            <div class="mb-options" v-if="node.type === 'buttons' || node.type === 'list'" @click.stop>

              <!-- Auto-suggest: pindah ke Daftar saat tombol ke-4 (1A) -->
              <div v-if="node.type === 'buttons' && node.options.length >= 3" class="mb-list-hint">
                <i class="bx bx-info-circle me-1"></i>Mau lebih dari 3 pilihan?
                <button class="mb-list-hint-btn" @click.stop="setNodeType(node, 'list')">Ubah ke Daftar ↗</button>
              </div>

              <div v-for="(opt, oi) in node.options" :key="oi" class="mb-option-row">
                <i class="bx bx-menu me-1 text-muted mb-opt-grip" style="cursor:grab" title="Geser untuk urutkan"></i>
                <div class="mb-option-inputs">
                  <input v-model="opt.label" class="mb-opt-label"
                    :placeholder="opt.kind === 'button' ? 'Label tombol (max 20)' : 'Judul baris (max 24)'"
                    :maxlength="opt.kind === 'button' ? 20 : 24" />
                  <span v-if="opt.label.length >= 15" class="mb-char-count"
                    :class="{ 'text-danger': opt.label.length >= (opt.kind === 'button' ? 20 : 24) }">
                    {{ opt.label.length }}/{{ opt.kind === 'button' ? 20 : 24 }}
                  </span>
                  <input v-if="opt.kind === 'list_row'" v-model="opt.description"
                    class="mb-opt-desc" placeholder="Deskripsi (opsional, max 72)" maxlength="72" />
                </div>
                <!-- Target: compact label + dropdown -->
                <div class="mb-option-target">
                  <span class="mb-opt-target-label"
                    :class="{ 'mb-opt-clickable': opt.target_action === 'goto_node' && opt.target_temp_id }"
                    @click.stop="opt.target_action === 'goto_node' && opt.target_temp_id ? jumpToCardNode(opt.target_temp_id) : null"
                    :title="opt.target_action === 'goto_node' && opt.target_temp_id ? 'Klik untuk loncat ke langkah tujuan' : ''">{{ optTargetLabel(opt) }}</span>
                  <select :value="combinedValue(opt)"
                    @change="setCombined(opt, $event.target.value)"
                    class="form-select form-select-sm mb-target-unified">
                    <optgroup label="— Ke langkah lain">
                      <template v-for="(n2, n2i) in nodes" :key="n2.temp_id">
                        <option v-if="n2.temp_id !== node.temp_id" :value="'goto:' + n2.temp_id">
                          L{{ n2i + 1 }} · {{ ringkasNode(n2) }}
                        </option>
                      </template>
                    </optgroup>
                    <option value="handoff">👤 Sambungkan ke CS</option>
                    <option value="back_to_start">↩ Kembali ke awal</option>
                    <option value="end">✋ Selesai</option>
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

            <!-- ── Anti-buntu (Tanpa pilihan mode, 1C) ── -->
            <div v-if="node.type === 'message'" class="mb-antibuntu" @click.stop>
              <div class="mb-ab-label">Cara pelanggan lanjut:</div>
              <div class="mb-ab-checks">
                <label class="mb-ab-check">
                  <input type="checkbox"
                    :checked="node._cont && node._cont.menu"
                    @change="toggleCont(node, 'menu', $event.target.checked)" />
                  <span>↩ Kembali ke menu</span>
                </label>
                <label class="mb-ab-check">
                  <input type="checkbox"
                    :checked="node._cont && node._cont.cs"
                    @change="toggleCont(node, 'cs', $event.target.checked)" />
                  <span>👤 Sambungkan ke CS</span>
                </label>
                <label class="mb-ab-check">
                  <input type="checkbox"
                    :checked="node._cont && node._cont.goto"
                    @change="toggleCont(node, 'goto', $event.target.checked)" />
                  <span>➡ Lanjut ke langkah…</span>
                </label>
              </div>
              <!-- Goto target selector -->
              <div v-if="node._cont && node._cont.goto" class="mb-ab-goto">
                <select v-model="node._cont.goto_target" class="form-select form-select-sm">
                  <option value="">-- Pilih langkah tujuan --</option>
                  <template v-for="(n2, n2i) in nodes" :key="n2.temp_id">
                    <option v-if="n2.temp_id !== node.temp_id" :value="n2.temp_id">
                      L{{ n2i + 1 }} · {{ nodeTitle(n2) || ('Langkah ' + (n2i + 1)) }}
                    </option>
                  </template>
                </select>
              </div>
              <!-- Quick chips preview -->
              <div v-if="node._cont && (node._cont.menu || node._cont.cs || (node._cont.goto && node._cont.goto_target))" class="mb-ab-preview">
                <span v-if="node._cont.menu" class="mb-ab-chip">↩ Menu</span>
                <span v-if="node._cont.cs" class="mb-ab-chip">👤 CS</span>
                <span v-if="node._cont.goto && node._cont.goto_target" class="mb-ab-chip">
                  ➡ L{{ gotoTargetIndex(node) }}
                </span>
              </div>
            </div>

          </div><!-- end v-for node -->

          <!-- Template picker (no nodes) -->
          <div v-if="nodes.length === 0" class="mb-tpl-picker">
            <div class="mb-tpl-title">Mulai dari contoh — tinggal ganti teksnya</div>
            <div class="mb-tpl-grid">
              <button class="mb-tpl-card" @click="applyTemplate('cs')">
                <i class="bx bx-headphone"></i><span>Menu Bantuan CS</span><small>Sapaan + FAQ + Chat CS</small>
              </button>
              <button class="mb-tpl-card" @click="applyTemplate('faq')">
                <i class="bx bx-help-circle"></i><span>Tanya Jawab (FAQ)</span><small>Daftar pertanyaan umum</small>
              </button>
              <button class="mb-tpl-card" @click="applyTemplate('katalog')">
                <i class="bx bx-store"></i><span>Katalog Produk</span><small>Tampilkan produk + order</small>
              </button>
              <button class="mb-tpl-card mb-tpl-blank" @click="applyTemplate('kosong')">
                <i class="bx bx-plus"></i><span>Mulai kosong</span><small>Susun sendiri dari nol</small>
              </button>
            </div>
          </div>

          <!-- Tambah Langkah -->
          <div class="mb-add-node-area">
            <div v-if="!addingNode" class="mb-add-node-btn" @click="addingNode = true">
              <i class="bx bx-plus"></i> Tambah langkah
            </div>
            <div v-else class="mb-type-picker">
              <div class="mb-type-btn" @click="addNode('buttons')">
                <i class="bx bx-layout"></i><span>Tombol</span>
              </div>
              <div class="mb-type-btn" @click="addNode('list')">
                <i class="bx bx-list-ul"></i><span>Daftar</span>
              </div>
              <div class="mb-type-btn" @click="addNode('message')">
                <i class="bx bx-message-detail"></i><span>Info</span>
              </div>
              <div class="mb-type-btn mb-type-btn-handoff" @click="addNode('handoff')">
                <i class="bx bx-user-voice"></i><span>Serah ke CS</span>
              </div>
              <button class="mb-icon-btn ms-2" @click="addingNode = false">
                <i class="bx bx-x"></i>
              </button>
            </div>
          </div>

        </div><!-- end steps list -->

        <!-- TAB: Peta alur (2a — Mermaid diagram) -->
        <div v-show="activeTab === 'map'" class="mb-map-view">
          <div class="mb-map-toolbar">
            <button class="mb-map-refresh-btn" @click="renderMermaid">
              <i class="bx bx-refresh me-1"></i>Rapikan
            </button>
            <span class="mb-map-hint">
              <i class="bx bx-info-circle me-1"></i>Klik kotak → buka langkahnya
            </span>
            <span v-if="mermaidError" class="mb-map-error-inline">⚠ {{ mermaidError }}</span>
          </div>
          <div class="mb-map-container">
            <!-- Loading: dikelola Vue, sibling dari target Mermaid -->
            <div class="mb-map-loading" v-if="!mermaidReady">
              <i class="bx bx-loader-circle bx-spin me-1"></i>Memuat diagram…
            </div>
            <!-- Target render Mermaid: div KOSONG, Vue tidak menyentuh isinya -->
            <div class="mb-map-svg" ref="mermaidContainer"></div>
          </div>
        </div>

      </div><!-- end mb-steps -->

      <!-- ── RIGHT: Preview + Coba kirim (1F) ── -->
      <div class="mb-preview">
        <div class="mb-preview-header">
          <i class="bx bx-mobile me-1"></i>Preview WA — tap buat coba
        </div>

        <!-- Toko header simulasi -->
        <div class="mb-pv-toko">Toko Kamu</div>

        <div class="mb-preview-phone" ref="previewScroll">
          <template v-if="previewLog.length > 0">
            <div v-for="(msg, mi) in previewLog" :key="mi" class="mb-pv-row"
              :class="msg.who === 'user' ? 'mb-pv-row-user' : 'mb-pv-row-bot'">

              <template v-if="msg.who === 'bot'">
                <div class="mb-pv-bot-card">
                  <div v-if="msg.header" class="mb-pv-header">{{ msg.header }}</div>
                  <div class="mb-pv-body">{{ msg.text || '…' }}</div>
                  <div v-if="msg.footer" class="mb-pv-footer">{{ msg.footer }}</div>
                  <div class="mb-pv-time">{{ now }}</div>
                </div>
                <!-- Buttons/list hanya di bubble terakhir -->
                <template v-if="mi === previewLog.length - 1 && msg.buttons && msg.buttons.length">
                  <template v-if="msg.type === 'list'">
                    <div class="mb-pv-list-trigger" @click="showListPanel = !showListPanel">
                      <i class="bx bx-list-ul me-1"></i>{{ msg.listLabel || 'Pilih' }}
                    </div>
                    <div v-if="showListPanel" class="mb-pv-list-panel">
                      <div v-for="btn in msg.buttons" :key="btn.label"
                        class="mb-pv-list-row" @click="previewTap(btn); showListPanel = false">
                        {{ btn.label }}
                      </div>
                    </div>
                  </template>
                  <div v-else class="mb-pv-btns">
                    <button v-for="btn in msg.buttons" :key="btn.label"
                      class="mb-pv-btn" @click="previewTap(btn)">
                      {{ btn.label || '…' }}
                    </button>
                  </div>
                </template>
              </template>

              <div v-else class="mb-pv-user-bubble">{{ msg.text }}</div>
            </div>
          </template>
          <div v-else class="mb-preview-empty">
            <i class="bx bx-message-dots fs-2 text-muted mb-2 d-block"></i>
            Belum ada langkah untuk dicoba
          </div>
        </div>

        <div class="mb-preview-footer">
          <button class="mb-pv-reset" @click="previewStart">
            <i class="bx bx-refresh me-1"></i>Ulang preview
          </button>
        </div>

        <!-- ── Coba kirim ke WA (1F) ── -->
        <div class="mb-testsend">
          <div class="mb-ts-header">
            <i class="bx bx-send me-1"></i>Coba kirim ke WA
          </div>
          <div class="mb-ts-body">
            <input v-model="testPhone" class="mb-ts-input" placeholder="0812…" type="tel"
              :disabled="testSending || !flowId" />
            <button class="mb-ts-btn" @click="doTestSend"
              :disabled="!flowId || testSending || !testPhone.trim()">
              <span v-if="testSending" class="spinner-border spinner-border-sm me-1"></span>
              <i v-else class="bx bx-navigation me-1"></i>
              Kirim tes
            </button>
          </div>
          <div v-if="!flowId" class="mb-ts-hint">
            <i class="bx bx-info-circle me-1"></i>Simpan flow dulu sebelum tes kirim.
          </div>
          <div class="mb-ts-note">
            Mengirimkan langkah pertama saja. Tes penuh: aktifkan flow lalu chat dari nomor sendiri.
          </div>
        </div>
      </div><!-- end mb-preview -->

    </div><!-- end mb-content -->

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
      activeTab: 'steps',
      settingsOpen: false,
      flow: {
        name: '',
        trigger_type: 'keyword',
        keyword_match: 'exact',
        trigger_keywords: [],
        channels: [],
        fallback_action: 'ai_agent',
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
      previewLog: [],
      showListPanel: false,
      testPhone: '',
      testSending: false,
      mermaidError: '',
      mermaidReady: false,
      TEMPLATES: {
        cs: {
          start: 'start',
          nodes: [
            { key: 'start', type: 'buttons', body: 'Halo! 👋 Terima kasih sudah menghubungi kami. Ada yang bisa dibantu?',
              opts: [{ label: 'Tanya (FAQ)', act: 'goto_node', to: 'faq' }, { label: 'Chat CS', act: 'handoff' }] },
            { key: 'faq', type: 'list', listLabel: 'Pilih', body: 'Pilih pertanyaan yang sering ditanyakan:',
              opts: [{ label: 'Cara Pesan', act: 'goto_node', to: 'pesan' }, { label: 'Jam & Lokasi', act: 'goto_node', to: 'jam' }, { label: 'Kembali ke Menu', act: 'back_to_start' }] },
            { key: 'pesan', type: 'message', body: 'Cara pesan gampang: chat kami *nama produk + jumlah*, nanti CS bantu proses ya 😊' },
            { key: 'jam', type: 'message', body: '🕐 Buka Senin–Sabtu 09.00–17.00 WIB\n📍 Alamat: (isi alamat kamu)' },
            { key: 'ho', type: 'handoff', body: 'Baik, kami sambungkan ke CS ya. Mohon tunggu sebentar 🙏' },
          ],
        },
        faq: {
          start: 'start',
          nodes: [
            { key: 'start', type: 'list', listLabel: 'Pilih', body: 'Halo! Mau tanya apa? Pilih di bawah 👇',
              opts: [{ label: 'Harga', act: 'goto_node', to: 'harga' }, { label: 'Pengiriman', act: 'goto_node', to: 'kirim' }, { label: 'Garansi', act: 'goto_node', to: 'garansi' }, { label: 'Chat CS', act: 'handoff' }] },
            { key: 'harga', type: 'message', body: '💰 Harga mulai Rp— (isi). Katalog lengkap: (link)' },
            { key: 'kirim', type: 'message', body: '📦 Pengiriman 1–3 hari via (ekspedisi). Ongkir sesuai lokasi.' },
            { key: 'garansi', type: 'message', body: '✅ Garansi 7 hari tukar kalau ada cacat produksi.' },
            { key: 'ho', type: 'handoff', body: 'Oke, kami sambungkan ke CS ya 🙏' },
          ],
        },
        katalog: {
          start: 'start',
          nodes: [
            { key: 'start', type: 'buttons', body: 'Halo! 👋 Lihat produk kami:',
              opts: [{ label: 'Produk A', act: 'goto_node', to: 'a' }, { label: 'Produk B', act: 'goto_node', to: 'b' }, { label: 'Order / CS', act: 'handoff' }] },
            { key: 'a', type: 'message', body: '✨ *Produk A* — (deskripsi singkat). Harga Rp—. Foto/link: (isi)' },
            { key: 'b', type: 'message', body: '✨ *Produk B* — (deskripsi singkat). Harga Rp—.' },
            { key: 'ho', type: 'handoff', body: 'Siap! Kami bantu proses order kamu 🙏' },
          ],
        },
      },
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

    // 1E: compact summary string
    settingsSummary() {
      const trig = this.flow.trigger_type === 'default' ? 'Semua pesan'
        : this.flow.keyword_match === 'exact' ? 'Sama persis' : 'Mengandung kata';
      const chArr = (this.flow.channels || []).filter(c => c);
      const ch = chArr.length > 0
        ? chArr.map(id => { const d = this.devices.find(d => d.id === id); return d ? d.phone : id.slice(0, 8); }).join(', ')
        : 'Semua WABA';
      const fb = { repeat_menu: 'Ulangi menu', ai_agent: 'AI chatbot', manual_reply: 'Teruskan ke agen' };
      return [trig, ch, `Ketik bebas → ${fb[this.flow.fallback_action] || '?'}`, `${this.flow.session_timeout_min} mnt`].join(' · ');
    },

    // 1D: buntu = message type dengan options kosong (baca data real, BUKAN _cont)
    buntuNodes() {
      return this.nodes.reduce((acc, n, i) => {
        if (this.isBuntu(n)) acc.push(i);
        return acc;
      }, []);
    },
  },

  watch: {
    startTempId() { this.previewStart(); },
    activeTab(val) {
      if (val === 'map') this.$nextTick(() => this.renderMermaid());
    },
    nodes: {
      deep: true,
      handler() {
        if (this.activeTab === 'map') {
          clearTimeout(this._mermaidTimer);
          this._mermaidTimer = setTimeout(() => this.renderMermaid(), 400);
        }
      },
    },
  },

  methods: {
    newTemp() { return 'n' + (++this.seq); },

    // 1B: title dari 4 kata pertama body_text
    nodeTitle(node) {
      if (!node.body_text || !node.body_text.trim()) return null;
      const words = node.body_text.trim().replace(/\n/g, ' ').split(/\s+/).slice(0, 4).join(' ');
      return words.length > 30 ? words.slice(0, 30) + '…' : words;
    },

    // 1D: buntu berdasarkan data node (bukan _cont)
    isBuntu(node) {
      return node.type === 'message' && (!node.options || node.options.length === 0);
    },

    // 1D: quick fix — tambah tombol ↩ Menu ke node buntu
    quickFixBuntu(node) {
      // Inisialisasi _cont jika belum ada
      if (!node._cont) node._cont = { menu: false, cs: false, goto: false, goto_target: '' };
      node._cont.menu = true;
      // Langsung tampilkan sebagai antibuntu checkbox checked
    },

    // 1A: ganti tipe node dengan konversi yang benar
    setNodeType(node, mode) {
      if (node.type === mode) return;

      if (mode === 'buttons') {
        // Konversi list_row → button, truncate ke maks 3, truncate label ke 20
        node.options.forEach(o => {
          o.kind = 'button';
          if (o.label.length > 20) o.label = o.label.slice(0, 20);
        });
        if (node.options.length > 3) node.options.splice(3);
        node.type = 'buttons';
        if (node.options.length === 0) this.addOption(node);
        node._cont = null;

      } else if (mode === 'list') {
        // Konversi button → list_row, set list_button_label default
        node.options.forEach(o => { o.kind = 'list_row'; });
        if (!node.list_button_label) node.list_button_label = 'Pilih';
        node.type = 'list';
        if (node.options.length === 0) this.addOption(node);
        node._cont = null;

      } else if (mode === 'handoff') {
        node.options = [];        // handoff gak punya options
        node.type = 'handoff';
        node._cont = null;

      } else if (mode === 'message') {
        node.options = [];        // message mulai kosong
        node.type = 'message';
        // Inisialisasi _cont dengan default Menu+CS ON
        node._cont = { menu: true, cs: true, goto: false, goto_target: '' };
      }
      this.previewStart();
    },

    toggleExtras(node) {
      node._showExtras = !node._showExtras;
    },

    // 1C: toggle checkbox _cont
    toggleCont(node, key, val) {
      if (!node._cont) {
        node._cont = { menu: false, cs: false, goto: false, goto_target: '' };
      }
      node._cont[key] = val;
      if (key === 'goto' && !val) node._cont.goto_target = '';
    },

    // Index langkah tujuan dari _cont.goto_target
    gotoTargetIndex(node) {
      if (!node._cont || !node._cont.goto_target) return '?';
      const idx = this.nodes.findIndex(n => n.temp_id === node._cont.goto_target);
      return idx >= 0 ? idx + 1 : '?';
    },

    // 1B: label compact untuk target option
    optTargetLabel(opt) {
      if (opt.target_action === 'goto_node' && opt.target_temp_id) {
        const idx = this.nodes.findIndex(n => n.temp_id === opt.target_temp_id);
        return idx >= 0 ? `→ L${idx + 1}` : '→ ?';
      }
      const labels = { handoff: '→ agen', back_to_start: '↩ menu', end: '✋' };
      return labels[opt.target_action] || '';
    },

    // Poles 2: auto-grow textarea
    autoGrow(el) {
      if (!el) return;
      el.style.height = 'auto';
      el.style.height = Math.min(el.scrollHeight, 400) + 'px';
    },
    onBodyInput(evt, node) {
      this.autoGrow(evt.target);
      this.previewStart();
    },
    growAllBodies() {
      this.$nextTick(() => {
        document.querySelectorAll('.mb-node-body').forEach(el => this.autoGrow(el));
      });
    },

    addKeyword() {
      const kw = this.kwDraft.trim();
      if (kw && !this.flow.trigger_keywords.includes(kw)) this.flow.trigger_keywords.push(kw);
      this.kwDraft = '';
    },
    removeKeyword(i) { this.flow.trigger_keywords.splice(i, 1); },

    addNode(type) {
      const tid = this.newTemp();
      const node = {
        temp_id: tid,
        type,
        body_text: '',
        header: '',
        footer: '',
        list_button_label: type === 'list' ? 'Pilih' : '',
        position: this.nodes.length + 1,
        options: [],
        // _cont: inisialisasi untuk message (UI helper, TIDAK disimpan ke server)
        _cont: type === 'message' ? { menu: true, cs: true, goto: false, goto_target: '' } : null,
        _showExtras: false,
      };
      this.nodes.push(node);
      if (!this.startTempId) this.startTempId = tid;
      this.selectedTempId = tid;
      this.addingNode = false;
      // Tambah 1 opsi default untuk tombol/list
      if (type === 'buttons' || type === 'list') this.addOption(node);
    },

    removeNode(i) {
      if (!confirm('Hapus langkah ini? Opsi yang mengarah ke langkah ini akan kehilangan target.')) return;
      const tid = this.nodes[i].temp_id;
      this.nodes.splice(i, 1);
      if (this.startTempId === tid) this.startTempId = this.nodes[0]?.temp_id || null;
      if (this.selectedTempId === tid) this.selectedTempId = this.nodes[0]?.temp_id || null;
      this.nodes.forEach(n => {
        n.options.forEach(o => {
          if (o.target_temp_id === tid) { o.target_action = 'end'; o.target_temp_id = ''; }
        });
        if (n._cont && n._cont.goto_target === tid) n._cont.goto_target = '';
      });
      this.reindex();
      this.previewStart();
    },
    reindex() { this.nodes.forEach((n, i) => n.position = i + 1); },

    addOption(node) {
      const kind = node.type === 'buttons' ? 'button' : 'list_row';
      node.options.push({
        kind, label: '', description: '',
        order: node.options.length + 1,
        target_action: 'end',
        target_temp_id: '',
      });
    },
    removeOption(node, i) {
      node.options.splice(i, 1);
      node.options.forEach((o, j) => o.order = j + 1);
    },

    toggleStatus() { this.flow.status = this.flow.status === 'active' ? 'inactive' : 'active'; },

    combinedValue(opt) {
      return opt.target_action === 'goto_node' ? ('goto:' + (opt.target_temp_id || '')) : opt.target_action;
    },
    setCombined(opt, val) {
      if (val.startsWith('goto:')) {
        opt.target_action = 'goto_node';
        opt.target_temp_id = val.slice(5);
      } else {
        opt.target_action = val;
        opt.target_temp_id = '';
      }
    },
    ringkasNode(n2) {
      return n2.body_text ? n2.body_text.slice(0, 20) : ({ message: 'Pesan', buttons: 'Tombol', list: 'Daftar', handoff: 'CS' }[n2.type] || n2.type);
    },

    // Build continuation options dari _cont (untuk save + preview)
    buildContOptions(node) {
      if (!node._cont) return [];
      const opts = [];
      if (node._cont.menu) {
        opts.push({ kind: 'button', label: 'Menu', description: '', order: opts.length + 1, target_action: 'back_to_start', target_temp_id: '' });
      }
      if (node._cont.cs) {
        opts.push({ kind: 'button', label: 'CS', description: '', order: opts.length + 1, target_action: 'handoff', target_temp_id: '' });
      }
      if (node._cont.goto && node._cont.goto_target) {
        opts.push({ kind: 'button', label: 'Lanjut', description: '', order: opts.length + 1, target_action: 'goto_node', target_temp_id: node._cont.goto_target });
      }
      return opts.slice(0, 3); // Maks 3 sesuai batas WA buttons
    },

    // ── Validasi ──
    validate() {
      const errs = [];
      if (!this.flow.name.trim()) errs.push('Nama menu wajib diisi.');
      if (this.nodes.length === 0) errs.push('Minimal 1 langkah.');
      if (!this.startTempId) errs.push('Langkah awal belum ditentukan.');
      if (this.flow.trigger_type === 'keyword' && this.flow.trigger_keywords.length === 0) {
        errs.push('Mode keyword butuh minimal 1 kata kunci.');
      }
      this.nodes.forEach((n, i) => {
        // Hitung tipe + opsi efektif (sama dengan save)
        let effType = n.type;
        let effOpts = n.options;
        if (n.type === 'message') {
          const contOpts = this.buildContOptions(n);
          if (contOpts.length > 0) { effType = 'buttons'; effOpts = contOpts; }
        }
        if ((effType === 'buttons' || effType === 'list') && effOpts.length === 0) {
          errs.push(`Langkah ${i + 1}: butuh minimal 1 opsi.`);
        }
        effOpts.forEach((o, oi) => {
          if (!o.label || !o.label.trim()) errs.push(`Langkah ${i + 1}, opsi ${oi + 1}: label wajib diisi.`);
          if (o.target_action === 'goto_node' && !o.target_temp_id) {
            errs.push(`Langkah ${i + 1}, opsi ${oi + 1}: pilih langkah tujuan.`);
          }
        });
      });
      return errs;
    },

    // ── Save — payload TIDAK BERUBAH dari kontrak ──
    async save() {
      this.errors = this.validate();
      if (this.errors.length) return;
      this.saving = true;
      try {
        const payload = {
          ...this.flow,
          id: this.flowId,
          start_temp_id: this.startTempId,
          nodes: this.nodes.map((n, i) => {
            // 1C: message + _cont → save sebagai buttons dengan managed options
            let saveType = n.type;
            let saveOpts = n.options;
            if (n.type === 'message') {
              const contOpts = this.buildContOptions(n);
              if (contOpts.length > 0) { saveType = 'buttons'; saveOpts = contOpts; }
            }
            return {
              temp_id: n.temp_id,
              type: saveType,
              body_text: n.body_text || null,
              header: n.header || null,
              footer: n.footer || null,
              list_button_label: n.list_button_label || null,
              position: i + 1,
              options: saveOpts,
            };
          }),
        };
        const { data } = await this.$axios.post('auto-reply/menu-otomatis/save', payload);
        this.flowId = data.id;
        this.$showToast('Menu otomatis tersimpan!', 'success');
        window.history.replaceState({}, '', `/app/auto-reply/menu-otomatis/${data.id}/edit`);
      } catch (e) {
        const errs = e.response?.data?.errors;
        const msg = errs ? Object.values(errs).flat().join(', ') : (e.response?.data?.message || 'Gagal menyimpan');
        this.$showToast(msg, 'error');
      } finally {
        this.saving = false;
      }
    },

    // 1F: test send
    async doTestSend() {
      if (!this.flowId || !this.testPhone.trim()) return;
      this.testSending = true;
      try {
        const { data } = await this.$axios.post(
          `auto-reply/menu-otomatis/${this.flowId}/test-send`,
          { phone: this.testPhone }
        );
        this.$showToast(data.message || (data.status === 'ok' ? 'Terkirim!' : 'Gagal'), data.status === 'ok' ? 'success' : 'error');
      } catch (e) {
        const msg = e.response?.data?.message || 'Gagal kirim. Cek device WABA aktif.';
        this.$showToast(msg, 'error');
      } finally {
        this.testSending = false;
      }
    },

    // ── Preview interaktif ──
    // Poles 5: loncat ke kartu langkah (tanpa pindah tab)
    jumpToCardNode(tempId) {
      this.selectedTempId = tempId;
      this.$nextTick(() => {
        const refKey = 'node_' + tempId;
        const ref = this.$refs[refKey];
        const card = Array.isArray(ref) ? ref[0] : ref;
        const el = card?.$el || card;
        if (el) {
          el.scrollIntoView({ behavior: 'smooth', block: 'center' });
          el.classList.add('mb-node-highlight');
          setTimeout(() => el.classList.remove('mb-node-highlight'), 1200);
        }
      });
    },
    scrollPreview() {
      this.$nextTick(() => { const el = this.$refs.previewScroll; if (el) el.scrollTop = el.scrollHeight; });
    },
    previewStart() {
      this.showListPanel = false;
      if (!this.startTempId || this.nodes.length === 0) { this.previewLog = []; return; }
      this.previewLog = [{ who: 'user', text: 'halo' }];
      this.pushPreviewNode(this.startTempId);
    },
    pushPreviewNode(tempId) {
      const n = this.nodes.find(x => x.temp_id === tempId);
      if (!n) return;
      // Hitung tipe + tombol efektif (termasuk _cont untuk message)
      let effType = n.type;
      let effBtns = (n.options || []).map(o => ({ label: o.label, action: o.target_action, target: o.target_temp_id }));
      if (n.type === 'message') {
        const contOpts = this.buildContOptions(n);
        if (contOpts.length > 0) {
          effType = 'buttons';
          effBtns = contOpts.map(o => ({ label: o.label, action: o.target_action, target: o.target_temp_id }));
        }
      }
      this.previewLog.push({
        who: 'bot',
        text: n.body_text || ('(' + ({ message: 'Pesan', buttons: 'Tombol', list: 'Daftar', handoff: 'CS' }[n.type] || n.type) + ')'),
        header: n.header || null,
        footer: n.footer || null,
        listLabel: n.list_button_label || null,
        type: effType,
        buttons: effBtns,
      });
      this.scrollPreview();
    },
    previewTap(btn) {
      this.showListPanel = false;
      this.previewLog.push({ who: 'user', text: btn.label || '…' });
      if (btn.action === 'goto_node' && btn.target) {
        this.pushPreviewNode(btn.target);
      } else if (btn.action === 'back_to_start') {
        this.pushPreviewNode(this.startTempId);
      } else if (btn.action === 'handoff') {
        this.previewLog.push({ who: 'bot', text: 'Menghubungkan ke CS kami… 👤', buttons: [], type: 'message' });
      } else {
        this.previewLog.push({ who: 'bot', text: '✅ Percakapan selesai.', buttons: [], type: 'message' });
      }
      this.scrollPreview();
    },

    // ── Peta alur (Fase 2a) ──────────────────────────────
    /**
     * Build string definisi Mermaid graph LR dari this.nodes.
     * Materialisi _cont untuk node message supaya edge-nya ikut tergambar.
     */
    buildMermaidDef() {
      if (!this.nodes.length) return null;
      const lines = ['graph LR'];
      const safe = (str, max = 22) => {
        if (!str) return '';
        return str.trim().replace(/"/g, "'").replace(/[\n\r]/g, ' ').slice(0, max);
      };
      // ID mermaid: awali n_, replace semua non-alphanum
      const mid = (tid) => 'n_' + tid.replace(/[^a-zA-Z0-9]/g, '_');

      let needAgen = false;
      let needEnd = false;

      // Definisi node kotak
      this.nodes.forEach((node, ni) => {
        const id = mid(node.temp_id);
        const title = safe(this.nodeTitle(node) || ('Langkah ' + (ni + 1)));
        const label = `L${ni + 1} · ${title}`;
        let cls = '';
        if (node.temp_id === this.startTempId) cls = ':::mulai';
        else if (this.isBuntu(node)) cls = ':::buntu';
        lines.push(`  ${id}["${label}"]${cls}`);
      });

      // Edge antar node
      this.nodes.forEach((node) => {
        const fromId = mid(node.temp_id);
        // Efektif options: _cont untuk message
        let opts = node.options || [];
        if (node.type === 'message' && node._cont) {
          const co = this.buildContOptions(node);
          if (co.length) opts = co;
        }
        opts.forEach((opt) => {
          const lbl = safe(opt.label, 18);
          const lp = lbl ? `|"${lbl}"|` : '';
          if (opt.target_action === 'goto_node' && opt.target_temp_id) {
            lines.push(`  ${fromId} -->${lp} ${mid(opt.target_temp_id)}`);
          } else if (opt.target_action === 'back_to_start' && this.startTempId) {
            const startLbl = lbl || '↩ menu';
            lines.push(`  ${fromId} -.->|"${startLbl}"| ${mid(this.startTempId)}`);
          } else if (opt.target_action === 'handoff') {
            needAgen = true;
            lines.push(`  ${fromId} -->${lp} _agen_`);
          } else if (opt.target_action === 'end') {
            needEnd = true;
            lines.push(`  ${fromId} -->${lp} _end_`);
          } else if (opt.target_action === 'back_previous') {
            lines.push(`  ${fromId} -.->|"↩ 1 langkah"| _prev_`);
          }
        });
        // Click handler — butuh securityLevel:'loose'
        lines.push(`  click ${fromId} call mbJumpToNode("${node.temp_id}")`);
      });

      // Node sintetis
      if (needAgen) {
        lines.push('  _agen_["👤 Agen"]:::agen');
        lines.push('  click _agen_ call mbJumpToNode("")');
      }
      if (needEnd)  lines.push('  _end_["✋ Selesai"]:::selesai');

      // Class definitions
      lines.push('  classDef mulai fill:#DCFCE7,stroke:#16A34A,color:#14532D,font-weight:bold');
      lines.push('  classDef buntu fill:#FEE2E2,stroke:#DC2626,color:#7F1D1D');
      lines.push('  classDef agen  fill:#E0F2FE,stroke:#0369A1,color:#0C4A6E');
      lines.push('  classDef selesai fill:#F1F5F9,stroke:#64748B,color:#334155');

      return lines.join('\n');
    },

    async renderMermaid() {
      this.mermaidReady = false;
      this.mermaidError = '';
      const container = this.$refs.mermaidContainer;
      if (!container) return;

      if (!window.mermaid) {
        this.mermaidError = 'Mermaid belum dimuat (cek koneksi internet).';
        this.mermaidReady = true;
        return;
      }
      if (!this.nodes.length) {
        container.innerHTML = '<div class="mb-map-empty"><i class="bx bx-sitemap fs-1 text-muted d-block mb-2"></i>Belum ada langkah.</div>';
        this.mermaidReady = true;
        return;
      }

      const def = this.buildMermaidDef();
      if (!def) { this.mermaidReady = true; return; }

      try {
        const uid = 'mbmap_' + Date.now();
        const { svg, bindFunctions } = await window.mermaid.render(uid, def);
        container.innerHTML = svg;
        if (bindFunctions) bindFunctions(container); // bind click mbJumpToNode
        this.mermaidReady = true;
      } catch (e) {
        this.mermaidError = 'Gagal render diagram.';
        console.warn('[Mermaid]', e.message);
        this.mermaidReady = true;
      }
    },

    // ── Template starter ──
    applyTemplate(key) {
      if (key === 'kosong') {
        const t = this.newTemp();
        this.nodes = [{ temp_id: t, type: 'buttons', body_text: '', header: '', footer: '', list_button_label: '', position: 1, options: [], _cont: null, _showExtras: false }];
        this.startTempId = t; this.selectedTempId = t; this.previewStart(); return;
      }
      const T = this.TEMPLATES[key];
      if (!T) return;
      const idMap = {};
      T.nodes.forEach(n => { idMap[n.key] = this.newTemp(); });
      this.nodes = T.nodes.map((n, i) => ({
        temp_id: idMap[n.key],
        type: n.type,
        body_text: n.body || '',
        header: '', footer: '',
        list_button_label: n.type === 'list' ? (n.listLabel || 'Pilih') : '',
        position: i + 1,
        options: (n.opts || []).map((o, oi) => ({
          kind: n.type === 'list' ? 'list_row' : 'button',
          label: o.label,
          description: o.desc || '',
          order: oi + 1,
          target_action: o.act,
          target_temp_id: o.to ? (idMap[o.to] || '') : '',
        })),
        _cont: n.type === 'message' ? { menu: false, cs: false, goto: false, goto_target: '' } : null,
        _showExtras: false,
      }));
      this.startTempId = idMap[T.start];
      this.selectedTempId = this.startTempId;
      this.previewStart();
      this.growAllBodies();
    },

    // ── Load data ──
    async loadData() {
      if (!this.flowId) {
        const el = document.getElementById('app');
        try { this.devices = JSON.parse(el.dataset.devices || '[]'); } catch {}
        return;
      }
      try {
        const { data } = await this.$axios.get(`auto-reply/menu-otomatis/${this.flowId}/data`);
        const f = data.flow;
        this.flow = {
          name: f.name,
          trigger_type: f.trigger_type,
          keyword_match: f.keyword_match || 'exact',
          trigger_keywords: f.trigger_keywords || [],
          channels: f.channels || [],
          fallback_action: f.fallback_action || 'ai_agent',
          session_timeout_min: f.session_timeout_min || 30,
          status: f.status,
        };
        this.devices = data.devices || [];

        const rawNodes = Array.isArray(f.nodes) ? f.nodes : [];
        const idToTemp = {};
        const pass1 = rawNodes.map(n => {
          if (!n) return null;
          const tid = this.newTemp();
          idToTemp[n.id] = tid;
          return { _rawNode: n, temp_id: tid };
        }).filter(Boolean);

        // NOTE (catatan merah A): node dari DB TIDAK di-reverse-map ke _cont.
        // type=buttons tetap tampil sebagai "Tombol" — chip "Tanpa pilihan" hanya
        // untuk node message baru yang dibuat via UI. Ini biar gak ambigu.
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
          // _cont null untuk semua node dari DB (tidak di-reverse-map)
          _cont: null,
          // Auto-buka header/footer jika sudah ada isinya
          _showExtras: !!(n.header || n.footer || n.list_button_label),
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
    this.loadData().then(() => { this.previewStart(); this.growAllBodies(); });
    setInterval(() => {
      this.now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }, 10000);

    // Expose global click handler untuk Mermaid (securityLevel:'loose')
    window.mbJumpToNode = (tempId) => {
      if (!tempId) return; // klik node sintetis (agen) diabaikan
      this.activeTab = 'steps';
      this.selectedTempId = tempId;
      this.$nextTick(() => {
        const refKey = 'node_' + tempId;
        const ref = this.$refs[refKey];
        const card = Array.isArray(ref) ? ref[0] : ref;
        if (card && card.$el) {
          card.$el.scrollIntoView({ behavior: 'smooth', block: 'center' });
          card.$el.classList.add('mb-node-highlight');
          setTimeout(() => card.$el.classList.remove('mb-node-highlight'), 1200);
        } else if (card) {
          card.scrollIntoView({ behavior: 'smooth', block: 'center' });
          card.classList.add('mb-node-highlight');
          setTimeout(() => card.classList.remove('mb-node-highlight'), 1200);
        }
      });
    };
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
.mb-title-input {
  border:none; outline:none; font-size:17px; font-weight:600; color:#1E2A4A;
  min-width:200px; max-width:340px; background:transparent;
  border-bottom:2px solid transparent; transition:border-color .15s;
}
.mb-title-input:focus { border-bottom-color:#2E8DE1; }
.mb-header-right { display:flex; align-items:center; gap:8px; flex-shrink:0; }
.mb-buntu-hint { font-size:12px; color:#DC2626; font-weight:600; }

/* ── Settings Bar (1E) ───────────────────────────────── */
.mb-settings-bar { background:#fff; border-bottom:1px solid #E4EAF2; }
.mb-settings-summary {
  display:flex; align-items:center; gap:6px; padding:10px 20px;
  font-size:12px; cursor:pointer; user-select:none;
  color:#1E2A4A; transition:background .12s;
}
.mb-settings-summary:hover { background:#F5F8FC; }
.mb-settings-divider { color:#CBD5E1; margin:0 2px; }
.mb-settings-info { color:#64748B; flex:1; }
.mb-settings-toggle { color:#2E8DE1; font-size:11px; font-weight:600; flex-shrink:0; }
.mb-settings-panel { padding:16px 20px; background:#F5F8FC; border-top:1px solid #E4EAF2; }
.mb-settings-grid { display:flex; gap:20px; flex-wrap:wrap; }
.mb-setting-item { flex:1; min-width:160px; max-width:280px; }
.mb-setting-fallback { max-width:100% !important; }
.mb-label { font-size:11px; font-weight:600; text-transform:uppercase; color:#64748B; letter-spacing:.05em; display:block; margin-bottom:4px; }

/* Fallback radios (1E) */
.mb-fallback-radios { display:flex; gap:10px; flex-wrap:wrap; margin-top:4px; }
.mb-fallback-radio {
  display:flex; align-items:flex-start; gap:8px; cursor:pointer;
  background:#fff; border:1.5px solid #E4EAF2; border-radius:8px;
  padding:8px 12px; flex:1; min-width:160px; transition:border-color .15s;
}
.mb-fallback-radio:hover { border-color:#2E8DE1; }
.mb-fallback-radio input[type=radio] { margin-top:2px; flex-shrink:0; accent-color:#2E8DE1; }
.mb-fr-content { display:flex; flex-direction:column; gap:2px; }
.mb-fr-title { font-size:13px; font-weight:500; color:#1E2A4A; }
.mb-fr-desc { font-size:11px; color:#94A3B8; }

/* Tag input */
.mb-tag-input {
  display:flex; flex-wrap:wrap; gap:4px; align-items:center;
  border:1px solid #E4EAF2; border-radius:6px; padding:4px 8px;
  cursor:text; background:#fff; min-height:32px;
}
.mb-tag { background:#EAF3FC; color:#2E8DE1; font-size:12px; padding:2px 6px; border-radius:4px; display:flex; align-items:center; gap:4px; }
.mb-tag button { border:none; background:none; color:#2E8DE1; cursor:pointer; font-size:14px; line-height:1; padding:0; }
.mb-tag-input-field { border:none; outline:none; background:transparent; font-size:13px; min-width:80px; flex:1; }

/* Slide transition */
.mb-slide-enter-active, .mb-slide-leave-active { transition:all .2s ease; }
.mb-slide-enter, .mb-slide-leave-to { opacity:0; transform:translateY(-6px); }

/* ── Tabs ────────────────────────────────────────────── */
.mb-tabs {
  display:flex; gap:0; padding:0 20px;
  background:#fff; border-bottom:2px solid #E4EAF2;
}
.mb-tab {
  background:none; border:none; padding:10px 16px; font-size:13px; font-weight:500;
  color:#64748B; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-2px;
  transition:all .15s; display:flex; align-items:center;
}
.mb-tab:hover { color:#1E2A4A; }
.mb-tab.active { color:#2E8DE1; border-bottom-color:#2E8DE1; font-weight:600; }

/* ── Main ─────────────────────────────────────────────── */
.mb-content { display:flex; gap:0; flex:1; overflow:hidden; min-height:0; }

/* ── Steps ────────────────────────────────────────────── */
.mb-steps { flex:1; min-width:0; overflow-y:auto; max-width:620px; }
.mb-steps-list { padding:16px 20px; display:flex; flex-direction:column; gap:12px; }

/* Buntu summary */
.mb-buntu-summary {
  background:#FEF2F2; border:1px solid #FECACA; border-radius:8px;
  padding:10px 14px; font-size:13px; color:#DC2626;
}

/* Node card */
.mb-node-card {
  background:#fff; border:1.5px solid #E4EAF2; border-radius:12px; padding:14px 16px;
  cursor:pointer; transition:border-color .15s, box-shadow .15s;
}
.mb-node-card:hover { border-color:#2E8DE1; }
.mb-node-selected { border-color:#2E8DE1; box-shadow:0 0 0 3px rgba(46,141,225,.1); }
.mb-node-start { border-color:#16A34A; }
.mb-node-start.mb-node-selected { box-shadow:0 0 0 3px rgba(22,163,74,.12); }
.mb-node-buntu { border-color:#DC2626 !important; }

.mb-node-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
.mb-node-badges { display:flex; align-items:center; gap:6px; flex-wrap:wrap; min-width:0; }
.mb-node-num { font-size:13px; font-weight:700; color:#1E2A4A; }
.mb-node-title-txt { font-size:12px; color:#64748B; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.mb-badge-start { background:#DCFCE7; color:#16A34A; font-size:11px; font-weight:600; padding:2px 7px; border-radius:20px; }
.mb-badge-buntu {
  background:#FEF2F2; color:#DC2626; border:1px solid #FECACA;
  font-size:11px; font-weight:600; padding:2px 8px; border-radius:20px;
  cursor:pointer; transition:background .12s; white-space:nowrap;
}
.mb-badge-buntu:hover { background:#FEE2E2; }

.mb-node-actions { display:flex; gap:4px; flex-shrink:0; }
.mb-icon-btn { background:none; border:none; cursor:pointer; color:#64748B; padding:3px 5px; border-radius:5px; font-size:14px; transition:background .15s; }
.mb-icon-btn:hover { background:#F1F5F9; color:#1E2A4A; }
.mb-icon-danger { color:#DC2626 !important; }

.mb-node-body {
  width:100%; border:1px solid #E4EAF2; border-radius:8px; padding:8px 10px;
  font-size:13px; resize:none; background:#F5F8FC; transition:border-color .15s; box-sizing:border-box;
  overflow:hidden; min-height:60px;
}
.mb-node-body:focus { border-color:#2E8DE1; outline:none; background:#fff; }

/* ── Cara lanjut chips (1A) ───────────────────────────── */
.mb-cara-lanjut { margin-top:10px; display:flex; align-items:flex-start; gap:8px; flex-wrap:wrap; }
.mb-cl-label { font-size:11px; font-weight:600; color:#94A3B8; text-transform:uppercase; letter-spacing:.04em; white-space:nowrap; padding-top:5px; }
.mb-cl-chips { display:flex; gap:5px; flex-wrap:wrap; }
.mb-cl-chip {
  background:#F5F8FC; border:1.5px solid #E4EAF2; border-radius:20px;
  padding:3px 10px; font-size:12px; font-weight:500; color:#64748B;
  cursor:pointer; display:flex; align-items:center; gap:4px;
  transition:all .15s; white-space:nowrap;
}
.mb-cl-chip:hover:not(:disabled) { border-color:#2E8DE1; color:#2E8DE1; background:#EAF3FC; }
.mb-cl-chip.active { background:#2E8DE1; color:#fff; border-color:#2E8DE1; }
.mb-cl-count { font-size:10px; opacity:.8; }
.mb-cl-disabled { opacity:.5; cursor:not-allowed !important; }
.mb-cl-soon { background:#F59E0B; color:#fff; font-size:9px; padding:1px 5px; border-radius:10px; }

/* ── Header/footer extras ─────────────────────────────── */
.mb-extras-toggle {
  background:none; border:none; color:#2E8DE1; font-size:12px; cursor:pointer;
  padding:4px 0; margin-top:6px; display:block;
}
.mb-node-extras { margin-top:6px; display:flex; flex-direction:column; gap:5px; }
.mb-extra-input { border:1px solid #E4EAF2; border-radius:6px; padding:5px 8px; font-size:12px; width:100%; background:#F5F8FC; box-sizing:border-box; }
.mb-extra-input:focus { border-color:#2E8DE1; outline:none; background:#fff; }
.mb-extra-label { font-size:11px; color:#94A3B8; display:block; margin-bottom:3px; margin-top:4px; }

/* ── Options ─────────────────────────────────────────── */
.mb-options { margin-top:10px; border-top:1px solid #F1F5F9; padding-top:10px; display:flex; flex-direction:column; gap:7px; }
.mb-option-row { display:flex; align-items:flex-start; gap:6px; }
.mb-option-inputs { display:flex; flex-direction:column; gap:3px; flex:1; min-width:0; }
.mb-opt-label { border:1px solid #E4EAF2; border-radius:5px; padding:4px 7px; font-size:12px; width:100%; box-sizing:border-box; }
.mb-opt-label:focus { border-color:#2E8DE1; outline:none; }
.mb-opt-desc { border:1px solid #E4EAF2; border-radius:5px; padding:4px 7px; font-size:12px; width:100%; color:#64748B; box-sizing:border-box; }
.mb-char-count { font-size:10px; color:#94A3B8; text-align:right; }

.mb-option-target { min-width:160px; display:flex; flex-direction:column; gap:2px; }
.mb-opt-target-label { font-size:10px; font-weight:600; color:#2E8DE1; text-align:right; padding-right:2px; }
.mb-opt-clickable { cursor:pointer; text-decoration:underline dotted; }
.mb-opt-clickable:hover { color:#1a6dbf; }
.mb-target-unified { font-size:12px !important; }

.mb-add-opt { background:none; border:1px dashed #CBD5E1; border-radius:6px; padding:5px 10px; font-size:12px; color:#64748B; cursor:pointer; width:100%; text-align:center; transition:all .15s; }
.mb-add-opt:hover:not(:disabled) { border-color:#2E8DE1; color:#2E8DE1; background:#EAF3FC; }
.mb-add-opt:disabled { opacity:.45; cursor:not-allowed; }

/* List hint */
.mb-list-hint { background:#FEF9EC; border:1px solid #FDE68A; border-radius:6px; padding:7px 10px; font-size:12px; color:#92400E; display:flex; align-items:center; gap:6px; }
.mb-list-hint-btn { background:#F59E0B; color:#fff; border:none; border-radius:5px; padding:3px 8px; font-size:12px; cursor:pointer; font-weight:500; }

/* ── Anti-buntu (1C) ─────────────────────────────────── */
.mb-antibuntu {
  margin-top:10px; border-top:1px solid #F1F5F9; padding-top:10px;
}
.mb-ab-label { font-size:11px; font-weight:600; color:#94A3B8; text-transform:uppercase; letter-spacing:.04em; margin-bottom:6px; }
.mb-ab-checks { display:flex; gap:10px; flex-wrap:wrap; }
.mb-ab-check { display:flex; align-items:center; gap:6px; font-size:13px; color:#1E2A4A; cursor:pointer; }
.mb-ab-check input[type=checkbox] { accent-color:#2E8DE1; width:14px; height:14px; }
.mb-ab-goto { margin-top:8px; }
.mb-ab-preview { margin-top:8px; display:flex; gap:5px; flex-wrap:wrap; }
.mb-ab-chip {
  background:#EAF3FC; color:#2E8DE1; border:1px solid #BFDBFE;
  font-size:12px; padding:2px 8px; border-radius:12px; font-weight:500;
}

/* ── Add node ─────────────────────────────────────────── */
.mb-add-node-area { margin-top:4px; }
.mb-add-node-btn { border:1.5px dashed #CBD5E1; border-radius:10px; padding:12px; text-align:center; color:#64748B; cursor:pointer; font-size:13px; transition:all .15s; }
.mb-add-node-btn:hover { border-color:#2E8DE1; color:#2E8DE1; background:#EAF3FC; }
.mb-type-picker { display:flex; align-items:center; gap:8px; flex-wrap:wrap; border:1.5px dashed #CBD5E1; border-radius:10px; padding:10px 14px; }
.mb-type-btn { display:flex; flex-direction:column; align-items:center; gap:4px; cursor:pointer; padding:8px 14px; border-radius:8px; font-size:12px; color:#1E2A4A; transition:background .15s; }
.mb-type-btn:hover { background:#F1F5F9; }
.mb-type-btn i { font-size:20px; color:#2E8DE1; }
.mb-type-btn-handoff i { color:#0369A1; }

/* Template picker */
.mb-tpl-picker { background:#fff; border:1.5px dashed #CBD5E1; border-radius:12px; padding:20px 16px; margin-bottom:12px; text-align:center; }
.mb-tpl-title { font-size:12px; font-weight:600; color:#64748B; text-transform:uppercase; letter-spacing:.06em; margin-bottom:14px; }
.mb-tpl-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.mb-tpl-card { display:flex; flex-direction:column; align-items:center; gap:5px; background:#F5F8FC; border:1.5px solid #E4EAF2; border-radius:10px; padding:14px 10px; cursor:pointer; transition:all .18s; font-family:inherit; }
.mb-tpl-card:hover { border-color:#2E8DE1; background:#EAF3FC; box-shadow:0 2px 8px rgba(46,141,225,.15); transform:translateY(-1px); }
.mb-tpl-card i { font-size:22px; color:#2E8DE1; }
.mb-tpl-card span { font-size:13px; font-weight:600; color:#1E2A4A; }
.mb-tpl-card small { font-size:11px; color:#94A3B8; line-height:1.3; text-align:center; }
.mb-tpl-blank i { color:#64748B; }
.mb-tpl-blank:hover { border-color:#64748B; background:#F1F5F9; box-shadow:none; }

/* ── Peta alur placeholder ───────────────────────────── */
.mb-map-placeholder { flex:1; display:flex; align-items:center; justify-content:center; padding:60px 20px; }
.mb-map-inner { text-align:center; max-width:320px; }
.mb-map-icon { font-size:48px; color:#CBD5E1; }

/* ── Preview ─────────────────────────────────────────── */
.mb-preview { width:280px; flex-shrink:0; border-left:1px solid #E4EAF2; background:#ECE5DD; display:flex; flex-direction:column; }
.mb-preview-header { padding:10px 14px; font-size:12px; font-weight:600; color:#64748B; text-transform:uppercase; letter-spacing:.05em; background:#fff; border-bottom:1px solid #E4EAF2; }
.mb-pv-toko { background:#128C7E; color:#fff; font-size:12px; font-weight:600; padding:8px 14px; }
.mb-preview-phone { flex:1; overflow-y:auto; padding:10px 8px; display:flex; flex-direction:column; gap:8px; }
.mb-preview-empty { text-align:center; color:#94A3B8; font-size:13px; margin:auto; }
.mb-preview-footer { padding:8px; background:#fff; border-top:1px solid #E4EAF2; text-align:center; }
.mb-pv-reset { background:none; border:none; font-size:12px; color:#2E8DE1; cursor:pointer; padding:4px 10px; border-radius:6px; transition:background .15s; }
.mb-pv-reset:hover { background:#EAF3FC; }

.mb-pv-row { display:flex; flex-direction:column; gap:4px; }
.mb-pv-row-user { align-items:flex-end; }
.mb-pv-row-bot  { align-items:flex-start; }
.mb-pv-bot-card { background:#fff; border-radius:2px 10px 10px 10px; padding:8px 10px; max-width:200px; box-shadow:0 1px 2px rgba(0,0,0,.12); }
.mb-pv-header { font-weight:700; font-size:12px; color:#1E2A4A; margin-bottom:2px; }
.mb-pv-body { font-size:13px; color:#333; white-space:pre-line; line-height:1.4; }
.mb-pv-footer { font-size:11px; color:#94A3B8; margin-top:2px; }
.mb-pv-time { font-size:10px; color:#94A3B8; text-align:right; margin-top:2px; }
.mb-pv-user-bubble { background:#DCF8C6; border-radius:10px 2px 10px 10px; padding:8px 10px; max-width:180px; font-size:13px; color:#111; box-shadow:0 1px 2px rgba(0,0,0,.10); }

.mb-pv-btns { display:flex; flex-direction:column; gap:3px; max-width:200px; }
.mb-pv-btn { background:#fff; border:none; border-radius:8px; padding:8px 10px; font-size:13px; color:#2E8DE1; font-weight:500; text-align:center; box-shadow:0 1px 2px rgba(0,0,0,.1); cursor:pointer; transition:background .12s, transform .1s; }
.mb-pv-btn:hover { background:#EAF3FC; transform:scale(1.02); }
.mb-pv-list-trigger { background:#fff; border-radius:8px; padding:8px 12px; font-size:13px; color:#2E8DE1; font-weight:500; text-align:center; max-width:200px; box-shadow:0 1px 2px rgba(0,0,0,.1); cursor:pointer; }
.mb-pv-list-trigger:hover { background:#EAF3FC; }
.mb-pv-list-panel { background:#fff; border-radius:8px; max-width:200px; box-shadow:0 2px 8px rgba(0,0,0,.15); }
.mb-pv-list-row { padding:8px 12px; border-bottom:1px solid #F1F5F9; font-size:13px; color:#1E2A4A; cursor:pointer; }
.mb-pv-list-row:last-child { border-bottom:none; }
.mb-pv-list-row:hover { background:#F1F5F9; }

/* ── Coba kirim ke WA (1F) ───────────────────────────── */
.mb-testsend { background:#fff; border-top:2px solid #E4EAF2; padding:12px 14px; flex-shrink:0; }
.mb-ts-header { font-size:12px; font-weight:600; color:#1E2A4A; margin-bottom:8px; display:flex; align-items:center; }
.mb-ts-body { display:flex; gap:6px; }
.mb-ts-input { flex:1; border:1px solid #E4EAF2; border-radius:6px; padding:6px 8px; font-size:13px; background:#F5F8FC; min-width:0; }
.mb-ts-input:focus { border-color:#2E8DE1; outline:none; background:#fff; }
.mb-ts-input:disabled { opacity:.5; }
.mb-ts-btn { background:#2E8DE1; color:#fff; border:none; border-radius:6px; padding:6px 10px; font-size:12px; font-weight:500; cursor:pointer; display:flex; align-items:center; white-space:nowrap; flex-shrink:0; }
.mb-ts-btn:disabled { opacity:.5; cursor:not-allowed; }
.mb-ts-hint { font-size:11px; color:#F59E0B; margin-top:5px; }
.mb-ts-note { font-size:10px; color:#94A3B8; margin-top:5px; line-height:1.4; }

/* ── Peta alur (Fase 2a) ────────────────────────────── */
.mb-map-view { flex:1; display:flex; flex-direction:column; min-height:0; overflow:hidden; }
.mb-map-toolbar {
  display:flex; align-items:center; gap:10px; padding:10px 20px;
  background:#fff; border-bottom:1px solid #E4EAF2; flex-shrink:0;
}
.mb-map-refresh-btn {
  background:none; border:1px solid #E4EAF2; border-radius:6px;
  padding:4px 10px; font-size:12px; color:#64748B; cursor:pointer;
  display:flex; align-items:center; transition:all .15s;
}
.mb-map-refresh-btn:hover { border-color:#2E8DE1; color:#2E8DE1; background:#EAF3FC; }
.mb-map-hint { font-size:12px; color:#94A3B8; display:flex; align-items:center; }
.mb-map-error-inline { font-size:12px; color:#DC2626; }
.mb-map-container {
  flex:1; overflow:auto; padding:20px;
  display:flex; align-items:flex-start; justify-content:flex-start;
}
.mb-map-container svg { max-width:none !important; height:auto; }
.mb-map-loading { color:#94A3B8; font-size:13px; margin:auto; display:flex; align-items:center; }
.mb-map-empty { color:#94A3B8; font-size:13px; text-align:center; margin:auto; }

/* Highlight saat klik dari peta */
.mb-node-highlight {
  animation: mb-flash 1.2s ease;
}
@keyframes mb-flash {
  0%,100% { box-shadow: 0 0 0 3px rgba(46,141,225,.1); }
  30%      { box-shadow: 0 0 0 6px rgba(46,141,225,.4); border-color:#2E8DE1; }
}

@media (max-width: 900px) {
  .mb-preview { display:none; }
  .mb-steps { max-width:100%; }
}
</style>
