// ============ CONFIG ============
const API_BASE = 'api';

// ============ DOM REFS ============
const authWrapper = document.getElementById('authWrapper');
const appWrapper = document.getElementById('appWrapper');
const doneWrapper = document.getElementById('doneWrapper');
const authError = document.getElementById('authError');
const selectKhoa = document.getElementById('id_khoa');
const inputLop = document.getElementById('id_lop');
const menuList = document.getElementById('menuList');
const titleText = document.getElementById('titleText');
const pageIcon = document.getElementById('pageIcon');
const dynamicContent = document.getElementById('dynamicContent');
const sidebarUser = document.getElementById('sidebarUser');
const progressFill = document.getElementById('progressFill');
const progressText = document.getElementById('progressText');
const submitBtn = document.getElementById('submitBtn');
const doneMessage = document.getElementById('doneMessage');

// ============ STATE ============
let tieuChiList = [];
let answers = {};
let hoTen = '';
let khoaData = [];
let currentTieuChiId = null;
let currentUser = null;

// ============ HELPERS ============
function showError(msg) { if (!authError) return; authError.textContent = msg; authError.classList.add('show'); }
function hideError() { if (!authError) return; authError.classList.remove('show'); }
function totalQuestions() { return tieuChiList.reduce((sum, tc) => sum + tc.cau_hoi.length, 0); }
function answeredCount() { return Object.keys(answers).length; }
function escapeHtml(str) { const div = document.createElement('div'); div.textContent = str ?? ''; return div.innerHTML; }

// ============ TABS ============
document.getElementById('tabLogin').addEventListener('click', () => {
    hideError();
    document.getElementById('tabLogin').classList.add('active');
    document.getElementById('tabRegister').classList.remove('active');
    document.getElementById('formLogin').style.display = 'block';
    document.getElementById('formRegister').style.display = 'none';
});
document.getElementById('tabRegister').addEventListener('click', () => {
    hideError();
    document.getElementById('tabRegister').classList.add('active');
    document.getElementById('tabLogin').classList.remove('active');
    document.getElementById('formRegister').style.display = 'block';
    document.getElementById('formLogin').style.display = 'none';
    if(document.getElementById('id_khoa').children.length === 1) loadKhoaLop();
});

