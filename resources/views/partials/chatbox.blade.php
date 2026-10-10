{{-- resources/views/partials/chatbox.blade.php --}}
{{-- Nhúng vào layout chính: @include('partials.chatbox') --}}

<style>
/* ── BẢNG MÀU SANG TRỌNG (FINE DINING THEME) ── */
:root {
    --cb-primary: #D4AF37;       /* Vàng Gold nguyên bản */
    --cb-primary-dark: #B5952F;  /* Vàng Gold sậm (hiệu ứng hover) */
    --cb-bg: #FCFAF5;            /* Trắng kem ấm (Nền chat) */
    --cb-header-bg: #141414;     /* Đen nhám sâu (Header) */
    --cb-text-main: #33312E;     /* Xám đen chỉn chu (Chữ) */
    --cb-text-light: #8A857D;    /* Xám nhạt (Ghi chú) */
    --cb-msg-bot: #F5EFEB;       /* Trắng sữa hơi ngả beige (Bot) */
    --cb-border: #EAE3D5;        /* Viền vàng nhạt */
    --cb-font: 'Helvetica Neue', Arial, sans-serif;
}

/* ── Hình ảnh món ăn ── */
.cb-dish-img {
    width: 100%;
    max-width: 250px;
    border-radius: 12px;
    margin-bottom: 8px;
    display: block;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--cb-border);
    object-fit: cover;
}

/* ── Nút bấm xem chi tiết món ăn (Chất liệu Kim Loại Gold) ── */
.cb-link-btn {
    display: block;
    margin-top: 10px;
    padding: 10px 16px;
    background: linear-gradient(135deg, #E6C875 0%, var(--cb-primary) 50%, var(--cb-primary-dark) 100%);
    color: #fff !important;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.35);
    transition: all 0.25s ease;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.cb-link-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(212, 175, 55, 0.5);
    background: linear-gradient(135deg, #EFD690 0%, var(--cb-primary) 50%, var(--cb-primary-dark) 100%);
}

/* ── Nút nổi bật (Floating Button) ── */
#cb-toggle {
    position: fixed; bottom: 30px; right: 30px; z-index: 9990;
    width: 65px; height: 65px; border-radius: 50%;
    background: linear-gradient(135deg, #E6C875, var(--cb-primary), var(--cb-primary-dark));
    border: 2px solid rgba(255,255,255,0.2); 
    cursor: pointer; 
    box-shadow: 0 8px 24px rgba(212, 175, 55, 0.4);
    display: flex; align-items: center; justify-content: center;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}
#cb-toggle:hover { 
    transform: translateY(-5px) scale(1.05); 
    box-shadow: 0 12px 28px rgba(212, 175, 55, 0.6); 
}
#cb-toggle svg { width: 32px; height: 32px; fill: #fff; }

/* ── Cửa sổ Chat (Panel) ── */
#cb-panel {
    position: fixed; bottom: 110px; right: 30px; z-index: 9991;
    width: 380px; max-height: 650px; height: 80vh;
    background: var(--cb-bg); border-radius: 20px;
    box-shadow: 0 15px 50px rgba(0,0,0,0.2);
    display: flex; flex-direction: column;
    transform: scale(0.95) translateY(20px); opacity: 0; pointer-events: none;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
    font-family: var(--cb-font);
    border: 1px solid rgba(212, 175, 55, 0.2);
}
#cb-panel.open { transform: scale(1) translateY(0); opacity: 1; pointer-events: auto; }

/* ── Header Đen Nhám & Vàng Gold ── */
#cb-header {
    background: var(--cb-header-bg);
    color: #fff; padding: 18px 20px;
    display: flex; align-items: center; gap: 12px;
    border-bottom: 2px solid var(--cb-primary);
}
#cb-header .cb-avatar {
    width: 44px; height: 44px; border-radius: 50%;
    background: rgba(212, 175, 55, 0.1);
    display: flex; align-items: center; justify-content: center; font-size: 22px;
    border: 1px solid var(--cb-primary);
}
#cb-header .cb-info { display: flex; flex-direction: column; }
#cb-header .cb-title { font-weight: 600; font-size: 1.15rem; color: var(--cb-primary); letter-spacing: 0.5px; }
#cb-header .cb-sub   { font-size: 0.8rem; color: #aaa; font-weight: 300; margin-top: 2px;}
#cb-close {
    margin-left: auto; background: transparent;
    border: none; color: #fff; border-radius: 50%; width: 32px; height: 32px;
    cursor: pointer; font-size: 20px; display: flex;
    align-items: center; justify-content: center; opacity: 0.6; transition: opacity 0.2s;
}
#cb-close:hover { opacity: 1; color: var(--cb-primary); }

/* ── Khu vực tin nhắn (Creamy Background) ── */
#cb-messages {
    flex: 1; overflow-y: auto; padding: 20px; display: flex;
    flex-direction: column; gap: 16px; scroll-behavior: smooth;
    background-color: var(--cb-bg);
}
#cb-messages::-webkit-scrollbar { width: 6px; }
#cb-messages::-webkit-scrollbar-track { background: transparent; }
#cb-messages::-webkit-scrollbar-thumb { background: #D6CFBE; border-radius: 10px; }

