<?php
$pageTitle = 'Chat Interno';
$currentPage = 'chat';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../Helpers/CSRF.php';
$csrfToken = \App\Helpers\CSRF::getToken();
?>

<style>
/* ===== Chat Layout ===== */
.chat-container {
    display: flex;
    height: calc(100vh - 80px);
    background: var(--bg-main, #f5f6fa);
    margin: -1.5rem;
    overflow: hidden;
}

/* --- Sidebar (Conversaciones) --- */
.chat-sidebar {
    width: 340px;
    min-width: 340px;
    background: #fff;
    border-right: 1px solid rgba(0,0,0,.08);
    display: flex;
    flex-direction: column;
    z-index: 2;
    transition: transform .3s ease;
}

.chat-sidebar__header {
    padding: 16px 20px;
    border-bottom: 1px solid rgba(0,0,0,.06);
    display: flex;
    align-items: center;
    gap: 12px;
}

.chat-sidebar__header h3 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--text-main, #1a1a2e);
    flex: 1;
}

.chat-sidebar__search {
    padding: 12px 16px;
    border-bottom: 1px solid rgba(0,0,0,.04);
}

.chat-sidebar__search input {
    width: 100%;
    padding: 10px 14px 10px 38px;
    border: 1px solid rgba(0,0,0,.1);
    border-radius: 10px;
    font-size: .875rem;
    background: var(--bg-main, #f5f6fa);
    transition: border-color .2s, box-shadow .2s;
    outline: none;
}

.chat-sidebar__search input:focus {
    border-color: var(--primary, #4e6bff);
    box-shadow: 0 0 0 3px rgba(78,107,255,.1);
}

.chat-sidebar__search-icon {
    position: absolute;
    left: 28px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
    font-size: .85rem;
    pointer-events: none;
}

.chat-list {
    flex: 1;
    overflow-y: auto;
    padding: 6px 0;
}

.chat-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    cursor: pointer;
    transition: background .15s;
    border-left: 3px solid transparent;
}

.chat-item:hover {
    background: rgba(78,107,255,.04);
}

.chat-item.active {
    background: rgba(78,107,255,.08);
    border-left-color: var(--primary, #4e6bff);
}

.chat-item__avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: .85rem;
    color: #fff;
    flex-shrink: 0;
    text-transform: uppercase;
}

.chat-item__info {
    flex: 1;
    min-width: 0;
}

.chat-item__name {
    font-weight: 600;
    font-size: .9rem;
    color: var(--text-main, #1a1a2e);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-item__preview {
    font-size: .8rem;
    color: #888;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 2px;
}

.chat-item__meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
    flex-shrink: 0;
}

.chat-item__time {
    font-size: .7rem;
    color: #aaa;
}

.chat-item__badge {
    background: var(--primary, #4e6bff);
    color: #fff;
    font-size: .65rem;
    font-weight: 700;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* --- Main Chat Area --- */
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.chat-main__header {
    padding: 14px 24px;
    border-bottom: 1px solid rgba(0,0,0,.06);
    background: #fff;
    display: flex;
    align-items: center;
    gap: 14px;
    min-height: 65px;
}

.chat-main__back {
    display: none;
    border: none;
    background: none;
    font-size: 1.2rem;
    color: var(--primary, #4e6bff);
    cursor: pointer;
    padding: 4px 8px;
}

.chat-main__name {
    font-weight: 700;
    font-size: 1rem;
    color: var(--text-main, #1a1a2e);
}

.chat-main__subtitle {
    font-size: .75rem;
    color: #888;
}

.chat-main__placeholder {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #bbb;
    text-align: center;
    padding: 40px;
}

.chat-main__placeholder i {
    font-size: 4rem;
    margin-bottom: 16px;
    opacity: .4;
}

.chat-main__placeholder p {
    font-size: 1rem;
    max-width: 280px;
}

/* --- Messages --- */
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px 24px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    background: var(--bg-main, #f5f6fa);
}

.msg-bubble {
    max-width: 70%;
    padding: 10px 16px;
    border-radius: 16px;
    font-size: .875rem;
    line-height: 1.5;
    animation: msgIn .25s ease;
    word-wrap: break-word;
}

@keyframes msgIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

.msg-bubble--mine {
    align-self: flex-end;
    background: linear-gradient(135deg, #4e6bff, #6c63ff);
    color: #fff;
    border-bottom-right-radius: 4px;
}

.msg-bubble--other {
    align-self: flex-start;
    background: #fff;
    color: var(--text-main, #1a1a2e);
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
}

.msg-author {
    font-size: .7rem;
    font-weight: 600;
    color: var(--primary, #4e6bff);
    margin-bottom: 2px;
}

.msg-bubble--mine .msg-author {
    color: rgba(255,255,255,.7);
}

.msg-time {
    font-size: .65rem;
    margin-top: 4px;
    opacity: .6;
    text-align: right;
}

/* --- Input Bar --- */
.chat-input-bar {
    padding: 14px 24px;
    border-top: 1px solid rgba(0,0,0,.06);
    background: #fff;
    display: flex;
    align-items: center;
    gap: 12px;
}

.chat-input-bar textarea {
    flex: 1;
    resize: none;
    border: 1px solid rgba(0,0,0,.1);
    border-radius: 12px;
    padding: 10px 16px;
    font-size: .875rem;
    font-family: inherit;
    outline: none;
    max-height: 120px;
    min-height: 42px;
    transition: border-color .2s;
}

.chat-input-bar textarea:focus {
    border-color: var(--primary, #4e6bff);
}

.chat-input-bar .btn-send {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #4e6bff, #6c63ff);
    color: #fff;
    font-size: 1.1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .15s, box-shadow .15s;
    flex-shrink: 0;
}

.chat-input-bar .btn-send:hover {
    transform: scale(1.08);
    box-shadow: 0 4px 14px rgba(78,107,255,.35);
}

.chat-input-bar .btn-send:disabled {
    opacity: .5;
    cursor: not-allowed;
    transform: none;
}

/* --- New Chat Button --- */
.btn-new-chat {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: none;
    background: linear-gradient(135deg, #4e6bff, #6c63ff);
    color: #fff;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .15s;
    flex-shrink: 0;
}

.btn-new-chat:hover {
    transform: scale(1.1);
}

/* --- Modal Nueva Conversación --- */
.new-chat-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.4);
    z-index: 1050;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.new-chat-overlay.active {
    display: flex;
}

.new-chat-modal {
    background: #fff;
    border-radius: 16px;
    width: 100%;
    max-width: 420px;
    max-height: 70vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
    animation: modalIn .2s ease;
}

@keyframes modalIn {
    from { opacity: 0; transform: scale(.95); }
    to { opacity: 1; transform: scale(1); }
}

.new-chat-modal__header {
    padding: 18px 24px;
    border-bottom: 1px solid rgba(0,0,0,.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.new-chat-modal__header h4 {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 700;
}

.new-chat-modal__close {
    border: none;
    background: none;
    font-size: 1.2rem;
    color: #888;
    cursor: pointer;
}

.new-chat-modal__search {
    padding: 12px 20px;
    border-bottom: 1px solid rgba(0,0,0,.04);
}

.new-chat-modal__search input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid rgba(0,0,0,.1);
    border-radius: 10px;
    font-size: .875rem;
    outline: none;
}

.new-chat-modal__body {
    flex: 1;
    overflow-y: auto;
    padding: 8px 0;
}

.user-group-title {
    padding: 8px 24px;
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #888;
    background: rgba(0,0,0,.02);
}

.user-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 24px;
    cursor: pointer;
    transition: background .15s;
}

.user-item:hover {
    background: rgba(78,107,255,.06);
}

.user-item__avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: .8rem;
    color: #fff;
    text-transform: uppercase;
}

.user-item__name {
    font-weight: 600;
    font-size: .875rem;
}

.user-item__role {
    font-size: .72rem;
    color: #888;
}

/* --- Empty State --- */
.chat-empty {
    text-align: center;
    padding: 40px 20px;
    color: #aaa;
}

/* --- Responsive --- */
@media (max-width: 768px) {
    .chat-sidebar {
        position: absolute;
        width: 100%;
        min-width: 100%;
        height: 100%;
        transform: translateX(0);
    }

    .chat-container.conv-active .chat-sidebar {
        transform: translateX(-100%);
    }

    .chat-main__back {
        display: block;
    }

    .chat-container {
        position: relative;
    }

    .msg-bubble {
        max-width: 85%;
    }
}
</style>

<input type="hidden" id="csrf_token" value="<?= $csrfToken ?>">
<input type="hidden" id="current_user_id" value="<?= $_SESSION['user_id'] ?>">

<div class="chat-container" id="chatContainer" x-data="chatApp()" x-init="init()">

    <!-- Sidebar: Lista de Conversaciones -->
    <div class="chat-sidebar">
        <div class="chat-sidebar__header">
            <h3><i class="fas fa-comments" style="color:var(--primary,#4e6bff); margin-right:8px; font-size:.95rem;"></i>Mensajes</h3>
            <button class="btn-new-chat" @click="openNewChat()" title="Nueva conversación">
                <i class="fas fa-pen-to-square"></i>
            </button>
        </div>

        <div class="chat-sidebar__search" style="position:relative;">
            <i class="fas fa-search chat-sidebar__search-icon"></i>
            <input type="text" placeholder="Buscar conversación..." x-model="searchQuery">
        </div>

        <div class="chat-list">
            <template x-if="filteredConversaciones.length === 0 && !loadingConvs">
                <div class="chat-empty">
                    <i class="fas fa-comment-slash" style="font-size:2rem; opacity:.3; margin-bottom:10px;"></i>
                    <p style="font-size:.85rem;">No hay conversaciones aún.<br>Inicia una nueva.</p>
                </div>
            </template>

            <template x-for="conv in filteredConversaciones" :key="conv.id">
                <div class="chat-item" 
                     :class="{ 'active': activeConvId == conv.id }"
                     @click="selectConversacion(conv)">
                    <div class="chat-item__avatar" :style="'background:' + getAvatarColor(conv.id)">
                        <span x-text="getInitials(conv)"></span>
                    </div>
                    <div class="chat-item__info">
                        <div class="chat-item__name" x-text="getConvName(conv)"></div>
                        <div class="chat-item__preview">
                            <span x-show="conv.ultimo_mensaje" x-text="truncate(conv.ultimo_mensaje, 40)"></span>
                            <span x-show="!conv.ultimo_mensaje" style="font-style:italic;">Sin mensajes</span>
                        </div>
                    </div>
                    <div class="chat-item__meta">
                        <span class="chat-item__time" x-text="formatTime(conv.ultimo_mensaje_fecha)"></span>
                        <div x-show="conv.no_leidos > 0" class="chat-item__badge" x-text="conv.no_leidos"></div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Main: Área de mensajes -->
    <div class="chat-main">
        <!-- Placeholder si no hay conv seleccionada -->
        <template x-if="!activeConvId">
            <div class="chat-main__placeholder">
                <i class="fas fa-comments"></i>
                <p>Selecciona una conversación o inicia una nueva para empezar a chatear</p>
            </div>
        </template>

        <!-- Conversación activa -->
        <template x-if="activeConvId">
            <div style="display:flex; flex-direction:column; height:100%;">
                <div class="chat-main__header">
                    <button class="chat-main__back" @click="closeConversation()">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="chat-item__avatar" style="width:40px; height:40px; font-size:.8rem;" :style="'background:' + getAvatarColor(activeConvId)">
                        <span x-text="getInitials(activeConv)"></span>
                    </div>
                    <div>
                        <div class="chat-main__name" x-text="getConvName(activeConv)"></div>
                        <div class="chat-main__subtitle" x-text="getConvSubtitle(activeConv)"></div>
                    </div>
                </div>

                <div class="chat-messages" id="chatMessages" x-ref="chatMessages">
                    <template x-for="msg in mensajes" :key="msg.id">
                        <div class="msg-bubble" :class="msg.id_usuario == userId ? 'msg-bubble--mine' : 'msg-bubble--other'">
                            <div class="msg-author" x-show="msg.id_usuario != userId" x-text="msg.autor_nombre"></div>
                            <div x-text="msg.mensaje"></div>
                            <div class="msg-time" x-text="formatMsgTime(msg.created_at)"></div>
                        </div>
                    </template>

                    <div x-show="mensajes.length === 0 && !loadingMsgs" class="chat-empty" style="margin:auto;">
                        <i class="fas fa-paper-plane" style="font-size:2rem; opacity:.3; margin-bottom:10px;"></i>
                        <p style="font-size:.85rem;">Envía el primer mensaje</p>
                    </div>
                </div>

                <div class="chat-input-bar">
                    <textarea x-model="newMessage" 
                              @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                              placeholder="Escribe un mensaje..." 
                              rows="1"
                              x-ref="msgInput"></textarea>
                    <button class="btn-send" @click="sendMessage()" :disabled="sending || !newMessage.trim()">
                        <i class="fas" :class="sending ? 'fa-circle-notch fa-spin' : 'fa-paper-plane'"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- Modal: Nueva Conversación -->
    <div class="new-chat-overlay" :class="{ 'active': showNewChat }" @click.self="showNewChat = false">
        <div class="new-chat-modal">
            <div class="new-chat-modal__header">
                <h4><i class="fas fa-user-plus" style="color:var(--primary,#4e6bff); margin-right:8px;"></i>Nueva Conversación</h4>
                <button class="new-chat-modal__close" @click="showNewChat = false">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <div class="new-chat-modal__search">
                <input type="text" placeholder="Buscar usuario..." x-model="userSearchQuery" @input="filterUsers()">
            </div>
            <div class="new-chat-modal__body">
                <template x-if="loadingUsers">
                    <div class="chat-empty">
                        <i class="fas fa-circle-notch fa-spin" style="font-size:1.5rem;"></i>
                    </div>
                </template>
                <template x-for="(users, sucName) in filteredUsers" :key="sucName">
                    <div>
                        <div class="user-group-title">
                            <i class="fas fa-store-alt" style="margin-right:6px;"></i>
                            <span x-text="sucName"></span>
                        </div>
                        <template x-for="user in users" :key="user.id">
                            <div class="user-item" @click="startConversation(user.id)">
                                <div class="user-item__avatar" :style="'background:' + getAvatarColor(user.id)">
                                    <span x-text="user.nombre ? user.nombre.substring(0,2) : '??'"></span>
                                </div>
                                <div>
                                    <div class="user-item__name" x-text="user.nombre"></div>
                                    <div class="user-item__role" x-text="capitalize(user.rol)"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function chatApp() {
    return {
        // State
        conversaciones: [],
        mensajes: [],
        activeConvId: null,
        activeConv: null,
        newMessage: '',
        searchQuery: '',
        userSearchQuery: '',
        showNewChat: false,
        allUsers: {},
        filteredUsers: {},
        loadingConvs: true,
        loadingMsgs: false,
        loadingUsers: false,
        sending: false,
        userId: parseInt(document.getElementById('current_user_id').value),
        csrfToken: document.getElementById('csrf_token').value,
        // Optimización: un solo timer
        syncTimer: null,
        lastActivity: null,
        SYNC_INTERVAL: 15000, // 15 segundos en chat activo
        tabVisible: true,

        async init() {
            // Carga inicial completa
            await this.sync(true);
            this.loadingConvs = false;

            // Un solo loop de sync (reemplaza poll + loadMensajes + loadConversaciones)
            this.startSyncLoop();

            // Page Visibility API: pausar cuando la pestaña está oculta
            document.addEventListener('visibilitychange', () => {
                this.tabVisible = !document.hidden;
                if (this.tabVisible) {
                    // Al volver, sync inmediato y reiniciar loop
                    this.sync(true);
                    this.startSyncLoop();
                } else {
                    // Detener todo polling
                    this.stopSyncLoop();
                }
            });
        },

        startSyncLoop() {
            this.stopSyncLoop();
            this.syncTimer = setInterval(() => {
                if (this.tabVisible) this.sync();
            }, this.SYNC_INTERVAL);
        },

        stopSyncLoop() {
            if (this.syncTimer) {
                clearInterval(this.syncTimer);
                this.syncTimer = null;
            }
        },

        /**
         * UN SOLO fetch que trae todo: badge, conversaciones, mensajes
         */
        async sync(full = false) {
            try {
                let url = '/chat/sync?';
                if (this.lastActivity) url += 'last_ts=' + encodeURIComponent(this.lastActivity) + '&';
                if (this.activeConvId) url += 'conv_id=' + this.activeConvId + '&';
                if (full) url += 'full=1&';

                const res = await fetch(url);
                const data = await res.json();

                if (!data.success) return;

                // Actualizar badge del sidebar
                this.updateBadge(data.no_leidos);

                // Detectar mensajes nuevos para push notification
                const oldActivity = this.lastActivity;
                this.lastActivity = data.ultima_actividad;

                // Si hay cambios, actualizar conversaciones
                if (data.conversaciones) {
                    const prevNoLeidos = this.conversaciones.reduce((sum, c) => sum + parseInt(c.no_leidos || 0), 0);
                    this.conversaciones = data.conversaciones;

                    // Push notification si hay nuevos no leídos
                    if (oldActivity && data.no_leidos > 0 && data.has_changes) {
                        const nuevoMsg = this.conversaciones.find(c => parseInt(c.no_leidos) > 0);
                        if (nuevoMsg && prevNoLeidos < data.no_leidos) {
                            this.pushNotification(nuevoMsg);
                        }
                    }
                }

                // Si hay mensajes de la conv activa, actualizar
                if (data.mensajes && this.activeConvId) {
                    const hadCount = this.mensajes.length;
                    this.mensajes = data.mensajes;
                    if (this.mensajes.length > hadCount) {
                        this.$nextTick(() => this.scrollToBottom());
                    }
                    // La conv activa se marca como leída en el server
                    const conv = this.conversaciones.find(c => c.id == this.activeConvId);
                    if (conv) conv.no_leidos = 0;
                }
            } catch (e) {
                // Silencioso - no saturar consola
            }
        },

        updateBadge(count) {
            const badge = document.getElementById('chat-badge');
            if (badge) {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'flex' : 'none';
            }
            // Activar/desactivar pulse en sidebar
            const chatLink = document.querySelector('a[href="/chat"]');
            if (chatLink) {
                if (count > 0) {
                    chatLink.classList.add('chat-has-unread');
                } else {
                    chatLink.classList.remove('chat-has-unread');
                }
            }
        },

        pushNotification(conv) {
            if (window.notificationManager && document.hidden) {
                const nombre = this.getConvName(conv);
                const preview = conv.ultimo_mensaje || 'Nuevo mensaje';
                window.notificationManager.showNotification(
                    '💬 ' + nombre,
                    preview
                );
            }
        },

        get filteredConversaciones() {
            if (!this.searchQuery.trim()) return this.conversaciones;
            const q = this.searchQuery.toLowerCase();
            return this.conversaciones.filter(c => {
                const name = this.getConvName(c).toLowerCase();
                return name.includes(q);
            });
        },

        async selectConversacion(conv) {
            this.activeConvId = conv.id;
            this.activeConv = conv;
            this.loadingMsgs = true;

            document.getElementById('chatContainer').classList.add('conv-active');

            // Sync inmediato con la conv seleccionada
            await this.sync(true);
            this.loadingMsgs = false;
            this.scrollToBottom();
        },

        closeConversation() {
            this.activeConvId = null;
            this.activeConv = null;
            this.mensajes = [];
            document.getElementById('chatContainer').classList.remove('conv-active');
        },

        async sendMessage() {
            const msg = this.newMessage.trim();
            if (!msg || this.sending || !this.activeConvId) return;

            this.sending = true;
            const fd = new FormData();
            fd.append('mensaje', msg);

            try {
                const res = await fetch('/chat/enviar/' + this.activeConvId, {
                    method: 'POST',
                    headers: { 'X-CSRF-Token': this.csrfToken },
                    body: fd
                });
                const data = await res.json();
                if (data.success) {
                    this.newMessage = '';
                    // Sync inmediato tras enviar (1 petición, no 2)
                    await this.sync(true);
                    this.scrollToBottom();
                    this.$refs.msgInput.focus();
                } else {
                    if (typeof SIPAN !== 'undefined') SIPAN.error(data.message);
                }
            } catch (e) {
                if (typeof SIPAN !== 'undefined') SIPAN.error('Error de conexión');
            } finally {
                this.sending = false;
            }
        },

        async openNewChat() {
            this.showNewChat = true;
            if (Object.keys(this.allUsers).length === 0) {
                await this.loadUsers();
            }
        },

        async loadUsers() {
            this.loadingUsers = true;
            try {
                const res = await fetch('/chat/usuarios');
                const data = await res.json();
                if (data.success) {
                    this.allUsers = data.usuarios;
                    this.filteredUsers = { ...data.usuarios };
                }
            } catch (e) {
                console.error('Error cargando usuarios:', e);
            } finally {
                this.loadingUsers = false;
            }
        },

        filterUsers() {
            if (!this.userSearchQuery.trim()) {
                this.filteredUsers = { ...this.allUsers };
                return;
            }
            const q = this.userSearchQuery.toLowerCase();
            const result = {};
            for (const [suc, users] of Object.entries(this.allUsers)) {
                const filtered = users.filter(u => u.nombre.toLowerCase().includes(q));
                if (filtered.length) result[suc] = filtered;
            }
            this.filteredUsers = result;
        },

        async startConversation(otherUserId) {
            this.showNewChat = false;
            try {
                const fd = new FormData();
                fd.append('user_id', otherUserId);
                const res = await fetch('/chat/conversacion-directa', {
                    method: 'POST',
                    headers: { 'X-CSRF-Token': this.csrfToken },
                    body: fd
                });
                const data = await res.json();
                if (data.success) {
                    await this.sync(true);
                    const conv = this.conversaciones.find(c => c.id == data.conversacion_id);
                    if (conv) this.selectConversacion(conv);
                } else {
                    if (typeof SIPAN !== 'undefined') SIPAN.error(data.message);
                }
            } catch (e) {
                if (typeof SIPAN !== 'undefined') SIPAN.error('Error creando conversación');
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.chatMessages;
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        // --- Helpers ---
        getConvName(conv) {
            if (!conv) return '';
            if (conv.tipo === 'grupo') return conv.grupo_nombre || 'Grupo';
            return conv.otro_usuario_nombre || 'Usuario';
        },

        getConvSubtitle(conv) {
            if (!conv) return '';
            if (conv.tipo === 'grupo') return 'Canal grupal';
            let parts = [];
            if (conv.otro_usuario_rol) parts.push(this.capitalize(conv.otro_usuario_rol));
            if (conv.otro_usuario_sucursal) parts.push(conv.otro_usuario_sucursal);
            return parts.join(' · ') || 'En línea';
        },

        getInitials(conv) {
            if (!conv) return '?';
            const name = this.getConvName(conv);
            const parts = name.split(' ');
            if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
            return name.substring(0, 2).toUpperCase();
        },

        getAvatarColor(id) {
            const colors = [
                '#4e6bff', '#6c63ff', '#e74c3c', '#2ecc71', '#f39c12',
                '#1abc9c', '#9b59b6', '#e67e22', '#3498db', '#e91e63'
            ];
            return colors[(id || 0) % colors.length];
        },

        truncate(text, len) {
            if (!text) return '';
            return text.length > len ? text.substring(0, len) + '...' : text;
        },

        formatTime(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const now = new Date();
            const diffMs = now - d;
            const diffMin = Math.floor(diffMs / 60000);

            if (diffMin < 1) return 'Ahora';
            if (diffMin < 60) return diffMin + ' min';
            if (diffMin < 1440) return Math.floor(diffMin / 60) + 'h';
            if (diffMin < 10080) return Math.floor(diffMin / 1440) + 'd';
            return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
        },

        formatMsgTime(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
        },

        capitalize(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    };
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