// ============ LOAD KHOA ============
async function loadKhoaLop() {
    if (!selectKhoa) return;
    try {
        const res = await fetch(`${API_BASE}/get_khoa_lop.php`);
        const data = await res.json();
        if (data.success) {
            khoaData = data.khoa || [];
            selectKhoa.innerHTML = '<option value="">-- Chọn khoá --</option>' +
                khoaData.map(k => `<option value="${k.id}">${escapeHtml(k.ten_khoa)}</option>`).join('');
        }
    } catch (err) { console.error('Không thể tải danh sách khóa:', err); }
}

        // ========== XỬ LÝ ĐĂNG NHẬP ==========
        document.getElementById('formLogin').addEventListener('submit', async (e) => {
            e.preventDefault();
            hideError();
            const username = document.getElementById('login_email').value.trim();
            const password = document.getElementById('login_password').value.trim();
            const btn = document.getElementById('loginSubmitBtn');

            if (!username || !password) {
                showError('Vui lòng nhập đầy đủ tài khoản và mật khẩu.');
                return;
            }

            if(btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đăng nhập...'; }
            try {
                const res = await fetch(`${API_BASE}/login.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
                });
                const data = await res.json();

                if (data.success) {
                    // 1. Chuyển trang Admin
                    if (data.role === 'admin') {
                        window.location.href = 'admin.html';
                        return;
                    }

                    // 2. Chuyển thẳng sang màn hình Kết quả (nếu tài khoản đã hoàn thành)
                    if (data.is_completed === true) {
                        document.getElementById('authWrapper').style.display = 'none';
                        document.getElementById('appWrapper').style.display = 'none';
                        document.getElementById('doneWrapper').style.display = 'flex';
                        document.getElementById('doneMessage').textContent = 'Chào mừng bạn trở lại! Kết quả khảo sát của bạn là:';
                        loadKetQua(); // Gọi hàm tải và hiển thị bảng kết quả ngay lập tức
                        return;
                    }

                    // 3. Vào giao diện khảo sát (User chưa làm)
                    document.getElementById('authWrapper').style.display = 'none';
                    document.getElementById('appWrapper').style.display = 'flex';
                    startSurvey();
                } else {
                    showError(data.message || 'Đăng nhập thất bại.');
                }
            } catch (err) {
                showError('Lỗi kết nối máy chủ: ' + err.message);
            } finally {
                if(btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Đăng nhập'; }
            }
        });

// ============ XỬ LÝ ĐĂNG KÝ ============
document.getElementById('formRegister').addEventListener('submit', async (e) => {
    e.preventDefault();
    hideError();
    const btn = document.getElementById('registerSubmitBtn');
    const dataToSend = new URLSearchParams();
    dataToSend.append('ho_ten', document.getElementById('reg_ho_ten').value);
    dataToSend.append('email_sdt', document.getElementById('reg_email').value);
    dataToSend.append('password', document.getElementById('reg_password').value);
    dataToSend.append('id_khoa', document.getElementById('id_khoa').value);
    dataToSend.append('lop', document.getElementById('id_lop').value);

    if(btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...'; }
    try {
        const res = await fetch(`${API_BASE}/register.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: dataToSend.toString()
        });
        const data = await res.json();
        if (data.success) {
            if (data.role === 'user') {
                document.getElementById('authWrapper').style.display = 'none';
                document.getElementById('appWrapper').style.display = 'flex';
                startSurvey();
            }
        } else {
            showError(data.message || 'Đăng ký thất bại!');
        }
    } catch (err) {
        showError('Lỗi kết nối: ' + err.message);
    } finally {
        if(btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-user-plus"></i> Đăng ký ngay'; }
    }
});

// ============ START SURVEY ============
async function startSurvey() {
    try {
        const res = await fetch(`${API_BASE}/get_cauhoi.php`);
        if (res.status === 401) { // Nếu bị từ chối quyền, thử logout hoặc reload
            sessionStorage.removeItem('user');
            location.reload();
            return;
        }
        const data = await res.json();

        if (res.status === 409) {
            if (authWrapper) authWrapper.style.display = 'none';
            if (appWrapper) appWrapper.style.display = 'none';
            if (doneWrapper) doneWrapper.style.display = 'flex';
            if (doneMessage) doneMessage.textContent = data.message || 'Bạn đã hoàn thành khảo sát.';
            loadKetQua();
            return;
        }

        if (!data.success) {
            if (authWrapper && authWrapper.style.display !== 'none') return;
            showError(data.message);
            return;
        }

        tieuChiList = data.tieu_chi;
        if (authWrapper) authWrapper.style.display = 'none';
        if (appWrapper) appWrapper.style.display = 'flex';

        renderMenu();
        if (tieuChiList.length > 0) switchTab(tieuChiList[0].id);
        await updateAllScoresBadge();
    } catch (err) {
        if (authWrapper && authWrapper.style.display !== 'none') return;
        showError('Không thể tải câu hỏi: ' + err.message);
    }
}

// ============ RENDER MENU ============
function renderMenu() {
    if (!menuList) return;
    menuList.innerHTML = tieuChiList.map(tc => `
        <li class="menu-item" data-tab="${tc.id}">
            <span class="menu-item-name">${escapeHtml(tc.ten_tieu_chi)}</span>
            <span class="badge" id="badge-${tc.id}">Chưa khảo sát</span>
        </li>
    `).join('');
    document.querySelectorAll('.menu-item').forEach(item => {
        item.addEventListener('click', () => switchTab(parseInt(item.dataset.tab)));
    });
    updateAllBadges();
    updateAllScoresBadge();
}

function updateAllBadges() {
    updateProgress();
    if (submitBtn) submitBtn.disabled = false;
}

async function updateAllScoresBadge() {
    try {
        const res = await fetch(`${API_BASE}/ket_qua_tieu_chi.php`);
        const data = await res.json();
        if (!data.success) return;
        let totalScoreSum = 0, countCompleted = 0;
        data.tieu_chi.forEach(tc => {
            const badge = document.getElementById(`badge-${tc.id}`);
            if (!badge) return;
            const score = parseFloat(tc.diem_tieu_chi);
            if (score > 0) {
                badge.textContent = `${score.toFixed(2)}/5 đ`;
                badge.classList.add('complete');
                totalScoreSum += score; countCompleted++;
            } else {
                badge.textContent = "Chưa nộp";
                badge.classList.remove('complete');
            }
        });
        const boxTongDiem = document.getElementById('box-tong-diem');
        const txtTongDiem = document.getElementById('txt-tong-diem');
        if (boxTongDiem && txtTongDiem) {
            boxTongDiem.style.display = 'block';
            txtTongDiem.textContent = countCompleted > 0 ? parseFloat(data.dms || totalScoreSum/countCompleted).toFixed(2) : '0.00';
        }
    } catch (err) { console.error('Không thể cập nhật điểm tiêu chí:', err); }
}

function updateProgress() {
    if (!progressFill || !progressText) return;
    const total = totalQuestions();
    const done = answeredCount();
    progressFill.style.width = total > 0 ? `${(done / total) * 100}%` : '0%';
    progressText.textContent = `${done}/${total} câu đã trả lời`;
}

function switchTab(tabId) {
    currentTieuChiId = tabId;
    const tc = tieuChiList.find(t => t.id === tabId);
    if (!tc) return;
    document.querySelectorAll('.menu-item').forEach(item => {
        item.classList.toggle('active', parseInt(item.dataset.tab) === tabId);
    });
    if (titleText) titleText.textContent = tc.ten_tieu_chi;
    if (pageIcon) pageIcon.className = `fas ${tc.icon}`;
    renderQuestions(tc);
}

function renderQuestions(tc) {
    if (!dynamicContent) return;
    if (!tc.cau_hoi || tc.cau_hoi.length === 0) {
        dynamicContent.innerHTML = `<div class="empty-state"><i class="fas fa-inbox"></i><p>Chưa có câu hỏi nào</p></div>`;
        return;
    }
    let html = `<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:1rem;margin-bottom:1.5rem;">...</div>`;
    tc.cau_hoi.forEach((q, index) => {
        const current = answers[q.id] || 0;
        html += `
            <div class="question-item">
                <div class="q">${index + 1}. ${escapeHtml(q.noi_dung)}</div>
                <div class="rating-scale" data-question-id="${q.id}">
                    ${[1,2,3,4,5].map(val => `
                        <button type="button" class="scale-dot ${val === current ? 'active' : ''}" data-value="${val}">
                            <span class="scale-dot-num">${val}</span>
                        </button>
                    `).join('')}
                </div>
            </div>
        `;
    });
    dynamicContent.innerHTML = html;
    document.querySelectorAll('.rating-scale').forEach(group => {
        const questionId = parseInt(group.dataset.questionId);
        const dots = group.querySelectorAll('.scale-dot');
        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                const value = parseInt(dot.dataset.value);
                answers[questionId] = value;
                dots.forEach(d => d.classList.toggle('active', parseInt(d.dataset.value) === value));
                updateAllBadges();
            });
        });
    });
}