.cb-msg { max-width: 85%; display: flex; flex-direction: column; gap: 4px; animation: fadeIn 0.3s ease-out forwards; opacity: 0; transform: translateY(10px); }
@keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }

.cb-msg.bot  { align-self: flex-start; }
.cb-msg.user { align-self: flex-end; }

.cb-bubble {
    padding: 14px 18px; font-size: 0.95rem; line-height: 1.6;
    word-break: break-word; 
    white-space: pre-wrap;
}
/* Tin nhắn Bot: Trắng sữa ngả Beige */
.cb-msg.bot  .cb-bubble { 
    background: var(--cb-msg-bot); 
    color: var(--cb-text-main); 
    border-radius: 18px 18px 18px 4px; 
    border: 1px solid var(--cb-border);
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}
/* Tin nhắn Khách hàng: Gradient Vàng Gold */
.cb-msg.user .cb-bubble { 
    background: linear-gradient(135deg, var(--cb-primary), var(--cb-primary-dark)); 
    color: #fff; 
    border-radius: 18px 18px 4px 18px; 
    box-shadow: 0 4px 10px rgba(212, 175, 55, 0.2);
}

.cb-msg.bot .cb-bubble strong {
    font-weight: 700;
    color: var(--cb-primary-dark);
}

/* Typing indicator */
.cb-typing .cb-bubble { display: flex; align-items: center; gap: 6px; padding: 14px 18px; }
.cb-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--cb-primary-dark); animation: cb-bounce 1.4s infinite ease-in-out both; }
.cb-dot:nth-child(1) { animation-delay: -0.32s; }
.cb-dot:nth-child(2) { animation-delay: -0.16s; }
@keyframes cb-bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }

/* ── Gợi ý nhanh (Thanh viền mỏng Gold) ── */
#cb-quick { 
    display: flex; gap: 10px; flex-wrap: nowrap; overflow-x: auto; 
    padding: 12px 20px; background: var(--cb-bg); border-top: 1px solid var(--cb-border); 
}
#cb-quick::-webkit-scrollbar { display: none; }
.cb-quick-btn {
    flex: 0 0 auto;
    font-size: 0.85rem; padding: 8px 16px; border-radius: 20px;
    border: 1px solid var(--cb-primary); color: var(--cb-primary-dark); background: transparent;
    cursor: pointer; transition: all 0.2s ease; font-weight: 600;
}
.cb-quick-btn:hover { background: var(--cb-primary); color: #fff; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(212, 175, 55, 0.2); }

/* ── Ô nhập liệu (Input) ── */
#cb-input-row {
    display: flex; gap: 12px; padding: 16px 20px;
    background: var(--cb-bg);
}
#cb-input {
    flex: 1; border: 1px solid var(--cb-border); border-radius: 24px;
    padding: 12px 18px; font-size: 0.95rem; outline: none;
    transition: all 0.2s; background: #fff; color: var(--cb-text-main);
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
}
#cb-input:focus { border-color: var(--cb-primary); box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15); }
#cb-input::placeholder { color: var(--cb-text-light); }

#cb-send {
    width: 46px; height: 46px; border-radius: 50%; border: none; cursor: pointer;
    background: linear-gradient(135deg, var(--cb-primary), var(--cb-primary-dark)); 
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    transition: all 0.2s; box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);
}
#cb-send:hover { background: var(--cb-primary-dark); transform: scale(1.05); }
#cb-send:disabled { background: #D6CFBE; box-shadow: none; cursor: not-allowed; transform: none; }
#cb-send svg { width: 20px; height: 20px; fill: #fff; margin-left: 2px; }
</style>

{{-- Toggle button --}}
<button id="cb-toggle" title="Trợ lý AI nhà hàng" aria-label="Mở chatbot">
    <svg viewBox="0 0 24 24"><path d="M20 2H4C2.9 2 2 2.9 2 4v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 10H6v-2h12v2zm0-3H6V7h12v2z"/></svg>
</button>

{{-- Chat panel --}}
<div id="cb-panel" role="dialog" aria-label="Chatbot trợ lý">
    <div id="cb-header">
        <img src="{{ asset('images/logo.png') }}" alt="Món Việt Logo" style="height: 40px; width: auto; margin-right: -5px; margin-left: -5px;">
        <div>
            <div class="cb-title">Trợ lý AI Nhà hàng</div>
            <div class="cb-sub">Tinh hoa ẩm thực</div>
        </div>
        <button id="cb-close" aria-label="Đóng">✕</button>
    </div>

    <div id="cb-messages" aria-live="polite"></div>

    <div id="cb-quick">
        <button class="cb-quick-btn" data-q="Xem thực đơn hôm nay">🍽 Thực đơn</button>
        <button class="cb-quick-btn" data-q="Đặt bàn như thế nào?">📅 Đặt bàn</button>
        <button class="cb-quick-btn" data-q="Liên hệ nhà hàng">📞 Liên hệ</button>
    </div>

    <div id="cb-input-row">
        <input id="cb-input" type="text" placeholder="Nhập câu hỏi…" maxlength="500" autocomplete="off"/>
        <button id="cb-send" aria-label="Gửi">
            <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
        </button>
    </div>
