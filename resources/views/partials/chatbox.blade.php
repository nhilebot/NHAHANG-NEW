{{-- resources/views/partials/chatbox.blade.php --}}
{{-- Nhúng vào layout chính: @include('partials.chatbox') --}}

<link rel="stylesheet" href="{{ asset('css/partials/chatbox.css') }}?v=1">

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