if (submitBtn) {
    submitBtn.addEventListener('click', async () => {
        const currentTc = tieuChiList.find(t => t.id === currentTieuChiId);
        if (!currentTc) return alert('Không tìm thấy thông tin tiêu chí.');
        if (currentTc.cau_hoi.filter(q => answers[q.id] === undefined).length > 0) {
            return alert(`Vui lòng trả lời hết câu hỏi còn lại trước khi nộp.`);
        }
        submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
        try {
            const res = await fetch(`${API_BASE}/nop_tieu_chi.php`, {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_tieu_chi: currentTieuChiId, answers: currentTc.cau_hoi.map(q => ({ id_cau_hoi: q.id, diem: answers[q.id] })) })
            });
            const data = await res.json();
            if (!data.success) { alert(data.message); } else { await updateAllScoresBadge(); alert(`Đã lưu! Điểm: ${data.diem_tieu_chi} / 5` + (data.da_hoan_tat_ca ? `\n\nHoàn thành! DMS: ${data.dms} / 5` : '')); }
        } catch (err) { alert('Lỗi: ' + err.message); } finally { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Nộp khảo sát'; }
    });
}

async function loadKetQua() {
    const resultPanel = document.getElementById('resultPanel');
    const dmsValue = document.getElementById('dmsValue');
    const resultPillars = document.getElementById('resultPillars');
    try {
        const res = await fetch(`${API_BASE}/ket_qua.php`);
        const data = await res.json();
        if (data.status === 'success') {
            if (dmsValue) dmsValue.textContent = data.dms_tong.toFixed(2) + '/5';
            if (resultPillars) {
                resultPillars.innerHTML = data.chi_tiet_tru_cot.map(tc => `
                    <div class="result-pillar">
                        <div class="result-pillar-head"><i class="fas ${tc.icon || 'fa-circle-exclamation'}"></i><span>${escapeHtml(tc.ten_tieu_chi)}</span><span>${parseFloat(tc.diem_tieu_chi).toFixed(2)}/5</span></div>
                        <div class="result-pillar-bar"><div class="result-pillar-fill" style="width:${(parseFloat(tc.diem_tieu_chi) / 5) * 100}%"></div></div>
                    </div>
                `).join('');
            }
            if (resultPanel) resultPanel.style.display = 'block';
        }
    } catch (err) { console.error(err); }
}

// ============ INIT ============
document.addEventListener('DOMContentLoaded', () => {
    loadKhoaLop();
    // Kiểm tra phiên đã lưu
    const savedUser = sessionStorage.getItem('user');
    if (savedUser) {
        currentUser = JSON.parse(savedUser);
        if (currentUser.role === 'admin') {
            window.location.href = 'admin.html';
            return;
        }
        if (sidebarUser) sidebarUser.textContent = currentUser.ho_ten;
        startSurvey();
    }
});

// ============ ĐĂNG XUẤT ============
document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) logoutBtn.addEventListener('click', (e) => { e.preventDefault(); if(confirm('Xác nhận đăng xuất?')) window.location.href = 'api/logout.php'; });
});