</div>
<script>
(function () {
    const panel    = document.getElementById('cb-panel');
    const toggle   = document.getElementById('cb-toggle');
    const closeBtn = document.getElementById('cb-close');
    const msgs     = document.getElementById('cb-messages');
    const input    = document.getElementById('cb-input');
    const sendBtn  = document.getElementById('cb-send');
    const ENDPOINT = '{{ route("chatbot.ask", [], false) }}';
    const CSRF     = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // 🌟 THÊM: Đọc lịch sử từ bộ nhớ tạm của trình duyệt khi load trang
    let history = JSON.parse(sessionStorage.getItem('aurora_chat_history')) || [];
    let busy    = false;

    function escHtml(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function addMsg(role, text) {
        const wrap = document.createElement('div');
        wrap.className = 'cb-msg ' + role;
        
        let formattedText = escHtml(text);
        
        if (role === 'bot') {
            // 1. Chuyển Markdown hình ảnh thành thẻ <img>
            formattedText = formattedText.replace(/!\[([^\]]*)\]\(([^)]+)\)/g, '<img src="$2" alt="$1" class="cb-dish-img">');

            // 2. Chuyển Markdown link thành nút bấm (Nút Đặt món/Xem chi tiết)
            formattedText = formattedText.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" class="cb-link-btn">$1</a>');

            // 3. In đậm và xuống dòng
            formattedText = formattedText.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            formattedText = formattedText.replace(/(^|\n)\* /g, '$1• ');
        }

        wrap.innerHTML = '<div class="cb-bubble">' + formattedText + '</div>';
        msgs.appendChild(wrap);
        msgs.scrollTop = msgs.scrollHeight;
        return wrap;
    }

    function addTyping() {
        const wrap = document.createElement('div');
        wrap.className = 'cb-msg bot cb-typing';
        wrap.innerHTML = '<div class="cb-bubble"><span class="cb-dot"></span><span class="cb-dot"></span><span class="cb-dot"></span></div>';
        msgs.appendChild(wrap);
        msgs.scrollTop = msgs.scrollHeight;
        return wrap;
    }

    // 🌟 THÊM: In lại lịch sử cũ ra khung chat nếu có
    if (history.length > 0) {
        history.forEach(item => {
            const role = item.role === 'model' ? 'bot' : 'user';
            addMsg(role, item.text);
        });
    }

    toggle.addEventListener('click', function () {
        const isOpen = panel.classList.toggle('open');
        if (isOpen && msgs.children.length === 0) {
            const greeting = 'Xin chào! 👋 Tôi là trợ lý AI của nhà hàng Aurora Garden. Bạn cần hỗ trợ xem thực đơn hay đặt bàn ạ?';
            addMsg('bot', greeting);
            
            // 🌟 THÊM: Lưu câu chào vào lịch sử
            history.push({ role: 'model', text: greeting });
            sessionStorage.setItem('aurora_chat_history', JSON.stringify(history));
        }
    });

    closeBtn.addEventListener('click', function () { panel.classList.remove('open'); });

    document.querySelectorAll('.cb-quick-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!busy) send(btn.dataset.q);
        });
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey && !busy) send(input.value);
    });
    sendBtn.addEventListener('click', function () { if (!busy) send(input.value); });

    async function send(text) {
        text = (text || '').trim();
        if (!text) return;
        busy = true;
        sendBtn.disabled = true;
        input.value = '';

        addMsg('user', text);
        const typing = addTyping();

        history.push({ role: 'user', text: text });
        
        // 🌟 THÊM: Lưu câu hỏi của khách hàng vào bộ nhớ
        sessionStorage.setItem('aurora_chat_history', JSON.stringify(history));

        try {
            const res = await fetch(ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'X-CSRF-TOKEN':  CSRF,
                    'Accept':        'application/json',
                },
                body: JSON.stringify({ message: text, history: history.slice(-10) }),
            });

            const raw = await res.text();
            let data = {};

            try {
                data = raw ? JSON.parse(raw) : {};
            } catch (parseErr) {
                data = { error: 'Máy chủ trả về dữ liệu không hợp lệ. Vui lòng thử lại sau.' };
            }

            typing.remove();

            const reply = data.reply || data.error?.message || data.error || 'Có lỗi xảy ra, vui lòng thử lại.';
            addMsg('bot', reply);
            history.push({ role: 'model', text: reply });

            if (history.length > 20) history = history.slice(-20);
            
            // 🌟 THÊM: Lưu câu trả lời của AI vào bộ nhớ
            sessionStorage.setItem('aurora_chat_history', JSON.stringify(history));
            
        } catch (err) {
            typing.remove();
            addMsg('bot', '⚠️ Không thể kết nối. Vui lòng kiểm tra mạng và thử lại.');
        }

        busy = false;
        sendBtn.disabled = false;
        input.focus();
    }
})();
</script>